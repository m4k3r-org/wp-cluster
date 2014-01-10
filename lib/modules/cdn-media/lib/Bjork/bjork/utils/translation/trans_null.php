<?php

namespace bjork\utils\translation;

// use bjork\conf\settings;

final class trans_null {
    
    public function activate($language) {}
    public function deactivate() {}
    public function deactivateAll() {}
    
    public function getLanguage() {
        return 'en';
        // return \bjork\conf\settings::get('LANGUAGE_CODE');
    }
    
    public function gettext_noop($msg) {
        return $msg;
    }
    
    public function gettext($msg) {
        return $msg;
    }
    
    public function ugettext($msg) {
        return $msg;
    }
    
    public function pgettext($context, $msg) {
        return $msg;
    }
    
    public function ngettext($singular, $plural, $number) {
        return $number === 1 ? $singular : $plural;
    }
    
    public function ungettext($singular, $plural, $number) {
        return $number === 1 ? $singular : $plural;
    }
    
    public function npgettext($context, $singular, $plural, $number) {
        return $number === 1 ? $singular : $plural;
    }
    
    public function get_language_from_request($request, $check_path=false) {
        return 'en';
        // return settings::get('LANGUAGE_CODE');
    }
    
    public function get_language_from_path($path) {
        return null;
    }
}
