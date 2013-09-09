<?php
/*
    This module is a collection of useful funtions for working with URLs,
    converted from the ``urlparse`` and ``urllib`` modules of the Python
    standard library.
    
    This module works heavily with strings and can be assumed that is
    significantly slower than other libraries that use regular expressions.
    If speed, however, is not an issue, the fact that this library is based
    on several RFCs (see below) might make it more appropriate in some cases.
*/

/**
* Parse (absolute and relative) URLs.
* 
* This module is based upon the following RFC specifications:
* 
* RFC 3986 (STD66): "Uniform Resource Identifiers" by T.Berners-Lee, R.Fielding
* and L. Masinter, January 2005.
* 
* RFC 2732: "Format for Literal IPv6 Addresses in URL's by R.Hinden,
* B.Carpenter and L.Masinter, December 1999.
* 
* RFC 2396: "Uniform Resource Identifiers (URI)": Generic Syntax by
* T.Berners-Lee, R. Fielding, and L. Masinter, August 1998.
* 
* RFC 2368: "The mailto URL scheme", by P.Hoffman , L Masinter, J.Zwinski,
* July 1998.
* 
* RFC 1808: "Relative Uniform Resource Locators", by R.Fielding, UC Irvine,
* June 1995.
* 
* RFC 1738: "Uniform Resource Locators (URL)" by T.Berners-Lee, L.Masinter,
* M.McCahill, December 1994
* 
* RFC 3986 is considered the current standard and any future changes to
* urlparse module should conform with it. The urlparse module is currently
* not entirely compliant with this RFC due to defacto scenarios for parsing,
* and for backward compatibility purposes, some parsing quirks from older
* RFCs are retained. The testcases in test_urlparse.py provide a good
* indicator of parsing behavior.
*/

namespace urllib {

use strutils as str;

final class props {
    public static
        // A classification of schemes (trailing space means apply by default)
        $nonHierarchical,
        $usesRelative,
        $usesNetloc,
        $usesParams,
        $usesQuery,
        $usesFragment,

        // Characters valid in scheme names
        $schemeChars,
        
        // Always safe characters when quoting URLs
        $alwaysSafeChars;
}

props::$usesRelative = explode(" ",
    "ftp http gopher nntp imap ".
    "wais file https shttp mms ".
    "prospero rtsp rtspu sftp ");
props::$usesNetloc = explode(" ",
    "ftp http gopher nntp telnet ".
    "imap wais file mms https shttp ".
    "snews prospero rtsp rtspu rsync ".
    "svn svn+ssh sftp nfs git git+ssh ");
props::$nonHierarchical = explode(" ",
    "gopher hdl mailto news ".
    "telnet wais imap snews sip sips");
props::$usesParams = explode(" ",
    "ftp hdl prospero http imap ".
    "https shttp rtsp rtspu sip sips ".
    "mms sftp ");
props::$usesQuery = explode(" ",
    "http wais imap https shttp mms ".
    "gopher rtsp rtspu sip sips ");
props::$usesFragment = explode(" ",
    "ftp hdl http gopher news ".
    "nntp wais https shttp snews ".
    "file prospero ");
props::$schemeChars = explode(" ",
    "a b c d e f g h i j k l m n o p q r s t u v w x y z ".
    "A B C D E F G H I J K L M N O P Q R S T U V W X Y Z ".
    "0 1 2 3 4 5 6 7 8 9 ".
    "+ - .");
props::$alwaysSafeChars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ".
                          "abcdefghijklmnopqrstuvwxyz".
                          "0123456789".
                          "_.-";

class URLParseException extends \Exception {}

class InvalidIP6URL extends URLParseException {
    function __toString() {
        return "Invalid IP6 URL: {$this->getMessage()}";
    }
}

/**
* Shared methods for the parsed result objects.
*/
class Result implements \ArrayAccess {
    
    protected static $attrs = array(
        "username",
        "password",
        "hostname",
        "port");
    
    protected $props;
    
