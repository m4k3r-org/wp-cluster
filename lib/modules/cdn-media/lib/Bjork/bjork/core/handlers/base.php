<?php

namespace bjork\core\handlers {

use os\environ;
use bjork\conf\settings;
use bjork\utils\encoding;

final class base {
    /**
    * Returns the equivalent of the HTTP request's SCRIPT_NAME environment
    * variable. If Apache mod_rewrite has been used, returns what would have
    * been the script name prior to any rewriting (so it's the script name 
    * as seen from the client's perspective), unless FORCE_SCRIPT_NAME
    * is set (to anything).
    */
    public static function get_script_name() {
        $scriptURL = settings::get('FORCE_SCRIPT_NAME', null);
        if (!is_null($scriptURL))
            return encoding::to_unicode($scriptURL);
        
        // If Apache's mod_rewrite had a whack at the URL, Apache set either
        // SCRIPT_URL or REDIRECT_URL to the full resource URL before applying
        // any rewrites. Unfortunately not every Web server (lighttpd!) passes
        // this information through all the time, so FORCE_SCRIPT_NAME, above,
        // is still needed.
        $scriptURL = environ::get('SCRIPT_URL', '');
        if (empty($scriptURL))
            $scriptURL = environ::get('REDIRECT_URL', '');
        if (!empty($scriptURL))
            return encoding::to_unicode(mb_substr($scriptURL, 0, -1 * mb_strlen(environ::get('PATH_INFO', ''))));
        return encoding::to_unicode(environ::get('SCRIPT_NAME', ''));
    }
}

}

namespace bjork\core\handlers\base {

use bjork\conf\settings,
    bjork\core\exceptions,
    bjork\core\mail,
    bjork\core\urlresolvers,
    bjork\dispatch,
    bjork\http,
    bjork\utils\functional,
    bjork\views\debug\ExceptionReporter;

require_once 'bjork/core/signals.php';

class BaseHandler {
    
    protected
        $requestMiddleware,
        $viewMiddleware,
        $responseMiddleware,
        $exceptionMiddleware;
    
    function __construct() {
        $this->requestMiddleware = null;
        $this->viewMiddleware = null;
        $this->responseMiddleware = null;
        $this->exceptionMiddleware = null;
    }
    
    /**
    * Populate middleware lists from settings.MIDDLEWARE_CLASSES.
    * Must be called after the environment is fixed.
    */
    protected function loadMiddleware() {
        $this->requestMiddleware = array();
        $this->viewMiddleware = array();
        $this->responseMiddleware = array();
        $this->exceptionMiddleware = array();
        
        $requestMiddleware = array();
        foreach (settings::get('MIDDLEWARE_CLASSES') as $middlewareClass) {
            try {
                $instance = new $middlewareClass();
            } catch (exceptions\MiddlewareNotUsed $e) {
                continue;
            }
            if (method_exists($instance, 'processRequest'))
                array_push($requestMiddleware, $instance);
            if (method_exists($instance, 'processView'))
                array_push($this->viewMiddleware, $instance);
            if (method_exists($instance, 'processResponse'))
                array_unshift($this->responseMiddleware, $instance);
            if (method_exists($instance, 'processException'))
                array_unshift($this->exceptionMiddleware, $instance);
        }
        
        // We only assign to this when initialization is complete as it is used
        // as a flag for initialization being complete.
        $this->requestMiddleware = $requestMiddleware;
    }
    
