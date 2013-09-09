<?php

if (version_compare(PHP_VERSION, '5.3.0', '<'))
    die('Bjork requires PHP 5.3.0 and greater. This PHP version is: '.phpversion());

require_once dirname(__FILE__).'/core/init.php';

use bjork\conf\settings,
    bjork\core\management,
    bjork\utils\importlib,
    bjork\utils\translation;

final class bjork {
    
    static $handler_class = null;
    
    public static function get_version(array $version=null) {
        static $mapping = array(
            'alpha' => 'a',
            'beta' => 'b',
            'rc' => 'c');
        
        if (null === $version)
            $version = include 'bjork/core/version.php';
        
        assert('count($version) == 5');
        assert('in_array($version[3], array("alpha", "beta", "rc", "final"))');
        
        $parts = $version[2] == 0 ? 2 : 3;
        $main = implode('.', array_slice($version, 0, $parts));
        
        $sub = '';
        if ($version[3] == 'alpha' && $version[4] == 0)
            $sub = '.dev';
        else if ($version[3] != 'final')
            $sub = $mapping[$version[3]] . $version[4];
        
        return "{$main}{$sub}";
    }
    
    public static function set_handler_class($class) {
        self::$handler_class = $class;
    }
    
    public static function get_handler_class() {
        if (null !== self::$handler_class)
            return self::$handler_class;
        $handler_class = null;
        switch (php_sapi_name()) {
            default:
                $handler_class = 'bjork\core\handlers\generic\GenericHandler';
                break;
        }
        return $handler_class;
    }
    
    public static function set_settings_file($path) {
        putenv("BJORK_SETTINGS_MODULE={$path}");
    }
    
    public static function add_include_path($path) {
        $path = realpath($path);
        if ($path)
            importlib::add_include_path($path);
    }
    
    public static function get_response() {
        $handler_class = self::get_handler_class();
        $handler = new $handler_class();
        $lang = settings::get('LANGUAGE_CODE');
        translation::activate($lang);
        $response = $handler();
        return $response;
    }
    
    public static function execute_from_command_line($argv=null) {
        if (null === $argv)
            $argv = $_SERVER['argv'];
        management::execute_from_command_line($argv);
    }
}