    public function toArray() {
        return array_values($this->props);
    }
    
    public function offsetExists($k) {
        $keys = array_merge(array_keys($this->props), self::$attrs);
        if (is_int($k))
            return $k < count($this->props);
        return in_array($k, $keys);
    }
    
    public function offsetGet($k) {
        if (in_array($k, self::$attrs)) {
            $methodname = "_get_{$k}";
            $v = $this->$methodname();
            return $v;
        }
        if (is_int($k)) {
            $keys = array_keys($this->props);
            $k = $keys[$k];
        }
        return $this->props[$k];
    }
    
    public function offsetSet($k, $value) {
        if (is_int($k)) {
            $keys = array_keys($this->props);
            $k = $keys[$k];
        }
        if (!in_array($k, array_keys($this->props)))
            throw new \Exception("".__CLASS__." object has no property '$k'.");
        $this->props[$k] = $value;
    }
    
    public function offsetUnset($k) {
        $this->offsetSet($k, null);
    }
    
    private function _get_username() {
        $netloc = $this["netloc"];
        if (str::in_string($netloc, "@")) {
            $p = str::rsplit($netloc, "@", 1);
            $userinfo = $p[0];
            if (str::in_string($userinfo, ":")) {
                $p = str::split($userinfo, ":", 1);
                $userinfo = $p[0];
            }
            return $userinfo;
        }
        return null;
    }
    
    private function _get_password() {
        $netloc = $this["netloc"];
        if (str::in_string($netloc, "@")) {
            $p = str::rsplit($netloc, "@", 1);
            $userinfo = $p[0];
            if (str::in_string($userinfo, ":")) {
                $p = str::split($userinfo, ":", 1);
                return $p[1];
            }
            return $userinfo;
        }
        return null;
    }
    
    private function _get_hostname() {
        $netlocParts = explode("@", $this["netloc"]);
        $netloc = array_pop($netlocParts);
        if (str::in_string($netloc, "[") && str::in_string($netloc, "]")) {
            $p = explode("]", $netloc);
            return strtolower(substr($p[0], 1));
        } else if (str::in_string($netloc, ":")) {
            $p = explode(":", $netloc);
            return strtolower($p[0]);
        } else if ($netloc === "") {
            return null;
        }
        return strtolower($netloc);
    }
    
    private function _get_port() {
        $netlocParts = explode("@", $this["netloc"]);
        array_pop($netlocParts);
        $netlocParts = explode("]", $this["netloc"]);
        $netloc = array_pop($netlocParts);
        if (str::in_string($netloc, ":")) {
            $p = explode(":", $netloc);
            return (int)$p[1];
        }
        return null;
    }
}

class SplitResult extends Result {
    function __construct($scheme, $netloc, $path, $query, $fragment) {
        $this->props = array(
            "scheme" => $scheme,
            "netloc" => $netloc,
            "path" => $path,
            "query" => $query,
            "fragment" => $fragment,
        );
    }
    
    function getURL() {
        return \urllib::unsplit($this);
    }
}

class ParseResult extends Result {
    function __construct($scheme, $netloc, $path, $params, $query, $fragment) {
        $this->props = array(
            "scheme" => $scheme,
            "netloc" => $netloc,
            "path" => $path,
            "params" => $params,
            "query" => $query,
            "fragment" => $fragment,
        );
    }
    
