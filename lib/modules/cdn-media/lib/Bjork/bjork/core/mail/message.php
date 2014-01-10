<?php

namespace bjork\core\mail\message;

use strutils;
use email\header\Header,
    email\charset,
    email\encoders,
    email\mime,
    email\utils as email_utils;

use bjork\conf\settings,
    bjork\core\mail,
    bjork\core\mail\utils as mail_utils;

// Don't BASE64-encode UTF-8 messages so that we avoid
// unwanted attention from some spam filters.
charset::add_charset('utf-8', charset::SHORTEST, null, 'utf-8');

// Default MIME type to use on attachments (if it is not explicitly given)
const DEFAULT_ATTACHMENT_MIME_TYPE = 'application/octet-stream';

/**
* A container for email information.
*/
class EmailMessage {
    
    protected
        $content_subtype = 'plain',
        $mixed_subtype = 'mixed',
        $encoding = null; // use settings default
    
    var $subject, $body,
        $from_email,
        $to, $cc, $bcc,
        $attachments, $headers,
        $connection;
    
    function __construct($subject='', $body='', $from_email=null, $to=null,
            array $headers=null, array $options=null, $connection=null)
    {
        extract(get_options_with_defaults($options, array(
            'cc' => array(),
            'bcc' => array(),
            'attachments' => array(),
        )));
        
        if (null === $from_email)
            $from_email = settings::get('DEFAULT_FROM_EMAIL');
        
        if (is_string($to))
            $to = array($to);
        
        if (!is_array($to)) throw new \Exception('`to` must be an array');
        if (!is_array($cc)) throw new \Exception('`cc` must be an array');
        if (!is_array($bcc)) throw new \Exception('`bcc` must be an array');
        
        if (!$headers) $headers = array();
        if (!$attachments) $attachments = array();
        
        $this->subject = $subject;
        $this->body = $body;
        $this->from_email = $from_email;
        $this->to = $to;
        $this->cc = $cc;
        $this->bcc = $bcc;
        $this->headers = $headers;
        $this->attachments = $attachments;
        $this->connection = $connection;
    }
    
    public function getConnection($fail_silently=false) {
        if (null === $this->connection)
            $this->connection = mail::get_connection($fail_silently);
        return $this->connection;
    }
    
    public function getContentSubtype() {
        return $this->content_subtype;
    }
    
    public function setContentSubtype($subtype) {
        $this->content_subtype = $subtype;
    }
    
    public function getEncoding() {
        return $this->encoding;
    }
    
    public function setEncoding($encoding) {
        $this->encoding = $encoding;
    }
    
    public function getMessage() {
        $encoding = $this->encoding ?: settings::get('DEFAULT_CHARSET');
        $msg = new SafeMIMEText($this->body, $this->content_subtype, $encoding);
        $msg = $this->createMessage($msg);
        $msg['Subject'] = $this->subject;
        $msg['From'] = array_key_exists('From', $this->headers)
            ? $this->headers['From']
            : $this->from_email;
        $msg['To'] = implode(', ', $this->to);
        if ($this->cc)
            $msg['Cc'] = implode(', ', $this->cc);
        
        // Email header names are case-insensitive (RFC 2045), so we have to
        // accommodate that when doing comparisons.
        $header_names = array_map('strtolower', array_keys($this->headers));
        if (!in_array('date', $header_names))
            $msg['Date'] = email_utils::formatdate(null, true);
        if (!in_array('message-id', $header_names))
            $msg['Message-ID'] = email_utils::make_msgid();
        foreach ($this->headers as $name => $value) {
            if (strtolower($name) == 'from')
                continue;
            $msg[$name] = $value;
        }
        return $msg;
    }
    
    public function getFromEmail() {
        return $this->from_email;
    }
    
    public function getRecipients() {
        return array_merge($this->to, $this->cc, $this->bcc);
    }
    
    public function send($fail_silently=false) {
        if (!$this->getRecipients())
            return 0;
        return $this->getConnection($fail_silently)->sendMessages(array($this));
    }
    
    /**
    * Attaches a file with the given filename and content. The filename can
    * be omitted and the mimetype is guessed, if not provided.
    *
    * If the first parameter is a MIMEBase subclass it is inserted directly
    * into the resulting message attachments.
    */
    public function attach($filename=null, $content=null, $mimetype=null) {
        if ($filename instanceof mime\MIMEBase)
            $this->attachments[] = $filename;
        else
            $this->attachments[] = array($filename, $content, $mimetype);
    }
    
