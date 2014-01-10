<?php

namespace bjork\core\urlresolvers {

use strutils;

use bjork\conf\urls,
    bjork\core\exceptions\ImproperlyConfigured,
    bjork\core\exceptions\ViewDoesNotExist,
    bjork\http\Http404,
    bjork\utils\datastructures\MultiValueDict,
    bjork\utils\encoding,
    bjork\utils\regex_helper,
    bjork\utils\translation;

const REGEX_FORMAT = '#%s#u';

class Resolver404 extends Http404 {
    var $tried = null,
        $path = null;
    
    function __construct($path, $tried=null) {
        $this->path = $path;
        $this->tried = $tried;
        parent::__construct($path);
    }
    
    function getPath() {
        return $this->path;
    }
    
    function getTriedPaths() {
        return $this->tried;
    }
}

class NoReverseMatch extends \Exception {}

class ResolverMatch implements \ArrayAccess {
    private $props, $func, $args, $kwargs, $urlName;
    
    function __construct($func, $args, $kwargs, $urlName=null) {
        $this->func = $func;
        $this->args = $args;
        $this->kwargs = $kwargs;
        if (empty($urlName))
            if (is_callable($func, false, $n))
                $urlName = $n;
            else
                $urlName = $func;
        $this->urlName = $urlName;
    }
    
    function __toString() {
        return sprintf("%s(view=%s, args=%s, kwargs=%s, viewName='%s')",
            __CLASS__, $this->func, $this->args, $this->kwargs, $this->urlName);
    }
    
    function __get($k) {
        $props = array(
            'viewName' => $this->urlName,
            'view'     => $this->func,
            'args'     => $this->args,
            'kwargs'   => $this->kwargs
        );
        return $props[$k];
    }
    
    public function offsetExists($k) {
        return in_array($key, range(0, 2));
    }
    
    public function offsetGet($k) {
        $props = array(
            'view'     => $this->func,
            'args'     => $this->args,
            'kwargs'   => $this->kwargs);
        $idxProps = array_values($props);
        return array_key_exists($k, $props)
          ? $props[$k]
          : $idxProps[$k];
    }
    
    public function offsetSet($k, $v) {
        throw new \Exception(__CLASS__.' is immutable.');
    }
    
    public function offsetUnset($k) {
        throw new \Exception(__CLASS__.' is immutable.');
    }
}

/**
* A class to provide a default regex property which can vary by active
* language.
*/
class LocaleRegexProvider {
    protected $_regex_dict, $_pattern;
    
    function __construct($regex) {
        $this->_pattern = $regex;
        $this->_regex_dict = array();
    }
    
    function __get($k) {
        $method = "get{$k}";
        return $this->$method();
    }
    
    public function getPattern() {
        return $this->_pattern;
    }
    
    /**
    * Returns a "compiled" regular expression, depending upon the activated
    * language-code.
    */
    public function getRegex() {
        $language_code = translation::get_language();
        if (!isset($this->_regex_dict[$language_code])) {
            $this->_regex_dict[$language_code] = sprintf(REGEX_FORMAT, $this->getPattern());
        }
        return $this->_regex_dict[$language_code];
    }
}

class RegexURLPattern extends LocaleRegexProvider {
    public $name;
    
    public $callback, $callbackName, $kwargs;
    
    function __construct($regex, $callback, $defaultKwargs=null, $name=null) {
        parent::__construct($regex);
        $this->callback = $callback;
        $this->defaultKwargs = empty($defaultKwargs) ? array() : $defaultKwargs;
        $this->name = $name;
        // check whether a _syntactically_ valid callback is being passed. 
        if (is_array($callback) && is_callable($callback, true)) {
            $callbackName = null;
        } else if (is_callable($callback)) {
            $callbackName = null;
        } else {
            $callbackName = $callback;
            $callback = null;
        }
        $this->callback = $callback;
        $this->callbackName = $callbackName;
    }
    
    function __toString() {
        return sprintf('<%s %s %s>', __CLASS__, $this->name, $this->regex);
    }
    
    function addPrefix($prefix) {
        if (empty($prefix) || empty($this->callbackName))
            return;
        $this->callbackName = $prefix . '\\' . $this->callbackName;
    }
    
