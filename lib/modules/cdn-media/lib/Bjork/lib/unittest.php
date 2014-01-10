<?php

namespace unittest;

use fnmatch;
use os, os\path;
use strutils;

require_once 'simpletest/collector.php';
require_once 'simpletest/dumper.php';
require_once 'simpletest/expectation.php';
require_once 'simpletest/reporter.php';
require_once 'simpletest/scorer.php';
require_once 'simpletest/simpletest.php';
require_once 'simpletest/test_case.php';

function proxy($obj, \Closure $fn) {
    return function() use ($obj, $fn) {
        $args = func_get_args();
        array_unshift($args, $obj);
        return call_user_func_array($fn, $args);
    };
}

class Reporter extends \SimpleReporter {
    // pass
}

// SimpleReporterDecorator subclass that adds some missing Reporter methods
class ReporterDecorator extends \SimpleReporterDecorator {
    function getStatus() {
        return $this->reporter->getStatus();
    }
    
    function getPassCount() {
        return $this->reporter->getPassCount();
    }
    
    function getFailCount() {
        return $this->reporter->getFailCount();
    }
    
    function getExceptionCount() {
        return $this->reporter->getExceptionCount();
    }
}

class TestReporter extends ReporterDecorator {
    function __construct(array $labels=null, $failfast=false, $verbosity=1) {
        $reporter = new ConsoleReporter($verbosity);
        if ($labels)
            $reporter = new SelectiveReporter($reporter, $labels);
        if ($failfast)
            $reporter = new FailfastReporter($reporter);
        parent::__construct($reporter);
    }
}

class FailfastReporter extends ReporterDecorator {
    function shouldInvoke($test_case, $method) {
        if (!$this->getStatus())
            return false;
        else
            return $this->reporter->shouldInvoke($test_case, $method);
    }
}

class SelectiveReporter extends ReporterDecorator {
    function __construct($reporter, array $labels) {
        parent::__construct($reporter);
        $this->labels = $labels;
    }
    
    function shouldInvoke($test_case, $method) {
        $lookups = array("{$test_case}::{$method}");
        $namespace_parts = strutils::split($test_case, '\\');
        while (count($namespace_parts) > 0) {
            $lookups[] = implode('\\', $namespace_parts);
            array_pop($namespace_parts);
        }
        foreach ($lookups as $lookup) {
            if (in_array($lookup, $this->labels))
                return $this->reporter->shouldInvoke($test_case, $method);
        }
        return false;
    }
}

class ConsoleReporter extends Reporter {
    var $verbosity,
        $starttime,
        $testcount,
        $skipcount;
    
    var $current_test_name,
        $current_test_case;
    
    var $messages = array();
    
    static $result_map = array(
        'pass'  => array('.', 'ok'),
        'fail'  => array('F', 'FAIL'),
        'error' => array('E', 'ERROR'),
        'skip'  => array('S', 'skipped'),
    );
    
    function __construct($verbosity=1) {
        $this->verbosity = $verbosity;
        $this->testcount = 0;
        $this->skipcount = 0;
        $this->current_test_name = null;
        $this->current_test_case = null;
    }
    
    function paintHeader($test_name) {
        parent::paintHeader($test_name);
        $this->starttime = microtime(true);
    }
    
    function paintFooter($test_name) {
        parent::paintFooter($test_name);
        
        $endtime = microtime(true);
        $testcount = $this->testcount;
        $skipcount = $this->skipcount;
        $starttime = $this->starttime;
        
        if ($this->messages) {
            print "\n----------------------------------------------------------------------\n";
            foreach ($this->messages as $msg) {
                print "{$msg}\n";
            }
        }
        
        print "\n----------------------------------------------------------------------\n";
        print sprintf("Ran %s test%s in %.03fs\n",
            $testcount,
            $testcount > 1 ? 's' : '',
            $endtime - $starttime);
        print "\n";
        
        if ($this->getStatus())
            print "OK";
        else
            print sprintf("FAIL (%d failures; %d errors)",
                $this->getFailCount(),
                $this->getExceptionCount());
        
        if ($skipcount > 0)
            print " (skipped={$skipcount})";
        
        print "\n";
    }
    
    function paintGroupStart($test_name, $size) {
        parent::paintGroupStart($test_name, $size);
    }
    
    function paintGroupEnd($test_name) {
        parent::paintGroupEnd($test_name);
    }
    
    function paintCaseStart($test_name) {
        parent::paintCaseStart($test_name);
        $this->current_test_case = $test_name;
    }
    
    function paintCaseEnd($test_name) {
        parent::paintCaseEnd($test_name);
        $this->current_test_case = null;
    }
    
    function paintMethodStart($test_name) {
        parent::paintMethodStart($test_name);
        $this->current_test_name = $test_name;
    }
    
    function paintMethodEnd($test_name) {
        parent::paintMethodEnd($test_name);
        $this->current_test_name = null;
    }
    
    function paintPass($message) {
        parent::paintPass($message);
        $this->printCaseResult('pass');
    }
    
    function paintFail($message) {
        parent::paintFail($message);
        $this->printCaseResult('fail');
        $this->deferMessage($message);
    }
    
