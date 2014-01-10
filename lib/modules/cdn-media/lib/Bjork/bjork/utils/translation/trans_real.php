<?php

namespace bjork\utils\translation {

use gettext;
use locale;
use os\path;

use bjork\conf\settings,
    bjork\core\loading;

final class trans_real {
    
    const CONTEXT_SEPARATOR = "\x04";
    
    const LANGUAGE_CODE_PREFIX_RE = '/^\/([\w-]+)(\/|$)/';
    const ACCEPT_LANGUAGE_RE = '/([A-Za-z]{1,8}(?:-[A-Za-z0-9]{1,8})*|\*)(?:\s*;\s*q=(0(?:\.\d{0,3})?|1(?:\.0{0,3})?))?(?:\s*,\s*|$)/A';
    
    private
        // Translations are cached in a dictionary for every language+app tuple.
        $translations = array(),
        $active = null,
        // The default translation is based on the settings file.
        $default = null,
        // This is a cache for normalized accept-header languages to prevent multiple
        // file lookups when checking the same locale on repeated requests.
        $accepted = array();
    
    /**
    * Turns a language name (en-us) into a locale name (en_US). If 'to_lower'
    * is True, the last component is lower-cased (en_us).
    */
    public static function to_locale($language, $to_lower=false) {
        $p = strpos($language, "-");
        if (false !== $p) {
            $l1 = substr($language, 0, $p);
            $l2 = substr($language, $p + 1);
            if ($to_lower)
                return strtolower($l1) . "_" . strtolower($l2);
            // Get correct locale for sr-latn
            if (strlen($l2) > 2)
                return strtolower($l1) . "_" . strtoupper(substr($l2, 0, 1)) .
                    strtolower(substr($l2, 1));
            return strtolower($l1) . "_" . strtoupper($l2);
        }
        return strtolower($language);
    }
    
    /**
    * Turns a locale name (en_US) into a language name (en-us).
    */
    public static function to_language($locale) {
        $p = strpos($locale, "_");
        if (false !== $p) {
            $l1 = substr($locale, 0, $p);
            $l2 = substr($locale, $p + 1);
            return strtolower($l1) . "-" . strtolower($l2);
        }
        return strtolower($locale);
    }
    
    /**
    * Returns a translation object.
    * 
    * This translation object will be constructed out of multiple
    * GNUTranslations objects by merging their catalogs. It will construct a
    * object for the requested language and add a fallback to the default
    * language, if it's different from the requested language.
    */
    public function translation($language) {
        if (isset($this->translations[$language]))
            return $this->translations[$language];
        $defaultTranslation = $this->fetchTranslation(settings::get("LANGUAGE_CODE"));
        $currentTranslation = $this->fetchTranslation($language, $defaultTranslation);
        return $currentTranslation;
    }
    
    /**
    * Fetches the translation object for a given tuple of application name and
    * language and installs it as the current translation object for the
    * current thread.
    */
    public function activate($language) {
        $this->active = $this->translation($language);
    }
    
    /**
    * Deinstalls the currently active translation object so that further
    * _ calls will resolve against the default translation object, again.
    */
    public function deactivate() {
        $this->active = null;
    }
    
    /**
    * Makes the active translation object a NullTranslations() instance. This
    * is useful when we want delayed translations to appear as the original
    * string for some reason.
    */
    public function deactivateAll() {
        $this->active = new gettext\NullTranslations();
    }
    
    public function getLanguage() {
        $t = $this->active;
        if (!is_null($t))
            return $t->getToLanguage();
        // If we don't have a real translation object, assume it's the default language.
        return settings::get("LANGUAGE_CODE");
    }
    
    public function getCatalog() {
        if (!is_null($this->active))
            return $this->active;
        if (is_null($this->default))
            $this->default = $this->translation(settings::get("LANGUAGE_CODE"));
        return $this->default;
    }
    
    public function gettext_noop($msg) {
        return $msg;
    }
    