    function resolve($path) {
        $params = null;
        $match = preg_match($this->regex, $path, $params);
        if ($match) {
            // $params[0] contains the whole string -- discard it
            array_shift($params);
            
            // Split indexed and named params
            $keys = array_keys($params);
            $kwargs = array();
            $args = array();
            $i = 0;
            foreach ($keys as $key) {
                if ($i === $key) { // could just do a is_string($key)...
                    $args[$i] = $params[$key];
                    $i++;
                } else {
                    $kwargs[$key] = $params[$key];
                }
            }
            
            // see if the regex has named captures
            // we could just do a count($kwargs), but anyway...
            if (preg_match('/\(\?P<[\w]+>.*\)/u', $this->regex)) {
            // if (count($kwargs) > 0) {
                // Yes, we do have named captures -- clear indexed
                $args = array();
            }
            
            $kwargs = array_merge($kwargs, $this->defaultKwargs);
            
            if (empty($this->callback))
                $this->loadCallback();
            
            $r = new ResolverMatch($this->callback, $args, $kwargs, $this->name);
            return $r;
        }
        
        return null;
    }
    
    function loadCallback() {
        $path = str_replace('\\', DIRECTORY_SEPARATOR, $this->callbackName);
        $parts = explode(DIRECTORY_SEPARATOR, $path);
        $viewname = array_pop($parts);
        $path = implode(DIRECTORY_SEPARATOR, $parts);
        try {
            require_once "{$path}.php";
        } catch (\ErrorException $e) {
            throw new ViewDoesNotExist(
                "Could not import '{$path}.php'. Error was: {$e->getMessage()}");
        }
        if (is_callable($this->callbackName)) {
            $this->callback = $this->callbackName;
            $this->callbackName = null;
            return;
        } else {
            throw new ViewDoesNotExist(
                "No view named '{$this->callbackName}' is defined in '{$path}.php'.");
        }
    }
}

class RegexURLResolver extends LocaleRegexProvider {
    public $urlconfName, $urlPatterns, $params, $callback, $reverseDict;
    
    function __construct($regex, $urlconfName, $defaultParams=null) {
        parent::__construct($regex);
        $this->urlconfName = $urlconfName;
        $this->params = empty($defaultParams) ? array() : $defaultParams;
        $this->callback = null;
        if (is_callable($urlconfName)) {
            $urlPatterns = call_user_func($urlconfName);
            $urlconfName = null;
        } else if (is_array($urlconfName)) {
            $urlPatterns = $urlconfName;
            $urlconfName = null;
        } else {
            $urlPatterns = null;
        }
        $this->urlconfName = $urlconfName;
        $this->urlPatterns = $urlPatterns;
        
        $this->reverseDict = array();
    }
    
    function __toString() {
        return sprintf('<%s %s %s>', __CLASS__, $this->urlconfName, $this->regex);
    }
    
    public function reverse($lookup_view, array $args) {
        if (is_callable($lookup_view, false, $n))
            $lookup_view = $n;
        $possibilities = $this->getReverseDict()->getList($lookup_view);
        
        foreach ($possibilities as $p) {
            list($possibility, $pattern, $defaults) = $p;
            foreach ($possibility as $pp) {
                list($result, $params) = $pp;
                if (!empty($args)) {
                    if (count($args) != count($params))
                        continue;
                    
                    $kwargs = array();
                    
                    $keys = array_keys($args);
                    if (count($args) > 0 && is_string($keys[0])) {
                        // if we received an associative array compare
                        // the keys with the params in the url
                        foreach ($params as $key) {
                            if (!array_key_exists($key, $args))
                                continue 2;
                            $kwargs[$key] = $args[$key];
                        }
                    } else {
                        $kwargs = array_combine($params, $args);
                    }
                    $candidate = strutils::format($result, $kwargs);
                } else {
                    if (count(array_merge(array_keys($args), array_keys($defaults))) !=
                        count(array_merge($params, array_keys($defaults))))
                        continue;
                    $matches = true;
                    foreach ($defaults as $k => $v) {
                        if (isset($args[$k])) {
                            if ($args[$k] != $v) {
                                $matches = false;
                                break;
                            }
                        }
                    }
                    if (!$matches)
                        continue;
                    $candidate = strutils::format($result, $args);
                }
                if (preg_match("#^{$pattern}#u", $candidate))
                    return $candidate;
            }
        }
        
        throw new NoReverseMatch(sprintf(
            "Reverse for '%s' with arguments '%s' not found.",
            $lookup_view,
            array_reduce($args, function($s, $elem) {
                try {
                    $repr = print_r($elem, true);
                } catch (\Exception $e) {
                    $repr = '***INVALID***';
                }
                if ($s === null)
                    return $repr;
                return "{$s}, {$repr}";
            }, null)
        ));
    }
    
