<?php

namespace bjork\utils;

final class http {
    /**
    * Formats the time to ensure compatibility with Netscape's cookie standard.
    * 
    * Accepts a floating point number expressed in seconds since the epoch, in
    * UTC - such as that output by time(). If set to None, defaults to the
    * current time.
    * 
    * Outputs a string in the format 'Wdy, DD-Mon-YYYY HH:MM:SS GMT'.
    */
    public static function cookie_date($epoch_seconds=null) {
        if (null === $epoch_seconds)
            $date = new \DateTime(); // now
        else
            $date = new \DateTime('@'.strval($epoch_seconds));
        $f = $date->format(\DateTime::RFC2822);
        return sprintf('%s-%s-%s GMT',
            substr($f, 0, 7),
            substr($f, 8, 3),
            substr($f, 12, 13)
        );
    }
    /**
    * Formats the time to match the RFC1123 date format as specified by HTTP
    * RFC2616 section 3.3.1.
    *
    * Accepts a floating point number expressed in seconds since the epoch, in
    * UTC - such as that outputted by time.time(). If set to None, defaults to
    * the current time.
    *
    * Outputs a string in the format 'Wdy, DD Mon YYYY HH:MM:SS GMT'.
    */
    public static function http_date($epoch_seconds=null) {
        if (null === $epoch_seconds)
            $date = new \DateTime(); // now
        else
            $date = new \DateTime('@'.strval($epoch_seconds));
        $f = $date->format(\DateTime::RFC1123);
        return sprintf('%s GMT',
            substr($f, 0, 25)
        );
    }
    
    /**
    * Converts an integer to a base36 string
    */
    public static function int_to_base36($i) {
        /*
        $digits = '0123456789abcdefghijklmnopqrstuvwxyz';
        $factor = 0;
        // find starting factor
        while (true) {
            $factor++;
            if ($i < pow(36, $factor)) {
                $factor--;
                break;
            }
        }
        $base36 = array();
        // construct base36 representation
        while ($factor >= 0) {
            $j = pow(36, $factor);
            $base36[] = $digits[$i / $j];
            $i = $i % $j;
            $factor--;
        }
        return implode('', $base36);
        */
        return base_convert($i, 10, 36);
    }
    
    /**
    * Converts a base 36 string to an ``int``. Raises ``ValueError` if the
    * input won't fit into an int.
    */
    public static function base36_to_int($s) {
        // To prevent overconsumption of server resources, reject any
        // base36 string that is longer than 13 base36 digits (13 digits
        // is sufficient to base36-encode any 64-bit integer)
        if (strlen($s) > 13)
            throw new \Exception('Base36 input too large');
        $value = intval(base_convert($s, 36, 10));
        return $value;
    }
    
    /**
    * Checks if two URLs are 'same-origin'
    */
    public static function same_origin($url1, $url2) {
        $p1 = urllib::parse($url1);
        $p2 = urllib::parse($url2);
        return $p1['scheme']   === $p2['scheme'] &&
               $p1['hostname'] === $p2['hostname'] &&
               $p1['port']     === $p2['port'];
    }
    
    public static function b64encode($value) {
        return base64_encode($value);
    }
    
    public static function b64decode($value) {
        return base64_decode($value);
    }
    
    public static function urlsafe_b64encode($value) {
        return strtr(base64_encode($value), '+/', '-_');
    }
    
    public static function urlsafe_b64decode($value) {
        return base64_decode(strtr($value, '-_', '+/'));
    }
}
