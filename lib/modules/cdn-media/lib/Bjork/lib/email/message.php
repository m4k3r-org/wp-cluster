<?php

namespace email\message;

use strutils;

use email\charset\Charset,
    email\generator\Generator,
    email\encoders,
    email\utils;

const SEMISPACE = '; ';
const TSPECIALS_RE = '/[ \(\)<>@,;:\\"\/\[\]\?=]/u';

/**
* Prepare string to be used in a quoted string.
* 
* Turns backslash and double quote characters into quoted pairs.  These
* are the only characters that need to be quoted inside a quoted string.
* Does not add the surrounding double quotes.
*/
function quote($str) {
    return str_replace('"', '\\"', str_replace('\\', '\\\\', $str));
}

/**
* Split header parameters.  BAW: this may be too simple.  It isn't
* strictly RFC 2045 (section 5.1) compliant, but it catches most headers
* found in the wild.  We may eventually need a full fledged parser
* eventually.
*/
function splitparam($param) {
    list($a, $sep, $b) = strutils::partition($param, ';');
    if (!$sep)
        return array(trim($a), null);
    return array(trim($a), trim($b));
}

/*
* Convenience function to format and return a key=value pair.
*
* This will quote the value if needed or if quote is true.  If value is a
* three tuple (charset, language, value), it will be encoded according
* to RFC2231 rules.
*/
function formatparam($param, $value=null, $quote=true) {
    if ($value !== 0 && $value) {
        if (is_array($value)) {
            $param .= '*';
            $value = utils::encode_rfc2231($value[2], $value[0], $value[1]);
        }
        if ($quote || preg_match(TSPECIALS_RE, $value))
            return sprintf('%s="%s"', $param, quote($value));
        else
            return sprintf('%s=%s', $param, $value);
    } else {
        return $param;
    }
}

function parseparam($str) {
    static $o = 0;
    $o++;
    $s = $str;
    $plist = array();
    while (strutils::startswith($s, ';')) {
        $s = ltrim($s, ';');
        $end = mb_strpos($s, ';');
        while ($end > 0 && (substr_count($s, '"', 0, $end) - substr_count($s, '\\', 0, $end)) % 2)
            $end = mb_strpos($s, ';', $end + 1);
        if (false === $end)
            $end = mb_strlen($s);
        $f = mb_substr($s, 0, $end);
        if ($index = mb_strpos($f, '=')) {
            $i = $index;
            $f = mb_strtolower(trim(mb_substr($f, 0, $i))) . '=' . trim(mb_substr($f, $i+1));
        }
        $plist[] = trim($f);
        $s = mb_substr($s, $end);
    }
    return $plist;
}

/*
* This is different than utils.collapse_rfc2231_value() because it doesn't
* try to convert the value to a unicode.  Message.get_param() and
* Message.get_params() are both currently defined to return the tuple in
* the face of RFC 2231 parameters.
*/
function unquotevalue($value) {
    if (is_array($value))
        return array($value[0], $value[1], utils::unquote($value[2]));
    return utils::unquote($value);
}

/**
* Basic message object.
* 
* A message object is defined as something that has a bunch of RFC 2822
* headers and a payload.  It may optionally have an envelope header
* (a.k.a. Unix-From or From_ header).  If the message is a container (i.e. a
* multipart or a message/rfc822), then the payload is a list of Message
* objects, otherwise it is a string.
* 
* Message objects implement part of the `mapping' interface, which assumes
* there is exactly one occurrence of the header per message.  Some headers
* do in fact appear multiple times (e.g. Received) and for those headers,
* you must use the explicit API to set or get all the headers.  Not all of
* the mapping methods are implemented.
*/
abstract class Message implements \ArrayAccess {
    
    protected $headers;
    
    protected
        $payload,
        $charset,
        $preample,
        $epilogue,
        $defects,
        $default_type;
    
    function __construct() {
        $this->headers = array();
        
        $this->payload = null;
        $this->charset = null;
        // defaults for multipart messages
        $this->preample = null;
        $this->epilogue = null;
        $this->defects = array();
        // default content type
        $this->default_type = 'text/plain';
    }
    
