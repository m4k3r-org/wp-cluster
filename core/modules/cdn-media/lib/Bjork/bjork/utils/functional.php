<?php

namespace bjork\utils {

use bjork\utils\functional\ContextManager;

final class functional {
    
    /**
    * Call a user function using named parameters.
    * 
    * If some of the named parameters are not present in the original 
    * function, and $strict is false, they will be applied to the
    * function as extra arguments which can be accessed with func_get_args(),
    * or else a \BadFunctionCallException will be thrown.
    * 
    * Can be used like this:
    * 
    *    $args = array('param1' => 'value1', 'param2' => 'value2');
    *    call_user_func_assoc('function', $args);
    *    call_user_func_assoc('class::static_method', $args);
    *    call_user_func_assoc(array('class', 'static_method'), $args);
    *    call_user_func_assoc(array($obj, 'method'), $args);
    * 
    * @param string|array   Name of function to be called, or array containing
    *                       an object and the method name, or array containing
    *                       a class name and the static method name
    * @param array          Array containing parameters to be passed to the
    *                       function using their name (ie array key)
    * @param bool           Whether to throw or suppress an exception if there
    *                       are extraneous arguments in the given parameters
    *                       array.
    * 
    * @props <http://www.php.net/manual/en/function.call-user-func-array.php#66121>
    */
    public static function call_user_func_assoc($function_name, array $params, $strict=false) {
        
        if (is_string($function_name) && preg_match('/::/', $function_name))
            $function_name = preg_split('/::/', $function_name, 2);
        
        if (is_array($function_name)) {
            try {
                if (is_object($function_name[0]))
                    $reflectObject = new \ReflectionObject($function_name[0]);
                else
                    $reflectObject = new \ReflectionClass($function_name[0]);
                $reflect = $reflectObject->getMethod($function_name[1]);
            } catch (\ReflectionException $e) {
                throw new \BadFunctionCallException(
                    "Call to undefined function '".
                        self::get_func_repr($function_name)."'");
            }
        } else {
            try {
                $reflect = new \ReflectionFunction($function_name);
            } catch (\ReflectionException $e) {
                throw new \BadFunctionCallException(
                    "Call to undefined function '".
                        self::get_func_repr($function_name)."'");
            }
        }
        
        $func_args = array();
        foreach ($reflect->getParameters() as $i => $param) {
            $pname = $param->getName();
            if (array_key_exists($pname, $params)) {
                $func_args[$pname] = $params[$pname];
            } else if ($param->isDefaultValueAvailable()) {
                $func_args[$pname] = $param->getDefaultValue();
            } else {
                // missing required parameter
                throw new \BadFunctionCallException(
                    sprintf('Call to "%s", missing parameter #%d "%s"',
                        self::get_func_repr($function_name), $i+1, $pname));
            }
        }
        
        $extra_func_args = array_diff_key($params, $func_args);
        if ($strict && !empty($extra_func_args))
            throw new \BadFunctionCallException(
                sprintf('Call to "%s", extraneous parameters given',
                    self::get_func_repr($function_name)));
        
        return call_user_func_array($function_name, array_merge($func_args,
                                                                $extra_func_args));
    }
    
    public static function get_func_repr($callable) {
        $n = null;
        is_callable($callable, true, $n);
        return $n;
    }
    
    /**
    *   >>> $contents = null;
    *   >>> $file = new File('path/to/file');
    *   >>> functional::with($file, function($handle) use (&$contents) {
    *   ...     $contents = fread($handle, 8 * 1024);
    *   ... });
    *   >>> print $contents;
    * 
    * Equivalent to:
    * 
    *   >>> $contents = null;
    *   >>> $handle = fopen('path/to/file', 'r');
    *   >>> try {
    *   ...     $contents = fread($handle, 8 * 1024);
    *   ... } catch (\Exception $e) {
    *   ...     fclose($handle);
    *   ...     throw $e;
    *   ... }
    *   >>> fclose($handle);
    *   >>> print $contents;
    */
    public static function with(ContextManager $manager, \Closure $fn) {
        $obj = $manager->__enter();
        try {
            $fn($obj);
        } catch (\Exception $e) {
            $ret = $manager->__exit($e);
            if ($ret !== true)
                throw $e;
            return;
        }
        $manager->__exit(null);
    }
    
