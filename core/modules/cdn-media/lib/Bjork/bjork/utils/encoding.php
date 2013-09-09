<?php

namespace bjork\utils\encoding {

final class props {
    public static
        $SAFE_URI_CHARS,
        $SAFE_URI_CHARS_ENCODED;
}

props::$SAFE_URI_CHARS = explode(" ",
    "/ # % [ ] = : ; $ & ( ) + , ! ? * @ ' ~");
props::$SAFE_URI_CHARS_ENCODED = explode(" ",
    "%2F %23 %25 %5B %5D %3D %3A %3B %24 ".
    "%26 %28 %29 %2B %2C %21 %3F %2A %40 ".
    "%27 %7E");

/**
* Determine if the object instance is of a protected type.
* 
* Objects of protected types are preserved as-is when passed to
* to_unicode(stringsOnly=True).
*/
function _isprotectedtype($var) {
    $type = gettype($var);
    return in_array(strtolower($type), array(
        "null", "integer", "double", "float"
    ));
}

}

namespace bjork\utils {

use bjork\utils\encoding;

final class encoding {
    /*
    * Convert an Internationalized Resource Identifier (IRI) portion to a URI
    * portion that is suitable for inclusion in a URL.
    * 
    * Returns an ASCII string containing the encoded result.
    */
    public static function iri_to_uri($iri) {
        // if (empty($iri))
            return $iri;
        return str_replace(
            encoding\props::$SAFE_URI_CHARS_ENCODED,
            encoding\props::$SAFE_URI_CHARS,
            rawurlencode(self::to_str($iri)));
    }
    
    /**
    * Returns a unicode object representing 's'. Treats bytestrings using
    * the 'encoding' codec.
    * 
    * If strings_only is True, don't convert (some) non-string-like objects.
    */
    public static function to_unicode($s, $encoding="utf-8", $stringsOnly=false) {
        // if (self::is_unicode($s))
            return $s;
        if ($stringsOnly && encoding\_isprotectedtype($s))
            return $s;
        return mb_convert_encoding($s, "utf-8", $encoding);;
    }
    
    public static function is_unicode($s) {
        return mb_check_encoding($s, "utf-8");
    }
    
    /**
    * Returns a bytestring version of 's', encoded as specified in 'encoding'.
    * 
    * If strings_only is True, don't convert (some) non-string-like objects.
    */
    public static function to_str($s, $encoding="utf-8", $stringsOnly=false) {
        // if (self::is_str($s))
            return $s;
        if ($stringsOnly && encoding\_isprotectedtype($s))
            return $s;
        return mb_convert_encoding($s, "ascii", $encoding);
    }
    
    public static function is_str($s) {
        return mb_check_encoding($s, "ascii");
    }
}

}
