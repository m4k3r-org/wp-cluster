<?php

namespace gettext {

/*
    TODO:
    
        - Remove php-gettext dependancy
        - Provide lazy .mo-file loading (php-gettext does that already -- see
          if we can extract it)
*/

require_once 'php-gettext/streams.php';
require_once 'php-gettext/gettext.php';

class IOError extends \Exception {}

/**
* Translation classes are what actually implement the translation of original
* source file message strings to translated message strings. The base class
* used by all translation classes is NullTranslations; this provides the basic
* interface you can use to write your own specialized translation classes.
*/
class NullTranslations {
    protected
        $info,
        $charset,
        $outputCharset,
        $fallback;
    
    function __construct($filename=null) {
        $this->info = array();
        $this->charset = null;
        $this->outputCharset = null;
        $this->fallback = null;
        
        if (!is_null($filename))
            $this->parse($filename);
    }
    
    protected function parse($filename) {}
    
    protected function encode($string, $charset=null) {
        if (is_null($charset))
            $charset = mb_internal_encoding();
        $sourceCharset = mb_detect_encoding($string);
        if (strtolower($sourceCharset) != strtolower($charset))
            return mb_convert_encoding($string, $charset, $sourceCharset);
        return $string;
    }
    
    public function addFallback($fallback) {
        if (!is_null($this->fallback))
            $this->fallback->addFallback($fallback);
        else
            $this->fallback = $fallback;
    }
    
    public function gettext($msg) {
        if (!is_null($this->fallback))
            return $this->fallback->gettext($msg);
        return $msg;
    }
    
    public function ngettext($msg1, $msg2, $n) {
        if (!is_null($this->fallback))
            return $this->fallback->ngettext($msg1, $msg2, $n);
        if ($n == 1)
            return $msg1;
        return $msg2;
    }
    
    public function lgettext($msg) {
        if (!is_null($this->fallback))
            return $this->fallback->lgettext($msg);
        return $msg;
    }
    
    public function lngettext($msg1, $msg2, $n) {
        if (!is_null($this->fallback))
            return $this->fallback->lngettext($msg1, $msg2, $n);
        if ($n == 1)
            return $msg1;
        return $msg2;
    }
    
    public function ugettext($msg) {
        if (!is_null($this->fallback))
            return $this->fallback->ugettext($msg);
        // return $this->encode($msg, "utf-8");
        return $msg;
    }
    
    public function ungettext($msg1, $msg2, $n) {
        if (!is_null($this->fallback))
            return $this->fallback->ungettext($msg1, $msg2, $n);
        if ($n == 1)
            // return $this->encode($msg1, "utf-8");
            return $msg1;
        // return $this->encode($msg2, "utf-8");
        return $msg2;
    }
    
    public function getInfo() {
        return $this->info;
    }
    
    public function getCharset() {
        return $this->charset;
    }
    
    public function getOutputCharset() {
        return $this->outputCharset;
    }
    
    public function setOutputCharset($charset) {
        $this->outputCharset = $charset;
    }
    
    public function install($names=null) {
        throw new \Exception("Not implemented");
    }
}

/**
* The gettext module provides one additional class derived from
* NullTranslations: GNUTranslations. This class overrides parse() to enable
* reading GNU gettext format .mo files in both big-endian and little-endian
* format. It also coerces both message ids and message strings to Unicode.
* 
* GNUTranslations parses optional meta-data out of the translation catalog.
* It is convention with GNU gettext to include meta-data as the translation
* for the empty string. This meta-data is in RFC 822-style key: value pairs,
* and should contain the Project-Id-Version key. If the key Content-Type is
* found, then the charset property is used to initialize the “protected”
* charset instance variable, defaulting to None if not found. If the charset
* encoding is specified, then all message ids and message strings read from
* the catalog are converted to Unicode using this encoding. The ugettext()
* method always returns a Unicode, while the gettext() returns an encoded
* 8-bit string. For the message id arguments of both methods, either Unicode
* strings or 8-bit strings containing only US-ASCII characters are acceptable.
* Note that the Unicode version of the methods (i.e. ugettext() and
* ungettext()) are the recommended interface to use for internationalized
* Python programs.
* 
* The entire set of key/value pairs are placed into a dictionary and set as
* the “protected” info instance variable.
* 
* If the .mo file’s magic number is invalid, or if other problems occur while
* reading the file, instantiating a GNUTranslations class can raise IOError.
*/
class GNUTranslations extends NullTranslations {
    /*protected*/ var $catalogue; // declared public to be able to test
    
