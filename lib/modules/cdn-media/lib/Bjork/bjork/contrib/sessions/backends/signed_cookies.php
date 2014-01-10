<?php

namespace bjork\contrib\sessions\backends\signed_cookies;

use bjork\conf\settings,
    bjork\contrib\sessions\backends\base\CreateError,
    bjork\contrib\sessions\backends\base\SessionBase,
    bjork\core\exceptions\BjorkException,
    bjork\core\signing,
    bjork\core\signing\BadSignature;

class PhpSerializer {
    function dumps($obj) {
        return serialize($obj);
    }
    
    function loads($data) {
        return unserialize($data);
    }
}

class SessionStore extends SessionBase {
    
    /**
    * We load the data from the key itself instead of fetching from
    * some external data store. Opposite of _get_session_key(),
    * raises BadSignature if signature fails.
    */
    function load() {
        $serializer = new PhpSerializer();
        try {
            return signing::loads(
                $this->session_key,
                null,
                'bjork.contrib.sessions.backends.signed_cookies',
                $serializer,
                settings::get('SESSION_COOKIE_AGE'));
        } catch (BadSignature $e) {
            $this->create();
        }
        return array();
    }
    
    /**
    * To create a new key, we simply make sure that the modified flag is set
    * so that the cookie is set on the client for the current request.
    */
    function create() {
        $this->modified = true;
    }
    
    /**
    * To save, we get the session key as a securely signed string and then
    * set the modified flag so that the cookie is set on the client for the
    * current request.
    */
    function save($must_create=false) {
        $this->session_key = $this->getSessionKey();
        $this->modified = true;
    }
    
    /**
    * This method makes sense when you're talking to a shared resource, but
    * it doesn't matter when you're storing the information in the client's
    * cookie.
    */
    function exists($session_key) {
        return false;
    }
    
    /**
    * To delete, we clear the session key and the underlying data structure
    * and set the modified flag so that the cookie is set on the client for
    * the current request.
    */
    function delete($session_key=null) {
        $this->session_key = '';
        $this->session_cache = array();
        $this->modified = true;
    }
    
    /**
    * Keeps the same data but with a new key.  To do this, we just have to
    * call ``save()`` and it will automatically save a cookie with a new key
    * at the end of the request.
    */
    function cycleKey() {
        $this->save();
    }
    
    /**
    * Most session backends don't need to override this method, but we do,
    * because instead of generating a random string, we want to actually
    * generate a secure url-safe Base64-encoded string of data as our
    * session key.
    */
    function getSessionKey() {
        $session_cache = $this->session_cache;
        $serializer = new PhpSerializer();
        if (empty($session_cache))
            $session_cache = array();
        return signing::dumps($session_cache,
            null,
            'bjork.contrib.sessions.backends.signed_cookies',
            $serializer,
            true);
    }
}
