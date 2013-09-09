<?php

namespace bjork\views\csrf;

use bjork\conf\settings,
    bjork\http\HttpResponseForbidden,
    bjork\middleware\csrf,
    bjork\template\loader;

/**
* Default view used when request fails CSRF protection
*/
function csrf_failure($request, $reason='') {
    
    $template = __DIR__.'/technical_403_template.php';
    
    $c = array(
        'DEBUG' => settings::get('DEBUG'),
        'reason' => $reason,
        'no_referer' => ($reason == csrf::REASON_NO_REFERER),
    );
    
    return new HttpResponseForbidden(
        loader::render_to_string($template, $c),
        'text/html');
}
