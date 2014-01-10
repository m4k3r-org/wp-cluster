<?php

namespace bjork\core\cache\backends\dummy;

use bjork\core\cache\backends\base\BaseBackend;

class DummyBackend extends BaseBackend {
    
    public function __construct($location, array $params) {
        parent::__construct($params);
    }
    
    public function add($key, $value, $timeout=null, $version=null) {
        $key = $this->makeKey($key, $version);
        $this->validateKey($key);
        return true;
    }
    
    public function set($key, $value, $timeout=null, $version=null) {
        $key = $this->makeKey($key, $version);
        $this->validateKey($key);
    }
    
    public function get($key, $default=null, $version=null) {
        $key = $this->makeKey($key, $version);
        $this->validateKey($key);
        return $default;
    }
    
    public function delete($key, $version=null) {
        $key = $this->makeKey($key, $version);
        $this->validateKey($key);
    }
    
    public function clear() {}
    
    public function getMany(array $keys, $version=null) {
        return array();
    }
    
    public function setMany(array $data, $timeout=null, $version=null) {}
    
    public function deleteMany(array $keys, $version=null) {}
    
    public function hasKey($key, $version=null) {
        $key = $this->makeKey($key, $version);
        $this->validateKey($key);
        return false;
    }
}
