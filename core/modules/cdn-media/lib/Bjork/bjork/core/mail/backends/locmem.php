<?php

namespace bjork\core\mail\backends\locmem;

use bjork\core\mail,
    bjork\core\mail\backends\base\BaseEmailBackend;

/**
* A email backend for use during test sessions.
* 
* The test connection stores email messages in a dummy outbox,
* rather than sending them out on the wire.
* 
* The dummy outbox is accessible through the outbox instance attribute.
*/
class EmailBackend extends BaseEmailBackend {
    
    function __construct(array $options=null, $fail_silently=false) {
        parent::__construct($options, $fail_silently);
        if (null === mail::$outbox)
            mail::$outbox = array();
    }
    
    public function sendMessages(array $email_messages) {
        foreach ($email_messages as $msg)
            mail::$outbox[] = $msg;
        return count($email_messages);
    }
}