    public function gettext($msg) {
        return $this->doTranslate($msg, "gettext");
    }
    
    public function ugettext($msg) {
        return $this->doTranslate($msg, "ugettext");
    }
    
    public function pgettext($context, $msg) {
        $result = $this->doTranslate(
            sprintf("%s%s%s", $context, self::CONTEXT_SEPARATOR, $msg),
            "ugettext");
        if (mb_strpos($result, self::CONTEXT_SEPARATOR, null, "utf-8"))
            // translation not found
            $result = $msg;
        return $result;
    }
    
    public function ngettext($singular, $plural, $number) {
        return $this->doNTranslate($singular, $plural, $number, "ngettext");
    }
    
    public function ungettext($singular, $plural, $number) {
        return $this->doNTranslate($singular, $plural, $number, "ungettext");
    }
    
    public function npgettext($context, $singular, $plural, $number) {
        $result = $this->doNTranslate(
            sprintf("%s%s%s", $context, self::CONTEXT_SEPARATOR, $singular),
            sprintf("%s%s%s", $context, self::CONTEXT_SEPARATOR, $plural),
            $number, "ungettext");
        if (mb_strpos($result, self::CONTEXT_SEPARATOR, null, "utf-8"))
            // translation not found
            $result = $this->doNTranslate($singular, $plural, $number, "ungettext");;
        return $result;
    }
    
    /**
    * Returns the language-code if there is a valid language-code
    * found in the `path`.
    */
    public function get_language_from_path($path, $supported=null) {
        if (is_null($supported))
            $supported = settings::get('LANGUAGES');
        $matches = null;
        $matched = preg_match(self::LANGUAGE_CODE_PREFIX_RE, $path, $matches);
        if ($matched) {
            $lang_code = $matches[1];
            if (isset($supported[$lang_code]) && $this->checkForLanguage($lang_code))
                return $lang_code;
        }
        return null;
    }
    
    /**
    * Analyzes the request to find what language the user wants the system to
    * show. Only languages listed in settings.LANGUAGES are taken into account.
    * If the user requests a sublanguage where we have a main language, we send
    * out the main language.
    *
    * If check_path is True, the URL path prefix will be checked for a
    * language code, otherwise this is skipped for backwards compatibility.
    */
    public function get_language_from_request($request, $check_path=false) {
        $supported = settings::get('LANGUAGES');
        
        if ($check_path) {
            $lang_code = $this->get_language_from_path($request->getPathInfo(), $supported);
            if (null !== $lang_code)
                return $lang_code;
        }
        
        if ($request->hasKey('session')) {
            $lang_code = $request['session']->get('bjork_language', null);
            if (null !== $lang_code && isset($supported[$lang_code]) && $this->checkForLanguage($lang_code))
                return $lang_code;
        }
        
        $lang_code = $request->COOKIES->get(settings::get('LANGUAGE_COOKIE_NAME'));
        
        try {
            return $this->getSupportedLanguageVariant($lang_code, $supported);
        } catch (LookupError $e) {
            // pass
        }
        
        $accept = $request->META->get('HTTP_ACCEPT_LANGUAGE', '');
        $parsed_accept = self::parse_accept_lang_header($accept);
        foreach ($parsed_accept as $accept_lang => $q) {
            if ($accept_lang == '*')
                break;
            // 'normalized' is the root name of the locale in POSIX format
            // (which is the format used for the directories holding the MO
            // files).
            $normalized = locale::locale_alias(self::to_locale($accept_lang, true));
            if (!$normalized)
                continue;
            // Remove the default encoding from locale_alias.
            $parts = explode('.', $normalized);
            $normalized = array_shift($parts);
            
            if (isset($this->accepted[$normalized]))
                // We've seen this locale before and have an MO file for it,
                // so no need to check again.
                return $this->accepted[$normalized];
            
            $pairs = array();
            $pairs[] = array($accept_lang, $normalized);
            $pairs[] = array(self::base_lang($accept_lang, '-'), self::base_lang($normalized, '_'));
            foreach ($pairs as $pair) {
                list($lang, $dirname) = $pair;
                if (!isset($supported[strtolower($lang)]))
                    continue;
                foreach ($this->getAllLocalePaths() as $path) {
                    $p = path::join($path, $dirname, 'LC_MESSAGES', 'bjork.mo');
                    if (is_file($p)) {
                        $this->accepted[$normalized] = $lang;
                        return $lang;
                    }
                }
            }
        }
        
        try {
            return $this->getSupportedLanguageVariant(settings::get('LANGUAGE_CODE'), $supported);
        } catch (LookupError $e) {
            return settings::get('LANGUAGE_CODE');
        }
    }
    
