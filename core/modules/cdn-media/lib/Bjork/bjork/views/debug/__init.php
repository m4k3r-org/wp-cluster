<?php

namespace bjork\views\debug;

use bjork,
    bjork\conf\settings,
    bjork\http\Http404,
    bjork\http\HttpResponse,
    bjork\http\HttpResponseServerError,
    bjork\http\HttpResponseNotFound,
    bjork\template\Template,
    bjork\template\TemplateDoesNotExist,
    bjork\template\context\Context,
    bjork\template\loader;

const HIDDEN_SETTINGS = 'API|TOKEN|KEY|SECRET|PASS|PROFANITIES_LIST|SIGNATURE';
const CLEANSED_SUBSTITUTE = '********************';

/**
* Create an empty URLconf 404 error response.
*/
function empty_urlconf($request) {
    $tplstr = file_get_contents(__DIR__.'/empty_urlconf_template.php');
    $t = new Template($tplstr, 'Empty URLConf template');
    $c = new Context(array(
        'project_name' => dirname(settings::setup()->SETTINGS_MODULE),
    ));
    return new HttpResponse($t->render($c), 'text/html');
}

/**
* Create a technical 404 error response. The exception should be the Http404.
*/
function technical_404_response($request, Http404 $exception) {
    if (method_exists($exception, 'getTriedPaths')) {
        $tried = $exception->getTriedPaths();
        if (empty($tried))
            // tried exists but is an empty list. The URLconf must've been empty.
            return empty_urlconf($request);
    } else {
        $tried = array();
    }
    
    $tplstr = file_get_contents(__DIR__.'/technical_404_template.php');
    $t = new Template($tplstr, 'Technical 404 template');
    $c = new Context(array(
        'urlconf' => settings::get('ROOT_URLCONF'),
        'request_path' => ltrim($request->getPathInfo(), '/'),
        'urlpatterns' => $tried,
        'reason' => $exception->getMessage(),
        'request' => $request,
        'settings' => get_safe_settings(),
    ));
    
    return new HttpResponseNotFound($t->render($c), 'text/html');
}

/**
* Create a technical server error response.
*/
function technical_500_response($request, $exception) {
    $reporter = new ExceptionReporter($request, $exception);
    if ($request->isAjax()) {
        $text = $reporter->getTracebackText();
        return new HttpResponseServerError($text, 'text/plain');
    } else {
        $html = $reporter->getTracebackHtml();
        return new HttpResponseServerError($html, 'text/html');
    }
}

/**
* Returns a dictionary of the settings module, with sensitive settings
* blurred out.
*/
function get_safe_settings() {
    $settings = array();
    foreach (settings::setup()->_wrapped as $key => $value) {
        if ($key == strtoupper($key))
            $settings[$key] = cleanse_setting($key, $value);
    }
    return $settings;
}

function cleanse_setting($key, $value) {
    if (preg_match('/'.HIDDEN_SETTINGS.'/', $key)) {
        $cleansed = CLEANSED_SUBSTITUTE;
    } else {
        if (is_array($value) || $value instanceof \Iterable) {
            $cleansed = array();
            foreach ($value as $k => $v)
                $cleansed[$k] = cleanse_setting($k, $v);
        } else {
            $cleansed = $value;
        }
    }
    return $cleansed;
}

/**
* A class to organize and coordinate reporting on exceptions.
*/
class ExceptionReporter {
    static $php_error_map = array(
        1     => 'E_ERROR',
        2     => 'E_WARNING',
        4     => 'E_PARSE',
        8     => 'E_NOTICE',
        16    => 'E_CORE_ERROR',
        32    => 'E_CORE_WARNING',
        64    => 'E_COMPILE_ERROR',
        128   => 'E_COMPILE_WARNING',
        256   => 'E_USER_ERROR',
        512   => 'E_USER_WARNING',
        1024  => 'E_USER_NOTICE',
        2048  => 'E_STRICT',
        4096  => 'E_RECOVERABLE_ERROR',
        8192  => 'E_DEPRECATED',
        16384 => 'E_USER_DEPRECATED',
    );
    
    var $request, $exception, $is_email,
        $template_info, $template_does_not_exist, $loader_debug_info;
    
    function __construct($request, $exception, $is_email=false) {
        $this->request = $request;
        $this->exception = $exception;
        $this->is_email = $is_email;
        
        $this->template_info = null;
        $this->template_does_not_exist = false;
        $this->loader_debug_info = null;
    }
    