    protected function parse($mofilename) {
        $stream = new \FileReader($mofilename);
        $reader = new \gettext_reader($stream, true); // always cache -- we use undocumented api
        
        // we're making use of private api -- load_tables and cache_translations
        $reader->load_tables();
        $info = $reader->cache_translations['']; // DANGER: this might be break
        $info = explode("\n", $info);
        $lastk = $k = null;
        foreach ($info as $line) {
            if (empty($line))
                continue;
            if (false !== strpos($line, ":")) {
                list($k, $v) = preg_split("/:/u", $line, 2, PREG_SPLIT_NO_EMPTY);
                $k = strtolower($k);
                $k = trim($k);
                $v = trim($v);
                $this->info[$k] = $v;
                $lastk = $k;
            } else if (!empty($lastk)) {
                $this->info[$lastk] .= "\n" . $line;
            }
            if ($k == "content-type") {
                $c = explode("charset=", $v);
                $this->charset = $c[1];
            }
        }
        
        if (empty($this->charset))
            $this->charset = "ascii";
        
        $this->catalogue = $reader;
    }
    
    protected function lookup($msg, $default=null) {
        // we make use of private api -- cache_translations
        if (isset($this->catalogue->cache_translations[$msg]))
            return $this->catalogue->cache_translations[$msg];
        return $default;
    }
    
    public function gettext($msg) {
        $tmsg = $this->lookup($msg, null);
        if (is_null($tmsg)) {
            if (!is_null($this->fallback))
                return $this->fallback->gettext($msg);
            return $msg;
        }
        // Encode the Unicode tmsg back to an 8-bit string, if possible
        if (!is_null($this->outputCharset))
            return $this->encode($tmsg, $this->outputCharset);
        else if (!is_null($this->charset))
            return $this->encode($tmsg, $this->charset);
        return $tmsg;
    }
    
    public function ngettext($msg1, $msg2, $n) {
        $key = $msg1 . chr(0) . $msg2; 
        $tmsg = $this->lookup($key, null);
        if (is_null($tmsg)) {
            if (!is_null($this->fallback))
                return $this->fallback->ngettext($msg1, $msg2, $n);
            if ($n == 1)
                return $msg1;
            return $msg2;
        }
        $tmsg = $this->catalogue->ngettext($msg1, $msg2, $n);
        if (!is_null($this->outputCharset))
            return $this->encode($tmsg, $this->outputCharset);
        else if (!is_null($this->charset))
            return $this->encode($tmsg, $this->charset);
        return $tmsg;
    }
    
    public function lgettext($msg) {
        $tmsg = $this->lookup($msg, null);
        if (is_null($tmsg)) {
            if (!is_null($this->fallback))
                return $this->fallback->lgettext($msg);
            return $msg;
        }
        // uses internal encoding if outputCharset is null
        return $this->encode($tmsg, $this->outputCharset);
    }
    
    public function lngettext($msg1, $msg2, $n) {
        $key = $msg1 . chr(0) . $msg2; 
        $tmsg = $this->lookup($key, null);
        if (is_null($tmsg)) {
            if (!is_null($this->fallback))
                return $this->fallback->lngettext($msg1, $msg2, $n);
            if ($n == 1)
                return $msg1;
            return $msg2;
        }
        return $this->encode($this->catalogue->ngettext($msg1, $msg2, $n), $this->outputCharset);
    }
    
    public function ugettext($msg) {
        $tmsg = $this->lookup($msg, null);
        if (is_null($tmsg)) {
            if (!is_null($this->fallback))
                return $this->fallback->ugettext($msg);
            //return $this->encode($msg, "utf-8");
            return $msg;
        }
        return $tmsg;
    }
    
    public function ungettext($msg1, $msg2, $n) {
        $key = $msg1 . chr(0) . $msg2; 
        $tmsg = $this->lookup($key, null);
        if (is_null($tmsg)) {
            if (!is_null($this->fallback))
                return $this->fallback->ungettext($msg1, $msg2, $n);
            if ($n == 1)
                // return $this->encode($msg1, "utf-8");
                return $msg1;
            // return $this->encode($msg2, "utf-8");
            return $msg2;
        }
        return $this->catalogue->ngettext($msg1, $msg2, $n);
    }
}

}

namespace {

final class gettext {
    
    private static
        $default_localedir = '/usr/local/share/locale',
        $translations = array(),
        $localedirs = array(),
        $localecodesets = array(),
        $current_domain = "messages";
    
