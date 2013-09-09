<?php

namespace bjork\http {

use os,
    os\environ,
    urllib;
use bjork\conf\settings,
    bjork\core\exceptions\BjorkException,
    bjork\core\exceptions\OperationNotSupported,
    bjork\core\signing,
    bjork\core\signing\BadSignature,
    bjork\http,
    bjork\utils\datastructures\Dict,
    bjork\utils\datastructures\List_,
    bjork\utils\datastructures\MultiValueDict,
    bjork\utils\encoding,
    bjork\utils\http as http_utils;

const ABSOLUTE_HTTP_URL_RE = "/^https?:\/\//i";

class Http404 extends BjorkException {}

class HttpRequest implements \ArrayAccess {
    
    const RAISE_ERROR = 'raise_error_sentinel_1214545';
    
    protected
        $_encoding,
        $_method;
    
    var // let those open for patching
        $_path,
        $_pathInfo;
    
    private
        $extraParams;
    
    public function __construct() {
        $this->_path = "";
        $this->_pathInfo = "";
        $this->_encoding = "";
        $this->_method = null;
        $this->extraParams = array();
    }
    
    function __toString() {
        return "<".get_class($this).": {$this->getMethod()} {$this->getPath()}>";
    }
    
    protected function loadPostAndFiles() {
        // ideally we'd parse php://input stream that contains
        // the raw post data, but php chose for ourselves to parse
        // the data itself when the submitted form is multipart
        // and leave raw post data empty.
        if (null === $this->_post) {
            $c = array();
            $r = array_merge(array(), $_POST);
            foreach ($r as $name => $value)
                $c[$name] = $value;
            $this->_post = new Dict($c);
        }
        
        if (null === $this->_files) {
            $files = new MultiValueDict();
            $c = array();
            $r = array_merge(array(), $_FILES);
            foreach ($r as $name => $array) {
                $files->setListDefault($name, array());
                // $skip = array();
                // $status = $r[$name]['error'];
                // if (!is_array($status))
                //     $status = array($status);
                // foreach ($status as $i => $st) {
                //     if ($st !== \UPLOAD_ERR_OK)
                //         $skip[] = $i;
                // }
                foreach ($array as $key => $value) {
                    if (!is_array($value))
                        $value = array($value);
                    $file_list = $files->getList($name);
                    $index = 0;
                    foreach ($value as $i => $v) {
                        // if (in_array($i, $skip))
                        //     continue;
                        if (!array_key_exists($i, $file_list))
                            $file_list[$i] = new Dict();
                        $file_list[$i][$key] = $v;
                    }
                    $files->setList($name, $file_list);
                }
            }
            $this->_files = $files;
        }
    }
    
    public function offsetExists($k) {
        return array_key_exists($k, $this->extraParams);
    }
    
    public function offsetGet($k) {
        return $this->extraParams[$k];
    }
    
    public function offsetSet($k, $value) {
        $this->extraParams[$k] = $value;
    }
    
    public function offsetUnset($k) {
        unset($this->extraParams[$k]);
    }
    
    public function get($k, $default=null) {
        return $this->offsetExists($k)
            ? $this->offsetGet($k)
            : $default;
    }
    
    public function hasKey($k) {
        return $this->offsetExists($k);
    }
    
    /**
    * A string representing the full path to the requested page, not
    * including the domain.
    * 
    * Example: "/music/bands/the_beatles/"
    */
    public function getPath() {
        return $this->_path;
    }
    
    public function getPathInfo() {
        return $this->_pathInfo;
    }
    
    public function getEncoding() {
        return $this->_encoding;
    }
    
    public function getMethod() {
        if (!empty($this->_method))
            return $this->_method;
        $method = strtoupper(environ::get('REQUEST_METHOD'));
        if (!in_array($method, http::valid_methods())) {
            // Apache accepts anything for REQUEST_METHOD. See this comment:
            // http://www.php.net/manual/en/reserved.variables.server.php#95672
            throw new OperationNotSupported($method);
        }
        $this->_method = $method;
        return $this->_method;
    }
    
