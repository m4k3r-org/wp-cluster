<?php

namespace bjork\core\cache\backends\memcache;

use bjork\core\cache\backends\base\BaseBackend;

class MemcacheBackend extends BaseBackend {
    
    protected $client;
    
    public function __construct($location, array $params) {
        if (strpos($location, ":") !== false) {
            $parts = explode(":", $location);
            $port = array_pop($parts);
            $host = implode(":", $parts);
        } else {
            $host = $location;
            $port = 11211;
        }
        
        parent::__construct($params);
        
        $client = new \Memcache();
        $client->addServer($host, $port);
        $this->client = $client;
    }
    
    public function add($key, $value, $timeout=null, $version=null) {
        $key = $this->makeKey($key, $version);
        return $this->client->add($key, $value,
            is_scalar($value) ? null : MEMCACHE_COMPRESSED,
            $this->getMemcacheTimeout($timeout));
    }
    
    public function set($key, $value, $timeout=null, $version=null) {
        $key = $this->makeKey($key, $version);
        $this->client->set($key, $value,
            is_scalar($value) ? null : MEMCACHE_COMPRESSED,
            $this->getMemcacheTimeout($timeout));
    }
    
    public function get($key, $default=null, $version=null) {
        $key = $this->makeKey($key, $version);
        $value = $this->client->get($key);
        if ($value === false)
            return $default;
        return $value;
    }
    
    public function delete($key, $version=null) {
        $key = $this->makeKey($key, $version);
        $this->client->delete($key, 0);
    }
    
    public function clear() {
        $this->client->flush();
    }
    
    public function getMany(array $keys, $version=null) {
        $self = $this;
        $newKeys = array_map(function($k) use ($self, $version) {
            return $this->makeKey($k, $version);
        }, $keys);
        $ret = $this->client->get($newKeys);
        if (!empty($ret)) {
            $o = array();
            for ($i = 0; $i < count($ret); $i++)
                $o[$keys[$i]] = $ret[$i];
            $ret = $o;
        }
        return $ret;
    }
    
    public function incr($key, $delta=1, $version=null) {
        $key = $this->makeKey($key, $version);
        $val = $this->client->increment($key, $delta);
        if (!$val && $val !== 0)
            throw new \OutOfBoundsException("Key '$key' not found.");
        return $val;
    }
    
    public function decr($key, $delta=1, $version=null) {
        $key = $this->makeKey($key, $version);
        $val = $this->client->decrement($key, $delta);
        if (!$val && $val !== 0)
            throw new \OutOfBoundsException("Key '$key' not found.");
        return $val;
    }
    
    /**
    * Memcached deals with long (> 30 days) timeouts in a special
    * way. Call this function to obtain a safe value for your timeout.
    */
    protected function getMemcacheTimeout($timeout) {
        if (is_null($timeout))
            $timeout = $this->defaultTimeout;
        if ($timeout > 2592000) { # 60*60*24*30, 30 days
            // See http://code.google.com/p/memcached/wiki/FAQ
            // "You can set expire times up to 30 days in the future. After
            // that memcached interprets it as a date, and will expire the
            // item after said date. This is a simple (but obscure) mechanic."
            // 
            // This means that we have to switch to absolute timestamps.
            $timeout += time();
        }
        return $timeout;
    }
    
    public function getStats() {
        return $this->client->getStats();
    }
    
    public function close() {
        $this->client->close();
    }
}