    /**
    * Helper to wrap an object and essentially re-bind it for use inside a
    * closure. Useful for working around PHP's restriction of using '$this'
    * inside a closure.
    *
    * Use like so:
    *
    *   >>> $proxy = functional::proxy($this, function($self, $arg1, $arg2) {
    *   ...     // in here: $self === $this
    *   ...     return "{$arg1} {$arg2}";
    *   ... });
    *   >>> print $proxy('hello', 'world');
    *   ... 'hello world'
    */
    public static function proxy($obj, \Closure $fn) {
        return function() use ($obj, $fn) {
            $args = func_get_args();
            array_unshift($args, $obj);
            return call_user_func_array($fn, $args);
        };
    }
}

}

namespace bjork\utils\functional {

interface ContextManager {
    public function __enter();
    public function __exit(\Exception $e=null);
}

/**
* A wrapper for another class that can be used to delay instantiation of the
* wrapped class.
* 
* By subclassing, you have the opportunity to intercept and alter the
* instantiation. If you don't need to do that, use SimpleLazyObject.
* 
* Caveats:
*
*   - Reflection on properties breaks.
*   - Static functions on wrapped objects cannot be called.
*   - All arguments provided are not passed by reference to the wrapped
*     object.
*/
abstract class LazyObject implements \Countable {
    
    const EMPTY_SENTINEL = -129834765;
    
    var $_wrapped;
    
    function __construct() {
        $this->_wrapped = self::EMPTY_SENTINEL;
    }
    
    function __get($name) {
        $this->load($name);
        return $this->_wrapped->$name;
    }
    
    function __set($name, $value) {
        $this->load($name);
        $this->_wrapped->$name = $value;
    }
    
    function __isset($name) {
        $this->load($name);
        return isset($this->_wrapped->$name);
    }
    
    function __unset($name) {
        $this->load($name);
        unset($this->_wrapped->$name);
    }
    
    function __call($name, $arguments) {
        $this->load($name);
        return call_user_func_array(array($this->_wrapped, $name), $arguments);
    }
    
    function count() {
        $this->load();
        return count($this->_wrapped);
    }
    
    function load($name=null) {
        if ($name === '_wrapped')
            throw new \Exception(
                '`_wrapped` is used internally and cannot be accessed');
        if ($this->_wrapped === self::EMPTY_SENTINEL)
            $this->setup();
    }
    
    abstract function setup();
}

/**
* A lazy object initialised from any function.
* 
* Designed for compound objects of unknown type. For builtins or objects of
* known type, use bjork.utils.functional.lazy.
*/
class SimpleLazyObject extends LazyObject {
    
    var $_setupfunc;
    
    /**
    * Pass in a callable that returns the object to be wrapped.
    *
    * If copies are made of the resulting SimpleLazyObject, which can happen
    * in various circumstances within Bjork, then you must ensure that the
    * callable can be safely run more than once and will return the same
    * value.
    */
    function __construct($func) {
        if (!is_callable($func))
            throw new \Exception('Argument must be a callable');
        $this->_setupfunc = $func;
        parent::__construct();
    }
    
    function __clone() {
        if ($this->_wrapped !== self::EMPTY_SENTINEL)
            $this->_wrapped = clone $this->_wrapped;
    }
    
    function __toString() {
        $this->load();
        return sprintf('%s', $this->_wrapped);
    }
    
    function setup() {
        $func = $this->_setupfunc;
        $this->_wrapped = $func();
    }
}

}
