<?php

namespace bjork\contrib\messages\storage\session;

use bjork\contrib\messages\storage\base\BaseStorage,
    bjork\core\exceptions\ImproperlyConfigured;

class SessionStorage extends BaseStorage {
    
    static $session_key = '_messages';
    
    function __construct($request, array $options=null) {
        if (!$request->hasKey('session')) {
            throw new ImproperlyConfigured(
                'The session-based temporary message storage requires '.
                'session middleware to be installed, and come before the '.
                'message middleware in the MIDDLEWARE_CLASSES list.');
        }
        parent::__construct($request, $options);
    }
    
    /**
    * Retrieves a list of messages from the request's session.
    * 
    * This storage always stores everything it is given, so return True
    * for the all_retrieved flag.
    */
    protected function get() {
        return array($this->request['session']->get(self::$session_key), true);
    }
    
    /**
    * Stores a list of messages to the request's session.
    */
    protected function store(array $messages, $response, array $options=null) {
        if (count($messages) > 0)
            $this->request['session'][self::$session_key] = $messages;
        else
            $this->request['session']->pop(self::$session_key, null);
        return array();
    }
}