    /**
    * This function implements the standard .mo file search algorithm. It
    * takes a domain, identical to what textdomain() takes. Optional localedir
    * is as in bindtextdomain(). Optional languages is a list of strings,
    * where each string is a language code.
    * 
    * If localedir is not given, then the default system locale directory is
    * used. If languages is not given, then the following environment
    * variables are searched: LANGUAGE, LC_ALL, LC_MESSAGES, and LANG. The
    * first one returning a non-empty value is used for the languages
    * variable. The environment variables should contain a colon separated
    * list of languages, which will be split on the colon to produce the
    * expected list of language code strings.
    * 
    * find() then expands and normalizes the languages, and then iterates
    * through them, searching for an existing file built of these components:
    * 
    *   localedir/language/LC_MESSAGES/domain.mo
    * 
    * The first such file name that exists is returned by find(). If no such
    * file is found, then None is returned. If all is given, it returns a list
    * of all file names, in the order in which they appear in the languages
    * list or the environment variables.
    */
    public static function find($domain, $localedir=null, $languages=null, $all=false) {
        // Get some reasonable defaults for arguments that were not supplied
        if (is_null($localedir))
            $localedir = self::$default_localedir;
        if (is_null($languages)) {
            $languages = array();
            foreach (array('LANGUAGE', 'LC_ALL', 'LC_MESSAGES', 'LANG') as $envar) {
                if (isset($_ENV[$envar])) {
                    $val = $_ENV[$envar];
                    if (!empty($val)) {
                        $languages = explode(":", $val);
                        break;
                    }
                }
            }
            if (!in_array("C", $languages))
                $languages[] = "C";
        }
        
        // now normalize and expand the languages
        $nelangs = array();
        foreach ($languages as $lang)
            foreach (self::expand_lang($lang) as $nelang)
                if (!in_array($nelang, $nelangs))
                    $nelangs[] = $nelang;
        
        // select a language
        if ($all) {
            $result = array();
        } else {
            $result = null;
        }
        foreach ($nelangs as $lang) {
            if ($lang == "C")
                break;
            $mofile = implode(DIRECTORY_SEPARATOR, array(
                $localedir,
                $lang,
                "LC_MESSAGES",
                "$domain.mo",
            ));
            if (is_file($mofile))
                if ($all)
                    $result[] = $mofile;
                else
                    return $mofile;
        }
        return $result;
    }
    
    /**
    * Return a Translations instance based on the domain, localedir, and
    * languages, which are first passed to find() to get a list of the
    * associated .mo file paths. Instances with identical .mo file names
    * are cached. The actual class instantiated is either class_ if provided,
    * otherwise GNUTranslations. The class’s constructor must take a single
    * file object argument. If provided, codeset will change the charset used
    * to encode translated strings.
    * 
    * If multiple files are found, later files are used as fallbacks for
    * earlier ones. To allow setting the fallback, copy.copy() is used to
    * clone each translation object from the cache; the actual instance data
    * is still shared with the cache.
    * 
    * If no .mo file is found, this function raises IOError if fallback is
    * false (which is the default), and returns a NullTranslations instance
    * if fallback is true.
    */
    public static function translation($domain, $localedir=null, $languages=null, $class=null, $fallback=false, $codeset=null) {
        if (is_null($class))
            $class = '\gettext\GNUTranslations';
        $mofiles = self::find($domain, $localedir, $languages, true); // find all
        if (empty($mofiles)) {
            if ($fallback)
                return new NullTranslations();
            throw new \gettext\IOError(
                "No translation file found for domain '$domain'");
        }
        
        $result = null;
        foreach ($mofiles as $mofile) {
            $key = $class . chr(0) . realpath($mofile);
            $t = null;
            if (isset(self::$translations[$key]))
                $t = self::$translations[$key];
            if (is_null($t)) {
                $t = new $class($mofile);
                self::$translations[$key] = $t;
            }
            $t = clone $t; // shallow copy
            if (!is_null($codeset))
                $t->setOutputCharset($codeset);
            if (is_null($result))
                $result = $t;
            else
                $result->addFallback($t);
        }
        return $result;
    }
    
    /**
    * This installs the function _() in Python’s builtins namespace, based
    * on domain, localedir, and codeset which are passed to the function
    * translation(). The unicode flag is passed to the resulting translation
    * object’s install() method.
    * 
    * For the names parameter, please see the description of the translation
    * object’s install() method.
    * 
    * As seen below, you usually mark the strings in your application that
    * are candidates for translation, by wrapping them in a call to the
    * _() function, like this:
    * 
    *   print _('This string will be translated.')
    * 
    * For convenience, you want the _() function to be installed in Python’s
    * builtins namespace, so it is easily accessible in all modules of your
    * application.
    */
    public static function install($domain, $localedir=null, $codeset=null, $names=null) {
        $t = self::translation($domain, $localedir, null, null, false, $codeset);
        $t->install($names);
    }
    
    public static function textdomain($domain=null) {
        if (!is_null($domain))
            self::$current_domain = $domain;
        return self::$current_domain;
    }
    