    function getURL() {
        return \urllib::unparse($this);
    }
}

function splitnetloc($url, $start=0) {
    $delim = strlen($url); // position of end of domain part of url, default is end
    $delimiters = array("/", "?", "#");
    foreach ($delimiters as $c) { // look for delimiters; the order is NOT important
        $wdelim = strpos($url, $c, $start); // find first of this delim
        if (false !== $wdelim)              // if found
            $delim = min($delim, $wdelim);  // use earliest delim position
    }
    return array(                           // return (domain, rest)
        substr($url, $start, $delim-$start),
        substr($url, $delim));
}

function splitparams($url) {
    if (str::in_string($url, "/")) {
        $ii = strrpos($url, "/");
        $i = strpos($url, ";", $ii === false ? -1 : $ii);
        if (false === $i)
            return array($url, "");
    } else {
        $i = strpos($url, ";"); // never false since we're already in this func
    }
    return array(
        substr($url, 0, $i),
        substr($url, $i+1));
}

/*
 * Convert a hex string to ASCII with hexstr()
 * Convert a ASCII string to a hex string with strhex()
 * 
 *
 * Paul Gregg <pgregg@pgregg.com>
 * 3 October 2003
 *
 * Open Source Code:   If you use this code on your site for public
 * access (i.e. on the Internet) then you must attribute the author and
 * source web site: http://www.pgregg.com/projects/php/code/hexstr.phps
 *
 */

function hextochr($hex) { // renamed hexstr to "hextochr"
    $hex = str_replace(' ', '', $hex);
    $hex = str_replace('\x', '', $hex);
    $ret = pack('H*', $hex);
    return $ret;
}

function chrtohex($string) { // renamed strhex to "chrtohex"
    $hex = unpack('H*', $string);
    return array_shift($hex);
}

}

namespace {

use strutils as str;

final class urllib {
    const MAX_CACHE_SIZE = 20;
    
    private static $cache = array();
    
    private static $safe_maps = array();
    
    private static function clear_cache() {
        self::$cache = array();
    }
    
    /**
    * Parse a URL into 5 components:
    * 
    *   <scheme>://<netloc>/<path>?<query>#<fragment>
    * 
    * Return a SplitResult instance.
    * 
    * Note that we don't break the components up in smaller bits (e.g. $netloc
    * is a single string) and we don't expand % escapes.
    */
    public static function split($url, $scheme="", $allowFragments=true) {
        $_url = $url; // cached here for reference in thrown exceptions
        
        $key = sprintf("%s:%s:%s:%s:%s",
            $url,
            $scheme,
            $allowFragments ? "1" : "0",
            gettype($url),
            gettype($scheme)
        );
        $cached = isset(self::$cache[$key])
            ? self::$cache[$key]
            : null;
        if ($cached)
            return $cached;
        if (count(self::$cache) >= self::MAX_CACHE_SIZE)
            self::clear_cache();
        
        $netloc = $query = $fragment = "";
        $i = strpos($url, ":");
        if (false !== $i) {
            if (substr($url, 0, $i) === "http") { // optimize the common case
                $scheme = strtolower(substr($url, 0, $i));
                $url = substr($url, $i+1);
                if (substr($url, 0, 2) === "//") {
                    list($netloc, $url) = urllib\splitnetloc($url, 2);
                    if ((str::in_string($netloc, "[") && !str::in_string($netloc, "]")) ||
                        (str::in_string($netloc, "]") && !str::in_string($netloc, "[")))
                        throw new urllib\InvalidIP6URL($_url);
                }
                if ($allowFragments && str::in_string($url, "#"))
                    list($url, $fragment) = str::split($url, "#", 2);
                if (str::in_string($url, "?"))
                    list($url, $query) = str::split($url, "?", 1);
                $v = new urllib\SplitResult($scheme, $netloc, $url, $query, $fragment);
                self::$cache[$key] = $v;
                return $v;
            }
            if (str::endswith($url, ":") || !str::is_digit($url[$i+1])) {
                $_s = substr($url, 0, $i);
                $_f = false;
                for ($_i = 0; $_i < strlen($_s); $_i++) {
                    $c = $_s[$_i];
                    if (!in_array($c, urllib\props::$schemeChars))
                        $_f = true;
                }
                if (!$_f) {
                    $scheme = strtolower(substr($url, 0, $i));
                    $url = substr($url, $i+1);
                }
            }
        }
        if (substr($url, 0, 2) === "//") {
            list($netloc, $url) = urllib\splitnetloc($url, 2);
            if ((str::in_string($netloc, "[") && !str::in_string($netloc, "]")) ||
                (str::in_string($netloc, "]") && !str::in_string($netloc, "[")))
                throw new urllib\InvalidIP6URL($_url);
        }
        if ($allowFragments && in_array($scheme, urllib\props::$usesFragment) &&
            str::in_string($url, "#"))
            list($url, $fragment) = str::split($url, "#", 1);
        if (in_array($scheme, urllib\props::$usesQuery) && str::in_string($url, "?"))
            list($url, $query) = str::split($url, "?", 1);
        $v = new urllib\SplitResult($scheme, $netloc, $url, $query, $fragment);
        self::$cache[$key] = $v;
        return $v;
    }
    
