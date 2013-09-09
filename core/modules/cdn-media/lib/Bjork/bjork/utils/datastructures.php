<?php

namespace bjork\utils\datastructures;

class KeyError extends \OutOfBoundsException {}
class IndexError extends \OutOfBoundsException {}

class Dict extends \ArrayObject {
    function __construct(array $array=null) {
        if ($array === null)
            $array = array();
        else if ($array instanceof Dict)
            $array = $array->getArrayCopy();
        parent::__construct($array);
    }
    
    function hasKey($key) {
        return $this->offsetExists($key);
    }
    
    function get($key, $default=null) {
        if ($this->offsetExists($key))
            return $this[$key];
        return $default;
    }
    
    function pop($key/*, $default=null*/) {
        if ($this->offsetExists($key)) {
            $val = $this[$key];
            $this->offsetUnset($key);
            return $val;
        }
        if (count($args = func_get_args()) > 1)
            return $args[1];
        throw new KeyError($key);
    }
    
    function set($key, $value) {
        $this->offsetSet($key, $value);
    }
    
    function setDefault($key, $value) {
        if ($this->offsetExists($key))
            return;
        $this->set($key, $value);
    }
}

// can't use "list" since it's a php function/keyword
class List_ extends \ArrayObject {
    function __construct(/*, ...*/) {
        $args = func_get_args();
        if (count($args) === 1) {
            $list = $args[0];
            if ($list instanceof List_)
                $args = $list->getArrayCopy();
            else
                $args = $list;
        }
        parent::__construct($args);
    }
    
    function append($item/*, ...*/) {
        $items = func_get_args();
        $list = new static($items);
        $this->extend($list);
    }
    
    function extend(List_ $other) {
        $items = $this->getArrayCopy();
        array_splice($items, $this->count(), 0, $other->getArrayCopy());
        $this->exchangeArray($items);
    }
    
    function pop($index=null) {
        if (is_null($index))
            $index = $this->count() - 1;
        if ($this->offsetExists($index)) {
            $item = $this->offsetGet($index);
            $this->offsetUnset($index);
            return $item;
        }
        throw new IndexError($index);
    }
    
    function slice($start=null, $end=null) {
        $array = $this->getArrayCopy();
        $length = null;
        if (is_null($start)) $start = 0;
        if (!is_null($end))  $length = $end - $start;
        $newArray = array_slice($array, $start, $length);
        return new static($newArray);
    }
    
    function toArray() {
        return $this->getArrayCopy();
    }
}

//----------------------------------------------------------------------------

class MultiValueDictOutOfBoundsException extends \OutOfBoundsException {}

/**
* A custom array subclass able to handle multiple values for the
* same key.
* 
*   >>> $d = new MultiValueDict(array(
*   ...     'name'     => array('Adrian', 'Simon'),
*   ...     'position' => array('Developer')));
*   >>> $d['name'];
*   'Simon'
*   >>> $d->getList('name');
*   array('Adrian', 'Simon')
*   >>> $d->get('lastname', 'nonexistent');
*   'nonexistent'
*   >>> $d->setList('lastname', array('Holovaty', 'Willison'));
*/
class MultiValueDict implements \IteratorAggregate, \ArrayAccess, \Countable {
    protected $props;
    
    function __construct(array $mapping=null) {
        $this->props = empty($mapping) ? array() : $mapping;
    }
    
    public function getIterator() {
        return new \ArrayIterator($this->props);
    }
    
    public function offsetExists($k) {
        return array_key_exists($k, $this->props);
    }
    
    /**
    * Returns the last data value for this key, or array() if it's an empty
    * list; throws MultiValueDictOutOfBoundsException if not found.
    */
    public function offsetGet($k) {
        if (!$this->offsetExists($k))
            throw new MultiValueDictOutOfBoundsException($k);
        $list = $this->props[$k];
        return end($list);
    }
    
    /**
    * Sets the value for key.
    */
    public function offsetSet($k, $value) {
        $this->props[$k] = array($value);
    }
    
    /**
    * Removes the internal list for key.
    */
    public function offsetUnset($k) {
        unset($this->props[$k]);
    }
    
    /**
    * Returns the count of keys.
    */
    public function count() {
        return count(array_keys($this->props));
    }
    
    /**
    * Returns the last data value for the passed key. If key doesn't exist
    * or value is an empty list, then default is returned.
    */
    public function get($k, $default=null) {
        try {
            $val = $this[$k];
        } catch (\OutOfBoundsException $e) {
            return $default;
        }
        if (array() === $val)
            return $default;
        return $val;
    }
    
    /**
    * Returns the list of values for the passed key. If key doesn't exist,
    * then a default value is returned.
    */
    public function getList($k, $default=null) {
        if (!$this->offsetExists($k)) {
            if (null !== $default)
                return $default;
            return array();
        }
        return $this->props[$k];
    }
    
    public function getLists() {
        return array_merge(array(), $this->props);
    }
    
    public function hasKey($k) {
        return $this->offsetExists($k);
    }
    
    /**
    * Sets the value for key.
    */
    public function set($k, $value) {
        $this[$k] = $value;
    }
    
    public function setDefault($k, $default=null) {
        if (!$this->offsetExists($k))
            $this[$k] = $default;
        return $this[$k];
    }
    
    public function setList($k, array $list) {
        $this->props[$k] = $list;
    }
    
    public function setListDefault($k, array $default_list=array()) {
        if (!$this->offsetExists($k))
            $this->setList($k, $default_list);
        return $this->getList($k);
    }
    
    /**
    * Appends an item to the internal list associated with key.
    */
    public function appendList($k, $value) {
        if (!$this->offsetExists($k))
            $this->setList($k, array());
        $this->props[$k][] = $value;
    }
}
