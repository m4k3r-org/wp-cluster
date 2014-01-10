<?php

namespace bjork\middleware\common;

use strutils,
    urllib;

use bjork\conf\settings,
    bjork\core\urlresolvers,
    bjork\http\HttpResponseForbidden,
    bjork\http\HttpResponsePermanentRedirect;

/**
* "Common" middleware for taking care of some basic operations:
* 
*   - Forbids access to User-Agents in settings.DISALLOWED_USER_AGENTS
*   
*   - URL rewriting: Based on the APPEND_SLASH and PREPEND_WWW settings,
*     this middleware appends missing slashes and/or prepends missing
*     "www."s.
*     
*     - If APPEND_SLASH is set and the initial URL doesn't end with a
*       slash, and it is not found in urlpatterns, a new URL is formed by
*       appending a slash at the end. If this new URL is found in
*       urlpatterns, then an HTTP-redirect is returned to this new URL;
*       otherwise the initial URL is processed as usual.
*/
class CommonMiddleware {
    
    function processRequest($request) {
        // Check for denied User-Agents
        if ($request->META->hasKey('HTTP_USER_AGENT')) {
            foreach (settings::get('DISALLOWED_USER_AGENTS') as $user_agent_regex) {
                if (preg_match($user_agent_regex, $request->META['HTTP_USER_AGENT']))
                    // @TODO: log
                    return new HttpResponseForbidden('<h1>Forbidden</h1>');
            }
        }
        
        // Check for a redirect based on settings.APPEND_SLASH
        // and settings.PREPEND_WWW
        $old_url = array($request->getHost(), $request->getPath());
        $new_url = $old_url;
        
        if (settings::get('PREPEND_WWW') && !empty($old_url[0]) &&
                !strutils::startswith($old_url[0], 'www.'))
            $new_url[0] = 'www.' . $old_url[0];
        
        // Append a slash if APPEND_SLASH is set and the URL doesn't have a
        // trailing slash and there is no pattern for the current path
        if (settings::get('APPEND_SLASH') && !strutils::endswith($old_url[1], '/')) {
            $urlconf = $request->get('urlconf', null);
            if (!urlresolvers::is_valid_path($request->getPathInfo(), $urlconf) &&
                 urlresolvers::is_valid_path($request->getPathInfo().'/', $urlconf))
            {
                $new_url[1] .= '/';
                if (settings::get('DEBUG') && $request->getMethod() == 'POST')
                    throw new \RuntimeException(sprintf(
                        "You called this URL via POST, but the URL doesn't end ".
                        "in a slash and you have APPEND_SLASH set. Bjork can't ".
                        "redirect to the slash URL while maintaining POST data. ".
                        "Change your form to point to %s%s (note the trailing ".
                        "slash), or set APPEND_SLASH=False in your Bjork settings.",
                        $new_url[0], $new_url[1]));
            }
        }
        
        if ($new_url === $old_url)
            // no redirects required
            return;
        
        if ($new_url[0])
            $newurl = sprintf('%s://%s%s',
                $request->isSecure() ? 'https' : 'http',
                $new_url[0],
                urllib::quote($new_url[1]));
        else
            $newurl = urllib::quote($new_url[1]);
        
        if ($request->META->get('QUERY_STRING', ''))
            $newurl .= '?' . $request->META['QUERY_STRING'];
        
        return new HttpResponsePermanentRedirect($newurl);
    }
}
