<?php

namespace bjork\core\mail\backends\php;

use bjork\core\mail\backends\base\BaseEmailBackend,
    bjork\core\mail\utils as mail_utils;

/**
* An email backend using PHP's mail() function.
*/
class EmailBackend extends BaseEmailBackend {
    
    var $additional_parameters = null;
    
    function __construct(array $options=null, $fail_silently=false) {
        parent::__construct($options, $fail_silently);
        if (!$options)
            $options = array();
        $this->additional_parameters = isset($options['additional_parameters'])
            ? $options['additional_parameters']
            : null;
    }
    
    public function sendMessages(array $email_messages) {
        $num_sent = 0;
        foreach ($email_messages as $msg) {
            $sent = $this->sendMessage($msg);
            if ($sent)
                $num_sent++;
        }
        return $num_sent;
    }
    
    function sendMessage($msg) {
        if (!$msg->getRecipients())
            return false;
        
        // Sanitize sender and recipient addresses
        $encoding = $msg->getEncoding();
        
        $from_email = mail_utils::sanitize_address(
            $msg->getFromEmail(), $encoding);
        
        $recipients = array();
        foreach ($msg->getRecipients() as $addr)
            $recipients[] = mail_utils::sanitize_address($addr, $encoding);
        $recipients = implode(', ', $recipients);
        
        // Separate the final message into the parts required by mail()
        list($subject, $message, $headers) = $this->processMessage($msg);
        
        // Actually send the message
        $error = null;
        
        try {
            $sent = mail($recipients,
                         $subject,
                         $message,
                         $headers,
                         $this->additional_parameters);
        } catch (\Exception $e) {
            $error = clone $e;
            $sent = false;
        }
        
// header('Content-Type: text/plain; charset="utf-8"');
// echo $msg->getMessage()->asString();exit();
        
        if (!$sent && !$this->fail_silently) {
            $errmsg = 'Failed to send message: %s';
            if ($error)
                $errmsg = sprintf($errmsg, $error->getMessage());
            else
                $errmsg = sprintf($errmsg, 'Unknown error');
            throw new \Exception($errmsg, 0, $error);
        }
        
        return $sent;
    }
    
    function processMessage($email) {
        $subject = '';
        $message = '';
        $headers = '';
        
        $msg = $email->getMessage();
        
        // rip out Subject: as it's set by mail()
        $subject = $msg['subject'];
        unset($msg['subject']);
        
        // also rip out To:
        unset($msg['to']);
        
        // we need to get the final message as string and
        // work on that to split the headers from the rest
        // of the message
        $s =& $msg->asString();
        $split_pos = mb_strpos($s, "\n\n");
        if (false === $split_pos)
            return array($subject, $s, null);
        $headers = mb_substr($s, 0, $split_pos + 2); // keep the blank line
        $message = mb_substr($s, $split_pos + 2);
        
        return array($subject, $message, $headers);
    }
}

