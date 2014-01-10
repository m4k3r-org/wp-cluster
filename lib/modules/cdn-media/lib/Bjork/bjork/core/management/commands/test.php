<?php

namespace bjork\core\management\commands\test;

use optparse,
    optparse\OptionGroup,
    optparse\OptionParser;

use strutils;

use bjork\conf\settings,
    bjork\core\management\base\BaseCommand,
    bjork\test\utils as test_utils;

class Command extends BaseCommand {
    static
        $help = "Runs the test suite for the specified applications, or the entire site if no apps are specified.",
        $args = "[appname ...]";
    
    public static function getOptionList() {
        return array(
            optparse::make_option('--noinput', array(
                'action'  => 'store_false',
                'dest'    => 'interactive',
                'default' => true,
                'help'    => 'Tells Bjork to NOT prompt the user for input of '.
                             'any kind.'
            )),
            optparse::make_option('--failfast', array(
                'action'  => 'store_true',
                'dest'    => 'failfast',
                'default' => false,
                'help'    => 'Tells Bjork to stop running the test suite after '.
                             'first failed test.'
            )),
            optparse::make_option('--testrunner', array(
                'action'  => 'store',
                'dest'    => 'testrunner',
                'help'    => 'Tells Bjork to use specified test runner class '.
                             'instead of the one specified by the TEST_RUNNER '.
                             'setting.'
            )),
        );
    }
    
    var $test_runner;
    
    function __construct() {
        $this->test_runner = null;
    }
    
    /**
     * Pre-parse the command line to extract the value of the --testrunner
     * option. This allows a test runner to define additional command line
     * arguments.
     */
    function runFromArgv($argv) {
        $option = '--testrunner=';
        foreach (array_slice($argv, 2) as $arg) {
            if (strutils::startswith($arg, $option)) {
                $this->test_runner = substr($arg, strlen($option));
                break;
            }
        }
        parent::runFromArgv($argv);
    }
    
    function createParser($prog_name, $subcommand) {
        $parser = new OptionParser(array(
            'prog' => $prog_name,
            'usage' => $this->getUsage($subcommand),
            'version' => $this->getVersion(),
            'option_list' => self::getDefaultOptionList(),
        ));
        
        $test_runner_class = $this->getTestRunnerClass($this->test_runner);
        
        $command_options = static::getOptionList();
        if (method_exists($test_runner_class, 'getOptionList')) {
            $command_options = array_merge($command_options,
                call_user_func("{$test_runner_class}::getOptionList"));
        }
        if ($command_options) {
            $command_option_group = new OptionGroup($parser, 'Command options');
            $command_option_group->addOptions($command_options);
            $parser->addOptionGroup($command_option_group);
        }
        
        return $parser;
    }
    
    function getTestRunnerClass($test_runner=null) {
        return $test_runner ?: settings::get('TEST_RUNNER');
    }
    
    function handle(array $test_labels, $options) {
        $options['verbosity'] = (int)$options['verbosity'];
        $test_runner_class = $this->getTestRunnerClass($this->test_runner);
        $test_runner = new $test_runner_class((array)$options);
        $failures = $test_runner->runTests($test_labels);
    }
}