    /**
    * Returns the HTTP host using the environment or request headers.
    */
    public function getHost() {
        $host = "";
        
        // We try three options, in order of decreasing preference.
        if (isset($this->META["HTTP_X_FORWARDED_HOST"])) {
            $host = $this->META["HTTP_X_FORWARDED_HOST"];
        } else if (isset($this->META["HTTP_HOST"])) {
            $host = $this->META["HTTP_HOST"];
        } else {
            // Reconstruct the host.
            $host = $this->META["SERVER_NAME"];
            $port = $this->META["SERVER_PORT"];
            if ($port != ($this->isSecure() ? "443" : "80"))
                $host .= ":".$port;
        }
        return $host;
    }
    
    public function getFullPath() {
        $qs = isset($this->META["QUERY_STRING"])
            ? $this->META["QUERY_STRING"]
            : "";
        
        // RFC 3986 requires query string arguments to be in the ASCII range.
        // Rather than crash if this doesn't happen, we encode defensively.
        return sprintf('%s%s', $this->_path, !empty($qs)
            ? '?'.encoding::iri_to_uri($qs)
            : '');
    }
    
    /**
    * Builds an absolute URI from the location and the variables available 
    * in this request. If no location is specified, the absolute URI is built
    * on ``request.get_full_path()``.
    */
    public function buildAbsoluteURI($location=null) {
        if (empty($location))
            $location = $this->getFullPath();
        if (!preg_match(ABSOLUTE_HTTP_URL_RE, $location)) {
            $currentURI = sprintf("%s://%s%s",
                $this->isSecure() ? "https" : "http",
                $this->getHost(),
                $this->getPath());
            $location = urllib::join($currentURI, $location);
        }
        return encoding::iri_to_uri($location);
    }
    
    public function isSecure() {
        $https = environ::get("HTTPS", "");
        return $https == "on" || $https == "1";
    }
    
    public function isAjax() {
        return "XMLHttpRequest" === (isset($this->META["HTTP_X_REQUESTED_WITH"])
            ? $this->META["HTTP_X_REQUESTED_WITH"]
            : "");
    }
    
    /**
    * Attempts to return a signed cookie. If the signature fails or the
    * cookie has expired, raises an exception... unless you provide the
    * default argument in which case that value will be returned instead.
    */
    public function getSignedCookie($key, $default=self::RAISE_ERROR, $salt='', $max_age=null) {
        if (!$this->COOKIES->hasKey($key)) {
            if ($default !== self::RAISE_ERROR)
                return $default;
            else
                throw new \OutOfBoundsException($key);
        }
        
        $cookie_value = $this->COOKIES[$key];
        $signer = signing::get_cookie_signer($key . $salt);
        
        try {
            $value = $signer->unsign($cookie_value, $max_age);
        } catch (BadSignature $e) {
            if ($default !== self::RAISE_ERROR)
                return $default;
            else
                throw $e;
        }
        
        return $value;
    }
}

/**
* A specialized MultiValueDict that takes a query string when initialized.
* This is immutable unless you create a copy of it.
* 
* Values retrieved from this class are converted from the given encoding
* (DEFAULT_CHARSET by default) to unicode.
*/
class QueryDict extends MultiValueDict {
    public $encoding;
    
    protected $_mutable = true;
    
    function __construct($queryString, $mutable=false, $encoding=null) {
        parent::__construct();
        if (empty($encoding))
            $encoding = settings::get("DEFAULT_CHARSET");
        if (empty($queryString))
            $queryString = "";
        $qs = urllib::parse_qsl($queryString, true); // $keepBlankValues=true
        foreach ($qs as $entry)
            $this->appendList(encoding::to_unicode($entry[0], $encoding),
                              encoding::to_unicode($entry[1], $encoding));
        $this->encoding = $encoding;
        $this->_mutable = $mutable;
    }
    
    private function assertMutable() {
        if (!$this->_mutable)
            throw new \BadMethodCallException(
                "This ".__CLASS__." instance is immutable");
    }
    
    public function offsetSet($k, $value) {
        $this->assertMutable();
        parent::offsetSet($k, $value);
    }
    
    public function offsetUnset($k) {
        $this->assertMutable();
        parent::offsetUnset($k);
    }
    
    public function setList($k, array $list) {
        $this->assertMutable();
        parent::setList($k, $list);
    }
    
    public function appendList($k, $value) {
        $this->assertMutable();
        parent::appendList($k, $value);
    }
    
