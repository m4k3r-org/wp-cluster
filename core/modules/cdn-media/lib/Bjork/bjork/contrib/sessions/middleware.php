<?php

namespace bjork\contrib\sessions\middleware;

use bjork\conf\settings;

class SessionMiddleware {
    
    function processRequest($request) {
        $backend_class = settings::get('SESSION_ENGINE') . '\SessionStore';
        $session_key = null;
        if (isset($request->COOKIES[settings::get('SESSION_COOKIE_NAME')]))
            $session_key = $request->COOKIES[settings::get('SESSION_COOKIE_NAME')];
        $request['session'] = new $backend_class($session_key);
    }
    
    /**
    * If request.session was modified, or if the configuration is to save the
    * session every time, save the changes and set a session cookie.
    */
    function processResponse($request, $response) {
        if (isset($request['session'])) {
            $session = $request['session'];
            $accessed = $session->isAccessed();
            $modified = $session->isModified();
            if ($accessed) {
                // patch_vary_headers($response, array('Cookie'));
            }
            if ($modified || settings::get('SESSION_SAVE_EVERY_REQUEST')) {
                if ($session->expiresAtBrowserClose()) {
                    $max_age = null;
                    $expires = null;
                } else {
                    $max_age = $session->getExpiryAge();
                    $expires = time() + $max_age;
                }
                $session->save();
                $response->setCookie(
                    settings::get('SESSION_COOKIE_NAME'),
                    $session->getSessionKey(),
                    array(
                        'max_age'   => $max_age,
                        'expires'   => $expires,
                        'path'      => settings::get('SESSION_COOKIE_PATH'),
                        'domain'    => settings::get('SESSION_COOKIE_DOMAIN'),
                        'secure'    => settings::get('SESSION_COOKIE_SECURE'),
                        'httponly'  => settings::get('SESSION_COOKIE_HTTPONLY')
                    ));
            }
        }
        return $response;
    }
}
