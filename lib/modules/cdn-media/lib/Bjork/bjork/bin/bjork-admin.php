#!/usr/bin/env php
<?php

if (version_compare(PHP_VERSION, '5.3.0', '<'))
    die('Bjork requires PHP 5.3.0 and greater. This PHP version is: '.phpversion());

if (php_sapi_name() == 'cli') {
    require_once __DIR__ . '/../core/init.php';
    \bjork\core\management::execute_from_command_line();
}