    /**
    * Returns an encoded string of all query string arguments.
    * 
    *   >>> q = new QueryDict('', true)
    *   >>> q['next'] = '/a&b/'
    *   >>> q->urlencode()
    *   'next=%2Fa%26b%2F'
    *   >>> q->urlencode('/')
    *   'next=/a%26b/'
    */
    public function urlencode($safe=null) {
        $output = array();
        if (null !== $safe) {
            $encode = function($k, $v) use ($safe) {
                return sprintf('%s=%s', urllib::quote($k, $safe), urllib::quote($v, $safe)); };
            $encode_list = function($k, $v) use ($safe) {
                return sprintf('%s[]=%s', urllib::quote($k, $safe), urllib::quote($v, $safe)); };
        } else {
            $encode = function($k, $v) use ($safe) {
                return sprintf('%s=%s', urlencode($k), urlencode($v)); };
            $encode_list = function($k, $v) use ($safe) {
                return sprintf('%s[]=%s', urlencode($k), urlencode($v)); };
        }
        foreach ($this->getLists() as $k => $list) {
            if (count($list) === 1)
                $output[] = $encode($k, $list[0]);
            else {
                foreach ($arr as $v)
                    $output[] = $encode_list($k, $v);
            }
        }
        return implode('&', $output);
    }
}

class BadHeaderError extends BjorkException {}

class HttpResponse implements \ArrayAccess {
    public
        $statusCode = 200;
    
    protected
        $_headers,
        $_attrs,
        $_cookies,
        $_charset,
        $_container,
        $_isString;
    
    function __construct($content="", $contentType=null, $status=null) {
        // _headers is a mapping of the lower-case name to the original case
        // of the header (required for working with legacy systems) and the
        // header value. Both the name of the header and its value are ASCII
        // strings.
        $this->_headers = array();
        $this->_attrs = array();
        $this->_charset = settings::get("DEFAULT_CHARSET");
        if (!is_null($status))
            $this->statusCode = $status;
        if (is_null($contentType))
            $contentType = sprintf("%s; charset=%s",
                settings::get("DEFAULT_CONTENT_TYPE"),
                $this->_charset);
        if (is_string($content)) {
            $this->_container = array($content);
            $this->_isString = true;
        } else {
            $this->_container = $content;
            $this->_isString = false;
        }
        $this["Content-Type"] = $contentType;
        
        $this->_cookies = array();
    }
    
    /**
    * Full HTTP message, including headers.
    */
    function __toString() {
        return $this->toString();
    }
    
    function toString() {
        ob_implicit_flush(false);
        
        ob_start();
        
        foreach ($this->_cookies as $name => $c)
            setcookie($name,
                $c['value'], $c['expires'], $c['path'],
                $c['domain'], $c['secure'], $c['httponly']);
        
        foreach ($this->_headers as $header)
            header("{$header[0]}: {$header[1]}");
        
        print $this->getContent();
        
        $content = ob_get_clean();
        
        return $content;
    }
    
    //-- headers
    
    public function offsetExists($header) {
        return array_key_exists(strtolower($header), $this->_headers);
    }
    
    public function offsetGet($header) {
        return $this->_headers[strtolower($header)][1];
    }
    
    public function offsetSet($header, $value) {
        list($header, $value) = $this->_convertToASCII($header, $value);
        $this->_headers[strtolower($header)] = array($header, $value);
    }
    
    public function offsetUnset($header) {
        unset($this->_headers[strtolower($header)]);
    }
    
    //-- runtime attributes
    
    public function hasAttribute($name) {
        return array_key_exists($name, $this->_attrs);
    }
    
    public function getAttribute($name/*, $default*/) {
        if (array_key_exists($name, $this->_attrs))
            return $this->_attrs[$name];
        $args = func_get_args();
        if (count($args) > 1)
            return $args[1];
        throw new \OutOfBoundsException($name);
    }
    
    public function setAttribute($name, $value) {
        $this->_attrs[$name] = $value;
    }
    
    //-- cookies
    
    public function setSignedCookie($key, $value, $salt='', array $options=null) {
        $value = signing::get_cookie_signer($key . $salt)->sign($value);
        $this->setCookie($key, $value, $options);
    }
    
