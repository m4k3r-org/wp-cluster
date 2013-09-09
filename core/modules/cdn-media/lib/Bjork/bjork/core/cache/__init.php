<?php

namespace bjork\core;

use bjork\conf\settings;

final class cache {
    
    const DEFAULT_CACHE_ALIAS = 'default';
    
    private static $cache = null;
    
    public static function get_default_cache() {
        if (null === self::$cache)
            self::$cache = self::get_cache(self::DEFAULT_CACHE_ALIAS);
        return self::$cache;
    }
    
    /**
    * Function to load a cache backend dynamically.
    * 
    * To load a backend that is pre-defined in the settings::
    * 
    *   $cache = cache::get_cache('default');
    * 
    * To load a backend with its import path, including arbitrary options::
    * 
    *   $cache = cache::get_cache(
    *       'bjork\core\cache\backends\memcache\MemcacheBackend', array(
    *           'location' => '127.0.0.1:11211',
    *           'timeout'  => 300,
    *   ));
    */
    public static function get_cache($backend, array $params=null) {
        if (is_null($params))
            $params = array();
        list($backend_cls, $location, $options) = self::parse_backend_conf($backend, $params);
        $cache = new $backend_cls($location, $options);
        return $cache;
    }
    
    private static function parse_backend_conf($backend, array $params) {
        $caches = settings::get("CACHES");
        $conf = isset($caches[$backend]) ? $caches[$backend] : null;
        if ($conf) {
            $args = array_merge(array(), $conf, $params);
            $backend_cls = isset($args["backend"]) ? $args["backend"] : "";
        } else {
            $args = array_merge(array(), $params);
            $backend_cls = $backend;
        }
        $location = isset($args["location"]) ? $args["location"] : "";
        $key_function = isset($args["key_function"]) ? $args["key_function"] : "";
        $key_prefix = isset($args["key_prefix"]) ? $args["key_prefix"] : "";
        $timeout = isset($args["timeout"]) ? $args["timeout"] : 300;
        $version = isset($args["version"]) ? $args["version"] : 1;
        $options = isset($args["options"]) ? $args["options"] : null;
        return array($backend_cls, $location, array(
            "key_function" => $key_function,
            "key_prefix" => $key_prefix,
            "timeout" => $timeout,
            "version" => $version,
            "options" => $options,
        ));
    }
}
