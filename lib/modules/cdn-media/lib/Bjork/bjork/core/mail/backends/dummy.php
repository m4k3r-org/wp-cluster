<?php

namespace bjork\core\mail\backends\dummy;

use bjork\core\mail\backends\base\BaseEmailBackend;

/**
* Dummy email backend that does nothing.
*/
class EmailBackend extends BaseEmailBackend {
    
    public function sendMessages(array $email_messages) {
        return count($email_messages);
    }
}