    /**
    * Sets a cookie.
    *
    * ``expires`` can be a string in the correct format or a ``DateTime``
    * object in UTC. If ``expires`` is a DateTime object then ``max_age``
    * will be calculated.
    */
    public function setCookie($key, $value='', array $options=null) {
        if (null === $options)
            $options = array();
        
        $defaults = array(
            'max_age'   => null,
            'expires'   => null,
            'domain'    => null,
            'path'      => '/',
            'secure'    => false,
            'httponly'  => false
        );
        
        extract(array_merge($defaults, $options));
        
        $cookie = array(
            'value'     => $value,
            'expires'   => 0,
            'domain'    => '',
            'path'      => '',
            'secure'    => false,
            'httponly'  => false,
        );
        
        if (null !== $expires) {
            if ($expires instanceof \DateTime) {
                $utcnow = new \DateTime(null, new \DateTimeZone(\DateTimeZone::UTC));
                $delta = $expires->diff($utcnow);
                $max_age = max(0,
                    $delta->days * 86400 +
                    $delta->h * 24 +
                    $delta->m * 60 +
                    $delta->s);
            } else if ($expires === 0) {
                // passing 0 will tell php to make the cookie expire at browser
                // close, which is not what we ment...
                $cookie['expires'] = time() - 86400; // yesterday
            } else {
                $cookie['expires'] = $expires;
            }
        }
        
        if (null !== $max_age) {
            if (!isset($cookie['expires']))
                $cookie['expires'] = time() + $max_age;
        }
        
        if (null !== $path)
            $cookie['path'] = $path;
        
        if (null !== $domain)
            $cookie['domain'] = $domain;
        
        if ($secure)
            $cookie['secure'] = true;
        
        if ($httponly)
            $cookie['httponly'] = true;
        
        $this->_cookies[$key] = $cookie;
    }
    
    public function deleteCookie($key, $path='/', $domain=null) {
        $this->setCookie($key, '', array(
            'expires' => 0,
            'path' => $path,
            'domain' => $domain,
        ));
    }
    
    protected function _convertToASCII(/*arg, ...*/) {
        $values = func_get_args();
        $headers = array();
        foreach ($values as $value) {
            if (encoding::is_unicode($value))
                $value = encoding::to_str($value, "utf-8");
            else
                $value = encoding::to_str($value, null); // use internal encoding
            if (0 !== preg_match("/[\r\n]+/s", $value))
                throw new BadHeaderError(
                    "Header values can't contain newlines (got {$value})");
            $headers[] = $value;
        }
        return $headers;
    }
    
    //-- content
    
    public function getContent() {
        $content = implode("", $this->_container);
        if (isset($this["Content-Encoding"]))
            return $content;
        return encoding::to_str($content, $this->_charset);
    }
    
    public function setContent($value) {
        $this->_container = array($value);
        $this->_isString = true;
    }
    
    public function write($content) {
        if (!$this->_isString)
            throw new \Exception("This ".__CLASS__." instance is not writable.");
        $this->_container[] = $content;
    }
    
    public function close() {
        if (method_exists($this->_container, "close"))
            $this->_container->close();
    }
}

class HttpResponseRedirect extends HttpResponse {
    public $statusCode = 302;

    function __construct($redirectTo) {
        parent::__construct();
        $this['Location'] = encoding::iri_to_uri($redirectTo);
    }
}

class HttpResponsePermanentRedirect extends HttpResponse {
    public $statusCode = 301;

    function __construct($redirectTo) {
        parent::__construct();
        $this['Location'] = encoding::iri_to_uri($redirectTo);
    }
}

class HttpResponseNotModified extends HttpResponse {
    public $statusCode = 304;
}

class HttpResponseBadRequest extends HttpResponse {
    public $statusCode = 400;
}

class HttpResponseNotFound extends HttpResponse {
    public $statusCode = 404;
}

class HttpResponseForbidden extends HttpResponse {
    public $statusCode = 403;
}

class HttpResponseNotAllowed extends HttpResponse {
    public $statusCode = 405;
    function __construct($permittedMethods) {
        parent::__construct();
        $this['Allow'] = implode(", ", $permittedMethods);
    }
}

class HttpResponseGone extends HttpResponse {
    public $statusCode = 410;
}

class HttpResponseServerError extends HttpResponse {
    public $statusCode = 500;
}

}

namespace bjork {

final class http {
    /**
    * Return a list of strings for the valid HTTP/1.1 request
    * methods (RFC2616).
    * 
    * http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html
    */
    public static function valid_methods() {
        return array(
            "GET", "HEAD", "POST", "PUT", "DELETE",
            "TRACE", "OPTIONS", "CONNECT",
        );
    }
}

}
