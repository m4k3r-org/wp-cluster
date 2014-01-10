<?php

namespace bjork\contrib\sessions\backends\cache;

use bjork\contrib\sessions\backends\base\CreateError,
    bjork\contrib\sessions\backends\base\SessionBase,
    bjork\core\cache;

/**
* A cache-based session store.
*/
class SessionStore extends SessionBase {
    
    const KEY_PREFIX = 'bjork.contrib.sessions.cache';
    
    protected $cache;
    
    function __construct($session_key=null) {
        $this->cache = cache::get_default_cache();
        parent::__construct($session_key);
    }
    
    function load() {
        $session_data = $this->cache->get($this->makeSessionCacheKey(null));
        if (null !== $session_data)
            return $session_data;
        $this->create();
        return array();
    }
    
    function create() {
        // Because a cache can fail silently (e.g. memcache), we don't know if
        // we are failing to create a new session because of a key collision or
        // because the cache is missing. So we try for a (large) number of times
        // and then raise an exception. That's the risk you should consider if
        // using cache backing.
        for ($i=0; $i < 10000; $i++) {
            $this->session_key = $this->getNewSessionKey();
            try {
                $this->save(true);
            } catch (CreateError $e) {
                continue;
            }
            $this->modified = true;
            return;
        }
        throw new \Exception('Unable to create a new session key');
    }
    
    function save($must_create=false) {
        $key = $this->makeSessionCacheKey(null);
        $data = $this->getSessionData($must_create);
        $expiry = $this->getExpiryAge();
        if ($must_create)
            $result = $this->cache->add($key, $data, $expiry);
        else
            $result = $this->cache->set($key, $data, $expiry);
        if ($must_create && !$result)
            throw new CreateError();
    }
    
    function exists($session_key) {
        return $this->cache->hasKey($this->makeSessionCacheKey($session_key));
    }
    
    function delete($session_key=null) {
        if (null === $session_key) {
            $session_key = $this->getSessionKey();
            if (null === $session_key)
                return;
        }
        $this->cache->delete($this->makeSessionCacheKey($session_key));
    }
    
    protected function makeSessionCacheKey($session_key=null) {
        if (null === $session_key)
            $session_key = $this->getSessionKey();
        return self::KEY_PREFIX . ':' . strval($session_key);
    }
}
