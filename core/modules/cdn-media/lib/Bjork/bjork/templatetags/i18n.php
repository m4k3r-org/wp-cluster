<?php

use bjork\template\Library;

use bjork\conf\settings,
    bjork\utils\translation;

$library = new Library();

$library->tag('trans', function($string) {
    return translation::gettext($string);
});

$library->tag('ntrans', function($singular, $plural, $count) {
    return translation::ngettext($singular, $plural, $count);
});

$library->tag('ptrans', function($string, $context=null) {
    return translation::pgettext($context, $string);
});

$library->tag('nptrans', function($singular, $plural, $count, $context=null) {
    return translation::npgettext($context, $singular, $plural, $count);
});

$library->tag('ftrans', function(/* $string, $arg1, ..., $argN */) {
    $args = func_get_args();
    $string = array_shift($args);
    return vsprintf(translation::gettext($string), $args);
});

$library->tag('get_current_language', function() {
    return translation::get_language();
});

$library->tag('get_available_languages', function() {
    return array_map(function($lang) {
        return translation::gettext($lang);
    }, settings::get('LANGUAGES'));
});

$library->tag('language', function($context, $language_code) {
    $old_language = translation::get_language();
    if ($old_language != $language_code) {
        $context->render_context['language_override'] = $old_language;
        translation::activate($language_code);
    }
    return '';
}, array(
    'takes_context' => true,
));

$library->tag('endlanguage', function() {
    if (isset($context->render_context['language_override']))
        translation::activate($context->render_context['language_override']);
    return '';
}, array(
    'takes_context' => true,
));

return $library;
/*
?><?php

// namespace bjork\templatetags;

use bjork\conf\settings,
    bjork\utils\translation;

final class i18n {
    
    public static function get_language() {
        return translation::get_language();
    }
    
    public static function _($string) {
        return translation::gettext($string);
    }
    
    public static function gettext($string) {
        return translation::gettext($string);
    }
    
    public static function pgettext($context, $string) {
        return translation::pgettext($context, $string);
    }
    
    public static function _n($singular, $plural, $count) {
        return translation::ngettext($singular, $plural, $count);
    }
    
    public static function ngettext($singular, $plural, $count) {
        return translation::ngettext($singular, $plural, $count);
    }
    
    public static function npgettext($context, $singular, $plural, $count) {
        return translation::ngettext($context, $singular, $plural, $count);
    }
}
*/