<?php

namespace bjork\utils;

use strutils;

use bjork\conf\settings;

final class crypto {
    /**
    * Returns the HMAC-SHA1 of 'value', using a key generated from key_salt
    * and a secret (which defaults to settings.SECRET_KEY).
    * 
    * A different key_salt should be passed in for every application of HMAC.
    */
    public static function salted_hmac($key_salt, $value, $secret=null) {
        if (null === $secret)
            $secret = settings::get('SECRET_KEY');
        
        // We need to generate a derived key from our base key.
        // We can do this by passing the key_salt and our base key
        // through a pseudo-random function and SHA1 works nicely.
        $key = sha1($key_salt . $secret);
        
        // If len(key_salt + secret) > sha_constructor().block_size, the
        // above line is redundant and could be replaced by
        // key = key_salt + secret, since the hmac module does the same
        // thing for keys longer than the block size. However, we need to
        // ensure that we *always* do this.
        return hash_hmac('sha1', $value, $key);
    }
    
    /**
    * Returns a random string of length characters from the set of a-z, A-Z, 0-9
    * for use as a salt.
    * 
    * The default length of 12 with the a-z, A-Z, 0-9 character set returns
    * a 71-bit salt. log_2((26+26+10)^12) =~ 71 bits
    */
    public static function get_random_string($length=12,
        $allowed_chars='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789')
    {
        $str = '';
        $chars = strutils::explode($allowed_chars);
        foreach (range(1, $length) as $i)
            $str .= $chars[array_rand($chars, 1)];
        return $str;
    }
    
    /**
    * Returns True if the two strings are equal, False otherwise.
    *
    * The time taken is independent of the number of characters that match.
    */
    public static function constant_time_compare($val1, $val2) {
        if (mb_strlen($val1) !== mb_strlen($val2))
            return false;
        $result = 0;
        for ($i=0; $i < mb_strlen($val1); $i++)
            $result |= ord($val1{$i}) ^ ord($val2{$i});
        return $result === 0;
    }
}
