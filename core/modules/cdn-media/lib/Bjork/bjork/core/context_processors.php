<?php

namespace bjork\core\context_processors;

use bjork\conf\settings,
    bjork\utils\functional\SimpleLazyObject,
    bjork\utils\translation;

function request($request) {
    return array(
        'request' => $request,
    );
}

function media($request) {
    return array(
        'MEDIA_URL' => settings::get('MEDIA_URL'),
        'STATIC_URL' => settings::get('STATIC_URL'),
    );
}

function i18n($request) {
    return array(
        'LANGUAGES' => settings::get('LANGUAGES'),
        'LANGUAGE_CODE' => translation::get_language(),
    );
}

function debug($request) {
    return array(
        'DEBUG' => settings::get('DEBUG'),
    );
}

function csrf($request) {
    return array(
        'csrf_token' => new SimpleLazyObject(function() use ($request) {
            $token = \bjork\middleware\csrf::get_token($request);
            if (null === $token)
                // In order to be able to provide debugging info in the
                // case of misconfiguration, we use a sentinel value
                // instead of returning an empty dict.
                return 'NOTPROVIDED';
            return $token;
        }),
    );
}

function tz($request) {
    return array(
        'TIME_ZONE' => date_default_timezone_get(),
    );
}