    /**
    * Combine the elements of a tuple as returned by urlsplit() into a
    * complete URL as a string. The data argument can be any five-item
    * iterable. This may result in a slightly different, but equivalent URL,
    * if the URL that was parsed originally had unnecessary delimiters (for
    * example, a ? with an empty query; the RFC states that these are
    * equivalent).
    */
    public static function unsplit($data) {
        $scheme = $data["scheme"];
        $netloc = $data["netloc"];
        $url = $data["path"];
        $query = $data["query"];
        $fragment = $data["fragment"];
        if (!empty($netloc) || (!empty($scheme) &&
                                in_array($scheme, urllib\props::$usesNetloc) &&
                                substr($url, 0, 2) !== "//")) {
            if (!empty($url) && $url[0] !== "/")
                $url = "/" + $url;
            $url = "//" . (empty($netloc) ? "" : $netloc) . $url;
        }
        if (!empty($scheme))
            $url = "$scheme:$url";
        if (!empty($query))
            $url = "$url?$query";
        if (!empty($fragment))
            $url = "$url#$fragment";
        return $url;
    }
    
    /**
    * Parse a URL into 6 components:
    * 
    *   <scheme>://<netloc>/<path>;<params>?<query>#<fragment>
    * 
    * Return a ParseResult instance.
    * 
    * Note that we don't break the components up in smaller bits (e.g. $netloc
    * is a single string) and we don't expand % escapes.
    */
    public static function parse($url, $scheme="", $allowFragments=true) {
        $result = self::split($url, $scheme, $allowFragments);
        $scheme = $result["scheme"];
        $netloc = $result["netloc"];
        $url = $result["path"];
        $query = $result["query"];
        $fragment = $result["fragment"];
        if (in_array($scheme, urllib\props::$usesParams) && str::in_string($url, ";"))
            list($url, $params) = urllib\splitparams($url);
        else
            $params = "";
        $v = new urllib\ParseResult($scheme, $netloc, $url,
                                    $params, $query, $fragment);
        return $v;
    }
    
    /**
    * Put a parsed URL back together again. This may result in a slightly
    * different, but equivalent URL, if the URL that was parsed originally had
    * redundant delimiters, e.g. a ? with an empty query (the draft states
    * that these are equivalent).
    */
    public static function unparse($data) {
        $scheme = $data["scheme"];
        $netloc = $data["netloc"];
        $url = $data["path"];
        $params = $data["params"];
        $query = $data["query"];
        $fragment = $data["fragment"];
        if (!empty($params))
            $url = "$url;$params";
        $v = new urllib\SplitResult($scheme, $netloc, $url, $query, $fragment);
        return self::unsplit($v);
    }
    
