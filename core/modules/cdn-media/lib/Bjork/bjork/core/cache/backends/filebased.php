<?php

namespace bjork\core\cache\backends\filebased;

use bjork\core\cache\backends\base\BaseBackend,
    bjork\core\exceptions\ImproperlyConfigured;

class FileBasedBackend extends BaseBackend {
    
    const TIMESTAMP_MARKER = "-$-bjork-cache-$-";
    
    protected $dir;
    
    public function __construct($dir, array $params) {
        parent::__construct($params);
        $this->createDir($dir);
        $this->dir = $dir;
    }
    
    public function add($key, $value, $timeout=null, $version=null) {
        if ($this->hasKey($key, $version))
            return false;
        $this->set($key, $value, $timeout, $version);
        return true;
    }
    
    public function set($key, $value, $timeout=null, $version=null) {
        $key = $this->makeKey($key, $version);
        $this->validateKey($key);
        
        $fname = $this->keyToFile($key);
        $dname = dirname($fname);
        
        if (is_null($timeout))
            $timeout = $this->defaultTimeout;
        
        $this->cull();
        
        $this->createDir($dname);
        
        $expiry = time() + $timeout;
        $out = (string)$expiry . self::TIMESTAMP_MARKER . $this->encodeData($value);
        $result = file_put_contents($fname, $out, LOCK_EX);
    }
    
    public function get($key, $default=null, $version=null) {
        $key = $this->makeKey($key, $version);
        $this->validateKey($key);
        
        $fname = $this->keyToFile($key);
        try {
            $contents = file_get_contents($fname);
        } catch (\ErrorException $e) {
            return $default;
        }
        
        if (false === $contents)
            return $default;
        
        $parts = explode(self::TIMESTAMP_MARKER, $contents);
// print "<pre>".htmlspecialchars(print_r($this->decodeData($parts[1]), true), ENT_QUOTES, "utf-8")."</pre>";
        if (((int)$parts[0]) < time())
            $this->deleteFile($fname);
        else
            return $this->decodeData($parts[1]);
        
        return $default;
    }
    
    public function delete($key, $version=null) {
        $key = $this->makeKey($key, $version);
        $this->validateKey($key);
        $fname = $this->keyToFile($key);
        $this->deleteFile($fname);
    }
    
    public function clear() {}
    
    public function getMany(array $keys, $version=null) {}
    
    public function incr($key, $delta=1, $version=null) {}
    
    public function decr($key, $delta=1, $version=null) {}
    
    protected function createDir($dir) {
        clearstatcache();
        if (!is_dir($dir)) {
            //try {
                $oldumask = umask(0);
                $created = mkdir($dir, 0755, true);
                chmod($dir, 0755);
                umask($oldumask);
            // } catch (\ErrorException $e) {
            //     $created = false;
            // }
        } else {
            $created = true;
        }
        if (false === $created)
            throw new ImproperlyConfigured(
                "Cache directory '{$dir}' does not exist and could not be created");
        // if (!is_writable($dir) || !is_readable($dir))
        //     throw new ImproperlyConfigured(
        //         "Cache directory '{$dir}' does not have read/write permissions");
    }
    
    /**
    * Convert the filename into an md5 string. We'll turn the first couple
    * bits of the path into directory prefixes to be nice to filesystems
    * that have problems with large numbers of files in a directory.
    * 
    * Thus, a cache key of "foo" gets turned into a file named
    * ``{cache-dir}a/c/b/d/18db4cc2f85cedef654fccc4a4d8``.
    */
    protected function keyToFile($key) {
        $path = md5($key);
        $path = substr($path, 0, 1) . DIRECTORY_SEPARATOR .
                substr($path, 1, 1) . DIRECTORY_SEPARATOR .
                substr($path, 2, 1) . DIRECTORY_SEPARATOR .
                substr($path, 3, 1) . DIRECTORY_SEPARATOR .
                substr($path, 4);
        return $this->dir . DIRECTORY_SEPARATOR . $path;
    }
    
    protected function deleteFile($fname) {
        try {
            @unlink($fname);
            $fname = dirname($fname); @rmdir($fname);
            $fname = dirname($fname); @rmdir($fname);
            $fname = dirname($fname); @rmdir($fname);
            $fname = dirname($fname); @rmdir($fname);
        } catch (\ErrorException $e) {}
    }
    
    protected function cull() {
        // not implemented
    }
    
    protected function encodeData($data) {
        return serialize($data);
        return base64_encode(serialize($data));
    }
    
    protected function decodeData($string) {
        return unserialize($string);
        return unserialize(base64_decode($string));
    }
}