    function paintError($message) {
        parent::paintError($message);
        $this->printCaseResult('error');
        $this->deferMessage($message);
    }
    
    function paintException($exception) {
        parent::paintException($exception);
        $this->printCaseResult('error');
        $this->deferMessage($exception);
    }
    
    function paintSkip($message) {
        parent::paintSkip($message);
        $this->skipcount++;
        $this->printCaseResult('skip');
    }
    
    function deferMessage($message) {
        $this->messages[] = sprintf("%s::%s:\n  %s",
            $this->current_test_case,
            $this->current_test_name,
            $message);
    }
    
    function printCaseResult($code) {
        $verbosity = $this->verbosity;
        $this->testcount++;
        
        if ($verbosity > 1)
            $msg = sprintf("%s (%s) ... %%s\n", $this->current_test_name, $this->current_test_case);
        else
            $msg = sprintf("%%s%s", $this->testcount % 70 === 0 ? "\n" : '');
        
        print sprintf($msg, self::$result_map[$code][$verbosity > 1 ? 1 : 0]);
    }
}

/**
 * A SimpleCollector subclass that walks down the given path adding any
 * found tests that match the given glob pattern.
 */
class TestLoader extends \SimpleCollector {
    var $pattern;
    
    function __construct($pattern='test*.php') {
        $this->pattern = $pattern;
    }
    
    function discover($path, $pattern=null) {
        $suite = new TestSuite();
        $old_pattern = $this->pattern;
        $this->pattern = $pattern ?: $old_pattern;
        $this->collect($suite, $path);
        $this->pattern = $old_pattern;
        return $suite->getTests();
    }
    
    function collect(&$suite, $path) {
        $path = path::abspath($path);
        if (is_dir($path)) {
            os::walk2($path, false, proxy($this, function($self, $thisdir, $dirnames, $filenames) use ($suite) {
                foreach ($filenames as $filename) {
                    if ($filename{0} == '.') // hidden file
                        continue;
                    $p = path::join($thisdir, $filename);
                    $self->_handle($suite, $p);
                }
            }));
        } else if (is_file($path)) {
            $this->_handle($suite, $path);
        }
    }
    
    protected function handle(&$suite, $file) {
        $this->_handle($suite, $file);
    }
    
    function _handle(&$suite, $file) {
        if (fnmatch::match($file, $this->pattern))
            $suite->addFile($file);
    }
}

class TestFileLoader extends \SimpleFileLoader {
    function createSuiteFromClasses($title, $classes) {
        if (count($classes) == 0) {
            $suite = new \BadTestSuite($title, "No runnable test cases in [$title]");
            return $suite;
        }
        \SimpleTest::ignoreParentsIfIgnored($classes);
        $suite = new TestSuite($title); // use our test suite class
        foreach ($classes as $class) {
            if (!\SimpleTest::isIgnored($class)) {
                $suite->add($class);
            }
        }
        return $suite;
    }
}

class TestSuite extends \TestSuite {
    var $_tests = array();
    
    function add($test_case) {
        $t = $test_case;
        if (is_string($test_case))
            $t = new $test_case();
        
        if ($t instanceof TestSuite)
            $tests = $t->getTests();
        else
            $tests = array($t);
        
        $this->_tests = array_merge($this->_tests, $tests);
        
        parent::add($test_case);
    }
    
    function addFile($test_file) {
        // override to use our custom file loader so that
        // we get suite instances of this class.
        $extractor = new TestFileLoader();
        $this->add($extractor->load($test_file));
    }
    
    function getTests() {
        return $this->_tests;
    }
}

function safe_repr($value) {
    static $dumper = null;
    if (!$dumper)
        $dumper = new \SimpleDumper();
    return $dumper->describeValue($value);
}

function get_func_repr($callable) {
    $n = null;
    is_callable($callable, true, $n);
    return $n;
}

class RaisesExpectation extends \SimpleExpectation {
    var $exc_type, $exc_value;
    
    function __construct($fn, array $args=null, $msg='%s') {
        parent::__construct($msg);
        $this->fn = $fn;
        $this->args = $args ?: array();
    }
    
    function test($exc) {
        try {
            call_user_func_array($this->fn, $this->args);
        } catch (\Exception $e) {
            $this->exc_type = $exc_type = get_class($e);
            $this->exc_value = $exc_value = $e->getMessage();
            if (is_array($exc))
                return in_array($exc_type, $exc);
            else
                return $exc_type == $exc;
        }
        return false;
    }
    
    function testMessage($exc) {
        $repr = get_func_repr($this->fn);
        if (is_array($exc))
            $exc = implode(', ', $exc);
        if ($this->test($exc)) {
            return "{$repr} raised {$exc}";
        } else {
            return "{$repr} did not raise {$exc}";
        }
    }
}

class RaisesRegexpExpectation extends RaisesExpectation {
    var $re;
    
    function __construct($re, $fn, array $args=null, $msg='%s') {
        parent::__construct($fn, $args, $msg);
        $this->re = $re;
    }
    
