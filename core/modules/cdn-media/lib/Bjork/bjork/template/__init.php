<?php

namespace bjork\template;

use os\path;
use pprint;

use bjork\conf\settings,
    bjork\core\loading,
    bjork\core\urlresolvers,
    bjork\template\context\Context,
    bjork\template\context\RequestContext,
    bjork\template\context\ContextPopException,
    bjork\utils\html;

// what to report as the origin for templates that come from
// non-loader sources (e.g. strings)
const UNKNOWN_SOURCE = '<unknown source>';

const TEMPLATE_SOURCE_SCHEME_NAME = 'x-template-source';

class TemplateSyntaxError extends \Exception {}
class TemplateDoesNotExist extends \Exception {}
class TemplateEncodingError extends \Exception {}
class InvalidTemplateLibrary extends \Exception {}

abstract class Origin {
    var $name;
    
    function __construct($name) {
        $this->name = $name;
    }
    
    function __toString() {
        return $this->name;
    }
    
    abstract function reload();
}

class StringOrigin extends Origin {
    var $source;
    
    function __construct($source) {
        parent::__construct(UNKNOWN_SOURCE);
        $this->source = $source;
    }
    
    function reload() {
        return $this->source;
    }
}

class TemplateSourceStreamWrapper {
    static $streams = array();
    
    var $url, $buffer, $buflen, $position;
    
    function __construct() {
        $this->buffer = '';
        $this->buflen = 0;
        $this->position = 0;
    }
    
    function stream_open($path, $mode, $options, &$opened_path) {
        if (!array_key_exists($path, self::$streams))
            self::$streams[$path] = '';
        $this->url = $path;
        $this->buffer = self::$streams[$path];
        $this->buflen = strlen($this->buffer);
        $this->position = 0;
        return true;
    }
    
    function stream_flush() {
        self::$streams[$this->url] = $this->buffer;
    }
    
    function stream_read($count) {
        $pos = $this->position;
        $buf = substr($this->buffer, $this->position, $count);
        $this->position += strlen($buf);
        return $buf;
    }
    
    function stream_write($data) {
        $datalen = strlen($data);
        $before = substr($this->buffer, 0, $this->position);
        $after = substr($this->buffer, min($this->buflen, $this->position + $datalen));
        $this->buffer = $before . $data . $after;
        $this->position += $datalen;
        $this->buflen += $datalen;
        return $datalen;
    }
    
    function stream_tell() {
        return $this->position;
    }
    
    function stream_eof() {
        return $this->position >= $this->buflen;
    }
    
    function stream_seek($offset, $whence) {
        if ($whence === \SEEK_SET) {
            if ($offset > 0 && $offset < $this->buflen) {
                $this->position = $offset;
                return true;
            } else {
                return false;
            }
        } else if ($whence === \SEEK_CUR) {
            if ($offset >= 0) {
                $this->position += $offset;
                return true;
            } else {
                return false;
            }
        } else if ($whence === \SEEK_END) {
            if ($this->buflen + $offset >= 0) {
                $this->position = $this->buflen + $offset;
                return true;
            } else {
                return false;
            }
        }
        
        return false;
    }
    
    function stream_truncate() {
        $this->buffer = '';
        $this->buflen = 0;
        $this->position = 0;
    }
    
    function stream_stat() {
        return array();
    }
}

stream_wrapper_register(TEMPLATE_SOURCE_SCHEME_NAME,
    'bjork\template\TemplateSourceStreamWrapper');

class Template {
    
    static $builtin_tags = array();
    
    var $name, $id, $root_node;
    
    function __construct($template_string, $origin=null, $name=null) {
        if (false === mb_detect_encoding($template_string, 'utf-8', true))
            throw new TemplateEncodingError(
                'Templates can only be constructed from UTF-8 strings.');
        
        if (settings::get('TEMPLATE_DEBUG') && null === $origin)
            $origin = new StringOrigin($template_string);
        
        $this->name = str_replace(DIRECTORY_SEPARATOR, '/', $name ?: '<Unknown Template>');
        $this->id = $name ? $this->name : str_replace(DIRECTORY_SEPARATOR, '/',
            path::join(uniqid(), $this->name));
        
        $handle = fopen($this->getURL(), 'r+');
        fwrite($handle, $template_string);
        fclose($handle);
        
        $this->root_node = new RenderNode($this->getURL());
    }
    