    /**
    * Return the entire formatted message as a string.
    * This includes the headers, body, and envelope header.
    */
    function __toString() {
        return $this->asString(true);
    }
    
    /**
    * Return the entire formatted message as a string.
    */
    public function &asString($unixfrom=false) {
        // @TODO: streams
        $buf = '';
        $g = new Generator($buf);
        $g->flatten($this, $unixfrom);
        return $buf;
    }
    
    /**
    * Return True if the message consists of multiple parts.
    */
    public function isMultipart() {
        return is_array($this->payload);
    }
    
    //
    // Array access interface ------------------------------------------------
    //
    
    function offsetGet($name) {
        return $this->get($name);
    }
    
    function offsetSet($name, $val) {
        $this->headers[] = array($name, $val);
    }
    
    function offsetUnset($name) {
        $headers = array();
        $name = strtolower($name);
        foreach ($this->headers as $pair) {
            if (strtolower($pair[0]) === $name)
                continue;
            $headers[] = $pair;
        }
        $this->headers = $headers;
    }
    
    function offsetExists($name) {
        return $this->hasKey($name);
    }
    
    /**
    * Return true if the message contains the header.
    */
    public function hasKey($name) {
        $name = strtolower($name);
        foreach ($this->headers as $pair) {
            if (strtolower($pair[0]) === $name)
                return true;
        }
        return false;
    }
    
    /**
    * Return a list of all the message's header field names.
    *
    * These will be sorted in the order they appeared in the original
    * message, or were added to the message, and may contain duplicates.
    * Any fields deleted and re-inserted are always appended to the header
    * list.
    */
    public function keys() {
        $keys = array();
        foreach ($this->headers as $pair)
            $keys[] = $pair[0];
        return $keys;
    }
    
    /**
    * Return a list of all the message's header values.
    *
    * These will be sorted in the order they appeared in the original
    * message, or were added to the message, and may contain duplicates.
    * Any fields deleted and re-inserted are always appended to the header
    * list.
    */
    public function values() {
        $values = array();
        foreach ($this->headers as $pair)
            $values[] = $pair[1];
        return $values;
    }
    
    /**
    * Get all the message's header fields and values.
    *
    * These will be sorted in the order they appeared in the original
    * message, or were added to the message, and may contain duplicates.
    * Any fields deleted and re-inserted are always appended to the header
    * list.
    */
    public function items() {
        return $this->headers;
    }
    
    /**
    * Get a header value.
    * 
    * Like offsetGet() but return $default instead of null when the field
    * is missing.
    */
    public function get($name, $default=null) {
        $name = strtolower($name);
        foreach ($this->headers as $pair) {
            if (strtolower($pair[0]) === $name)
                return $pair[1];
        }
        return $default;
    }
    
    /**
    * Extended header setting.
    * 
    * $name is the header field to add. $params can be used to set additional
    * parameters for the header field, with underscores converted to dashes.
    * Normally the parameter will be added as key="value" unless value is
    * null, in which case only the key will be added. If a parameter value
    * contains non-ASCII characters it must be specified as a three-tuple
    * of (charset, language, value), in which case it will be encoded
    * according to RFC2231 rules.
    * 
    * Example:
    * 
    *   $msg->addHeader('content-disposition', 'attachment', array(
    *       'filename' => 'bud.gif'
    *   ));
    */
    public function addHeader($name, $value, array $params=null) {
        if (!$params)
            $params = array();
        $parts = array();
        foreach ($params as $k => $v) {
            $k = str_replace('_', '-', $k);
            if (null === $v)
                $parts[] = $k;
            else
                $parts[] = formatparam($k, $v);
        }
        if (null !== $value)
            array_unshift($parts, $value);
        $this->headers[] = array($name, implode(SEMISPACE, $parts));
    }
    
