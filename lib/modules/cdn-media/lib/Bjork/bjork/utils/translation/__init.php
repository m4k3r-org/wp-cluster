<?php

namespace bjork\utils {
    
require_once __DIR__.'/trans_null.php';

// use bjork\conf\settings,
//     bjork\utils\translation\LanguageOverrideContextManager;

final class translation {
    private static $trans = null;
    
    private static function trans() {
        // if (is_null(self::$trans))
        //     if (settings::get("USE_I18N"))
        //         self::$trans = new \bjork\utils\translation\trans_real();
        //     else
                self::$trans = new \bjork\utils\translation\trans_null();
        return self::$trans;
    }
    
    public static function get_language() {
        return self::trans()->getLanguage();
    }
    
    public static function activate($language) {
        return self::trans()->activate($language);
    }
    
    public static function deactivate() {
        return self::trans()->deactivate();
    }
    
    public static function deactivate_all() {
        return self::trans()->deactivateAll();
    }
    
    public static function gettext($msg) {
        return self::trans()->gettext($msg);
    }
    
    public static function ugettext($msg) {
        return self::trans()->ugettext($msg);
    }
    
    public static function pgettext($context, $msg) {
        return self::trans()->pgettext($context, $msg);
    }
    
    public static function ngettext($singular, $plural, $number) {
        return self::trans()->ngettext($singular, $plural, $number);
    }
    
    public static function ungettext($singular, $plural, $number) {
        return self::trans()->ungettext($singular, $plural, $number);
    }
    
    public static function npgettext($context, $singular, $plural, $number) {
        return self::trans()->npgettext($context, $singular, $plural, $number);
    }
    
    public static function get_language_from_request($request, $check_path=false) {
        return self::trans()->get_language_from_request($request, $check_path);
    }
    
    public static function get_language_from_path($path) {
        return self::trans()->get_language_from_path($path);
    }
    
    // public static function override($language, $deactivate=false) {
    //     return new LanguageOverrideContextManager($language, $deactivate);
    // }
}

}

// namespace bjork\utils\translation {

// use bjork\utils\functional\ContextManager,
//     bjork\utils\translation;

// class LanguageOverrideContextManager implements ContextManager {
//     private $language, $deactivate, $oldLanguage;
    
//     function __construct($language, $deactivate=false) {
//         $this->language = $language;
//         $this->deactivate = $deactivate;
//         $this->oldLanguage = translation::get_language();
//     }
    
//     public function __enter() {
//         if (!is_null($this->language))
//             translation::activate($this->language);
//         else
//             translation::deactivate_all();
//     }
    
//     public function __exit(\Exception $e=null) {
//         if ($this->deactivate)
//             translation::deactivate();
//         else
//             translation::activate($this->oldLanguage);
//     }
// }

// class LookupError extends \Exception {}

// }
