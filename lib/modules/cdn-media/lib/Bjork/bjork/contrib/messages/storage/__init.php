<?php

namespace bjork\contrib\messages;

use bjork\conf\settings;

final class storage {
    
    protected static $default_storage = null;
    
    /**
    * Attempts to add a message to the request using the 'messages' app.
    */
    public static function get_default_storage($request) {
        if (null === self::$default_storage) {
            $storage_cls = settings::get('MESSAGE_STORAGE');
            self::$default_storage = new $storage_cls($request);
        }
        return self::$default_storage;
    }
}