    function getTracebackData() {
        $exc_type = get_class($this->exception);
        $exc_value = $this->exception->getMessage();
        
        if ($this->exception instanceof TemplateDoesNotExist) {
            $exc_type = str_replace('bjork\\template\\', '', $exc_type);
            $this->template_does_not_exist = true;
            $this->loader_debug_info = array();
            foreach (loader::get_template_source_loaders() as $loader) {
                $template_list = array();
                if (method_exists($loader, 'getTemplateSources')) {
                    foreach ($loader->getTemplateSources($exc_value) as $t) {
                        $template_list[] = array(
                            'name' => $t,
                            'exists' => file_exists($t),
                        );
                    }
                }
                $this->loader_debug_info[] = array(
                    'loader' => get_class($loader),
                    'templates' => $template_list,
                );
            }
        } else if ($this->exception instanceof \ErrorException) {
            $t = $this->exception->getSeverity();
            if (isset(self::$php_error_map[$t]))
                $exc_type = self::$php_error_map[$t];
        }
        
        if (settings::get('TEMPLATE_DEBUG') &&
            isset($this->exception->bjork_template_source))
            $this->getTemplateExceptionInfo();
        
        $frames = $this->getTracebackFrames();
        
        $c = array(
            'is_email' => $this->is_email,
            'frames' => $frames,
            'request' => $this->request,
            'settings' => get_safe_settings(),
            'settings_module' => settings::setup()->SETTINGS_MODULE,
            'sys_sapi_name' => php_sapi_name(),
            'sys_version' => phpversion(),
            'server_time' => new \DateTime(),
            'bjork_version' => bjork::get_version(),
            'sys_path' => explode(PATH_SEPARATOR, get_include_path()),
            'template_info' => $this->template_info,
            'template_does_not_exist' => $this->template_does_not_exist,
            'loader_debug_info' => $this->loader_debug_info,
            
            'exception_type' => $exc_type,
            'exception_value' => $exc_value,
            'lastframe' => end($frames),
        );
        
        return $c;
    }
    
    function getTracebackHtml() {
        $tplstr = file_get_contents(__DIR__.'/technical_500_template.html.php');
        $t = new Template($tplstr, 'Technical 500 template');
        $c = new Context($this->getTracebackData());
        return $t->render($c);
    }
    
    function getTracebackText() {
        $tplstr = file_get_contents(__DIR__.'/technical_500_template.text.php');
        $t = new Template($tplstr, 'Technical 500 template');
        $c = new Context($this->getTracebackData(), array('autoescape' => false));
        return $t->render($c);
    }
    
    function getTracebackFrames() {
        $frames = array();
        $tb = array_reverse($this->exception->getTrace());
        foreach ($tb as $frame) {
            $filename = isset($frame['file']) ? $frame['file'] : '[internal]';
            $lineno = isset($frame['line']) ? $frame['line'] : '[internal]';
            $funcargs = isset($frame['args']) ? $frame['args'] : array();
            $function = $frame['function'];
            if (!empty($frame['class']))
                $function = "{$frame['class']}{$frame['type']}{$function}";
            
            $funcargs = array();
            if (isset($frame['args'])) {
                foreach ($frame['args'] as $arg) {
                    $type = gettype($arg);
                    switch ($type) {
                        case 'boolean':
                            $funcargs[] = $arg ? 'TRUE' : 'FALSE';
                            break;
                        case 'string':
                            $out = "\"{$arg}\"";
                            $funcargs[] = truncate_inner($out, 15, 15, '...');
                            break;
                        case 'array':
                            $funcargs[] = $type . "(".count($arg).")";
                            break;
                        case 'object':
                            $out = get_class($arg);
                            $funcargs[] = truncate_inner($out, 15, 15, '...');
                            break;
                        case 'resource':
                        case 'NULL':
                            $funcargs[] = $type;
                            break;
                        default:
                            $funcargs[] = $arg;
                            break;
                    }
                }
            }
            
            $frames[] = array(
                'frame' => $frame,
                'type' => preg_match('/^'.preg_quote(BJORK_ROOT, '/').'/', $filename) ? 'bjork' : 'user',
                'filename' => str_replace(BJORK_ROOT.'/', '', $filename),
                'function' => $function,
                'funcargs' => $funcargs,
                'lineno' => $lineno,
            );
        }
        return $frames;
    }
    
    function getTemplateExceptionInfo() {
        $origin = $this->exception->bjork_template_source;
        $message = '(Could not get exception message)';
        
        $this->template_info = array(
            'message' => $message,
            'name' => $origin->name,
            'line' => -1,
        );
    }
    
    function formatException() {
        $frames = $this->getTracebackFrames();
        $tb = array();
        foreach ($frames as $f)
            $tb[] = array($f['filename'], $f['lineno'], $f['function'], '');
        $list = traceback_format_list($tb);
        $list[] = traceback_format_exception_only($this->exception);
        return $list;
    }
}

function traceback_format_list($tb) {
    $list = array();
    foreach ($tb as $frame) {
        list($filename, $lineno, $name, $line) = $frame;
        $item = "  File \"{$filename}\", line {$lineno}, in {$name}\n";
        if ($line)
            $item .= "    ".trim($line)."\n";
        $list[] = $item;
    }
    return $list;
}

function traceback_format_exception_only(\Exception $e) {
    $type = get_class($e);
    $valuestr = $e->getMessage();
    if ($valuestr)
        $line = "{$type}: {$valuestr}\n";
    else
        $line = "{$type}\n";
    return $line;
}

function truncate_inner($str, $len_before, $len_after, $char='…', $buffer=3) {
    if (mb_strlen($str) <= ($len_before + $len_after + mb_strlen($char) - 1 + $buffer)) {
        return $str;
    }
    
    $out = mb_substr($str, 0, $len_before);
    $out .= $char;
    $out .= mb_substr($str, -1 * $len_after);
    
    return $out;
}