    public function resolve($path) {
        $tried = array();
        $params = array();
        $match = preg_match($this->regex, $path, $params, PREG_OFFSET_CAPTURE);
        if ($match) {
            $end = $params[0][1] + strlen($params[0][0]);
            $newPath = substr($path, $end);
            $patterns = $this->getURLPatterns();
            foreach ($patterns as $pattern) {
                try {
                    $subMatch = $pattern->resolve($newPath);
                } catch (Resolver404 $e) {
                    $subTried = $e->getTriedPaths();
                    if (!is_null($subTried)) {
                        foreach ($subTried as $p)
                            $tried[] = array_merge(array($pattern), $p);
                    } else
                        $tried[] = array($pattern);
                    continue;
                }
                if ($subMatch) {
                    $subMatchKwargs = array();
                    foreach ($params as $key => $value)
                        if (is_string($key))
                            $subMatchKwargs[encoding::to_str($key)] = $value[0];
                    $subMatchKwargs = array_merge($subMatchKwargs, $this->params);
                    foreach ($subMatch->kwargs as $k=>$v)
                        $subMatchKwargs[encoding::to_str($k)] = $v;
                    $r = new ResolverMatch(
                        $subMatch->view,
                        $subMatch->args,
                        $subMatchKwargs,
                        $subMatch->viewName);
                    return $r;
                }
                $tried[] = array($pattern);
            }
            throw new Resolver404($path, $tried);
        }
        throw new Resolver404($path);
    }
    
    public function resolve404() {
        require_once 'bjork/views/defaults.php';
        return array(urls::handler404, array());
    }
    
    public function resolve500() {
        require_once 'bjork/views/defaults.php';
        return array(urls::handler500, array());
    }
    
    public function getURLPatterns() {
        if (!empty($this->urlPatterns))
            return $this->urlPatterns;
        $urlPatterns = include $this->urlconfName;
        if (!is_array($urlPatterns))
            throw new ImproperlyConfigured(
                "The included urlconf {$this->urlconfName} doesn't have any ".
                "patterns in it.");
        $this->urlPatterns = $urlPatterns;
        $this->urlconfName = null;
        return $urlPatterns;
    }
    
    protected function getReverseDict() {
        $language_code = translation::get_language();
        if (!isset($this->reverseDict[$language_code]))
            $this->populate();
        return $this->reverseDict[$language_code];
    }
    
