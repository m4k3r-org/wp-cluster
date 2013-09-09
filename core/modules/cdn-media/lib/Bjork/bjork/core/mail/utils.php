<?php

namespace bjork\core\mail;

use email\header\Header,
    email\utils as email_utils;

use bjork\conf\settings;

final class utils {
    
    static
        $address_headers = array(
            'from',
            'sender',
            'reply-to',
            'to',
            'cc',
            'bcc',
            'resent-from',
            'resent-sender',
            'resent-to',
            'resent-cc',
            'resent-bcc',
        );
    
    /**
    * Forbids multi-line headers, to prevent header injection.
    */
    public static function forbid_multi_line_headers($name, $val, $encoding) {
        $encoding = $encoding ?: settings::get('DEFAULT_CHARSET');
        $val = mb_convert_encoding($val, 'utf-8');
        if (false !== strpos($val, "\n") || false !== strpos($val, "\r"))
            throw new \Exception('Header values can\'t contain newlines (for header: '.$name.')');
        if (!mb_check_encoding($val, 'ascii')) {
            if (in_array(strtolower($name), self::$address_headers)) {
                $newaddresses = array();
                // can't use array_map because 'self' is not being made
                // available to the lambda so we loop over the array.
                foreach (email_utils::getaddresses(array($val)) as $addr)
                    $addresses[] = self::sanitize_address($addr, $encoding);
                $val = implode(', ', $addresses);
            } else {
                $val = strval(new Header($val, $encoding));
            }
        } else {
            $val = mb_convert_encoding($val, 'ascii');
            if (strtolower($name) == 'subject')
                $val = new Header($val);
        }
        return array($name, $val);
    }
    
    public static function sanitize_address($address, $encoding=null) {
        if (is_string($address))
            $address = email_utils::parseaddr($address);
        list($nm, $addr) = $address;
        $nm = strval(new Header($nm, $encoding));
        $addr = strval(new Header($addr, $encoding));
        return email_utils::formataddr(array($nm, $addr));
    }
}