    public static function bindtextdomain($domain, $localedir=null) {
        if (!is_null($localedir))
            self::$localedirs[$domain] = $localedir;
        if (isset(self::$localedirs[$domain]))
            return self::$localedirs[$domain];
        return self::$default_localedir;
    }
    
    public static function bind_textdomain_codeset($domain, $codeset=null) {
        if (!is_null($codeset))
            self::$localecodesets[$domain] = $codeset;
        if (isset(self::$localecodesets[$domain]))
            return self::$localecodesets[$domain];
        throw new \OutOfBoundsException($domain);
    }
    
    public static function dgettext($domain, $msg) {
        $localedir = self::get_localedir_or_null($domain);
        $codeset = self::get_codeset_or_null($domain);
        try {
            $t = self::translation($domain, $localedir, null, null, false, $codeset);
        } catch (\gettext\IOError $e) {
            return $msg;
        }
        return $t->gettext($msg);
    }
    
    public static function ldgettext($domain, $msg) {
        $localedir = self::get_localedir_or_null($domain);
        $codeset = self::get_codeset_or_null($domain);
        try {
            $t = self::translation($domain, $localedir, null, null, false, $codeset);
        } catch (\gettext\IOError $e) {
            return $msg;
        }
        return $t->lgettext($msg);
    }
    
    public static function dngettext($domain, $msg1, $msg2, $n) {
        $localedir = self::get_localedir_or_null($domain);
        $codeset = self::get_codeset_or_null($domain);
        try {
            $t = self::translation($domain, $localedir, null, null, false, $codeset);
        } catch (\gettext\IOError $e) {
            if ($n == 1)
                return $msg1;
            return $msg2;
        }
        return $t->ngettext($msg1, $msg2, $n);
    }
    
    public static function ldngettext($domain, $msg1, $msg2, $n) {
        $localedir = self::get_localedir_or_null($domain);
        $codeset = self::get_codeset_or_null($domain);
        try {
            $t = self::translation($domain, $localedir, null, null, false, $codeset);
        } catch (\gettext\IOError $e) {
            if ($n == 1)
                return $msg1;
            return $msg2;
        }
        return $t->lngettext($msg1, $msg2, $n);
    }
    
    /*
        cannot declare these shortcuts because php thinks gettext() is 
        a constructor (even though it is declared static). oh well.
    
    public static function gettext($msg) {
        return self::dgettext(self::$current_domain, $msg);
    }
    
    public static function lgettext($msg) {
        return self::ldgettext(self::$current_domain, $msg);
    }
    
    public static function ngettext($msg1, $msg2, $n) {
        return self::dngettext(self::$current_domain, $msg1, $msg2, $n);
    }
    
    public static function lngettext($msg1, $msg2, $n) {
        return self::ldngettext(self::$current_domain, $msg1, $msg2, $n);
    }
    */
    
    private static function get_localedir_or_null($domain) {
        if (isset(self::$localedirs[$domain]))
            return self::$localedirs[$domain];
        return null;
    }
    
    private static function get_codeset_or_null($domain) {
        if (isset(self::$localecodesets[$domain]))
            return self::$localecodesets[$domain];
        return null;
    }
    
    private static function expand_lang($loc) {
        $COMPONENT_CODESET   = 1 << 0;
        $COMPONENT_TERRITORY = 1 << 1;
        $COMPONENT_MODIFIER  = 1 << 2;
        
        // split up the locale into its base components
        $mask = 0;
        $pos = strpos($loc, "@");
        if (false !== $pos) {
            $modifier = substr($loc, $pos);
            $loc = substr($loc, 0, $pos);
            $mask |= $COMPONENT_MODIFIER;
        } else {
            $modifier = "";
        }
        $pos = strpos($loc, ".");
        if (false !== $pos) {
            $codeset = substr($loc, $pos);
            $loc = substr($loc, 0, $pos);
            $mask |= $COMPONENT_CODESET;
        } else {
            $codeset = "";
        }
        $pos = strpos($loc, "_");
        if (false !== $pos) {
            $territory = substr($loc, $pos);
            $loc = substr($loc, 0, $pos);
            $mask |= $COMPONENT_TERRITORY;
        } else {
            $territory = "";
        }
        
        $language = $loc;
        $ret = array();
        for ($i = 0; $i < $mask+1; $i++) { 
            if (!($i & ~$mask)) { // if all components for this combo exist ...
                $val = $language;
                if ($i & $COMPONENT_TERRITORY) $val .= $territory;
                if ($i & $COMPONENT_CODESET)   $val .= $codeset;
                if ($i & $COMPONENT_MODIFIER)  $val .= $modifier;
                $ret[] = $val;
            }
        }
        
        return array_reverse($ret);
    }
}

}
