<?php

namespace email;

use email\message\Message;

final class encoders {
    
    /**
    * Encode the message's payload in Base64.
    * 
    * Also, add an appropriate Content-Transfer-Encoding header.
    */
    public static function encode_base64(Message $msg) {
        $orig = $msg->getPayload();
        $encdata = base64_encode($orig);
        $msg->setPayload($encdata);
        $msg['Content-Transfer-Encoding'] = 'base64';
    }
    
    /**
    * Encode the message's payload in quoted-printable.
    * 
    * Also, add an appropriate Content-Transfer-Encoding header.
    */
    public static function encode_quopri(Message $msg) {
        $orig = $msg->getPayload();
        $encdata = mb_convert_encoding($orig, 'Quoted-Printable');
        
        /*
        // props: <http://php.net/manual/en/ref.stream.php#70826>
        $fp = fopen('php://temp/', 'r+');
        stream_filter_append($fp, 'convert.quoted-printable-encode',
            \STREAM_FILTER_READ, array(
                'line-length' => 70,
                'line-break-chars' => "\n", // must match content's line-endings
                // Other opts:
                // 'binary': boolean, hex encodes all control chars,
                //           including spaces and line breaks, but leaves
                //           alphanumerics untouched
        ));
        
        fputs($fp, $orig);
        rewind($fp);
        $encdata = stream_get_contents($fp);
        */

        /*
        Better impl without tmpfile:
        
            $h = fopen('gecodeerd.txt', 'r');
            stream_filter_append($h, 'convert.quoted-printable-encode');
            fpassthru($h);
            fclose($h);
        
        Or
        
            $filter = 'convert.quoted-printable-encode';
            $file = 'coded.txt';
            $h = fopen('php://filter/read=' . $filter . '/resource=' . $file,'r'); 
            fpassthru($h);
            fclose($h);
        
        */
        
        $msg->setPayload($encdata);
        $msg['Content-Transfer-Encoding'] = 'quoted-printable';
    }
    
    /**
    * Set the Content-Transfer-Encoding header to 7bit or 8bit.
    */
    public static function encode_7or8bit(Message $msg) {
        $orig = $msg->getPayload();
        if (null === $orig) {
            $msg['Content-Transfer-Encoding'] = '7bit';
            return;
        }
        if (mb_check_encoding($orig, '7bit'))
            $msg['Content-Transfer-Encoding'] = '7bit';
        else
            $msg['Content-Transfer-Encoding'] = '8bit';
    }
}
