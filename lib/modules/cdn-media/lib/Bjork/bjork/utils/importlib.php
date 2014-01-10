<?php

namespace bjork\utils;

final class importlib {
    
    static $lookup_formats = array(
        '%{module}\%{class}.php',
        '%{module}\%{class}\__init.php',
        
        '%{module}.php',
        '%{module}\__init.php',
        
        '%{class}.php',
        '%{class}\__init.php',
    );
    
    public static function register_callbacks() {
        spl_autoload_register(__CLASS__.'::import_class', true);
    }
    
    public static function unregister_callbacks() {
        spl_autoload_unregister(__CLASS__.'::import_class');
    }
    
    public static function get_include_paths() {
        return explode(PATH_SEPARATOR, get_include_path());
    }
    
    public static function set_include_paths(array $include_paths) {
        set_include_path(implode(PATH_SEPARATOR, array_unique($include_paths)));
    }
    
    public static function add_include_path($path, $prepend=false) {
        $include_paths = self::get_include_paths();
        self::remove_include_path($path);
        
        // Remove "." as we explicitly add it later
        $include_paths = array_filter($include_paths, function($p) {
            return $p != '.';
        });
        
        // Prepend or append the given path
        $prepend
            ? array_unshift($include_paths, $path)
            : array_push($include_paths, $path);
        
        // Prepend '.'
        array_unshift($include_paths, '.');
        
        self::set_include_paths($include_paths);
    }
    
    public static function remove_include_path($path) {
        $include_paths = self::get_include_paths();
        if (!in_array($path, $include_paths))
            return;
        $index = array_search($path, $include_paths);
        self::set_include_paths($include_paths);
    }
    
    public static function import_class($classname) {
        // Extract the module and class names
        $parts = preg_split('/\\\\/u', ltrim($classname, '\\'));
        $cls = array_pop($parts);
        $mod = implode('\\', $parts);
        
        // Build up the replacement patterns
        $search = array('%{class}', '%{module}');
        $replace = array($cls, $mod);
        
        $include_paths = self::get_include_paths();
        
        // Test each format
        foreach (self::$lookup_formats as $format) {
            // Build up and normalise the file path
            $lookup = str_replace($search, $replace, $format);
            $lookup = str_replace('\\', DIRECTORY_SEPARATOR, $lookup);
            $lookup = trim($lookup, DIRECTORY_SEPARATOR);
            
            // Strange things happen here. Using include_once directly
            // to try load the file is 4 times slower than us iterating
            // over the include paths and doing two more checks (is_file
            // and is_readable) and then including the file.
            foreach ($include_paths as $p) {
                $path = $p . DIRECTORY_SEPARATOR . $lookup;
                if (@is_file($path) && @is_readable($path)) {
                    include_once $path;
                    return true;
                }
            }
        }
        return false;
    }
}