    /**
    * Replace a header.
    * 
    * Replace the first matching header found in the message, retaining
    * header order and case.  If no matching header was found, a KeyError is
    * raised.
    */
    public function replaceHeader($name, $value) {
        if (!$this->hasKey($name))
            throw new \OutOfBoundsException($name);
        $name = strtolower($name);
        for ($i = 0; $i < count($this->headers); $i++) {
            if (strtolower($this->headers[$i][0]) == $name) {
                $this->headers[$i][0] = $name;
                $this->headers[$i][1] = $value;
                break;
            }
        }
    }
    
    //
    // Payload manipulation --------------------------------------------------
    //
    
    /**
    * Add the given payload to the current payload.
    * 
    * The current payload will always be a list of objects after this method
    * is called.  If you want to set the payload to a scalar object, use
    * set_payload() instead.
    */
    public function attach($payload) {
        if (null === $this->payload)
            $this->payload = array($payload);
        else
            $this->payload[] = $payload;
    }
    
    /**
    * Return a reference to the payload.
    * 
    * The payload will either be a list object or a string.  If you mutate
    * the list object, you modify the message's payload in place.  Optional
    * i returns that index into the payload.
    * 
    * Optional decode is a flag indicating whether the payload should be
    * decoded or not, according to the Content-Transfer-Encoding header
    * (default is False).
    * 
    * When True and the message is not a multipart, the payload will be
    * decoded if this header's value is `quoted-printable' or `base64'.  If
    * some other encoding is used, or the header is missing, or if the
    * payload has bogus data (i.e. bogus base64 or uuencoded data), the
    * payload is returned as-is.
    * 
    * If the message is a multipart and the decode flag is True, then None
    * is returned.
    */
    public function &getPayload($i=null, $decode=false) {
        if (null === $i)
            $payload = $this->payload;
        else if (!is_array($this->payload))
            throw new \UnexpectedValueException(
                'Expected list, got '. gettype($this->payload));
        else
            $payload = $this->payload[$i];
        
        if ($decode) {
            throw new \BadMethodCallException('Not implemented');
        }
        
        // Everything else, including encodings with 8bit or 7bit are returned
        // unchanged.
        return $payload;
    }
    
    /**
    * Set the payload to the given value.
    * 
    * Optional charset sets the message's default character set.  See
    * set_charset() for details.
    */
    public function setPayload($payload, $charset=null) {
        $this->payload = $payload;
        if (null !== $charset)
            $this->setCharset($charset);
    }
    
    /**
    * Return the charset of the message's payload.
    */
    public function getCharset() {
        return $this->charset;
    }
    
    /**
    * Set the charset of the payload to a given character set.
    *
    * $charset can be a Charset instance, a string naming a character set, or
    * None.  If it is a string it will be converted to a Charset instance.
    * If charset is None, the charset parameter will be removed from the
    * Content-Type field.  Anything else will generate a TypeError.
    *
    * The message will be assumed to be of type text/* encoded with
    * charset.input_charset. It will be converted to charset.output_charset
    * and encoded properly, if needed, when generating the plain text
    * representation of the message. MIME headers (MIME-Version,
    * Content-Type, Content-Transfer-Encoding) will be added as needed.
    */
    public function setCharset($charset) {
        if (null === $charset) {
            $this->deleteParameter('charset');
            $this->charset = null;
            return;
        }
        
        if (!($charset instanceof Charset))
            $charset = new Charset($charset);
        
        $this->charset = $charset;
        
        if (!$this->hasKey('MIME-Version'))
            $this->addHeader('MIME-Version', '1.0');
        if (!$this->hasKey('Content-Type'))
            $this->addHeader('Content-Type', 'text/plain', array(
                'charset' => $charset->getMIMEOutputCharset()));
        else
            $this->setParameter('charset', $charset->getMIMEOutputCharset());
        
        if (strval($charset) !== $charset->getOutputCharset())
            $this->payload = $charset->bodyEncode($this->payload);
        
        if (!$this->hasKey('Content-Transfer-Encoding')) {
            $cte = $charset->getBodyTransferEncoding();
            if (null === $cte) {
                encoders::encode_7or8bit($this);
            } else {
                $this->payload = $charset->bodyEncode($this->payload);
                $this->addHeader('Content-Transfer-Encoding', $cte);
            }
        }
    }
    
