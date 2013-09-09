<?php

namespace bjork\core\mail\backends\base;

/**
* Base class for email backend implementations.
* Subclasses must at least overwrite sendMessages().
*/
abstract class BaseEmailBackend {
    
    protected $fail_silently;
    
    function __construct(array $options=null, $fail_silently=false) {
        $this->fail_silently = $fail_silently;
    }
    
    /**
    * Open a network connection.
    * 
    * This method can be overwritten by backend implementations to
    * open a network connection.
    * 
    * It's up to the backend implementation to track the status of
    * a network connection if it's needed by the backend.
    * 
    * This method can be called by applications to force a single
    * network connection to be used when sending mails. See the
    * sendMessages() method of the SMTP backend for a reference
    * implementation.
    * 
    * The default implementation does nothing.
    */
    public function open() {}
    
    /**
    * Close a network connection.
    * 
    * The default implementation does nothing.
    */
    public function close() {}
    
    /**
    * Sends one or more EmailMessage objects and returns the number of email
    * messages sent.
    */
    abstract public function sendMessages(array $email_messages);
}
