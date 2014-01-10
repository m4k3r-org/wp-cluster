<?php

namespace bjork\template\context;

use bjork\conf\settings,
    bjork\core\exceptions\ImproperlyConfigured,
    bjork\utils\importlib;

class ContextPopException extends \Exception {}

class BaseContext implements \ArrayAccess, \IteratorAggregate {
    
    var $dicts;
    
    function __construct($dict=null) {
        $this->dicts = array();
        if ($dict)
            $this->dicts[] = $dict;
    }
    
    function __clone() {
        $this->dicts = array_merge(array(), $this->dicts);
    }
    
    function getIterator() {
        return new \ArrayIterator(array_reverse($this->dicts));
    }
    
    function toArray() {
        $c = array();
        foreach ($this->dicts as $dict) {
            foreach ($dict as $key => $value)
                $c[$key] = $value;
        }
        return $c;
    }
    
    public function &push() {
        $d = array();
        $this->dicts[] = $d;
        return $d;
    }
    
    public function &pop() {
        if (count($this->dicts) === 0)
            throw new ContextPopException();
        $d = array_pop($this->dicts);
        return $d;
    }
    
    public function get($key, $default=null) {
        foreach ($this as $dict) {
            if (array_key_exists($key, $dict))
                return $dict[$key];
        }
        return $default;
    }
    
    public function hasKey($key) {
        foreach ($this as $dict) {
            if (array_key_exists($key, $dict))
                return true;
        }
        return false;
    }
    
    function offsetGet($key) {
        foreach ($this as $dict) {
            if (array_key_exists($key, $dict))
                return $dict[$key];
        }
        throw new \OutOfBoundsException($key);
    }
    
    function offsetExists($key) {
        return $this->hasKey($key);
    }
    
    function offsetSet($key, $value) {
        $dict =& $this->dicts[count($this->dicts) - 1];
        $dict[$key] = $value;
    }
    
    function offsetUnset($key) {
        $dict =& $this->dicts[count($this->dicts) - 1];
        unset($dict[$key]);
    }
}

class Context extends BaseContext {
    var $extract, $autoescape, $current_app, $use_i10n, $use_tz;
    
    var $render_context;
    
    function __construct($array=null, array $options=null) {
        if (null === $options)
            $options = array();
        
        extract(array_merge(array(
            'extract' => true,
            'autoescape' => true,
            'current_app' => null,
            'use_i10n' => null,
            'use_tz' => null,
        ), $options));
        
        $this->extract = $extract;
        $this->autoescape = $autoescape;
        $this->current_app = $current_app;
        $this->use_i10n = $use_i10n;
        $this->use_tz = $use_tz;
        $this->render_context = new RenderContext();
        
        parent::__construct($array);
    }
    
    function __clone() {
        $this->render_context = clone $this->render_context;
    }
    
    public function update($other_dict) {
        if (!is_array($other_dict) && !($other_dict instanceof \ArrayAccess))
            throw new \Exception('other_dict must be an array type');
        $this->dicts[] = $other_dict;
        return $other_dict;
    }
}

class RenderContext extends BaseContext {
    function getIterator() {
        return new \ArrayIterator($this->dicts[count($this->dicts) - 1]);
    }

    function toArray() {
        $c = array();
        foreach ($this as $key => $value)
            $c[$key] = $value;
        return $c;
    }
        
    function hasKey($key) {
        return array_key_exists($key, $this->dicts[count($this->dicts) - 1]);
    }
    
    function get($key, $default=null) {
        $d = $this->dicts[count($this->dicts) - 1];
        if (array_key_exists($key, $d))
            return $d[$key];
        return $default;
    }
}

// We need the CSRF processor no matter what the user has in their settings,
// because otherwise it is a security vulnerability, and we can't afford to
// leave this to human error or failure to read migration instructions.
function get_builtin_context_processors() {
    return array(
        'bjork\core\context_processors\csrf',
    );
}

class RequestContext extends Context {
    function __construct($request, $data=null) {
        parent::__construct($data);
        $builtins = get_builtin_context_processors();
        $processors = settings::get('TEMPLATE_CONTEXT_PROCESSORS');
        foreach (array_merge($builtins, $processors) as $processor) {
            if (!is_callable($processor))
                importlib::import_class($processor);
            try {
                $this->update(call_user_func($processor, $request));
            } catch (\ErrorException $e) {
                throw new ImproperlyConfigured(
                    "'{$processor}' template context processor could not be ".
                    "loaded. Error was: {$e->getMessage()}");
            }
        }
    }
}
