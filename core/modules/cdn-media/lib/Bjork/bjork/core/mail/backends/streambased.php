<?php

namespace bjork\core\mail\backends\streambased;

use bjork\core\mail\backends\base\BaseEmailBackend;

/**
* Email backend that writes messages to a stream instead of sending them.
*/
class EmailBackend extends BaseEmailBackend {
    
    var $stream, $stream_created;
    
    function __construct(array $options=null, $fail_silently=false) {
        parent::__construct($options, $fail_silently);
        if (!$options)
            $options = array();
        $this->stream = array_key_exists('stream', $options)
            ? $options['stream']
            : fopen(STDERR);
    }
    
    function sendMessages(array $email_messages) {
        if (!$email_messages)
            return;
        
        $this->lock();
        
        try {
            $this->stream_created = $this->open();
            foreach ($email_messages as $msg) {
                fwrite($this->stream, sprintf("%s\n",
                    $msg->getMessage()->asString()));
                fwrite($this->stream, str_repeat('-', 79));
                fwrite($this->stream, "\n");
                fflush($this->stream);
            }
            if ($this->stream_created)
                $this->close();
            $this->unlock();
        } catch (\Exception $e) {
            if ($this->stream_created)
                $this->close();
            $this->unlock();
            if (!$this->fail_silently)
                throw $e;
        }
        
        return count($email_messages);
    }
    
    function lock() {}
    
    function unlock() {}
}
