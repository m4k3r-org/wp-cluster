<?php

namespace email\mime;

/*
    Message
        MIMEBase
            MIMEMultipart
            MIMENonMultipart
                MIMEText
                MIMEApplication
                MIMEAudio
                MIMEImage
                MIMEMessage
*/

use email\message\Message;

/**
* Base class for MIME specializations.
*/
class MIMEBase extends Message {
    
    /**
    * This constructor adds a Content-Type: and a MIME-Version: header.
    * 
    * The Content-Type: header is taken from the _maintype and _subtype
    * arguments.  Additional parameters for this header are taken from the
    * keyword arguments.
    */
    function __construct($maintype, $subtype, array $params=null) {
        parent::__construct();
        $ctype = "{$maintype}/{$subtype}";
        $this->addHeader('Content-Type', $ctype, $params);
        $this['MIME-Version'] = '1.0';
    }
}

/**
* Base class for MIME multipart/* type messages.
*/
class MIMEMultipart extends MIMEBase {
    /**
    * Creates a multipart/* type message.
    *
    * By default, creates a multipart/mixed message, with proper
    * Content-Type and MIME-Version headers.
    * 
    * _subtype is the subtype of the multipart content type, defaulting to
    * `mixed'.
    * 
    * boundary is the multipart boundary string.  By default it is
    * calculated as needed.
    * 
    * _subparts is a sequence of initial subparts for the payload.  It
    * must be an iterable object, such as a list.  You can always
    * attach new subparts to the message by using the attach() method.
    * 
    * Additional parameters for the Content-Type header are taken from the
    * keyword arguments (or passed into the _params argument).
    */
    function __construct($subtype='mixed', $boundary=null, $subparts=null, array $params=null) {
        parent::__construct('multipart', $subtype, $params);
        $this->payload = array();
        if ($subparts) {
            foreach ($subparts as $p)
                $this->attach($p);
        }
        if ($boundary)
            $this->setBoundary($boundary);
    }
}

/**
* Base class for non MIME multipart/* type messages.
*/
class MIMENonMultipart extends MIMEBase {
    
    function attach($payload) {
        throw new \Exception('Cannot attach additional subparts to non-multipart/*');
    }
}

/**
* Class for generating text/* type MIME documents.
*/
class MIMEText extends MIMENonMultipart {
    
    /**
    * Create a text/* type MIME document.
    * 
    * _text is the string for this message object.
    * 
    * _subtype is the MIME sub content type, defaulting to "plain".
    * 
    * _charset is the character set parameter added to the Content-Type
    * header.  This defaults to "us-ascii".  Note that as a side-effect, the
    * Content-Transfer-Encoding header will also be set.
    */
    function __construct($text, $subtype='plain', $charset='us-ascii') {
        parent::__construct('text', $subtype, array('charset' => $charset));
        $this->setPayload($text, $charset);
    }
}
