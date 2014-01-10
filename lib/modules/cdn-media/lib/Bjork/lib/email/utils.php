<?php

namespace email;

use strutils;
use urllib;

use email\address\AddressParser;

final class utils {
    
    const CRLF = "\r\n";
    const TICK = "'";
    const COMMASPACE = ', ';
    const EMPTYSTRING = '';
    const ADDR_SPECIALS_RE = '/[][\\()<>@,:;".]/';
    const ADDR_ESCAPES_RE = '/[][\\()"]/';
    
    static $fqdn = null;
    
    public static function decode_params($params) {
        // @TODO: not implemented
        return $params;
    }
    
    public static function unquote($str) {
        if (mb_strlen($str) > 1) {
            if (strutils::startswith($str, '"') && strutils::endswith($str, '"'))
                return str_replace('\\"', '"',
                        str_replace('\\\\', '\\',
                            mb_substr($str, 1, -1)));
            if (strutils::startswith($str, '<') && strutils::endswith($str, '>'))
                return mb_substr($str, 1, -1);
        }
        return $str;
    }
    
    public static function collapse_rfc2231_value($value) {
        if (is_array($value)) {
            $rawval = self::unquote($value[2]);
            $charset = $value[0] ?: 'us-ascii';
            return mb_convert_encoding($rawval, $charset);
        } else {
            return self::unquote($value);
        }
    }
    
    /**
    * Encode string according to RFC 2231.
    *
    * If neither charset nor language is given, then s is returned as-is.  If
    * charset is given but not language, the string is encoded using the empty
    * string for language.
    */
    public static function encode_rfc2231($s, $charset=null, $language=null) {
        $s = urllib::quote($s, '');
        if (null === $charset && null === $language)
            return $s;
        if (null === $language)
            $language = '';
        return "{$charset}'{$language}'{$s}";
    }
    
    /**
    * Return a list of (REALNAME, EMAIL) for each fieldvalue.
    */
    public static function getaddresses($fieldvalues) {
        $all = implode(self::COMMASPACE, $fieldvalues);
        $a = new AddressParser($all);
        return $a->getAddressList();
    }
    
    /**
    * Return a list of (REALNAME, EMAIL) for each fieldvalue.
    */
    public static function parseaddr($addr) {
        $parser = new AddressParser($addr);
        $addrs = $parser->getAddressList();
        if (!$addrs)
            return array('', '');
        return $addrs[0];
    }
    
    /**
    * The inverse of parseaddr(), this takes a 2-tuple of the form
    * (realname, email_address) and returns the string value suitable
    * for an RFC 2822 From, To or Cc header.
    * 
    * If the first element of pair is false, then the second element is
    * returned unmodified.
    */
    public static function formataddr($addr) {
        list($name, $address) = $addr;
        if ($name) {
            $quotes = '';
            if (preg_match(self::ADDR_SPECIALS_RE, $name))
                $quotes = '"';
            $name = preg_replace_callback(self::ADDR_ESCAPES_RE, function($m) {
                return '\\'.$m[0];
            }, $name);
            return "{$quotes}{$name}{$quotes} <{$address}>";
        }
        return $address;
    }
    
    /**
    * Returns a date string as specified by RFC 2822, e.g.:
    * 
    * Fri, 09 Nov 2001 01:08:47 -0000
    * 
    * Optional timeval if given is a floating point time value as accepted by
    * gmtime() and localtime(), otherwise the current time is used.
    * 
    * Optional localtime is a flag that when True, interprets timeval, and
    * returns a date relative to the local timezone instead of UTC, properly
    * taking daylight savings time into account.
    * 
    * Optional argument usegmt means that the timezone is written out as
    * an ascii string, not numeric one (so "GMT" instead of "+0000"). This
    * is needed for HTTP, and is only used when localtime==False.
    */
    public static function formatdate($timeval=null, $localtime=false, $usegmt=false) {
        if (null === $timeval)
            $timeval = time();
        if (is_int($timeval))
            $timeval = new \DateTime("@{$timeval}");
        
        if ($localtime) {
            // @TODO
        }
        
        $date = $timeval->format(\DateTime::RFC2822);
        
        if ($localtime && $usegmt)
            $date = preg_replace('/[+\-]0000$/', 'GMT', $date);
        
        return $date;
    }
    
    /**
    * Returns a string suitable for RFC 2822 compliant Message-ID, e.g:
    * 
    * <20020201195627.33539.96671@nightshade.la.mastaler.com>
    * 
    * Optional idstring if given is a string used to strengthen the
    * uniqueness of the message id.
    */
    public static function make_msgid($idstring=null) {
        $utcdate = gmstrftime('%Y%m%d%H%M%S', time());
        $pid = getmypid();
        $randint = mt_rand(0, 100000);
        $idstring = $idstring ? '.' . $idstring : '';
        $idhost = self::getfqdn();
        return sprintf('<%s.%s.%s%s@%s>', $utcdate, $pid, $randint,
            $idstring, $idhost);
    }
    
    /**
    * Get the fully qualified domain name of this computer.
    */
    public static function getfqdn() {
        if (null === self::$fqdn)
            self::$fqdn = php_uname('n');
        return self::$fqdn;
    }
    
    /**
    * Replace all line-ending characters with \r\n.
    */
    public static function fix_eols($s) {
        // Fix newlines with no preceding carriage return
        $s = preg_replace('/(?<!\r)\n/', self::CRLF, $s);
        // Fix carriage returns with no following newline
        $s = preg_replace('/\r(?!\n)/', self::CRLF, $s);
        return $s;
    }
}
