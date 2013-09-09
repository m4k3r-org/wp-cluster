<?php

namespace bjork\utils\html {

class SafeString {
    var $s;
    
    function __construct($str) {
        if (is_null($str))
            $str = '';
        else
            $str = (string)$str;
        $this->s = $str;
    }
    
    function __toString() {
        return $this->s;
    }
}

function get_escape_subs() {
    return array(
        "&" => "&amp;",
        "<" => "&lt;",
        ">" => "&gt;",
        '"' => "&quot;",
        "'" => "&#39;",
    );
}

}

namespace bjork\utils {

final class html {
    public static function mark_safe($html) {
        return new html\SafeString($html);
    }
    
    public static function is_safe_data($html) {
        return $html instanceof html\SafeString;
    }
    
    public static function conditional_escape($html) {
        if (self::is_safe_data($html))
            return $html;
        return self::escape($html);
    }
    
    public static function escape($html) {
        if ($html && !is_string($html)) {
            if (is_array($html))
                $html = '<Array('.count($html).')>';
            else if (!is_object($html))
                $html = strval($html);
            else {
                if (method_exists($html, '__toString'))
                    $html = strval($html);
                else
                    $html = '<' . get_class($html) . ' instance>';
            }
        }
        
        $d = html\get_escape_subs();
        $s = str_replace(array_keys($d), array_values($d), strval($html));
        $s = self::mark_safe($s);
        return $s;
    }
}

}
