<?php

namespace bjork\middleware\csrf {

use bjork\conf\settings,
    bjork\middleware\csrf,
    bjork\utils\crypto,
    bjork\utils\functional,
    bjork\utils\http;

/**
* Middleware that requires a present and correct csrfmiddlewaretoken
* for POST requests that have a CSRF cookie, and sets an outgoing
* CSRF cookie.
* 
* This middleware should be used in conjunction with the csrf_token template
* tag.
*/
class CsrfViewMiddleware {
    
    function processView($request, $callback, $args, $kwargs) {
        if ($request->get('csrf_processing_done', false))
            return null;
        
        $cookie = $request->COOKIES->get(settings::get('CSRF_COOKIE_NAME'), null);
        if (null !== $cookie) {
            $csrf_token = csrf::sanitize_token($cookie);
            // use same token next time
            $request->META['CSRF_COOKIE'] = $csrf_token;
        } else {
            $csrf_token = null;
            // Generate token and store it in the request, so it's
            // available to the view.
            $request->META['CSRF_COOKIE'] = csrf::get_new_csrf_key();
        }
        
        // Wait until request.META["CSRF_COOKIE"] has been manipulated before
        // bailing out, so that get_token still works
        // @TODO: work around the fact php can't have function decorators.
        if (in_array($callback, settings::get('CSRF_EXEMPT_VIEWS')))
            return null;
        
        // Assume that anything not defined as 'safe' by RC2616 needs protection.
        if (!in_array($request->getMethod(), array('GET', 'HEAD', 'OPTIONS', 'TRACE'))) {
            if ($request->get('_dont_enforce_csrf_checks', false))
                // Mechanism to turn off CSRF checks for test suite. It comes after
                // the creation of CSRF cookies, so that everything else continues to
                // work exactly the same (e.g. cookies are sent etc), but before
                // any branches that call reject()
                return $this->accept($request);
            
            if ($request->isSecure()) {
                // Suppose user visits http://example.com/
                // An active network attacker,(man-in-the-middle, MITM) sends a
                // POST form which targets https://example.com/detonate-bomb/ and
                // submits it via javascript.
                // 
                // The attacker will need to provide a CSRF cookie and token, but
                // that is no problem for a MITM and the session independent
                // nonce we are using. So the MITM can circumvent the CSRF
                // protection. This is true for any HTTP connection, but anyone
                // using HTTPS expects better!  For this reason, for
                // https://example.com/ we need additional protection that treats
                // http://example.com/ as completely untrusted.  Under HTTPS,
                // Barth et al. found that the Referer header is missing for
                // same-domain requests in only about 0.2% of cases or less, so
                // we can use strict Referer checking.
                $referer = $request->META->get('HTTP_REFERER');
                if (null === $referer)
                    // @TODO: log
                    return $this->reject($request, csrf::REASON_NO_REFERER);
                
                // Note that request.get_host() includes the port
                $good_referer = "https://{$request->getHost()}/";
                if (!http::same_origin($referer, $good_referer)) {
                    $reason = sprintf(csrf::REASON_BAD_REFERER,
                        $referer, $good_referer);
                    // @TODO: log
                    return $this->reject($request, $reason);
                }
            }
            
            if (null === $csrf_token)
                // No CSRF cookie. For POST requests, we insist on a CSRF cookie,
                // and in this way we can avoid all CSRF attacks, including login
                // CSRF.
                // @TODO: log
                return $this->reject($request, csrf::REASON_NO_CSRF_COOKIE);
            
            // check non-cookie token for match
            $request_csrf_token = '';
            if ($request->getMethod() === 'POST')
                $request_csrf_token = $request->POST->get('csrfmiddlewaretoken', '');
            if ($request_csrf_token === '')
                // Fall back to X-CSRFToken, to make things easier for AJAX,
                // and possible for PUT/DELETE
                $request_csrf_token = $request->META->get('HTTP_X_CSRFTOKEN', '');
            
            if (!crypto::constant_time_compare($request_csrf_token, $csrf_token))
                // @TODO: log
                return $this->reject($request, csrf::REASON_BAD_TOKEN);
        }
        
        return $this->accept($request);
    }
    
    function processResponse($request, $response) {
        if ($response->getAttribute('csrf_processing_done', false))
            return $response;
        
        // If CSRF_COOKIE is unset, then CsrfViewMiddleware.process_view was
        // never called, probably because a request middleware returned a
        // response (for example, contrib.auth redirecting to a login page).
        if (null === $request->META->get('CSRF_COOKIE'))
            return $response;
        
        if (!$request->META->get('CSRF_COOKIE_USED', false))
            return $response;
        
        $response->setCookie(
            settings::get('CSRF_COOKIE_NAME'),
            $request->META['CSRF_COOKIE'],
            array(
                'max_age' => 60 * 60 * 24 * 7 * 52,
                'secure'  => settings::get('CSRF_COOKIE_SECURE'),
                'domain'  => settings::get('CSRF_COOKIE_DOMAIN'),
                'path'    => settings::get('CSRF_COOKIE_PATH'),
            )
        );
        
        $response->setAttribute('csrf_processing_done', true);
        return $response;
    }
    
    protected function accept($request) {
        // Avoid checking the request twice by adding a custom attribute to
        // request. This will be relevant when both decorator and middleware
        // are used.
        $request['csrf_processing_done'] = true;
        return null;
    }
    
    protected function reject($request, $reason) {
        return functional::call_user_func_assoc(csrf::get_failure_view(), array(
            'request' => $request,
            'reason' => $reason,
        ));
    }
}

}

namespace bjork\middleware {

use bjork\conf\settings,
    bjork\core\exceptions\ImproperlyConfigured,
    bjork\utils\importlib;

final class csrf {
    
    const REASON_NO_REFERER = 'Referer checking failed - no Referer.';
    const REASON_BAD_REFERER = 'Referer checking failed - %s does not match %s.';
    const REASON_NO_CSRF_COOKIE = 'CSRF cookie not set.';
    const REASON_BAD_TOKEN = 'CSRF token missing or incorrect.';
    
    const MAX_CSRF_KEY = 18446744073709551616; // 2<<63
    
    /**
    * Returns the the CSRF token required for a POST form. The token is an
    * alphanumeric value.
    * 
    * A side effect of calling this function is to make the the csrf_protect
    * decorator and the CsrfViewMiddleware add a CSRF cookie and a
    * 'Vary: Cookie' header to the outgoing response. For this reason, you
    * may need to use this function lazily, as is done by the csrf context
    * processor.
    */
    public static function get_token($request) {
        $request->META['CSRF_COOKIE_USED'] = true;
        return $request->META->get('CSRF_COOKIE', null);
    }
    
    // protected
    
    static function sanitize_token($token) {
        // Allow only alphanum, and ensure we return a 'str'
        // for the sake of the post processing middleware.
        $token = preg_replace('/[^a-zA-Z0-9]/u', '', $token);
        if (empty($token))
            // In case the cookie has been truncated to nothing at some point.
            return self::get_new_csrf_key();
        return $token;
    }
    
    static function get_new_csrf_key() {
        $rnd = mt_rand(0, self::MAX_CSRF_KEY);
        $key = settings::get('SECRET_KEY');
        return md5($rnd . $key);
    }
    
    /**
    * Returns the view to be used for CSRF rejections.
    */
    static function get_failure_view() {
        $view = settings::get('CSRF_FAILURE_VIEW');
        importlib::import_class($view);
        if (!is_callable($view))
            throw new ImproperlyConfigured("{$view} is not callable");
        return $view;
    }
}

}
