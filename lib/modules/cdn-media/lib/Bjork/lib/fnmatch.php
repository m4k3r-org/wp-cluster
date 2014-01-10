<?php

namespace {

final class fnmatch {
    
    const MAX_CACHE = 100;
    
    private static $cache = array();
    
    public static function match($name, $pat) {
        // @@@todo
        return self::matchcase($name, $pat);
    }
    
    public static function matchcase($name, $pat) {
        if (!isset(self::$cache[$pat])) {
            $res = self::translate($pat);
            if (count(self::$cache) > self::MAX_CACHE)
                self::$cache = array();
            self::$cache[$pat] = $res;
        }
        return (bool)preg_match(self::$cache[$pat], $name);
    }
    
    public static function translate($pat) {
        $i = 0;
        $n = strlen($pat);
        $res = "";
        while ($i < $n) {
            $c = $pat{$i};
            $i++;
            switch ($c) {
                case "*": $res .= ".*"; break;
                case "?": $res .= "."; break;
                case "[":
                    $j = $i;
                    if ($j < $n && $pat{$j} == "!")
                        $j++;
                    if ($j < $n && $pat{$j} == "]")
                        $j++;
                    while ($j < $n && $pat{$j} !== "]")
                        $j++;
                    if ($j >= $n)
                        $res .= '\\[';
                    else {
                        $stuff = str_replace('\\', '\\\\', substr($pat, $i, $j - $i));
                        $i = $j + 1;
                        if ($stuf{0} == "!")
                            $stuff = "^" . substr($stuff, 1);
                        else if ($stuff{0} == "^")
                            $stuff = '\\' . $stuff;
                        $res = sprintf('%s[%s]', $res, $stuff);
                    }
                    break;
                default:
                    $res .= preg_quote($c, "/");
            }
        }
        return "/{$res}\Z(?ms)/u";
    }
}

}
