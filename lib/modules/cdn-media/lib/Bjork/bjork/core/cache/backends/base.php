<?php

namespace bjork\core\cache\backends\base;

function make_key($key, $prefix, $version) {
    $parts = array();
    if (!empty($prefix)) $parts[] = $prefix;
    if (!empty($version)) $parts[] = $version;
    $parts[] = $key;
    return implode(":", $parts);
}

abstract class BaseBackend {
    
    const MEMCACHE_MAX_KEY_LENGTH = 250;
    
    public $defaultTimeout;
    
    protected
        $maxEntries,
        $cullFrequency,
        $keyPrefix,
        $keyFunction,
        $version;
    
    function __construct(array $params=null) {
        $this->defaultTimeout = (int)(isset($params["timeout"]) ? $params["timeout"] : 300);
        $this->keyPrefix = isset($params["key_prefix"]) ? $params["key_prefix"] : "";
        $this->keyFunction = !empty($params["key_function"]) ? $params["key_function"] : "bjork\core\cache\backends\base\make_key";
        $this->version = (int)(isset($params["version"]) ? $params["version"] : 1);
        
        $options = isset($params["options"]) ? $params["options"] : array();
        $this->maxEntries = (int)(isset($options["max_entries"]) ? $options["max_entries"] : 300);
        $this->cullFrequency = (int)(isset($options["cull_frequency"]) ? $options["cull_frequency"] : 3);
    }
    
    /**
    * Set a value in the cache if the key does not already exist. If
    * $timeout is given, that timeout will be used for the key; otherwise
    * the default cache timeout will be used.
    * 
    * Returns True if the value was stored, False otherwise.
    */
    public abstract function add($key, $value, $timeout=null, $version=null);
    
    /**
    * Set a value in the cache. If $timeout is given, that timeout will be
    * used for the key; otherwise the default cache timeout will be used.
    */
    public abstract function set($key, $value, $timeout=null, $version=null);
    
    /**
    * Fetch a given key from the cache. If the key does not exist, return
    * $default, which itself defaults to None.
    */
    public abstract function get($key, $default=null, $version=null);
    
    /**
    * Delete a key from the cache, failing silently.
    */
    public abstract function delete($key, $version=null);
    
    /**
    * Remove *all* values from the cache at once.
    */
    public abstract function clear();
    
    /**
    * Fetch a bunch of keys from the cache. For certain backends (memcached,
    * pgsql) this can be *much* faster when fetching multiple values.
    * 
    * Returns a dict mapping each key in keys to its value. If the given
    * key is missing, it will be missing from the response dict.
    */
    public function getMany(array $keys, $version=null) {
        $d = array();
        foreach ($keys as $key) {
            $val = $this->get($key, null, $version);
            if (!is_null($val))
                $d[$key] = $val;
        }
        return $d;
    }
    
    /**
    * Set a bunch of values in the cache at once from a dict of key/value
    * pairs.  For certain backends (memcached), this is much more efficient
    * than calling set() multiple times.
    * 
    * If $timeout is given, that timeout will be used for the key; otherwise
    * the default cache timeout will be used.
    */
    public function setMany(array $data, $timeout=null, $version=null) {
        foreach ($data as $key => $value)
            $this->set($key, $value, $timeout, $version);
    }
    
    /**
    * Set a bunch of values in the cache at once from a dict of key/value
    * pairs.  For certain backends (memcached), this is much more efficient
    * than calling set() multiple times.
    * 
    * If $timeout is given, that timeout will be used for the key; otherwise
    * the default cache timeout will be used.
    */
    public function deleteMany(array $keys, $version=null) {
        foreach ($keys as $key)
            $this->delete($key, $version);
    }
    
    /**
    * Add $delta to value in the cache. If the key does not exist, raise an
    * OutOfBoundsException.
    */
    public function incr($key, $delta=1, $version=null) {
        $val = $this->get($key, null, $version);
        if (is_null($val))
            throw new \OutOfBoundsException("Key '$key' not found.");
        $newVal = $val + $delta;
        $this->set($key, $newVal, null, $version);
        return $newVal;
    }
    
    /**
    * Subtract $delta from value in the cache. If the key does not exist,
    * raise an OutOfBoundsException.
    */
    public function decr($key, $delta=1, $version=null) {
        return $this->incr($key, -1 * $delta, $version);
    }
    
    /**
    * Adds delta to the cache version for the supplied key. Returns the
    * new version.
    */
    public function incrVersion($key, $delta=1, $version=null) {
        if (is_null($version))
            $version = $this->version;
        
        $val = $this->get($key, null, $version);
        if (is_null($val))
            throw new \OutOfBoundsException("Key '$key' not found.");
        
        $this->set($key, $val, null, $version + $delta);
        $this->delete($key, $version);
        
        return $version + $delta;
    }
    
    /**
    * Subtracts delta from the cache version for the supplied key. Returns
    * the new version.
    */
    public function decrVersion($key, $delta=1, $version=null) {
        return $this->incrVersion($key, -1 * $delta, $version);
    }
    
    /**
    * Returns True if the key is in the cache and has not expired.
    */
    public function hasKey($key, $version=null) {
        return !is_null($this->get($key, null, $version));
    }
    
    /**
    * Constructs the key used by all other methods. By default it
    * uses the keyFunction to generate a key (which, by default,
    * prepends the `keyPrefix' and 'version'). A different key
    * function can be provided at the time of cache construction;
    * alternatively, you can subclass the cache backend to provide
    * custom key making behavior.
    */
    protected function makeKey($key, $version=null) {
        if (empty($version))
            $version = $this->version;
        return call_user_func($this->keyFunction, $key, $this->keyPrefix, $version);
    }
    
    /**
    * Warn about keys that would not be portable to the memcached
    * backend. This encourages (but does not force) writing backend-portable
    * cache code.
    */
    protected function validateKey($key) {
        if (strlen($key) > self::MEMCACHE_MAX_KEY_LENGTH)
            trigger_error("Cache key will cause errors if used with memcached: ".
                          "$key (longer than {self::MEMCACHE_MAX_KEY_LENGTH})",
                          E_USER_WARNING);
        $chars = preg_split("//", $key, -1, PREG_SPLIT_NO_EMPTY);
        foreach ($chars as $char) {
            if (ord($char) < 33 || ord($char) == 127)
                trigger_error("Cache key contains characters that will cause errors ".
                              "if used with memcached: $key",
                              E_USER_WARNING);
        }
    }
}