    /**
    * Return the `default' content type.
    * 
    * Most messages have a default content type of text/plain, except for
    * messages that are subparts of multipart/digest containers.  Such
    * subparts have a default content type of message/rfc822.
    */
    public function getDefaultType() {
        return $this->default_type;
    }
    
    /**
    * Set the `default' content type.
    * 
    * ctype should be either "text/plain" or "message/rfc822", although this
    * is not enforced.  The default content type is not stored in the
    * Content-Type header.
    */
    public function setDefaultType($ctype) {
        $this->default_type = $ctype;
    }
    
    /**
    * Return the message's content type.
    * 
    * The returned string is coerced to lower case of the form
    * `maintype/subtype'.  If there was no Content-Type header in the
    * message, the default type as given by get_default_type() will be
    * returned.  Since according to RFC 2045, messages always have a default
    * type this will always return a value.
    * 
    * RFC 2045 defines a message's default type to be text/plain unless it
    * appears inside a multipart/digest container, in which case it would be
    * message/rfc822.
    */
    public function getContentType() {
        $value = $this->get('content-type', null);
        if (null === $value) 
            return $this->getDefaultType();
        list($ctype, $_) = splitparam($value);
        $ctype = strtolower($ctype);
        if (count(explode('/', $ctype)) !== 2)
            return 'text/plain';
        return $ctype;
    }
    
    /**
    *
    * Return the message's main content type.
    *
    * This is the `maintype' part of the string returned by
    * get_content_type().
    */
    public function getContentMainType() {
        $parts = explode('/', $this->getContentType());
        return $parts[0];
    }
    
    /**
    *
    * Return the message's sub-content type.
    *
    * This is the `subtype' part of the string returned by
    * get_content_type().
    */
    public function getContentSubType() {
        $parts = explode('/', $this->getContentType());
        return $parts[1];
    }
    
    /**
    * Set the main type and subtype for the Content-Type header.
    * 
    * type must be a string in the form "maintype/subtype", otherwise a
    * ValueError is raised.
    * 
    * This method replaces the Content-Type header, keeping all the
    * parameters in place.  If requote is False, this leaves the existing
    * header's quoting as is.  Otherwise, the parameters will be quoted (the
    * default).
    * 
    * An alternative header can be specified in the header argument.  When
    * the Content-Type header is set, we'll always also add a MIME-Version
    * header.
    */
    public function setType($type, $header='Content-Type', $requote=true) {
        // Set the Content-Type, you get a MIME-Version
        if (strtolower($header) == 'content-type') {
            unset($this['mime-version']);
            $this['MIME-Version'] = '1.0';
        }
        if (!$this->hasKey($header)) {
            $this[$header] = $type;
            return;
        }
        $params = $this->getParameters(null, $header, $requote);
        unset($this[$header]);
        $this[$header] = $type;
        // Skip the first param; it's the old type.
        foreach (array_slice($params, 1) as $param)
            $this->setParameter($p[0], $p[1]);
    }
    
    /**
    * Return the charset parameter of the Content-Type header.
    * 
    * The returned string is always coerced to lower case.  If there is no
    * Content-Type header, or if that header has no charset parameter,
    * failobj is returned.
    */
    public function getContentCharset($default=null) {
        $charset = $this->getParameter('charset', null);
        if (null === $charset)
            return $default;
        if (is_array($charset)) {
            throw new \Exception('Not implemented');
        }
        return strtolower($charset);
    }
    
    /**
    * Return the filename associated with the payload if present.
    * 
    * The filename is extracted from the Content-Disposition header's
    * `filename' parameter, and it is unquoted.  If that header is missing
    * the `filename' parameter, this method falls back to looking for the
    * `name' parameter.
    */
    public function getFilename($default=null) {
        $filename = $this->getParameter('filename', null, 'content-disposition');
        if (null === $filename)
            $filename = $this->getParameter('name', null, 'content-type');;
        if (null === $filename)
            return $default;
        return trim(utils::collapse_rfc2231_value($filename));
    }
    
