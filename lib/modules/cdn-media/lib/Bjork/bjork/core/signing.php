<?php

namespace bjork\core\signing {

use strutils;

use bjork\conf\settings,
    bjork\utils\baseconv,
    bjork\utils\crypto,
    bjork\utils\http;

/**
* Signature does not match
*/
class BadSignature extends \Exception {}

/**
* Signature timestamp is older than required max_age
*/
class SignatureExpired extends BadSignature {}

class JSONSerializer {
    function dumps($obj) {
        return json_encode($obj);
    }
    
    function loads($data) {
        return json_decode($data);
    }
}

function b64_encode($s) {
    return rtrim(http::urlsafe_b64encode($s), '=');
}

function b64_decode($s) {
    $pad = str_repeat('=', strlen($s) % 4);
    return http::urlsafe_b64decode($s . $pad);
}

function b64_hmac($salt, $value, $key) {
    return b64_encode(crypto::salted_hmac($salt, $value, $key));
}

class Signer {
    
    protected
        $sep,
        $key,
        $salt;
    
    function __construct($key=null, $salt=null, $sep=':') {
        if (null === $key)
            $key = settings::get('SECRET_KEY');
        if (null === $salt)
            $salt = get_class();
        $this->sep = $sep;
        $this->key = $key;
        $this->salt = $salt;
    }
    
    function getSignature($value) {
        return b64_hmac($this->salt . 'signer', $value, $this->key);
    }
    
    function sign($value) {
        return sprintf('%s%s%s',
            $value,
            $this->sep,
            $this->getSignature($value)
        );
    }
    
    function unsign($signed_value) {
        if (false === mb_strpos($signed_value, $this->sep))
            throw new BadSignature("No '{$this->sep}' found in value");
        list($value, $sig) = strutils::rsplit($signed_value, $this->sep, 1);
        if (crypto::constant_time_compare($sig, $this->getSignature($value)))
            return $value;
        throw new BadSignature("Signature '{$sig}' does not match");
    }
}

class TimestampSigner extends Signer {
    
    function sign($value) {
        $value = sprintf('%s%s%s', $value, $this->sep, $this->getTimestamp());
        return parent::sign($value);
    }
    
    function unsign($signed_value, $max_age=null) {
        $result = parent::unsign($signed_value);
        list($value, $timestamp) = strutils::rsplit($result, $this->sep, 1);
        $timestamp = baseconv::b62decode($timestamp);
        if (null !== $max_age) {
            $age = time() - $timestamp;
            if ($age > $max_age)
                throw new BadSignature("Signature age {$age} > {$max_age} seconds");
        }
        return $value;
    }
    
    function getTimestamp() {
        return baseconv::b62encode(time());
    }
}

}

namespace bjork\core {

use bjork\conf\settings;

final class signing {
    
    /**
    * Returns URL-safe, sha1 signed base64 compressed JSON string. If key is
    * None, settings.SECRET_KEY is used instead.
    * 
    * If compress is True (not the default) checks if compressing using zlib can
    * save some space. Prepends a '.' to signify compression. This is included
    * in the signature, to protect against zip bombs.
    * 
    * Salt can be used to namespace the hash, so that a signed string is
    * only valid for a given namespace. Leaving this at the default
    * value or re-using a salt value across different parts of your
    * application without good cause is a security risk.
    */
    public static function dumps($obj, $key=null, $salt='bjork.core.signing',
            $serializer='bjork\core\signing\JSONSerializer', $compress=false) {
        
        if (is_string($serializer))
            $serializer = new $serializer();
        $data = $serializer->dumps($obj);
        
        // flag for if it's been compressed or not
        $is_compressed = false;
        
        if ($compress && function_exists('gzcompress')) {
            $compressed = gzcompress($data);
            if (strlen($compressed) < strlen($data) - 1) {
                $data = $compressed;
                $is_compressed = true;
            }
        }
        
        $base64d = signing\b64_encode($data);
        if ($is_compressed)
            $base64d = '.' . $base64d;
        
        $signer = new signing\TimestampSigner($key, $salt);
        return $signer->sign($base64d);
    }
    
    /**
    * Reverse of dumps(), raises BadSignature if signature fails
    */
    public static function loads($s, $key=null, $salt='bjork.core.signing',
            $serializer='bjork\core\signing\JSONSerializer', $max_age=null)
    {
        $signer = new signing\TimestampSigner($key, $salt);
        $base64d = $signer->unsign($s, $max_age);
        $decompress = false;
        
        if ($base64d{0} === '.') {
            $base64d = ltrim($base64d, '.');
            $decompress = true;
        }
        
        $data = signing\b64_decode($base64d);
        if ($decompress && function_exists('gzuncompress'))
            $data = gzuncompress($data);
        
        if (is_string($serializer))
            $serializer = new $serializer();
        return $serializer->loads($data);
    }
    
    public static function get_cookie_signer($salt='bjork.core.signing.get_cookie_signer') {
        $backend = settings::get('SIGNING_BACKEND');
        $signer = new $backend('bjork.http.cookies.'.settings::get('SECRET_KEY'), $salt);
        return $signer;
    }
}

}
