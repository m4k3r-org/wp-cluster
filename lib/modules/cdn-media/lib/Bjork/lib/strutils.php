<?php

namespace {

final class strutils {
    
    public static function format($string, array $args=null) {
        if (null === $args)
            $args = array();
        $arg_nums = array_slice(array_flip(array_keys(array(0 => 0) + $args)), 1);
        $string = preg_replace_callback('/%\(([\w\-\_]+)\)s/', function($match) use ($arg_nums) {
            return '%' . $arg_nums[$match[1]] . '$s';
        }, $string);
        return vsprintf($string, array_values($args));
    }
    
    public static function in_string($string, $needle) {
        return false !== mb_stripos($string, $needle);
    }
    
    public static function is_digit($string) {
        return ctype_digit($string);
    }
    
    public static function explode($string) {
        $array = preg_split('//u', $string);
        array_shift($array);
        array_pop($array);
        return $array;
    }
    
    /**
    * Return a string which is the concatenation of the strings in the
    * iterable $items. $glue is the separator between elements.
    */
    public static function join($glue, array $items) {
        return implode($glue, array_filter($items, function($o) {
            return !empty($o);
        }));
    }
    
    /**
    * Return True if string starts with the $prefix, otherwise return False.
    * $prefix can also be a tuple of prefixes to look for.
    */
    public static function startswith($string, $prefix) {
        if (is_array($prefix)) {
            foreach ($prefix as $n) {
                if (mb_substr($string, 0, mb_strlen($n)) === $n)
                    return true;
            }
            return false;
        } else {
            return mb_substr($string, 0, mb_strlen($prefix)) === $prefix;
        }
    }
    
    /**
    * Return True if the string ends with the specified $suffix, otherwise
    * return False. $suffix can also be a tuple of suffixes to look for.
    */
    public static function endswith($string, $suffix) {
        if (is_array($suffix)) {
            foreach ($suffix as $n) {
                if (mb_substr($string, -1 * mb_strlen($n)) === $n)
                    return true;
            }
            return false;
        } else {
            return mb_substr($string, -1 * mb_strlen($suffix)) === $suffix;
        }
    }
    
    /**
    * Return a list of the words in the string, using $sep as the delimiter
    * string. If $maxsplit is given, at most $maxsplit splits are done (thus,
    * the list will have at most $maxsplit+1 elements). If $maxsplit is not
    * specified or -1, then there is no limit on the number of splits (all
    * possible splits are made).
    *
    * If $sep is given, consecutive delimiters are not grouped together and
    * are deemed to delimit empty strings (for example, split('1,,2', ',')
    * returns ['1', '', '2']). The sep argument may consist of multiple
    * characters (for example, split('1<>2<>3', '<>') returns ['1', '2', '3']).
    * Splitting an empty string with a specified separator returns [''].
    *
    * If $sep is not specified or is null, a different splitting algorithm
    * is applied: runs of consecutive whitespace are regarded as a single
    * separator, and the result will contain no empty strings at the start
    * or end if the string has leading or trailing whitespace. Consequently,
    * splitting an empty string or a string consisting of just whitespace
    * with a None separator returns [].
    *
    * For example, split(' 1  2   3  ') returns ['1', '2', '3'], and
    * split('  1  2   3  ', null, 1) returns ['1', '2   3  '].
    */
    public static function split($string, $sep=null, $maxsplit=-1) {
        if (empty($sep))
            throw new \Exception('Empty separator');
        $regex = sprintf('/%s/u', str_replace('/', '\/', preg_quote($sep)));
        $ret = preg_split($regex, $string, $maxsplit+1);
        return $ret;
    }
    
    /**
    * Return a list of the words in the string, using $sep as the delimiter
    * string. If $maxsplit is given, at most $maxsplit splits are done, the
    * rightmost ones. If $sep is not specified or null, any whitespace
    * string is a separator. Except for splitting from the right, rsplit()
    * behaves like split() which is described in detail above.
    */
    public static function rsplit($string, $sep=null, $maxsplit=-1) {
        $sep = $sep === null ? ' ' : $sep;
        $parts = self::split($string, $sep);
        $count = count($parts);
        $offset = max(0, $count - $maxsplit);
        $last = array_splice($parts, $offset);
        return array_merge(
            array(implode($sep, $parts)),
            $last
        );
    }
    
    /**
    * Split the string at the first occurrence of sep, and return a
    * 3-tuple containing the part before the separator, the separator
    * itself, and the part after the separator. If the separator is not
    * found, return a 3-tuple containing the string itself, followed by
    * two empty strings.
    */
    public static function partition($string, $sep) {
        $pos = mb_strpos($string, $sep);
        if (false === $pos)
            return array($string, '', '');
        return array(
            mb_substr($string, 0, $pos),
            $sep,
            mb_substr($string, $pos+1)
        );
    }
    /**
    * Split the string at the last occurrence of sep, and return a
    * 3-tuple containing the part before the separator, the separator
    * itself, and the part after the separator. If the separator is not
    * found, return a 3-tuple containing two empty strings, followed by
    * the string itself.
    */
    public static function rpartition($string, $sep) {
        $pos = mb_strrpos($string, $sep);
        if (false === $pos)
            return array('', '', $string);
        return array(
            mb_substr($string, 0, $pos),
            $sep,
            mb_substr($string, $pos+1)
        );
    }
}

}