    /**
    * Return the boundary associated with the payload if present.
    * 
    * The boundary is extracted from the Content-Type header's `boundary'
    * parameter, and it is unquoted.
    */
    public function getBoundary($default=null) {
        $boundary = $this->getParameter('boundary', null);
        if (null === $boundary)
            return $default;
        return rtrim(utils::collapse_rfc2231_value($boundary));
    }
    
    /**
    * Set the boundary parameter in Content-Type to 'boundary'.
    * 
    * This is subtly different than deleting the Content-Type header and
    * adding a new one with a new boundary parameter via add_header().  The
    * main difference is that using the set_boundary() method preserves the
    * order of the Content-Type header in the original message.
    * 
    * HeaderParseError is raised if the message has no Content-Type header.
    */
    public function setBoundary($boundary) {
        $params = $this->getParametersPreserve(null, 'content-type');
        if (null === $params)
            throw new \Exception('No Content-Type header found');
        $newparams = array();
        $foundp = false;
        foreach ($params as $param) {
            list($pk, $pv) = $param;
            if (strtolower($pk) == 'boundary') {
                $newparams[] = array('boundary', "\"{$boundary}\"");
                $foundp = true;
            } else {
                $newparams[] = array($pk, $pv);
            }
        }
        if (!$foundp) {
            // The original Content-Type header had no boundary attribute.
            // Tack one on the end.
            $newparams[] = array('boundary', "\"{$boundary}\"");
        }
        // Replace the existing Content-Type header with the new value
        $newheaders = array();
        foreach ($this->headers as $pair) {
            list($h, $v) = $pair;
            if (strtolower($h) == 'content-type') {
                $parts = array();
                foreach ($newparams as $p) {
                    list($k, $v) = $p;
                    if ($v === '')
                        $parts[] = $k;
                    else
                        $parts[] = "{$k}={$v}";
                }
                $newheaders[] = array($h, implode(SEMISPACE, $parts));
            } else {
                $newheaders[] = array($h, $v);
            }
        }
        $this->headers = $newheaders;
    }
    
    //
    // Header parameters -----------------------------------------------------
    //
    
    /**
    * Return the message's Content-Type parameters, as a list.
    * 
    * The elements of the returned list are 2-tuples of key/value pairs, as
    * split on the `=' sign.  The left hand side of the `=' is the key,
    * while the right hand side is the value.  If there is no `=' sign in
    * the parameter the value is the empty string.  The value is as
    * described in the get_param() method.
    * 
    * Optional failobj is the object to return if there is no Content-Type
    * header.  Optional header is the header to search instead of
    * Content-Type.  If unquote is True, the value is unquoted.
    */
    public function getParameters($default=null, $header='content-type', $unquote=true) {
        $params = $this->getParametersPreserve(null, $header);
        if (null === $params)
            return $default;
        if ($unquote) {
            return array_map(function($pair) {
                return array($pair[0], unquotevalue($pair[1]));
            }, $params);
        }
        return $params;
    }
    
    /**
    * Return the parameter value if found in the Content-Type header.
    * 
    * Optional $default is the object to return if there is no Content-Type
    * header, or the Content-Type header has no such parameter.  Optional
    * header is the header to search instead of Content-Type.
    * 
    * Parameter keys are always compared case insensitively.  The return
    * value can either be a string, or a 3-tuple if the parameter was RFC
    * 2231 encoded.  When it's a 3-tuple, the elements of the value are of
    * the form (CHARSET, LANGUAGE, VALUE).  Note that both CHARSET and
    * LANGUAGE can be None, in which case you should consider VALUE to be
    * encoded in the us-ascii charset.  You can usually ignore LANGUAGE.
    * 
    * Your application should be prepared to deal with 3-tuple return
    * values, and can convert the parameter to a Unicode string like so:
    * 
    *     param = msg.get_param('foo')
    *     if isinstance(param, tuple):
    *         param = unicode(param[2], param[0] or 'us-ascii')
    * 
    * In any case, the parameter value (either the returned string, or the
    * VALUE item in the 3-tuple) is always unquoted, unless unquote is set
    * to False.
    */
    public function getParameter($name, $default=null, $header='content-type', $unquote=true) {
        if (!$this->hasKey($header))
            return $default;
        foreach ($this->getParametersPreserve($default, $header) as $pair) {
            list($k, $v) = $pair;
            if (strtolower($k) === strtolower($name)) {
                if ($unquote)
                    return unquotevalue($v);
                return $v;
            }
        }
        return $default;
    }
    