    function getURL() {
        return TEMPLATE_SOURCE_SCHEME_NAME."://{$this->id}";
    }
    
    public function render($context) {
        $context->render_context->push();
        try {
            $content = $this->doRender($context);
        } catch (\Exception $e) {
            $context->render_context->pop();
            throw $e;
        }
        $context->render_context->pop();
        return $content;
    }
    
    function doRender($context) {
        return $this->root_node->render($context);
    }
}

abstract class Node implements \ArrayAccess {
    var $tags, $context;
    
    function __construct() {
        $this->context = null;
        $this->tags = Library::$builtin_tags;
    }
    
    function render($context) {
        $this->context = $context;
        $result = $this->doRender($context);
        $this->context = null;
        return $result;
    }
    
    abstract function doRender($context);
    
    // template vars
    
    function offsetGet($key) {
        return $this->get($key, settings::get('TEMPLATE_STRING_IF_INVALID'));
    }
    
    function offsetExists($key) {
        return $this->hasKey($key);
    }
    
    function offsetSet($key, $value) {
        throw new \Exception('Template nodes are immutable');
    }
    
    function offsetUnset($key) {
        throw new \Exception('Template nodes are immutable');
    }
    
    function hasKey($key) {
        return $this->context->hasKey($key);
    }
    
    function get($key, $default=null) {
        return html::conditional_escape($this->context->get($key, $default));
    }
    
    function __get($name) {
        return $this->offsetGet($name);
    }
    
    function __isset($name) {
        return $this->hasKey($name);
    }
    
    // template tags
    
    function addTagLibrary($lib) {
        foreach ($lib->tags as $name => $fn)
            $this->tags[$name] = $fn;
    }
    
    function __call($name, array $args) {
        if (array_key_exists($name, $this->tags))
            $tag = $this->tags[$name];
        else
            throw new TemplateSyntaxError("Invalid template tag '{$name}'");
        
        list($fn, $options) = $tag;
        
        if (isset($options['takes_context']) && $options['takes_context'])
            array_unshift($args, $this->context);
        if (isset($options['takes_self']) && $options['takes_self'])
            array_unshift($args, $this);
        
        return call_user_func_array($fn, $args);
    }
}

class RenderNode extends Node {
    var $s, $parent, $blocks;
    
    function __construct($stringURL) {
        parent::__construct();
        $this->s = $stringURL;
        $this->parent = null;
        $this->blocks = array();
    }
    
    function doRender($context) {
        $error = null;
        
        $fn = function($self) use ($context) {
            if ($context->extract)
                extract($context->toArray());
            require $self->s;
        };
        
        ob_start();
        try {
            $fn($this);
        } catch (\Exception $e) {
            $error = $e;
        }
        $result = ob_get_clean();
        
        if ($error)
            throw $error;
        
        if ($this->parent)
            return $this->parent->doRender($context);
        return $result;
    }
    
    // template tags:
    
    function extend($template) {
        $this->parent = loader::get_template($template);
    }
    
    function block($name) {
        $this->context->push();
        $this->blocks[] = $name;
        ob_start();
    }
    
    function endblock() {
        $result = ob_get_clean();
        $name = array_pop($this->blocks);
        $this->context->pop();
        $this->context[$name] = $result;
    }
    
    function import($template, array $extra_context=null) {
        $tpl = loader::get_template($template);
        $context = clone $this->context;
        if (null !== $extra_context)
            $context->update($extra_context);
        echo $tpl->render($context);
    }
    
    function load() {
        $libs = func_get_args();
        foreach ($libs as $lib_name) {
            $lib = get_library($lib_name);
            $this->addTagLibrary($lib);
        }
        return '';
    }
    
    function get_csrf_token() {
        $csrf_token = $this->get('csrf_token', null);
        if ($csrf_token) {
            if ($csrf_token === 'NOTPROVIDED')
                return '';
            else
                return '<div style="display:none">'.
                    '<input type="hidden" name="csrfmiddlewaretoken" value="'.
                        $csrf_token.
                    '" /></div>';
        } else {
            // It's very probable that the token is missing because of
            // misconfiguration, so we raise a warning
            if (settings::get('DEBUG'))
                throw new TemplateError(
                    '``get_csrf_token()`` was used in the template, but '.
                    'the context did not provide the value. This is usually '.
                    'caused by not using RequestContext.');
            return '';
        }
    }
    