    /**
    * Join a base URL and a possibly relative URL to form an absolute
    * interpretation of the latter.
    */
    public static function join($base, $url, $allowFragments=true) {
        if (empty($base))
            return $url;
        if (empty($url))
            return $base;
        
        $bresult = self::parse($base, '', $allowFragments);
        $bscheme = $bresult["scheme"];
        $bnetloc = $bresult["netloc"];
        $bpath = $bresult["path"];
        $bparams = $bresult["params"];
        $bquery = $bresult["query"];
        $bfragment = $bresult["fragment"];
        
        $result = self::parse($url, $bscheme, $allowFragments);
        $scheme = $result["scheme"];
        $netloc = $result["netloc"];
        $path = $result["path"];
        $params = $result["params"];
        $query = $result["query"];
        $fragment = $result["fragment"];
        
        if ($scheme !== $bscheme || !in_array($scheme, urllib\props::$usesRelative))
            return $url;
        if (in_array($scheme, urllib\props::$usesNetloc)) {
            if (!empty($netloc))
                return self::unparse(new urllib\ParseResult(
                    $scheme, $netloc, $path,
                    $params, $query, $fragment));
            $netloc = $bnetloc;
        }
        if (!empty($path) && $path{0} === "/")
            return self::unparse(new urllib\ParseResult(
                $scheme, $netloc, $path,
                $params, $query, $fragment));
        if (empty($path) && empty($params)) {
            $path = $bpath;
            $params = $bparams;
            if (empty($query))
                $query = $bquery;
            return self::unparse(new urllib\ParseResult(
                $scheme, $netloc, $path,
                $params, $query, $fragment));
        }
        $_s = explode("/", $bpath);
        array_pop($_s);
        $segments = array_merge($_s, explode("/", $path));
        // XXX The stuff below is bogus in various ways...
        if (end($segments) === ".")
            $segments[count($segments)-1] = "";
        $segments = array_values(array_filter($segments, function($v) {
            return $v !== ".";
        })); // we do array_values because array_filter preserves keys and
            // the keys in the array become non-sequential
        while (true) {
            $i = 1;
            $n = count($segments) - 1;
            if ($n <= $i)
                break;
            $_f = false;
            while ($i < $n) {
                if ($i+1 == $n)
                    $_f = true;
                if ($segments[$i] === ".." &&
                    !in_array($segments[$i-1], array("", ".."))) {
                    array_splice($segments, $i-1, 2);
                    break;
                }
                $i++;
            }
            if ($_f)
                break;
        }
        $_c = count($segments);
        if ($segments == array("", ".."))
            $segments[$_c-1] = "";
        else if ($_c >= 2 && end($segments) === "..")
            array_splice($segments, $_c-2, 2, array(""));
        return self::unparse(new urllib\ParseResult(
            $scheme, $netloc, implode("/", $segments),
            $params, $query, $fragment));
    }
    
    /**
    * Removes any existing fragment from URL.
    * 
    * Returns an array with the defragmented URL and the fragment. If the URL
    * contained no fragments, the second element is the empty string.
    */
    public static function defrag($url) {
        if (str::in_string($url, "#")) {
            list($s, $n, $p, $a, $q, $frag) = self::parse($url)->toArray();
            $defrag = self::unparse(new urllib\ParseResult($s, $n, $p, $a, $q, ''));
            return array($defrag, $frag);
        }
        return array($url, "");
    }
    
    /**
    * Parse a query string given as a string argument (data of type 
    * application/x-www-form-urlencoded). Data are returned as a dictionary.
    * The dictionary keys are the unique query variable names and the values
    * are lists of values for each name.
    * 
    * Arguments:
    * 
    *   $qs: Percent-encoded query string to be parsed.
    * 
    *   $keepBlankValues: A flag indicating whether blank values in
    *       percent-encoded queries should be treated as blank strings.
    *       A true value indicates that blanks should be retained as blank
    *       strings. The default false value indicates that blank values are
    *       to be ignored and treated as if they were not included.
    * 
    *   $strict: A flag indicating what to do with parsing errors. If false
    *       (the default), errors are silently ignored. If true, errors throw
    *       UnexpectedValueException.
    */
    public static function parse_qs($qs, $keepBlankValues=false, $strict=false) {
        $dict = array();
        $parts = self::parse_qsl($qs, $keepBlankValues, $strict);
        foreach ($parts as $part) {
            list($name, $value) = $part;
            if (isset($dict[$name]))
                $dict[$name][] = $value;
            else
                $dict[$name] = array($value);
        }
        return $dict;
    }
    
