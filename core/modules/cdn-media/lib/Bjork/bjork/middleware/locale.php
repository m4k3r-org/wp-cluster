<?php

namespace bjork\middleware\locale;

use strutils;

use bjork\conf\settings,
    bjork\core\urlresolvers,
    bjork\core\urlresolvers\LocaleRegexURLResolver,
    bjork\http\HttpResponseRedirect,
    bjork\utils\translation;

/**
* This is a very simple middleware that parses a request and decides what
* translation object to install for the current request. This allows pages
* to be dynamically translated to the language the user desires (if the
* language is available, of course).
*/
class LocaleMiddleware {
    var $supported_languages, $is_language_prefix_patterns_used;
    
    function __construct() {
        $this->supported_languages = settings::get('LANGUAGES');
        $this->is_language_prefix_patterns_used = false;
        $patterns = urlresolvers::get_resolver(null)->getURLPatterns();
        foreach ($patterns as $url_pattern) {
            if ($url_pattern instanceof LocaleRegexURLResolver) {
                $this->is_language_prefix_patterns_used = true;
                break;
            }
        }
    }
    
    function processRequest($request) {
        $check_path = $this->isLanguagePrefixPatternsUsed();
        $language = translation::get_language_from_request($request, $check_path);
        translation::activate($language);
        $request['LANGUAGE_CODE'] = translation::get_language();
    }
    
    function processResponse($request, $response) {
        $language = translation::get_language();
        $language_from_path = translation::get_language_from_path(
            $request->getPathInfo(),
            $this->supported_languages);
        
        if ($response->statusCode === 404 && !$language_from_path
                && $this->isLanguagePrefixPatternsUsed()) {
            $urlconf = $request->get('urlconf', null);
            $language_path = "/{$language}{$request->getPathInfo()}";
            $path_valid = urlresolvers::is_valid_path($language_path, $urlconf);
            $ends_with_slash = strutils::endswith($language_path, '/');
            if (!$path_valid && settings::get('APPEND_SLASH') && !$ends_with_slash)
                $path_valid = urlresolvers::is_valid_path("{$language_path}/", $urlconf);
            
            if ($path_valid) {
                $language_url = sprintf('%s://%s/%s%s',
                    $request->isSecure() ? 'https' : 'http',
                    $request->getHost(), $language, $request->getFullPath());
                return new HttpResponseRedirect($language_url);
            }
        }
        
        // if (!($this->isLanguagePrefixPatternsUsed() && $language_from_path))
        //     patch_vary_headers($response, array('Accept-Language'));
        
        if (!isset($response['Content-Language']))
            $response['Content-Language'] = $language;
        return $response;
    }
    
    /**
    * Returns `True` if the `LocaleRegexURLResolver` is used
    * at root level of the urlpatterns, else it returns `False`.
    */
    protected function isLanguagePrefixPatternsUsed() {
        return $this->is_language_prefix_patterns_used;
    }
}