    protected function populate() {
        $lookups = new MultiValueDict();
        $patterns = array_reverse($this->getURLPatterns());
        $language_code = translation::get_language();
        foreach ($patterns as $pattern) {
            $p_pattern = ltrim($pattern->pattern, '^');
            if ($pattern instanceof RegexURLResolver) {
                $parent = regex_helper::normalize($pattern->pattern);
                $iter = $pattern->getReverseDict()->getIterator();
                foreach ($iter as $name => $v) {
                    foreach ($pattern->getReverseDict()->getList($name) as $v) {
                        list($matches, $pat, $defaults) = $v;
                        $new_matches = array();
                        foreach ($parent as $v) {
                            list($piece, $p_args) = $v;
                            $mm = array();
                            foreach ($matches as $m) {
                                list($suffix, $args) = $m;
                                $mm[] = array(
                                    $piece . $suffix,
                                    array_merge($p_args, $args),
                                );
                            }
                            array_splice($new_matches, count($new_matches), 0, $mm);
                        }
                        $lookups->appendList($name, array(
                            $new_matches,
                            $p_pattern . $pat,
                            array_merge($defaults, $pattern->params),
                        ));
                    }
                }
            } else {
                $bits = regex_helper::normalize($p_pattern);
                
                // make up a key
                if (!empty($pattern->callbackName))
                    $k = $pattern->callbackName;
                else if (is_callable($pattern->callback, false, $n))
                    $k = $n;
                else
                    $k = strval($pattern->callback);
                
                $lookups->appendList($k, array(
                    $bits,
                    $p_pattern,
                    $pattern->defaultKwargs,
                ));
                
                if (!is_null($pattern->name))
                    $lookups->appendList($pattern->name, array(
                        $bits,
                        $p_pattern,
                        $pattern->defaultKwargs,
                    ));
            }
        }
        $this->reverseDict[$language_code] = $lookups;
    }
}

/**
* A URL resolver that always matches the active language code as URL prefix.
* 
* Rather than taking a regex argument, we just override the ``regex``
* function to always return the active language-code as regex.
*/
class LocaleRegexURLResolver extends RegexURLResolver {
    function __construct($urlconfName, $defaultParams=null) {
        parent::__construct(null, $urlconfName, $defaultParams);
    }
    
    public function getPattern() {
        $language_code = translation::get_language();
        return "^{$language_code}/";
    }
}

}

namespace bjork\core {

use strutils;
use bjork\conf\settings,
    bjork\utils\encoding,
    bjork\utils\functional\SimpleLazyObject;

final class urlresolvers {
    
    private static
        $_script_prefix,
        $_resolver_cache = array();
    
    public static function clear_cache() {
        self::$_resolver_cache = array();
    }
    
    public static function reverse_lazy($view_name, $urlconf=null, $prefix=null, $args=null) {
        return new SimpleLazyObject(function() use ($view_name, $urlconf, $prefix, $args) {
            return urlresolvers::reverse($view_name, $urlconf, $prefix, $args);
        });
    }
    
    public static function reverse($view_name, $urlconf=null, $prefix=null, $args=null) {
        $resolver = self::get_resolver($urlconf);
        
        if (is_array($view_name) && !is_callable($view_name)) {
            $args = array($view_name[1]);
            $view_name = $view_name[0];
        } else {
            $args = array_slice(func_get_args(), 3);
        }
        
        if (count($args) === 1) {
            if (is_array($args[0])) {
                $args = $args[0];
            } else if (is_null($args[0])) {
                $args = array();
            }
        }
        if (is_null($prefix))
            $prefix = self::get_script_prefix();
        
        if (is_callable($view_name)) {
            $view = $view_name;
        } else {
            $parts = explode(':', $view_name);
            $parts = array_reverse($parts);
            $view = array_shift($parts);
            $path = $parts;
            
            // ... skip namespace ...
            
        }
        
        return encoding::iri_to_uri(sprintf('%s%s',
            $prefix,
            $resolver->reverse($view, $args)
        ));
    }
    
    public static function resolve($path, $urlconf=null) {
        return self::get_resolver($urlconf)->resolve($path);
    }
    
    public static function get_resolver($urlconf=null) {
        if (is_null($urlconf))
            $urlconf = settings::get('ROOT_URLCONF');
        $cachekey = is_array($urlconf) ? md5(print_r($urlconf, true)) : md5($urlconf);
        if (isset(self::$_resolver_cache[$cachekey]))
            return self::$_resolver_cache[$cachekey];
        $resolver = new urlresolvers\RegexURLResolver('^/', $urlconf);
        self::$_resolver_cache[$cachekey] = $resolver;
        return $resolver;
    }
    
    public static function get_script_prefix() {
        return self::$_script_prefix;
    }
    
    public static function set_script_prefix($prefix) {
        self::$_script_prefix = strutils::endswith($prefix, '/') ? $prefix
                                                                 : $prefix . '/';
    }
    
    public static function is_valid_path($path, $urlconf=null) {
        try {
            self::resolve($path, $urlconf);
            return true;
        } catch (urlresolvers\Resolver404 $e) {
            return false;
        }
    }
}

}