    /**
    * Similar to urllib::parse_qs but differs in that data are returned as
    * a list of name, value pairs.
    */
    public static function parse_qsl($qs, $keepBlankValues=false, $strict=false) {
        $pairs = array();
        foreach (explode("&", $qs) as $pair)
            foreach (explode(";", $pair) as $v)
                $pairs[] = $v;
        $r = array();
        foreach ($pairs as $name_value) {
            if (empty($name_value) && !$strict)
                continue;
            $nv = str::split($name_value, "=", 1);
            if (count($nv) != 2) {
                if ($strict)
                    throw new \UnexpectedValueException(
                        "Bad query field: $name_value");
                // Handle case of a control-name with no equal sign
                if ($keepBlankValues)
                    $nv[] = "";
                else
                    continue;
            }
            if (count($nv[1]) || $keepBlankValues) {
                $name = self::unquote_plus($nv[0]);
                $value = self::unquote_plus($nv[1]);
                $r[] = array(rtrim($name, ']['), $value);
            }
        }
        return $r;
    }
    
    /**
    * quote('abc def') -> 'abc%20def'
    * 
    * Each part of a URL, e.g. the path info, the query, etc., has a
    * different set of reserved characters that must be quoted.
    * 
    * RFC 2396 Uniform Resource Identifiers (URI): Generic Syntax lists
    * the following reserved characters.
    * 
    *   reserved = ";" | "/" | "?" | ":" | "@" | "&" | "=" | "+" | "$" | ","
    * 
    * Each of these characters is reserved in some component of a URL,
    * but not necessarily in all of them.
    * 
    * By default, the quote function is intended for quoting the path
    * section of a URL. Thus, it will not encode '/'. This character
    * is reserved, but in typical usage the quote function is being
    * called on a path where the existing slash characters are used as
    * reserved characters.
    */
    public static function quote($s, $safe="/") {
        if (empty($s)) {
            if (null === $s)
                throw new \UnexpectedValueException('NULL object cannot be quoted');
            return $s;
        }
        if (empty($safe))
            $safe = "";
        $safe .= urllib\props::$alwaysSafeChars;
        $cachekey = $safe;
        if (isset(self::$safe_maps[$cachekey])) {
            $safeMap = self::$safe_maps[$cachekey];
        } else {
            $safeMap = array();
            foreach (range(0, 255) as $i) {
                $c = chr($i);
                if (false !== strpos($safe, $c)) // is it a safe char?
                    $safeMap[$c] = $c;
                else
                    $safeMap[$c] = sprintf("%%%02X", $i);
            }
            self::$safe_maps[$cachekey] = $safeMap;
        }
        $ss = str::explode($s, "");
        $res = array_map(function($c) use ($safeMap)  {
            return $safeMap[$c];
        }, $ss);
        return implode("", $res);
    }
    
    /**
    * Quote the query fragment of a URL; replacing ' ' with '+'.
    */
    public static function quote_plus($s, $safe="") {
        if (false !== strpos($s, " ")) {
            $s = self::quote($s, $safe . " ");
            return str_replace(" ", "+", $s);
        }
        return self::quote($s, $safe);
    }
    
    /**
    * unquote('abc%20def') -> 'abc def'.
    */
    public static function unquote($s) {
        $res = explode("%", $s);
        // fastpath
        if (count($res) == 1)
            return $s;
        foreach (range(1, count($res) - 1) as $i) {
            $item = $res[$i];
            try {
                $res[$i] = urllib\hextochr(substr($item, 0, 2)) . substr($item, 2);
            } catch (\Exception $e) {
                $res[$i] = "%{$item}";
            }
        }
        return implode('', $res);
    }
    
    /**
    * unquote('%7e/abc+def') -> '~/abc def'"""
    */
    public static function unquote_plus($s) {
        return self::unquote(str_replace('+', ' ', $s));
    }
}

}

?>
