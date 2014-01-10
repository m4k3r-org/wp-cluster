<?php

namespace bjork\core\handlers\generic;

use os\environ;
use bjork\conf\settings,
    bjork\core\loading,
    bjork\core\handlers\base,
    bjork\core\handlers\base\BaseHandler,
    bjork\core\urlresolvers,
    bjork\dispatch,
    bjork\http\HttpRequest,
    bjork\http\QueryDict,
    bjork\utils\datastructures\Dict,
    bjork\utils\encoding;

class GenericRequest extends HttpRequest {
    
    protected
        $_get,
        $_post,
        $_cookies,
        $_meta,
        $_files;
    
    function __construct() {
        parent::__construct();
        $this->_get = null;
        $this->_post = null;
        $this->_cookies = null;
        $this->_meta = null;
        $this->_files = null;
        $this->_request = null;
        
        $scriptName = base::get_script_name();
        $pathInfo = encoding::to_unicode(
            environ::get('ORIG_PATH_INFO',
            environ::get('PATH_INFO',
            '/')));
        if (empty($pathInfo) || $pathInfo == $scriptName) {
            // Sometimes PATH_INFO exists, but is empty (e.g. accessing
            // the SCRIPT_NAME URL without a trailing slash). We really need
            // to operate as if they'd requested '/'. Not amazingly nice to
            // force the path like this, but should be harmless.
            $pathInfo = '/';
        }
        $this->_pathInfo = $pathInfo;
        $this->_path = "{$scriptName}{$pathInfo}";
        $this->META['PATH_INFO'] = $pathInfo;
        $this->META['SCRIPT_NAME'] = $scriptName;
    }
    
    function __get($name) {
        static $arrays = array(
            'GET', 'POST', 'FILES', 'COOKIES', 'REQUEST', 'META');
        if (in_array($name, $arrays)) {
            $method_name = 'get' . ucfirst($name);
            return $this->$method_name();
        }
        throw new \OutOfBoundsException($name);
    }
    
    protected function getRequest() {
        if (null === $this->_request) {
            // @TODO: REQUEST should be a MergeDict
            // holding *references* to POST and GET
            $c = array();
            $r = array_merge(array(), $_REQUEST);
            foreach ($r as $name => $value)
                $c[$name] = $value;
            $this->_request = new Dict($c);
        }
        return $this->_request;
    }
    
    protected function getGet() {
        if (null === $this->_get) {
            $this->_get = new QueryDict(
                environ::get('QUERY_STRING', ''),
                false, $this->getEncoding());
        }
        return $this->_get;
    }
    
    protected function getPost() {
        if (null === $this->_post) {
            $this->loadPostAndFiles();
        }
        return $this->_post;
    }
    
    protected function getFiles() {
        if (null === $this->_files) {
            $this->loadPostAndFiles();
        }
        return $this->_files;
    }
    
    protected function getCookies() {
        if (null === $this->_cookies) {
            $c = array();
            $r = array_merge(array(), $_COOKIE);
            foreach ($r as $name => $value)
                $c[$name] = $value;
            $this->_cookies = new Dict($c);
        }
        return $this->_cookies;
    }
    
    protected function getMeta() {
        if (null === $this->_meta) {
            $c = array();
            $r = array_merge(array(), $_SERVER);
            foreach ($r as $name => $value) {
                if (substr($name, 0, 5) == 'HTTP_')
                    $name = str_replace('-', '_', strtoupper($name));
                $c[$name] = $value;
            }
            $this->_meta = new Dict($c);
        }
        return $this->_meta;
    }
}

class GenericHandler extends BaseHandler {
    static $STATUS_CODE_TEXT = array(
        100 => 'CONTINUE',
        101 => 'SWITCHING PROTOCOLS',
        102 => 'PROCESSING',
        200 => 'OK',
        201 => 'CREATED',
        202 => 'ACCEPTED',
        203 => 'NON-AUTHORITATIVE INFORMATION',
        204 => 'NO CONTENT',
        205 => 'RESET CONTENT',
        206 => 'PARTIAL CONTENT',
        207 => 'MULTI-STATUS',
        208 => 'ALREADY REPORTED',
        226 => 'IM USED',
        300 => 'MULTIPLE CHOICES',
        301 => 'MOVED PERMANENTLY',
        302 => 'FOUND',
        303 => 'SEE OTHER',
        304 => 'NOT MODIFIED',
        305 => 'USE PROXY',
        306 => 'RESERVED',
        307 => 'TEMPORARY REDIRECT',
        400 => 'BAD REQUEST',
        401 => 'UNAUTHORIZED',
        402 => 'PAYMENT REQUIRED',
        403 => 'FORBIDDEN',
        404 => 'NOT FOUND',
        405 => 'METHOD NOT ALLOWED',
        406 => 'NOT ACCEPTABLE',
        407 => 'PROXY AUTHENTICATION REQUIRED',
        408 => 'REQUEST TIMEOUT',
        409 => 'CONFLICT',
        410 => 'GONE',
        411 => 'LENGTH REQUIRED',
        412 => 'PRECONDITION FAILED',
        413 => 'REQUEST ENTITY TOO LARGE',
        414 => 'REQUEST-URI TOO LONG',
        415 => 'UNSUPPORTED MEDIA TYPE',
        416 => 'REQUESTED RANGE NOT SATISFIABLE',
        417 => 'EXPECTATION FAILED',
        422 => 'UNPROCESSABLE ENTITY',
        423 => 'LOCKED',
        424 => 'FAILED DEPENDENCY',
        426 => 'UPGRADE REQUIRED',
        500 => 'INTERNAL SERVER ERROR',
        501 => 'NOT IMPLEMENTED',
        502 => 'BAD GATEWAY',
        503 => 'SERVICE UNAVAILABLE',
        504 => 'GATEWAY TIMEOUT',
        505 => 'HTTP VERSION NOT SUPPORTED',
        506 => 'VARIANT ALSO NEGOTIATES',
        507 => 'INSUFFICIENT STORAGE',
        508 => 'LOOP DETECTED',
        510 => 'NOT EXTENDED',
    );
    
    public function __invoke() {
        $request = new GenericRequest();
        
        if (is_null($this->requestMiddleware)) {
            $this->loadMiddleware();
            
            // make sure app cache is populated
            loading::load_apps();
        }
        
        urlresolvers::set_script_prefix(base::get_script_name());
        
        dispatch::send('bjork.request_started');
        $response = $this->getResponse($request);
        dispatch::send('bjork.request_finished');
        
        $statusCode = $response->statusCode;
        
        if ($statusCode !== 200) {
            try {
                $statusCodeText = self::$STATUS_CODE_TEXT[$statusCode];
            } catch (\Exception $e) {
                $statusCodeText = 'UNKNOWN STATUS CODE';
            }
        
            $status = sprintf('%s %s %s',
                environ::get('SERVER_PROTOCOL', 'HTTP/1.0'),
                $statusCode,
                $statusCodeText);
        
            header($status, true, $statusCode);
        }
        
        return $response;
    }
}
