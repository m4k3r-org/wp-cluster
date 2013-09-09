<?php

namespace bjork\forms;

require_once __DIR__.'/../utils/html.php';

use bjork\utils\html;

function flatatt($attrs) {
    $out = "";
    foreach ($attrs as $name => $val) {
        $val = html::conditional_escape($val);
        $out .= " $name=\"$val\"";
    }
    return $out;
}

abstract class IterableErrors implements \IteratorAggregate, \ArrayAccess, \Countable {
    protected $errors;
    
    function __construct(array $errors=null) {
        if (is_null($errors))
            $errors = array();
        $this->errors = $errors;
    }
    
    function getIterator() {
        return new \ArrayIterator($this->errors);
    }
    
    function count() {
        return count($this->errors);
    }
    
    function offsetExists($offset) {
        return isset($this->errors[$offset]);
    }
    
    function offsetGet($offset) {
        return $this->errors[$offset];
    }
    
    function offsetSet($offset, $value) {
        $this->errors[$offset] = $value;
    }
    
    function offsetUnset($offset) {
        unset($this->errors[$offset]);
    }
}

class ErrorDict extends IterableErrors {
    function __toString() {
        return $this->asUl();
    }
    
    function asUl() {
        $html = '<ul class="errorlist">%s</ul>';
        $out = "";
        foreach ($this as $k => $v) {
            $out .= "\n<li>{$k}{$v}</li>";
        }
        return sprintf($html, $out);
    }
    
    function asText() {
        $out = "";
        foreach ($this as $k => $v) {
            $o = "";
            foreach ($v as $error)
                $o .= "  * {$error}\n";
            $out .= "* {$k}\n{$o}";
        }
        return $out;
    }
}


class ErrorList extends IterableErrors {
    function __toString() {
        return $this->asUl();
    }
    
    function getFirst() {
        return $this[0];
    }
    
    function asUl() {
        $html = '<ul class="errorlist">%s</ul>';
        $out = "";
        foreach ($this as $error) {
            $out .= "\n<li>{$error}</li>";
        }
        return sprintf($html, $out);
    }
    
    function asText() {
        $out = "";
        foreach ($this as $error) {
            $out .= "* {$error}\n";
        }
        return sprintf($html, $out);
    }
}
