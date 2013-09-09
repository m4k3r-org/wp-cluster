<?php

namespace bjork;

use bjork\core\urlresolvers,
    bjork\http\HttpResponse,
    bjork\http\HttpResponseRedirect,
    bjork\http\HttpResponsePermanentRedirect,
    bjork\template\loader,
    bjork\template\context\RequestContext;

final class shortcuts {
    
    /**
    * Returns an HttpResponse whose content is filled with the result of
    * rendering the given template with the specified context instance.
    * Uses a RequestContext by default.
    */
    public static function render($request, $templateName, array $kwargs=null, $contextInstance=null) {
        $content_type = null;
        $status = null;
        if (is_array($kwargs)) {
            if (isset($kwargs['content_type'])) {
                $content_type = $kwargs['content_type'];
                unset($kwargs['content_type']);
            }
            if (isset($kwargs['status'])) {
                $status = $kwargs['status'];
                unset($kwargs['status']);
            }
        }
        if (is_null($contextInstance))
            $contextInstance = new RequestContext($request, $kwargs);
        return new HttpResponse(loader::render_to_string($templateName, $kwargs, $contextInstance),
            $content_type, $status);
    }
    
    /**
    * Returns an HttpResponseRedirect to the apropriate URL for the arguments
    * passed.
    * 
    * The arguments could be:
    * 
    *   * A view name, possibly with arguments: `urlresolvers.reverse()` will
    *     be used to reverse-resolve the name.
    *   * A URL, which will be used as-is for the redirect location.
    * 
    * By default issues a temporary redirect; pass permanent=True to issue a
    * permanent redirect.
    */
    public static function redirect($request, $to, $permanent=false/*, array $args=null, array $kwargs=null*/) {
        // $url = urlresolvers::reverse($to, null, $args, $kwargs);
        $url = $request->buildAbsoluteURI(strval($to));
        if ($permanent)
            return new HttpResponsePermanentRedirect($url);
        return new HttpResponseRedirect($url);
    }
}
