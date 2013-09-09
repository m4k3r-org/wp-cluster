<?php

namespace bjork\contrib\messages {

class MessageFailure extends \Exception {}

}

namespace bjork\contrib {

use bjork\conf\settings,
    bjork\contrib\messages\storage,
    bjork\utils\datastructures\Dict;

final class messages {
    
    const DEBUG = 10,
          INFO = 20,
          SUCCESS = 25,
          WARNING = 30,
          ERROR = 40;
    
    /**
    * Attempts to add a message to the request using the 'messages' app.
    */
    public static function add_message($request, $level, $message,
                                       $extra_tags='',
                                       $fail_silently=false)
    {
        if ($request->hasKey('_messages'))
            return $request['_messages']->add($level, $message, $extra_tags);
        if (!$fail_silently)
            throw new messages\MessageFailure(
                'You cannot add messages without installing '.
                'bjork\contrib\messages\middleware\MessageMiddleware');
    }
    
    /**
    * Returns the message storage on the request if it exists, otherwise
    * returns an empty list.
    */
    public static function get_messages($request) {
        if ($request->hasKey('_messages'))
            return $request['_messages'];
        return array();
    }
    
    /**
    * Returns the minimum level of messages to be recorded.
    * 
    * The default level is the ``MESSAGE_LEVEL`` setting. If this is not
    * found, the ``INFO`` level is used.
    */
    public static function get_level($request) {
        if ($request->hasKey('_messages'))
            $storage = $request['_messages'];
        else
            $storage = storage::get_default_storage($request);
        return $storage->getLevel();
    }
    
    public static function get_level_tags() {
        $levels = array(
            self::DEBUG => 'debug',
            self::INFO => 'info',
            self::SUCCESS => 'success',
            self::WARNING => 'warning',
            self::ERROR => 'error',
        );
        
        foreach (settings::get('MESSAGE_TAGS', array()) as $k => $v)
            $levels[$k] = $v;
        
        return new Dict($levels);
    }
    
    /**
    * Sets the minimum level of messages to be recorded, returning ``True``
    * if the level was recorded successfully.
    * 
    * If set to ``None``, the default level will be used (see the
    * ``get_level`` method).
    */
    public static function set_level($request, $level) {
        if (!$request->hasKey('_messages'))
            return false;
        $request['_messages']->setLevel($level);
        return true;
    }
    
    /**
    * Adds a message with the ``DEBUG`` level.
    */
    public static function debug($request, $message, $extra_tags='',
                                 $fail_silently=false)
    {
        self::add_message($request, self::DEBUG, $message, $extra_tags,
            $fail_silently);
    }
    
    /**
    * Adds a message with the ``INFO`` level.
    */
    public static function info($request, $message, $extra_tags='',
                                $fail_silently=false)
    {
        self::add_message($request, self::INFO, $message, $extra_tags,
            $fail_silently);
    }
    
    /**
    * Adds a message with the ``SUCCESS`` level.
    */
    public static function success($request, $message, $extra_tags='',
                                   $fail_silently=false)
    {
        self::add_message($request, self::SUCCESS, $message, $extra_tags,
            $fail_silently);
    }
    
    /**
    * Adds a message with the ``WARNING`` level.
    */
    public static function warning($request, $message, $extra_tags='',
                                   $fail_silently=false)
    {
        self::add_message($request, self::WARNING, $message, $extra_tags,
            $fail_silently);
    }
    
    /**
    * Adds a message with the ``ERROR`` level.
    */
    public static function error($request, $message, $extra_tags='',
                                 $fail_silently=false)
    {
        self::add_message($request, self::ERROR, $message, $extra_tags,
            $fail_silently);
    }
}

}
