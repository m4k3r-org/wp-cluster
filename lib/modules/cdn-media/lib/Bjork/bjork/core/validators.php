<?php

namespace bjork\core {

final class validators {
    public static function empty_values() {
        return array(null, '', array());
    }
    
    public static function is_empty($value) {
        return in_array($value, self::empty_values(), true);
    }
}

}

namespace bjork\core\validators {

require_once __DIR__.'/../core/exceptions.php';
require_once __DIR__.'/../utils/translation/__init.php';

use bjork\core\exceptions\ValidationError,
    bjork\utils\translation;

interface Validator {
    public function validate($value);
    public function getMessage();
    public function getCode();
}

interface ComparisonValidator extends Validator {
    public function clean($x);
    public function compare($a, $b);
}

abstract class AbstractValidator implements Validator {
    protected $message, $code;
    
    function __construct($message=null, $code=null) {
        if (!is_null($message))
            $this->message = $message;
        if (!is_null($code))
            $this->code = $code;
    }
    
    function __invoke($value) {
        $this->validate($value);
    }
    
    //abstract public function validate($value);
    
    public function getMessage() {
        return $this->message;
    }
    
    public function getCode() {
        return $this->code;
    }
    
    public function raise(array $params=null) {
        throw new ValidationError($this->getMessage(), $this->getCode(), $params);
    }
}

abstract class AbstractComparisonValidator extends AbstractValidator implements ComparisonValidator {
    protected
        $message = 'Ensure this value is %s (it is %s).',
        $code = 'limit_value';
    
    protected
        $limit_value;
    
    function __construct($limit_value, $message=null, $code=null) {
        parent::__construct($message, $code);
        $this->limit_value = $limit_value;
    }
    
    public function clean($x) {
        return $x;
    }
    
    public function compare($a, $b) {
        return $a !== $b;
    }
    
    public function validate($value) {
        $cleaned = $this->clean($value);
        $params = array(
            "limit_value" => $this->limit_value,
            "show_value" => $cleaned,
        );
        if ($this->compare($cleaned, $this->limit_value))
            throw new ValidationError(
                vsprintf($this->getMessage(), $params),
                $this->getCode(),
                $params);
    }
    
    public function getMessage() {
        return translation::gettext('Ensure this value is %s (it is %s).');
    }
}

class RegexValidator extends AbstractValidator {
    protected
        $regex = '',
        $message = 'Enter a valid value.',
        $code = 'invalid';
    
    function __construct($regex=null, $message=null, $code=null) {
        parent::__construct($message, $code);
        if (!is_null($regex))
            $this->regex = $regex;
    }
    
    public function validate($value) {
        if (!preg_match($this->regex, $value))
            $this->raise();
    }
    
    public function getMessage() {
        return translation::gettext('Enter a valid value.');
    }
}

class IntegerValidator extends RegexValidator {
    const regex = "/^[+\-]?\d+$/";
    
    function __construct($message=null, $code=null) {
        parent::__construct(self::regex, $message, $code);
    }
}

class EmailValidator extends RegexValidator {
    const regex = "/(^[-!#$%&'*+\/=?^_`{}|~0-9A-Z]+(\.[-!#$%&'*+\/=?^_`{}|~0-9A-Z]+)*|^\"([\001-\010\013\014\016-\037!#-\[\]-\177]|\\[\001-011\013\014\016-\177])*\")@(?:[A-Z0-9](?:[A-Z0-9-]{0,61}[A-Z0-9])?\.)+[A-Z]{2,6}\.?$/i";
    
    protected $message = 'Enter a valid e-mail address.';
    
    function __construct($message=null, $code=null) {
        parent::__construct(self::regex, $message, $code);
    }
    
    public function getMessage() {
        return translation::gettext('Enter a valid e-mail address.');
    }
}

class SlugValidator extends RegexValidator {
    const regex = "/^[\w\-]+$/";
    
    protected $message = "Enter a valid 'slug' consisting of letters, numbers, underscores or hyphens.";
    
    function __construct($message=null, $code=null) {
        parent::__construct(self::regex, $message, $code);
    }
    
    public function getMessage() {
        return translation::gettext("Enter a valid 'slug' consisting of letters, numbers, underscores or hyphens.");
    }
}

class IPv4Validator extends RegexValidator {
    const regex = "/^(25[0-5]|2[0-4]\d|[0-1]?\d?\d)(\.(25[0-5]|2[0-4]\d|[0-1]?\d?\d)){3}$/";
    
    protected $message = 'Enter a valid IPv4 address.';
    
    function __construct($message=null, $code=null) {
        parent::__construct(self::regex, $message, $code);
    }
    
    public function getMessage() {
        return translation::gettext('Enter a valid IPv4 address.');
    }
}

class CommaSeparatedIntegerListValidator extends RegexValidator {
    const regex = "/^[\d,]+$/";
    
    protected $message = 'Enter only digits separated by commas.';
    
    function __construct($message=null, $code=null) {
        parent::__construct(self::regex, $message, $code);
    }
    
    public function getMessage() {
        return translation::gettext('Enter only digits separated by commas.');
    }
}

class MaxValueValidator extends AbstractComparisonValidator {
    protected
        $message = 'Ensure this value is less than or equal to %s.',
        $code = 'max_value';
    function compare($a, $b) { return $a > $b; }
    
    public function getMessage() {
        return translation::gettext('Ensure this value is less than or equal to %s.');
    }
}

class MinValueValidator extends AbstractComparisonValidator {
    protected
        $message = 'Ensure this value is greater than or equal to %s.',
        $code = 'min_value';
    function compare($a, $b) { return $a < $b; }
    
    public function getMessage() {
        return translation::gettext('Ensure this value is greater than or equal to %s.');
    }
}

class MaxLengthValidator extends AbstractComparisonValidator {
    protected
        $message = 'Ensure this value has at most %d characters (it has %d).',
        $code = 'max_length';
    function clean($x) { return mb_strlen($x); }
    function compare($a, $b) { return $a > $b; }
    
    public function getMessage() {
        return translation::gettext('Ensure this value has at most %d characters (it has %d).');
    }
}

class MinLengthValidator extends AbstractComparisonValidator {
    protected
        $message = 'Ensure this value has at least %d characters (it has %d).',
        $code = 'min_length';
    function clean($x) { return mb_strlen($x); }
    function compare($a, $b) { return $a < $b; }
    
    public function getMessage() {
        return translation::gettext('Ensure this value has at least %d characters (it has %d).');
    }
}

}
