<?php

namespace optparse;

require_once __DIR__ . '/option.php';

class OptParseError extends \Exception {
    function __construct($msg) {
        $this->message = $msg;
    }
    
    function __toString() {
        return $this->getMessage();
    }
}

class OptionError extends OptParseError {
    var $option_id;
    
    function __construct($msg, Option $option=null) {
        parent::__construct($msg);
        $this->option_id = $option ? strval($option) : null;
    }
    
    function __toString() {
        if ($this->option_id)
            return "option {$this->option_id}: {$this->getMessage()}";
        else
            return parent::__toString();
    }
}

class OptionConflictError extends OptionError {}

class OptionValueError extends OptParseError {}

class BadOptionError extends OptParseError {
    function __toString() {
        return "no such option: {$this->getMessage()}";
    }
}

class AmbiguousOptionError extends BadOptionError {
    var $possibilities;
    
    function __construct($opt_str, array $possibilities) {
        parent::__construct($opt_str);
        $this->possibilities = $possibilities;
    }
    
    function __toString() {
        return sprintf('ambiguous option: %s (%s?)', $this->getMessage(),
            implode(', ', $this->possibilities));
    }
}
