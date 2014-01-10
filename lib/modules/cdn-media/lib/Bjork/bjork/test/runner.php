<?php

namespace bjork\test\runner;

use optparse;
use os\path;
use unittest\TestSuite,
    unittest\TestLoader,
    unittest\TestReporter;

class DiscoverRunner {
    var $pattern,
        $verbosity,
        $interactive,
        $failfast;
    
    var $labels;
    
    public static function getOptionList() {
        return array(
            optparse::make_option('-p', '--pattern', array(
                'action'  => 'store',
                'dest'    => 'pattern',
                'default' => 'test*.php',
                'help'    => 'The test matching pattern. Defaults to test*.php.'
            )),
        );
    }
    
    function __construct($options=null) {
        if (!$options)
            $options = array();
        
        extract(array_merge(array(
            'pattern' => null,
            'verbosity' => 1,
            'interactive' => true,
            'failfast' => false,
        ), $options));
        
        $this->pattern = $pattern;
        
        $this->verbosity = $verbosity;
        $this->interactive = $interactive;
        $this->failfast = $failfast;
        
        $this->labels = null;
    }
    
    /**
     * Run the unit tests for all the test labels in the provided list.
     *
     * Labels must be of the form:
     *
     *  - app.TestClass::test_method
     *      Run a single specific test method
     *  - app.TestClass
     *      Run all the test methods in a given class
     *  - app
     *      Search for unittests in the named application.
     *
     * When looking for tests, the test runner will look in the tests modules
     * for the application.
     *
     * A list of 'extra' tests may also be provided; these tests will be added
     * to the test suite.
     *
     * Returns the number of tests that failed.
     */
    function runTests($test_labels, $extra_tests=null, $options=null) {
        $this->setupTestEnvironment();
        $suite = $this->buildSuite($test_labels, $extra_tests);
        $result = $this->runSuite($suite);
        $this->teardownTestEnvironment();
        return $this->getSuiteResult($suite, $result);
    }
    
    function buildSuite($test_labels=null, $extra_tests=null, $options=null) {
        $test_labels = $test_labels ?: array('.');
        $extra_tests = $extra_tests ?: array();
        
        $suite = new TestSuite();
        $loader = new TestLoader();
        
        $labels = array();
        foreach ($test_labels as $label) {
            $label_as_path = path::abspath($label);
            if (!@file_exists($label_as_path)) {
                // we're given a namespace
                $labels[] = str_replace('.', '\\', $label);
            }
        }
        $this->labels = $labels;
        
        $tests = $loader->discover('.', $this->pattern);
        foreach ($tests as $test)
            $suite->add($test);
        foreach ($extra_tests as $test)
            $suite->add($test);
        
        return $suite;
    }
    
    function runSuite($suite, $options=null) {
        $result = new TestReporter($this->labels, $this->failfast, $this->verbosity);
        $suite->run($result);
        return $result;
    }
    
    function getSuiteResult($suite, $result, $options=null) {
        return $result->getFailCount() + $result->getExceptionCount();
    }
    
    function setupTestEnvironment($options=null) {
        
    }
    
    function teardownTestEnvironment($options=null) {
        
    }
}