    function url() {
        $args = func_get_args();
        array_splice($args, 1, 0, array(null, null));
        return call_user_func_array('bjork\core\urlresolvers::reverse', $args);
    }
    
    function safe($str) {
        return html::mark_safe($str);
    }
    
    function escape($str) {
        return html::conditional_escape($str);
    }
    
    function force_escape($str) {
        return html::escape($str);
    }
    
    function lower($str) {
        return mb_strtolower($str);
    }
    
    function upper($str) {
        return mb_strtoupper($str);
    }
    
    function truncate($str, $length) {
        if (mb_strlen($str) > $length + 3) {
            $str = mb_substr($str, 0, $length);
            $str = mb_substr($str, 0, mb_strrpos($str, ' ')).' …';
        }
        return $str;
    }
    
    function join($glue, array $array) {
        return implode($glue, array_filter($array, function($o) {
            return !empty($o);
        }));
    }
    
    function date($format, $ts=null) {
        if (!$ts)
            $ts = time();
        return strftime($format, $ts);
    }
    
    function pprint($value) {
        return pprint::dump($value);
    }
}

class Library {
    static $builtin_tags = array();
    
    var $tags;
    
    function __construct() {
        $this->tags = array();
    }
    
    /**
    * Two ways to register a tag:
    *
    *   >>> function format_number($value) {
    *   ...     return number_format($value, 2);
    *   ... });
    *   >>> $register->tag('namespace\format_number');
    *
    * or:
    *
    *   >>> $register->tag('format_number', function($value) {
    *   ...     return number_format($value);
    *   ... });
    */
    function tag($name, $fn=null, $options=null) {
        if (is_string($name) && !is_callable($name)) {
            if (!is_callable($fn))
                throw new \Exception(
                    "You must supply a callable for template tag {$name}");
        } else if (is_string($name)) {
            $options = $fn;
            $fn = $name;
            $name = basename(str_replace('\\', DIRECTORY_SEPARATOR, $name));
        }
        
        if (!$options)
            $options = array();
        
        $this->tags[$name] = array($fn, $options);
    }
}

function import_library($libmodule, $path=null) {
    $path = null === $path ? $libmodule : path::join($path, $libmodule);
    
    $lib = null;
    
    // wrapped in a closure to limit variable scope
    $import_fn = function($p) {
        return include $p;
    };
    
    try {
        $lib = $import_fn($path);
    } catch (\ErrorException $e) {
        if (!is_file($path))
            return null;
        else
            throw new InvalidTemplateLibrary(
                "Error loading tag library {$libmodule}: {$e->getMessage()}");
    }
    
    if (!($lib instanceof Library))
        throw new InvalidTemplateLibrary(
            "Template library {$libmodule} does not return ".
            "a valid library instance.");
    
    return $lib;
}

function get_templatetags_modules() {
    static $modules = null;
    
    if (null === $modules) {
        $modules = array();
        foreach (loading::get_apps() as $app) {
            $path = null;
            
            $pos = path::join($app->getFullPath(), 'templatetags.php');
            if (is_file($pos)) {
                $path = $app->getPath().'/%1$s/templatetags.php';
            } else {
                $pos = path::join($app->getFullPath(), 'templatetags');
                if (is_dir($pos))
                    $path = $app->getFullPath().'/templatetags/%1$s.php';
            }
            
            if (null !== $path)
                $modules[] = array($app, $path);
        }
    }
    
    return $modules;
}

function get_library($library_name) {
    static $libraries = array();
    
    $lib = array_key_exists($library_name, $libraries)
        ? $libraries[$library_name]
        : null;
    
    if (!$lib) {
        $tried = array();
        foreach (get_templatetags_modules() as $module) {
            list($app, $path_format) = $module;
            $libmodule = sprintf($path_format, $library_name);
            $tried[] = $libmodule;
            $lib = import_library($libmodule);
            if (null !== $lib) {
                $libraries[$library_name] = $lib;
                break;
            }
        }
        
        if (!$lib)
            throw new InvalidTemplateLibrary(sprintf(
                "Tag library '%s' not found. Tried %s.",
                $library_name, implode(', ', $tried)));
    }
    
    return $lib;
}

function add_to_builtins($libmodule, $path=null) {
    $lib = import_library($libmodule, $path);
    if (!$lib)
        throw new InvalidTemplateLibrary($libmodule);
    foreach ($lib->tags as $name => $fn)
        Library::$builtin_tags[$name] = $fn;
}