    //------------------------------------------------------------------------
    
    /**
    * Checks whether there is a global language file for the given language
    * code. This is used to decide whether a user-provided language is
    * available. This is only used for language codes from either the cookies
    * or session and during format localization.
    */
    function checkForLanguage($lang_code) {
        foreach ($this->getAllLocalePaths() as $path) {
            if (!is_null(gettext::find('bjork', $path, array(self::to_locale($lang_code)))))
                return true;
        }
        return false;
    }
    
    /**
    * Returns the language-code that's listed in supported languages, possibly
    * selecting a more generic variant. Raises LookupError if nothing found.
    */
    function getSupportedLanguageVariant($lang_code, $supported=null) {
        if (is_null($supported))
            $supported = settings::get('LANGUAGES');
        if ($lang_code && !isset($supported[$lang_code])) {
            $p = explode('-', $lang_code);
            $lang_code = $p[0]; // e.g. if fr-ca is not supported fallback to fr
        }
        if ($lang_code && isset($supported[$lang_code]) && $this->checkForLanguage($lang_code))
            return $lang_code;
        throw new LookupError($lang_code);
    }
    
    /**
    * Returns a list of paths to user-provided languages files.
    */
    function getAllLocalePaths() {
        $locale_paths = settings::get('LOCALE_PATHS', array());
        $globalpath = BJORK_ROOT . DIRECTORY_SEPARATOR . dirname(settings\DEFAULTS_MODULE_PATH) . DIRECTORY_SEPARATOR . "locale";
        return array_merge(array($globalpath), $locale_paths);
    }
    
    function fetchTranslation($lang, $fallback=null) {
        if (isset($this->translations[$lang]))
            return $this->translations[$lang];
        
        $globalpath = BJORK_ROOT . DIRECTORY_SEPARATOR . dirname(settings\DEFAULTS_MODULE_PATH) . DIRECTORY_SEPARATOR . "locale";
        $projectpath = null;
        if (settings::is_configured())
            $projectpath = dirname(settings::$settings->SETTINGS_MODULE) . DIRECTORY_SEPARATOR . "locale";
        $loc = self::to_locale($lang);
        $res = $this->getTranslation($lang, $loc, $globalpath);
        
        // We want to ensure that, for example, "en-gb" and "en-us" don't share
        // the same translation object (thus, merging en-us with a local update
        // doesn't affect en-gb), even though they will both use the core "en"
        // translation. So we have to subvert Python's internal gettext caching.
        // if (in_array(_base_lang($lang), array_map('\bjork\utils\translation\_base_lang', $translations))) {
            // @@@TODO: we need to clone the catalogue here. Any clues?
        // }
        
        $apps = loading::get_apps();
        foreach ($apps as $app) {
            $apppath = $app->getFullPath() . DIRECTORY_SEPARATOR . "locale";
            if (is_dir($apppath))
                $res = $this->mergeTranslation($res, $lang, $loc, $apppath);
        }
        
        $localepaths = array_map("realpath", settings::get("LOCALE_PATHS"));
        if (!empty($projectpath) && is_dir($projectpath) &&
                !in_array(realpath($projectpath), $localepaths))
            $res = $this->mergeTranslation($res, $lang, $loc, $projectpath);
        foreach ($localepaths as $localepath)
            if (is_dir($localepath))
                $res = $this->mergeTranslation($res, $lang, $loc, $localepath);
        
        if (is_null($res))
            if (!is_null($fallback))
                $res = $fallback;
            else
                return new gettext\NullTranslations();
        $this->translations[$lang] = $res;
        return $res;
    }
    
