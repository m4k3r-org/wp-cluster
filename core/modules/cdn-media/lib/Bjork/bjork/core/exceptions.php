<?php

namespace bjork\core\exceptions;

// http://php.net/manual/en/reserved.exceptions.php
// http://php.net/manual/en/spl.exceptions.php
// 
// Exception
//     LogicException
//         BadFunctionCallException
//             BadMethodCallException
//         DomainException
//         InvalidArgumentException
//         LengthException
//         OutOfRangeException
//     RuntimeException
//         OutOfBoundsException
//         OverflowException
//         RangeException
//         UnderflowException
//         UnexpectedValueException

// Base Bjork exception
class BjorkException extends \Exception {
    function __construct($message, $code=0, $previous=null) {
        if ($message instanceof \Exception) {
            $previous = $message;
            $message = sprintf('%s with message: "%s"',
                get_class($previous), $previous->getMessage());
        }
        parent::__construct($message, $code, $previous);
    }
    
    function __toString() {
        return $this->getMessage();
    }
}

// Bjork is somehow improperly configured.
class ImproperlyConfigured extends BjorkException {}

// The requested object does not exist.
class ObjectDoesNotExist extends BjorkException {}

// The requested view does not exist.
class ViewDoesNotExist extends BjorkException {}

// This middleware is not used in this server configuration.
class MiddlewareNotUsed extends BjorkException {}

// The attempted operation is not supported.
class OperationNotSupported extends BjorkException {}

// The user did something suspicious.
class SuspiciousOperation extends BjorkException {}

// The user did not have permission to do that.
class PermissionDenied extends BjorkException {}

const NON_FIELD_ERRORS_KEY = "__all__";

class ValidationError extends BjorkException {
    protected $messages;
    
    function __construct($message, $code=null, array $params=null) {
        if (is_array($message)) {
            $this->messages = $message;
            $message = array_reduce($message, function($o, $v) {
                return $o . $v;
            }, "");
        } else {
            $this->messages = array($message);
        }
        
        $this->errorCode = $code;
        $this->params = $params;
        
        parent::__construct($message);
    }
    
    function getParams() {
        return $this->params;
    }
    
    function getMessages() {
        return $this->messages;
    }
    
    function getErrorCode() {
        return $this->errorCode;
    }
    
    /*function updateErrorDict($errorDict) {
        if (!is_null($this->messageDict)) {
            if (!empty($errorDict)) {
                foreach ($this->messageDict as $k => $v) {
                    if (!isset($errorDict[$k]))
                        $errorDict[$k] = new ErrorList();
                    $errorDict[$k][] = $v;
                }
            } else {
                $errorDict = $this->messageDict;
            }
        } else {
            $errorDict[NON_FIELD_ERRORS_KEY] = $this->messages;
        }
        return $errorDict;
    }*/
}