    function test($exc) {
        return parent::test($exc) && preg_match($this->re, $this->exc_value);
    }
}

/*
    assertTrue(x)                                       bool(x) is True
    assertFalse(x)                                      bool(x) is False
    
    assertEqual(a, b)                                   a == b
    assertNotEqual(a, b)                                a != b
    
    assertGreater(a, b)                                 a > b
    assertGreaterEqual(a, b)                            a >= b
    assertLess(a, b)                                    a < b
    assertLessEqual(a, b)                               a <= b
    
    assertIn(a, b)                                      a in b
    assertNotIn(a, b)                                   a not in b
    
    assertIs(a, b)                                      a is b
    assertIsNot(a, b)                                   a is not b
    
    assertIsInstance(a, b)                              isinstance(a, b)
    assertNotIsInstance(a, b)                           not isinstance(a, b)
    
    assertIsNone(x)                                     x is None
    assertIsNotNone(x)                                  x is not None
    
    assertRaises(exc, fun, *args, **kwds)               fun(*args, **kwds) raises exc    
    assertRaisesRegexp(exc, re, fun, *args, **kwds)     fun(*args, **kwds) raises exc and the message matches re
    
    assertRegexpMatches(s, re)                          regex.search(s)
    assertNotRegexpMatches(s, re)                       not regex.search(s)
    
    assertAlmostEqual(a, b)                             round(a-b, 7) == 0
    assertNotAlmostEqual(a, b)                          round(a-b, 7) != 0
*/
abstract class TestCase extends \SimpleTestCase {
    function assertTrue($x, $msg='%s') {
        // bool(x) is True
        return $this->assert(new \TrueExpectation(), $x, $msg);
    }
    
    function assertFalse($x, $msg='%s') {
        // bool(x) is False
        return $this->assert(new \FalseExpectation(), $x, $msg);
    }
    
    function assertEqual($a, $b, $msg='%s') {
        // a == b
        return $this->assert(new \EqualExpectation($a), $b, $msg);
    }
    
    function assertNotEqual($a, $b, $msg='%s') {
        // a != b
        return $this->assert(new \NotEqualExpectation($a), $b, $msg);
    }
    
    function assertGreater($a, $b, $msg='%s') {
        // a > b
        return $this->assertTrue($a > $b, $msg);
    }
    
    function assertGreaterEqual($a, $b, $msg='%s') {
        // a >= b
        return $this->assertTrue($a >= $b, $msg);
    }
    
    function assertLess($a, $b, $msg='%s') {
        // a < b
        return $this->assertTrue($a < $b, $msg);
    }
    
    function assertLessEqual($a, $b, $msg='%s') {
        // a <= b
        return $this->assertTrue($a <= $b, $msg);
    }
    
    function assertIn($a, $b, $msg='%s') {
        // a in b
    }
    
    function assertNotIn($a, $b, $msg='%s') {
        // a not in b
    }
    
    function assertIs($a, $b, $msg='%s') {
        // a is b
        return $this->assertTrue($a === $b, $msg);
    }
    
    function assertIsNot($a, $b, $msg='%s') {
        // a is not b
        return $this->assertTrue($a !== $b, $msg);
    }
    
    function assertIsNone($x, $msg='%s') {
        // x is None
        $msg = sprintf($msg, '[' . safe_repr($x) . '] should be null');
        return $this->assertTrue($x === null, $msg);
    }
    
    function assertIsNotNone($x, $msg='%s') {
        // x is not None
        $msg = sprintf($msg, '[' . safe_repr($x) . '] should not be null');
        return $this->assertTrue($x !== null, $msg);
    }
    
    function assertIsInstance($a, $b, $msg='%s') {
        // isinstance(a, b)
        return $this->assert(new \IsAExpectation($b), $a, $msg);
    }
    
    function assertNotIsInstance($a, $b, $msg='%s') {
        // not isinstance(a, b)
        return $this->assert(new \NotAExpectation($b), $a, $msg);
    }
    
    function assertRaises($exc, $fun, array $args=null, $msg='%s') {
        // fun(*args, **kwds) raises exc
        return $this->assert(new RaisesExpectation($fun, $args), $exc, $msg);
    }
    
    function assertRaisesRegexp($exc, $re, $fun, array $args=null, $msg='%s') {
        // fun(*args, **kwds) raises exc and the message matches re
        return $this->assert(new RaisesRegexpExpectation($re, $fun, $args), $exc, $msg);
    }
    
    function assertRegexpMatches($s, $re, $msg='%s') {
        // regex.search(s)
        return $this->assert(new \PatternExpectation($re), $s, $msg);
    }
    
    function assertNotRegexpMatches($s, $re, $msg='%s') {
        // not regex.search(s)
        return $this->assert(new \NoPatternExpectation($re), $s, $msg);
    }
    
    function assertAlmostEqual($a, $b, $msg='%s') {
        // round(a-b, 7) == 0
        $this->assertTrue(round($a - $b, 7) == 0);
    }
    
    function assertNotAlmostEqual($a, $b, $msg='%s') {
        // round(a-b, 7) != 0
        $this->assertTrue(round($a - $b, 7) != 0);
    }
}
