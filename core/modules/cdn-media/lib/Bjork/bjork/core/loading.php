<?php

namespace bjork\core\loading {

use os\path;

use bjork\conf\settings,
    bjork\core\exceptions,
    bjork\utils\importlib;

class App implements \ArrayAccess {
    protected
        $label,
        $module,
        $path;
    
    function __construct($label, $module, $baseDir) {
        $this->label = $label;
        $this->module = $module;
        $this->path = $baseDir;
    }
    
    public function getLabel() {
        return $this->label;
    }
    
    public function getPath() {
        $parts = array();
        $parts[] = $this->path;
        if (!empty($this->module))
            $parts[] = $this->module;
        return str_replace('\\', DIRECTORY_SEPARATOR, implode(DIRECTORY_SEPARATOR, $parts));
    }
    
    public function getFullPath() {
        $parts = array();
        $parts[] = $this->path;
        if (!empty($this->module))
            $parts[] = $this->module;
        $parts[] = $this->label;
        return str_replace('\\', DIRECTORY_SEPARATOR, implode(DIRECTORY_SEPARATOR, $parts));
    }
    
    public function offsetExists($k) {
        return in_array($k, array('label', 'module', 'path'));
    }
    
    public function offsetGet($k) {
        return $this->{$k};
    }
    
    public function offsetSet($k, $value) {
        throw new \Exception(__CLASS__.' is immutable.');
    }
    
    public function offsetUnset($k) {
        throw new \Exception(__CLASS__.' is immutable.');
    }

}

class AppCache {
    protected
        $cache = array(),
        $loaded = false;
    
    function __construct() {
        $this->populate();
    }
    
    function getApps() {
        return $this->cache;
    }
    
    function getApp($label) {
        return $this->cache[$label];
    }
    
    function loadApp($label, $module, $path) {
        unset($this->cache[$label]);
        
        $app = new App($label, $module, $path);
        
        $inits = array();
        $parts = explode('\\', $module);
        while ($p = array_pop($parts))
            $inits[] = path::join($path, $p, '__init.php');
        $inits[] = path::join($path, $module, $label, '__init.php');
        
        foreach ($inits as $path) {
            if (is_file($path))
                include_once $path;
        }
        
        $this->cache[$label] = $app;
    }
    
    function isReady() {
        return $this->loaded;
    }
    
    function populate() {
        if ($this->loaded)
            return;
        
        // Explicitly add bjork as an app
        $this->loadApp('bjork', '', BJORK_ROOT);
        
        // Load the apps specified in settings
        $apps = settings::get('INSTALLED_APPS');
        $incpaths = importlib::get_include_paths();
        foreach ($apps as $app) {
            $app = trim($app, '\\'); // strip initial backslash
            $parts = explode('\\', $app);
            $label = array_pop($parts);
            $module = implode('\\', $parts);
            if (isset($this->cache[$label]))
                continue;
            foreach ($incpaths as $path) {
                $path = rtrim($path, DIRECTORY_SEPARATOR);
                $p = trim(str_replace('\\', DIRECTORY_SEPARATOR, $app),
                    DIRECTORY_SEPARATOR);
                $p = $path . DIRECTORY_SEPARATOR . $p;
                if (is_dir($p)) {
                    $this->loadApp($label, $module, $path);
                    continue 2;
                }
            }
            throw new exceptions\ImproperlyConfigured(
                "App '{$app}' could not be found.");
        }
        $this->loaded = true;
    }
}

}

namespace bjork\core {

use bjork\core\loading\AppCache;

final class loading {
    
    private static $cache = null;
    
    private static function get_cache() {
        if (is_null(self::$cache)) 
            self::$cache = new AppCache();
        return self::$cache;
    }
    
    public static function load_apps() {
        self::get_cache(); // trigger loading
    }
    
    public static function get_apps() {
        return self::get_cache()->getApps();
    }
    
    public static function get_app($label) {
        return self::get_cache()->getApp($label);
    }
    
    public static function app_cache_ready() {
        return self::get_cache()->isReady();
    }
}

}