    /**
    * Set a parameter in the Content-Type header.
    *
    * If the parameter already exists in the header, its value will be
    * replaced with the new value.
    * 
    * If header is Content-Type and has not yet been defined for this
    * message, it will be set to "text/plain" and the new parameter and
    * value will be appended as per RFC 2045.
    * 
    * An alternate header can specified in the header argument, and all
    * parameters will be quoted as necessary unless requote is False.
    * 
    * If charset is specified, the parameter will be encoded according to RFC
    * 2231.  Optional language specifies the RFC 2231 language, defaulting
    * to the empty string.  Both charset and language should be strings.
    */
    public function setParameter($name, $value, $header='Content-Type', $requote=true,
        $charset=null, $language='')
    {
        if (!is_array($value) && $charset)
            $value = array($charset, $language, $value);
        
        if (!$this->hasKey($header) && strtolower($header) == 'content-type')
            $ctype = 'text/plain';
        else
            $ctype = $this->get($header);
        if (!$this->getParameter($name, null, $header)) {
            if (!$ctype)
                $ctype = formatparam($name, $value, $requote);
            else
                $ctype = implode(SEMISPACE, array($ctype, formatparam(
                    $name, $value, $requote)));
        } else {
            $ctype = '';
            foreach ($this->getParameters(null, $header, $requote) as $pair) {
                list($old_param, $old_value) = $pair;
                $append_param = '';
                if (strtolower($old_param) == strtolower($name))
                    $append_param = formatparam($name, $value, $requote);
                else
                    $append_param = formatparam($old_param, $old_value, $requote);
                if (!$ctype)
                    $ctype = $append_param;
                else
                    $ctype = implode(SEMISPACE, array($ctype, $append_param));
            }
        }
        if ($ctype != $this->get($header)) {
            unset($this[$header]);
            $this[$header] = $ctype;
        }
    }
    
    /**
    * Remove the given parameter completely from the Content-Type header.
    * 
    * The header will be re-written in place without the parameter or its
    * value. All values will be quoted as necessary unless requote is
    * False.  Optional header specifies an alternative to the Content-Type
    * header.
    */
    public function deleteParameter($name, $header='content-type', $requote=true) {
        if (!$this->hasKey($header))
            return;
        $new_ctype = '';
        foreach ($this->getParameters(null, $header, $requote) as $pair) {
            list($p, $v) = $pair;
            if (strtolower($p) != strtolower($name)) {
                if (!$new_ctype)
                    $new_ctype = formatparam($p, $v, $requote);
                else
                    $new_ctype = implode(SEMISPACE, array($new_ctype, formatparam(
                        $p, $v, $requote)));
            }
        }
        if ($new_ctype != $this->get($header)) {
            unset($this[$header]);
            $this[$header] = $new_ctype;
        }
    }
    
    protected function getParametersPreserve($default, $header) {
        // Like get_params() but preserves the quoting of values.
        
        $value = $this->get($header, null);
        if (null === $value)
            return $default;
        
        $params = array();
        foreach (parseparam(';' . $value) as $p) {
            $parts = strutils::split($p, '=', 1);
            if (count($parts) == 2) {
                list($name, $val) = $parts;
                $name = trim($name);
                $val = trim($val);
            } else {
                $name = trim($p);
                $val = '';
            }
            $params[] = array($name, $val);
        }
        $params = utils::decode_params($params);
        return $params;
    }
}
