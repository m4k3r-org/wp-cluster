<?php

namespace bjork {

use os\path;

use bjork\core\loading,
    bjork\dispatch\Signal;

final class dispatch {
    
    static $registered_signals = array();
    
    public static function register_signal($name /*, provided_arg1, provided_arg2, ... */) {
        $provided_args = func_get_args();
        $name = array_shift($provided_args);
        
        if (array_key_exists($name, self::$registered_signals)) {
            $signal = self::$registered_signals[$name];
        } else {
            $signal = new Signal($provided_args);
            self::$registered_signals[$name] = $signal;
        }
        
        return $signal;
    }
    
    public static function connect($signal, $sender=null, $dispatch_uid=null, $receiver=null) {
        return self::get_signal($signal)->connect($sender, $dispatch_uid, $receiver);
    }
    
    public static function disconnect($sender=null, $dispatch_uid=null, $receiver=null) {
        return self::get_signal($signal)->disconnect($sender, $dispatch_uid, $receiver);
    }
    
    public static function send($signal, $sender=null /*, provided_arg1, provided_arg2, ... */) {
        $args = func_get_args();
        $signal = array_shift($args);
        return call_user_func_array(array(self::get_signal($signal), 'send'), $args);
    }
    
    public static function send_robust($signal, $sender=null /*, provided_arg1, provided_arg2, ... */) {
        $args = func_get_args();
        $signal = array_shift($args);
        return call_user_func_array(array(self::get_signal($signal), 'sendRobust'), $args);
    }
    
    static function get_signal($name) {
        if ($name instanceof Signal)
            return $name;
        if (array_key_exists($name, self::$registered_signals))
            return self::$registered_signals[$name];
        
        // Try to load the signal
        $parts = explode('.', $name);
        $signal_name = array_pop($parts);
        $app = array_pop($parts);
        
        $path = loading::get_app($app)->getFullPath();
        require_once path::join($path, 'signals.php');
        return self::$registered_signals[$name];
    }
}

}

namespace bjork\dispatch {

use bjork\utils\functional;

function make_id($target) {
    if (!$target)
        return 'NULL';
    if (is_string($target))
        return $target;
    if (is_array($target)) {
        $obj = $target[0];
        $func = $target[1];
        if (is_string($obj))
            return $obj . ':' . $func;
        return spl_object_hash($obj) . ':' . $func;
    }
    return spl_object_hash($target);
}

class Signal {
    var $receivers, $provided_args;
    
    function __construct(array $providing_args=null) {
        if (null === $providing_args)
            $providing_args = array();
        $this->receivers = array();
        $this->provided_args = $providing_args;
    }
    
    function connect($sender=null, $dispatch_uid=null, $receiver=null) {
        if (null === $receiver) { $receiver = $dispatch_uid; $dispatch_uid = null; }
        if (null === $receiver) { $receiver = $sender; $sender = null; }
        
        if (!is_callable($receiver))
            throw new \Exception('Signal receivers must be callable.');
        
        if ($dispatch_uid)
            $lookup_key = $dispatch_uid . ':' . make_id($sender);
        else
            $lookup_key = make_id($receiver) . ':' . make_id($sender);
        
        if (!array_key_exists($lookup_key, $this->receivers))
            $this->receivers[$lookup_key] = $receiver;
    }
    
    function disconnect($sender=null, $dispatch_uid=null, $receiver=null) {
        if (null === $receiver) { $receiver = $dispatch_uid; $dispatch_uid = null; }
        if (null === $receiver) { $receiver = $sender; $sender = null; }
        
        if ($dispatch_uid)
            $lookup_key = $dispatch_uid . ':' . make_id($sender);
        else
            $lookup_key = make_id($receiver) . ':' . make_id($sender);
        
        unset($this->receivers[$lookup_key]);
    }
    
    function send($sender=null /*, provided_arg1, provided_arg2, ... */) {
        $responses = array();
        if (empty($this->receivers))
            return $responses;
        
        $args = func_get_args();
        $sender = array_shift($args);
        
        foreach ($this->receivers as $key => $receiver) {
            $response = $this->callReceiver($receiver, $sender, $args);
            $responses[] = array($receiver, $response);
        }
        
        return $responses;
    }
    
    function sendRobust($sender=null /*, provided_arg1, provided_arg2, ... */) {
        $responses = array();
        if (empty($this->receivers))
            return $responses;
        
        $args = func_get_args();
        $sender = array_shift($args);
        
        foreach ($this->receivers as $key => $receiver) {
            try {
                $response = $this->callReceiver($receiver, $sender, $args);
            } catch (\Exception $e) {
                $responses[] = array($receiver, $e);
                continue;
            }
            $responses[] = array($receiver, $response);
        }
        
        return $responses;
    }
    
    function callReceiver($receiver, $sender, array $args) {
        $named_args = array();
        for ($i=0; $i < count($this->provided_args); $i++)
            $named_args[$this->provided_args[$i]] = $args[$i];
        return functional::call_user_func_assoc($receiver, array_merge(array(
            'signal' => $this,
            'sender' => $sender,
        ), $named_args));
    }
}

}