    function getTranslation($lang, $loc, $path) {
        try {
            $t = gettext::translation("bjork", $path, array($loc),
                '\bjork\utils\translation\trans_real\BjorkTranslation');
        } catch (gettext\IOError $e) {
            return null;
        }
        $t->setLanguage($lang);
        return $t;
    }
    
    function mergeTranslation($res, $lang, $loc, $path) {
        $t = $this->getTranslation($lang, $loc, $path);
        if (!is_null($t))
            if (is_null($res))
                return $t;
            else
                $res->merge($t);
        return $res;
    }
    
    function doTranslate($message, $translation_function) {
        $eol_message = str_replace("\r", "\n", str_replace("\r\n", "\n", $message));
        $t = $this->getCatalog();
        $result = $t->{$translation_function}($eol_message);
        // mark_safe($result);
        return $result;
    }
    
    function doNTranslate($singular, $plural, $number, $translation_function) {
        $t = $this->getCatalog();
        $result = $t->{$translation_function}($singular, $plural, $number);
        // mark_safe($result);
        return $result;
    }
    
    static function base_lang($l, $sep) {
        $parts = preg_split("/{$sep}/", $l, 2);
        return $parts[0];
    }
    
    /**
    * Parses the lang_string, which is the body of an HTTP Accept-Language
    * header, and returns a list of (lang, q-value), ordered by 'q' values.
    * 
    * Any format errors in lang_string results in an empty list being returned.
    */
    static function parse_accept_lang_header($lang_string) {
        $pieces = null;
        preg_match_all(self::ACCEPT_LANGUAGE_RE, $lang_string, $pieces, \PREG_SET_ORDER);
        
        $result = array();
        foreach ($pieces as $chunk) {
            if (count($chunk) == 2)
                $chunk[] = 1.0;
            list($_, $lang, $priority) = $chunk;
            $result[$lang] = $priority ? (floatval($priority) ?: 1.0) : 1.0;
        }
        
        arsort($result, \SORT_NUMERIC);
        return $result;
    }
}

}

namespace bjork\utils\translation\trans_real {

use gettext;

/**
* This class sets up the GNUTranslations context with regard to output charset.
*/
class BjorkTranslation extends gettext\GNUTranslations {
    private $language, $toLanguage, $catalogues;
    
    function __construct($mofilename) {
        parent::__construct($mofilename);
        $this->setOutputCharset("utf-8");
        $this->language = "??";
        $this->toLanguage = null;
        $this->catalogues = array(clone $this->catalogue);
    }
    
    function __toString() {
        return "<BjorkTranslation lang: {$this->language}>";
    }
    
    function merge(BjorkTranslation $other) {
        $this->catalogues = array_merge($other->catalogues, $this->catalogues);
    }
    
    public function getLanguage() {
        return $this->language;
    }
    
    public function setLanguage($lang) {
        $this->language = $lang;
        $this->toLanguage = \bjork\utils\translation\trans_real::to_language($lang);
    }
    
    public function getToLanguage() {
        return $this->toLanguage;
    }
    
    protected function lookup($msg, $default=null) {
        foreach ($this->catalogues as $catalogue) {
            // we make use of private api -- cache_translations
            if (isset($catalogue->cache_translations[$msg])) {
                $this->catalogue = $catalogue; // stick the catalogue so that fetching will work
                return $catalogue->cache_translations[$msg];
            }
        }
        return $default;
    }
}

}