    /**
    * Attaches a file from the filesystem.
    */
    public function attachFile($path, $mimetype=null) {
        $filename = basename($path);
        $content = file_get_contents($path);
        $this->attach($filename, $content, $mimetype);
    }
    
    protected function createMessage($msg) {
        return $this->createAttachments($msg);
    }
    
    protected function createAttachments($msg) {
        if ($this->attachments) {
            $encoding = $this->encoding ?: settings::get('DEFAULT_CHARSET');
            $body_msg = $msg;
            $msg = new SafeMIMEMultipart($this->mixed_subtype, $encoding);
            if ($this->body)
                $msg->attach($body_msg);
            foreach ($this->attachments as $attachment) {
                if ($attachment instanceof mime\MIMEBase)
                    $msg->attach($attachment);
                else
                    $msg->attach($this->createAttachment(
                        $attachment[0], $attachment[1], $attachment[2]));
            }
        }
        return $msg;
    }
    
    protected function createAttachment($filename, $content, $mimetype=null) {
        if (!$mimetype)
            $mimetype = DEFAULT_ATTACHMENT_MIME_TYPE;
        $attachment = $this->createMIMEAttachment($content, $mimetype);
        if ($filename)
            $attachment->addHeader('Content-Disposition', 'attachment', array(
                'filename' => $filename,
            ));
        return $attachment;
    }
    
    protected function createMIMEAttachment($content, $mimetype) {
        list($basetype, $subtype) = strutils::split($mimetype, '/', 1);
        if ($basetype == 'text') {
            $encoding = $this->encoding ?: settings::get('DEFAULT_CHARSET');
            $attachment = new SafeMIMEText($content, $subtype, $encoding);
        } else {
            $attachment = new mime\MIMEBase($basetype, $subtype);
            $attachment->setPayload($content);
            encoders::encode_base64($attachment);
        }
        return $attachment;
    }
}

/**
* A version of EmailMessage that makes it easy to send multipart/alternative
* messages. For example, including text and HTML versions of the text is
* made easier.
*/
class EmailMultiAlternatives extends EmailMessage {
    
    protected $alternative_subtype = 'alternative';
    
    var $alternatives;
    
    function __construct($subject='', $body='', $from_email=null, $to=null,
            array $headers=null, array $options=null, $connection=null)
    {
        if (!$options)
            $options = array();
        
        if (array_key_exists('alternatives', $options)) {
            $this->alternatives = $options['alternatives'];
            unset($options['alternatives']);
        } else {
            $this->alternatives = array();
        }
        
        parent::__construct($subject, $body, $from_email, $to, $headers,
            $options, $connection);
    }
    
    /**
    * Attach an alternative content representation.
    */
    public function attachAlternative($content, $mimetype) {
        $this->alternatives[] = array($content, $mimetype);
    }
    
    protected function createMessage($msg) {
        return $this->createAttachments($this->createAlternatives($msg));
    }
    
    protected function createAlternatives($msg) {
        $encoding = $this->encoding ?: settings::get('DEFAULT_CHARSET');
        if ($this->alternatives) {
            $body_msg = $msg;
            $msg = new SafeMIMEMultipart($this->alternative_subtype, $encoding);
            if ($this->body)
                $msg->attach($body_msg);
            foreach ($this->alternatives as $alternative)
                $msg->attach($this->createMIMEAttachment($alternative[0], $alternative[1]));
        }
        return $msg;
    }
}

class SafeMIMEText extends mime\MIMEText {
    protected $encoding;
    
    function __construct($text, $subtype, $charset) {
        $this->encoding = $charset;
        parent::__construct($text, $subtype, $charset);
    }
    
    function offsetSet($name, $val) {
        list($name, $val) = mail_utils::forbid_multi_line_headers(
            $name, $val, $this->encoding);
        parent::offsetSet($name, $val);
    }
}

class SafeMIMEMultipart extends mime\MIMEMultipart {
    protected $encoding;
    
    function __construct($subtype='mixed', $encoding=null, $boundary=null,
                         $subparts=null, array $params=null)
    {
        $this->encoding = $encoding;
        parent::__construct($subtype, $boundary, $subparts, $params);
    }
    
    function offsetSet($name, $val) {
        list($name, $val) = mail_utils::forbid_multi_line_headers(
            $name, $val, $this->encoding);
        parent::offsetSet($name, $val);
    }
}

function get_options_with_defaults($options, array $defaults) {
    if (null === $options)
        $options = array();
    return array_merge($defaults, $options);
}
