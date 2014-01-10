<?php
// Port of 'django.utils.baseconv' module.
// 
// Copyright (c) 2010 Taurinus Collective. All rights reserved.
// Copyright (c) 2009 Simon Willison. All rights reserved.
// Copyright (c) 2002 Drew Perttula. All rights reserved.
// 
// License:
//   Python Software Foundation License version 2
// 
// See the file "LICENSE" for terms & conditions for usage, and a DISCLAIMER OF
// ALL WARRANTIES.
// 
// This Baseconv distribution contains no GNU General Public Licensed (GPLed)
// code so it may be used in proprietary projects just like prior ``baseconv``
// distributions.
// 
// All trademarks referenced herein are property of their respective holders.

namespace bjork\utils\baseconv {

use strutils;

class BaseConverter {
    const decimal_digits = '0123456789';
    
    protected
        $sign,
        $digits;
    
    function __construct($digits, $sign='-') {
        $this->sign = $sign;
        $this->digits = $digits;
        if (false !== strpos($digits, $sign))
            throw new \Exception('Sign character found in converter base digits');
    }
    
    function __toString() {
        return '<BaseConverter: base'.count($this->digits).' ('.$this->digits.')>';
    }
    
    function encode($i) {
        list($neg, $value) = $this->convert($i,
            self::decimal_digits,
            $this->digits,
            '-');
        if ($neg)
            return $this->sign . $value;
        return $value;
    }
    
    function decode($s) {
        list($neg, $value) = $this->convert($s,
            $this->digits,
            self::decimal_digits,
            $this->sign);
        if ($neg)
            $value = '-' . $value;
        return intval($value);
    }
    
    function convert($number, $from_digits, $to_digits, $sign) {
        $str = strval($number);
        if ($str{0} === $sign) {
            $number = substr($str, 1);
            $neg = 1;
        } else {
            $number = $str;
            $neg = 0;
        }
        
        //make an integer out of the number
        $x = 0;
        foreach (strutils::explode($number) as $digit) {
            $index = strpos($from_digits, $digit);
            if (false === $index)
                throw new \Exception();
            $x = $x * strlen($from_digits) + $index;
        }
        
        // create the result in base 'len(to_digits)'
        if ($x === 0) {
            $res = $to_digits{0};
        } else {
            $res = '';
            $to_digits_count = strlen($to_digits);
            while ($x > 0) {
                $digit = $x % $to_digits_count;
                $res = $to_digits{$digit} . $res;
                $x = intval($x / $to_digits_count);
            }
        }
        
        return array($neg, $res);
    }
}

}

namespace bjork\utils {

/**
* Convert numbers from base 10 integers to base X strings and back again.
* 
* Sample usage::
* 
*   >>> base20 = BaseConverter('0123456789abcdefghij')
*   >>> base20.encode(1234)
*   '31e'
*   >>> base20.decode('31e')
*   1234
*   >>> base20.encode(-1234)
*   '-31e'
*   >>> base20.decode('-31e')
*   -1234
*   >>> base11 = BaseConverter('0123456789-', sign='$')
*   >>> base11.encode('$1234')
*   '$-22'
*   >>> base11.decode('$-22')
*   '$1234'
* 
*/
final class baseconv {

    const BASE2_ALPHABET  = '01';
    const BASE16_ALPHABET = '0123456789ABCDEF';
    const BASE36_ALPHABET = '0123456789abcdefghijklmnopqrstuvwxyz';
    const BASE56_ALPHABET = '23456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz';
    const BASE62_ALPHABET = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    const BASE64_ALPHABET = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-_';
    
    protected static $cache = array();
    
    protected static function get_converter($base_type) {
        if (!array_key_exists($base_type, self::$cache)) {
            switch ($base_type) {
                case 'base2': $converter = new baseconv\BaseConverter(self::BASE2_ALPHABET); break;
                case 'base16': $converter = new baseconv\BaseConverter(self::BASE16_ALPHABET); break;
                case 'base36': $converter = new baseconv\BaseConverter(self::BASE36_ALPHABET); break;
                case 'base56': $converter = new baseconv\BaseConverter(self::BASE56_ALPHABET); break;
                case 'base62': $converter = new baseconv\BaseConverter(self::BASE62_ALPHABET); break;
                case 'base64': $converter = new baseconv\BaseConverter(self::BASE64_ALPHABET, '$'); break;
                default:
                    throw new \Exception('Invalid converter base ('.$base_type.')');
                    break;
            }
            self::$cache[$base_type] = $converter;
        }
        return self::$cache[$base_type];
    }
    
    public static function b2encode($i) { return self::get_converter('base2')->encode($i); }
    public static function b2decode($s) { return self::get_converter('base2')->decode($s); }
    
    public static function b16encode($i) { return self::get_converter('base16')->encode($i); }
    public static function b16decode($s) { return self::get_converter('base16')->decode($s); }
    
    public static function b36encode($i) { return self::get_converter('base36')->encode($i); }
    public static function b36decode($s) { return self::get_converter('base36')->decode($s); }
    
    public static function b56encode($i) { return self::get_converter('base56')->encode($i); }
    public static function b56decode($s) { return self::get_converter('base56')->decode($s); }
    
    public static function b62encode($i) { return self::get_converter('base62')->encode($i); }
    public static function b62decode($s) { return self::get_converter('base62')->decode($s); }
    
    public static function b64encode($i) { return self::get_converter('base64')->encode($i); }
    public static function b64decode($s) { return self::get_converter('base64')->decode($s); }
}

}
