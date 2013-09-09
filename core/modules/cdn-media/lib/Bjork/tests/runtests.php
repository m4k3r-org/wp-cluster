<?php

define('TESTS_ROOT', dirname(__FILE__) . '/');
define('TESTSUITE_DIR', TESTS_ROOT . 'testsuite/');

require_once dirname(TESTS_ROOT) . '/bjork/core/init.php';
require_once 'simpletest/autorun.php';

use bjork\conf\settings,
    bjork\utils\importlib;

class BjorkTests extends TestSuite {
    function __construct() {
        parent::__construct('Bjork tests');
        $this->addFile(TESTSUITE_DIR . 'libs/test_urllib.php');
        $this->addFile(TESTSUITE_DIR . 'core/test_urls.php');
        // $this->addFile(TESTSUITE_DIR . 'core/test_encoding.php');
    }
}

function setup() {
    importlib::add_include_path(dirname(__FILE__));
    
    settings::configure(null, array(
        
    ));
}

setup();
