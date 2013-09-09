<?php

// Setup an error handler that throws exceptions for any errors occured
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    $errlvl = error_reporting();
    
    // ignore errors from calls suppressed with `@`
    if ($errlvl === 0)
        return false;
    
    // only report errors according to the current
    // error_reporting() level
    if ($errlvl === -1 || ($errlvl & $errno))
        throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
    
    // ignore all other errors
    return false;
});

// Report everything until we startup
error_reporting(-1);
assert_options(ASSERT_ACTIVE, 1);
assert_options(ASSERT_WARNING, 1);
assert_options(ASSERT_BAIL, 0);

// Store our install location
define('BJORK_ROOT', dirname(dirname(__DIR__)));

// Setup the autoloader
require_once BJORK_ROOT.'/bjork/utils/importlib.php';
use bjork\utils\importlib;
importlib::register_callbacks();
importlib::add_include_path(BJORK_ROOT);
importlib::add_include_path(BJORK_ROOT.'/lib');
importlib::add_include_path(BJORK_ROOT.'/vendor');