    public function getResponse($request) {
        $resolver = urlresolvers::get_resolver();
        $response = null;
        
        try {
            // apply request middleware
            foreach ($this->requestMiddleware as $middleware) {
                $response = $middleware->processRequest($request);
                if (!is_null($response))
                    break;
            }
            
            if (is_null($response)) {
                list($callback, $args, $kwargs) = $resolver->resolve($request->getPathInfo());
                
                // apply view middleware
                foreach ($this->viewMiddleware as $middleware) {
                    $response = $middleware->processView($request, $callback, $args, $kwargs);
                    if (!is_null($response))
                        break;
                }
            }
            
            if (is_null($response)) {
                // execute view
                try {
                    if (!empty($kwargs)) {
                        $params = array_merge($kwargs, array('request' => $request));
                        $response = functional::call_user_func_assoc($callback, $params);
                    } else {
                        $params = array_merge(array($request), $args);
                        $response = call_user_func_array($callback, $params);
                    }
                } catch (\Exception $e) {
                    // If the view raised an exception, run it through exception
                    // middleware, and if the exception middleware returns a
                    // response, use that. Otherwise, reraise the exception.
                    foreach ($this->exceptionMiddleware as $middleware) {
                        $response = $middleware->processException($request, $e);
                        if (!is_null($response))
                            break;
                    }
                    if (is_null($response))
                        throw $e;
                }
            }
            
            // Complain if the view returned None (a common error).
            if (is_null($response)) {
                throw new \Exception(
                    "The view {$callback} didn't return an HttpResponse object.");
            }
        } catch (exceptions\PermissionDenied $e) {
            $response = $this->handlePermissionRequiredException($request, $resolver, $e);
        } catch (http\Http404 $e) {
            $response = $this->handleNotFoundException($request, $resolver, $e);
        } catch (\Exception $e) {
            $response = $this->handleUncaughtException($request, $resolver, $e);
        }
        
        try {
            // Apply response middleware, regardless of the response
            foreach ($this->responseMiddleware as $middleware) {
                $response = $middleware->processResponse($request, $response);
                if (is_null($response)) {
                    throw new \Exception(
                        'Response middleware must return an HttpResponse object.');
                }
            }
            $response = $this->apply_response_fixes($request, $response);
        } catch (\Exception $e) { // Any exception should be gathered and handled
            $response = $this->handleUncaughtException($request, $resolver, $e);
        }
        
        return $response;
    }
    
    public function handlePermissionRequiredException($request, $resolver, $e) {
        $response = new http\HttpResponseForbidden('<h1>Permission denied</h1>');
        dispatch::send('bjork.got_request_exception', null, $request);
        return $response;
    }
    
    public function handleNotFoundException($request, $resolver, $e) {
        if (settings::get('DEBUG')) {
            require_once 'bjork/views/debug/__init.php';
            return \bjork\views\debug\technical_404_response($request, $e);
        }
        
        list($callback, $kwargs) = $resolver->resolve404();
        $params = array_merge($kwargs, array('request' => $request));
        
        try {
            $response = functional::call_user_func_assoc($callback, $params);
        } catch (\Exception $exc) {
            return $this->handleUncaughtException($request, $resolver, $exc);
        }
        
        dispatch::send('bjork.got_request_exception', null, $request);
        return $response;
    }
    
    /**
    * Processing for any otherwise uncaught exceptions (those that will
    * generate HTTP 500 responses). Can be overridden by subclasses who want
    * customised 500 handling.
    * 
    * Be *very* careful when overriding this because the error could be
    * caused by anything, so assuming something like the database is always
    * available would be an error.
    */
    public function handleUncaughtException($request, $resolver, $e) {
        dispatch::send('bjork.got_request_exception', null, $request);
        
        if (settings::get('DEBUG_PROPAGATE_EXCEPTIONS'))
            throw $e;
        
        if (settings::get('DEBUG')) {
            require_once 'bjork/views/debug/__init.php';
            return \bjork\views\debug\technical_500_response($request, $e);
        }
        
        try {
            mail_admins($request, $e);
        } catch (\Exception $exc) {
            @error_log(
                "Failed to mail exception details to admins: ".
                "{$exc->getMessage()}\n{$exc->getTraceAsString()}");
        }
        
        list($callback, $kwargs) = $resolver->resolve500();
        $params = array_merge($kwargs, array('request' => $request));
        $response = functional::call_user_func_assoc($callback, $params);
        dispatch::send('bjork.got_request_exception', null, $request);
        return $response;
    }
    
    public function apply_response_fixes($request, $response) {
        return $response;
    }
}

function mail_admins($request, $exception) {
    try {
        $subject = sprintf('Exception (%s IP): %s',
            in_array($request->META->get('REMOTE_ADDR'), settings::get('INTERNAL_IPS'))
                ? 'internal'
                : 'EXTERNAL',
            $exception->getMessage());
    } catch (\Exception $e) {
        $subject = sprintf('Exception: %s', $exception->getMessage());
    }
    
    $subject = preg_replace("/\n/", "\\n", $subject);
    $subject = preg_replace("/\r/", "\\r", $subject);
    $subject = mb_substr($subject, 0, 989);
    
    $message = strval($exception);
    $reporter = new ExceptionReporter($request, $exception, true);
    $html_message = $reporter->getTracebackHtml();
    
    mail::mail_admins($subject, $message, true, null, $html_message);
}

}
