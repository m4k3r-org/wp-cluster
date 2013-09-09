<?php

namespace optparse;

require_once __DIR__ . '/errors.php';
require_once __DIR__ . '/help.php';

use strutils;
use optparse;

class Values extends \ArrayObject {
    function ensureValue($attr, $value) {
        if (!isset($this[$attr]) || $this[$attr] === null)
            $this[$attr] = $value;
        return $this[$attr];
    }
    
    function get($key, $default=null) {
        return array_key_exists($key, $this) ? $this[$key] : $default;
    }
}

abstract class OptionContainer {
    
    const SUPPRESS_HELP = 'SUPPRESS+HELP';
    const SUPPRESS_USAGE = 'SUPPRESS+USAGE';
    
    protected $conflict_handler, $description;
    
    var $option_class, $option_list,
        $short_opt, $long_opt, $defaults;
    
    function __construct($option_class, $conflict_handler, $description) {
        $this->option_class = $option_class;
        
        $this->createOptionList();
        $this->setDescription($description);
        $this->setConflictHandler($conflict_handler);
    }
    
    // properties
    
    function getDescription() {
        return $this->description;
    }
    
    function setDescription($description) {
        $this->description = $description;
    }
    
    function getConflictHandler() {
        return $this->conflict_handler;
    }
    
    function setConflictHandler($handler) {
        if (!in_array($handler, array('error', 'resolve')))
            throw new \Exception(
                "invalid conflict resolution handler: {$handler}");
        $this->conflict_handler = $handler;
    }
    
    // option management
    
    public function addOption(/* $opt1, $opt2, ..., array $attrs=null*/) {
        list($args, $kwargs) = optparse::parse_funcargs(func_get_args());
        if (is_string($args[0])) {
            $option_class = $this->option_class;
            $option = new $option_class($args, $kwargs);
        } else if (count($args) == 1 && empty($kwargs)) {
            $option = $args[0];
            if (!($option instanceof Option))
                throw new \Exception(
                    sprintf('not an Option instance: %s',
                        strval($option)));
        } else {
            throw new \Exception('invalid arguments');
        }
        
        $this->checkOptionConflict($option);
        
        $this->option_list[] = $option;
        $option->container = $this;
        foreach ($option->short_opts as $opt)
            $this->short_opt[$opt] = $option;
        foreach ($option->long_opts as $opt)
            $this->long_opt[$opt] = $option;
        
        if ($option->dest !== null) {
            if ($option->default !== Option::NO_DEFAULT)
                $this->defaults[$option->dest] = $option->default;
            else if (!array_key_exists($option->dest, $this->defaults))
                $this->defaults[$option->dest] = null;
        }
        
        return $option;
    }
    
    public function addOptions(array $option_list) {
        foreach ($option_list as $option)
            $this->addOption($option);
    }
    
    public function getOption($opt_str) {
        return array_key_exists($opt_str, $this->short_opt)
            ? $this->short_opt[$opt_str]
            : (array_key_exists($opt_str, $this->long_opt)
                ? $this->long_opt[$opt_str]
                : null);
    }
    
    public function hasOption($opt_str) {
        return array_key_exists($opt_str, $this->short_opt) ||
               array_key_exists($opt_str, $this->long_opt);
    }
    
    public function removeOption($opt_str) {
        $option = $this->getOption($opt_str);
        if ($option === null)
            throw new \Exception("no such option {$opt_str}");
        
        foreach ($option->short_opts as $opt)
            unset($this->short_opt[$opt]);
        foreach ($option->long_opts as $opt)
            unset($this->long_opt[$opt]);
        
        $index = array_search($option, $option->container->option_list, true);
        if (false !== $index)
            unset($option->container->option_list[$index]);
    }
    
    // formatting
    
    function formatOptionHelp($formatter) {
        if (empty($this->option_list))
            return '';
        $result = array();
        foreach ($this->option_list as $option) {
            if ($option->help !== self::SUPPRESS_HELP)
                $result[] = $formatter->formatOption($option);
        }
        return implode('', $result);
    }
    
    function formatDescription($formatter) {
        return $formatter->formatDescription($this->getDescription());
    }
    
    function formatHelp($formatter) {
        $result = array();
        if ($this->description)
            $result[] = $this->formatDescription($formatter);
        if (!empty($this->option_list))
            $result[] = $this->formatOptionHelp($formatter);
        return implode("\n", $result);
    }
    
    // internal
    
    function checkOptionConflict(Option $option) {
        $conflict_opts = array();
        foreach ($option->short_opts as $opt) {
            if (in_array($opt, $this->short_opt))
                $conflict_opts[] = array($opt, $this->short_opt[$opt]);
        }
        foreach ($option->long_opts as $opt) {
            if (in_array($opt, $this->long_opt))
                $conflict_opts[] = array($opt, $this->long_opt[$opt]);
        }
        
        if (!empty($conflict_opts)) {
            $handler = $this->getConflictHandler();
            switch ($handler) {
                case 'error':
                    throw new OptionConflictError(
                        sprintf('conflicting option string(s): %s', implode(
                            ', ', array_map(function($opt) {
                                return $opt[0];
                            }, $conflict_opts))),
                        $option);
                    break;
                
                case 'resolve':
                    foreach ($conflict_opts as $o) {
                        list($opt, $c_option) = $o;
                        if (strutils::startswith($opt, '--')) {
                            unset($c_option->long_opts[0]);
                            unset($this->long_opt[0]);
                        } else {
                            unset($c_option->short_opts[0]);
                            unset($this->short_opt[0]);
                        }
                        if (empty($c_option->short_opts) && empty($c_option->long_opts)) {
                            $index = array_search($c_option,
                                $c_option->container->option_list, true);
                            if (false !== $index)
                                unset($c_option->container->option_list[$index]);
                        }
                    }
                    
                    break;
                
                default:
                    break;
            }
        }
    }
    
    // for subclasses
    
    abstract function createOptionList();
    
    protected function createOptionMappings() {
        $this->short_opt = array();
        $this->long_opt = array();
        $this->defaults = array();
    }
    
    protected function shareOptionMappings($parser) {
        $this->short_opt =& $parser->short_opt;
        $this->long_opt =& $parser->long_opt;
        $this->defaults =& $parser->defaults;
    }
}

class OptionGroup extends OptionContainer {
    var $parser, $title;
    
    function __construct($parser, $title, $description=null) {
        $this->parser = $parser;
        parent::__construct($parser->option_class, $parser->conflict_handler,
            $description);
        $this->title = $title;
    }
    
    function getTitle() {
        return $this->title;
    }
    
    function setTitle($title) {
        $this->title = $title;
    }
    
    function createOptionList() {
        $this->option_list = array();
        $this->shareOptionMappings($this->parser);
    }
    
    function formatHelp($formatter) {
        $result = $formatter->formatHeading($this->getTitle());
        $formatter->indent();
        $result .= parent::formatHelp($formatter);
        $formatter->dedent();
        return $result;
    }
}

class OptionParser extends OptionContainer {
    
    static $standard_option_list = array();
    
    var $prog,
        $usage,
        $version,
        $formatter,
        $add_help_option,
        $epilog;
    
    var $option_groups;
    
    var $allow_interspersed_args;
    
    var $rargs, $largs, $values;
    
    function __construct(array $options=null) {
        if (null === $options)
            $options = array();
        
        $options = array_merge(array(
            'add_help_option'   => true,
            'conflict_handler'  => 'error',
            'description'       => null,
            'epilog'            => null,
            'formatter'         => null,
            'option_class'      => 'optparse\Option',
            'option_list'       => null,
            'prog'              => null,
            'usage'             => null,
            'version'           => null,
        ), $options);
        
        parent::__construct(
            $options['option_class'],
            $options['conflict_handler'],
            $options['description']);
        
        $this->setUsage($options['usage']);
        
        $this->prog = $options['prog'];
        $this->version = $options['version'];
        $this->epilog = $options['epilog'];
        $this->allow_interspersed_args = true;
        
        $this->formatter = isset($options['formatter'])
            ? $options['formatter']
            : new IndentedHelpFormatter();
        $this->formatter->setParser($this);
        
        $this->populateOptionList(
            $options['option_list'],
            $options['add_help_option']);
        
        $this->initParsingState();
    }
    
    // properties
    
    public function getDescription() {
        return $this->expandProgName($this->description);
    }
    
    public function getVersion() {
        if ($this->version)
            return $this->expandProgName($this->version);
        return '';
    }
    
    public function getProgName() {
        if (null === $this->prog)
            return basename($_SERVER['argv'][0]);
        return $this->prog;
    }
    
    public function getUsage() {
        if ($this->usage)
            return $this->formatter->formatUsage($this->expandProgName($this->usage));
        return '';
    }
    
    public function setUsage($usage) {
        if ($usage === null)
            $this->usage = '%prog [options]';
        else if (strtolower($usage) === self::SUPPRESS_USAGE)
            $this->usage = null;
        else
            $this->usage = $usage;
    }
    
    public function getAllOptions() {
        $options = array_merge(array(), $this->option_list);
        foreach ($this->option_groups as $group)
            $options = array_merge($options, $group->option_list);
        return $options;
    }
    
    public function getDefaultValues() {
        $defaults = array_merge(array(), $this->defaults);
        foreach ($this->getAllOptions() as $option) {
            $default = isset($defaults[$option->dest])
                ? $defaults[$option->dest]
                : null;

            $default_str = null;
            try {
                $default_str = (string)$default;
            } catch (\Exception $e) {
                
            }
            if ($default === $default_str) {
                $opt_str = $option->getOptString();
                $defaults[$option->dest] = $option->checkValue($opt_str, $default);
            }
        }
        return new Values($defaults);
    }
    
    public function setDefault($dest, $default) {
        $this->defaults[$dest] = $default;
    }
    
    public function setDefaults(array $defaults) {
        $this->defaults = array_merge($this->defaults, $defaults);
    }
    
    public function enableInterspersedArgs() {
        $this->allow_interspersed_args = true;
    }
    
    public function disableInterspersedArgs() {
        $this->allow_interspersed_args = false;
    }
    
    // option group management
    
    public function addOptionGroup(/* $opt1, $opt2, ..., array $attrs=null*/) {
        list($args, $kwargs) = optparse::parse_funcargs(func_get_args());
        if (is_string($args[0])) {
            $group = new OptionGroup($args, $kwargs);
        } else if (count($args) == 1 && empty($kwargs)) {
            $group = $args[0];
            if (!($group instanceof OptionGroup))
                throw new \Exception(
                    sprintf('not an OptionGroup instance: %s',
                        strval($group)));
            if ($group->parser !== $this)
                throw new \Exception(
                    sprintf('invalid OptionGroup (wrong parser)',
                        strval($group)));
        } else {
            throw new \Exception('invalid arguments');
        }
        
        $this->option_groups[] = $group;
        return $group;
    }
    
    public function getOptionGroup($opt_str) {
        $option =  array_key_exists($opt_str, $this->short_opt)
            ? $this->short_opt[$opt_str]
            : (array_key_exists($opt_str, $this->long_opt)
                ? $this->long_opt[$opt_str]
                : null);
        if ($option && $option->container !== $this)
            return $option->container;
        return null;
    }
    
    // public methods
    
    public function printUsage($file=null) {
        if (null === $file)
            $file = STDOUT;
        if ($this->usage)
            fwrite($file, $this->getUsage() . "\n");
    }
    
    public function printVersion($file=null) {
        if (null === $file)
            $file = STDOUT;
        if ($this->version)
            fwrite($file, $this->getVersion() . "\n");
    }
    
    public function printHelp($file=null) {
        if (null === $file)
            $file = STDOUT;
        fwrite($file, $this->formatHelp());
    }
    
    public function parseArgs($args=null, $values=null) {
        $rargs = $this->getArgs($args);
        if (null === $values)
            $values = $this->getDefaultValues();
        
        $this->rargs = $rargs;
        $largs = array();
        $this->largs = array();
        $this->values = $values;
        
        try {
            $this->processArgs($largs, $rargs, $values);
        } catch (OptionValueError $e) {
            $this->error($e);
        } catch (BadOptionError $e) {
            $this->error($e);
        }
        
        $args = array_merge($largs, $rargs);
        return $this->checkValues($values, $args);
    }
    
    // feedback methods
    
    function quit($status=0, $msg=null) {
        if ($msg)
            fwrite(STDERR, $msg);
        exit($status);
    }
    
    function error($msg) {
        $this->printUsage(STDERR);
        $this->quit(2, "{$this->getProgName()}: error: {$msg}\n");
    }
    
    // parsing methods
    
    function getArgs($args) {
        if (null === $args) {
            $args = $_SERVER['argv'];
            array_shift($args);
            return $args;
        }
        return $args;
    }
    
    function checkValues($values, $args) {
        return array($values, $args);
    }
    
    function processArgs(&$largs, &$rargs, Values $values) {
        while (!empty($rargs)) {
            $arg = $rargs[0];
            if ($arg === '--') {
                array_shift($rargs);
                return;
            } else if (substr($arg, 0, 2) == '--') {
                $this->processLongOpt($rargs, $values);
            } else if (substr($arg, 0, 1) == '-' && strlen($arg) > 1) {
                $this->processShortOpts($rargs, $values);
            } elseif ($this->allow_interspersed_args) {
                $largs[] = $arg;
                array_shift($rargs);
            } else {
                return;
            }
        }
    }
    
    function processLongOpt(&$rargs, Values $values) {
        $arg = array_shift($rargs);
        
        if (false !== strpos($arg, '=')) {
            list($opt, $next_arg) = strutils::split($arg, '=', 1);
            array_unshift($rargs, $next_arg);
            $has_explicit_value = true;
        } else {
            $opt = $arg;
            $has_explicit_value = false;
        }
        
        $opt = $this->matchLongOpt($opt);
        $option = $this->long_opt[$opt];
        if ($option->takesValue()) {
            $nargs = $option->nargs;
            if (count($rargs) < $nargs)
                $this->error("{$opt} option requires {$nargs} argument(s)");
            else if ($nargs === 1)
                $value = array_shift($rargs);
            else
                $value = array_splice($rargs, 0, $nargs);
        } else if ($has_explicit_value) {
            $this->error("{$opt} option does not take a value");
        } else {
            $value = null;
        }
        
        $option->process($opt, $value, $values, $this);
    }
    
    function processShortOpts(&$rargs, Values $values) {
        $arg = array_shift($rargs);
        $stop = false;
        $i = 1;
        $chars = strutils::explode(substr($arg, 1));
        foreach ($chars as $ch) {
            $opt = '-' . $ch;
            $option = isset($this->short_opt[$opt])
                ? $this->short_opt[$opt]
                : null;
            $i++;
            
            if (!$option)
                throw new BadOptionError($opt);
            
            if ($option->takesValue()) {
                if ($i < strlen($arg)) {
                    array_unshift($rargs, substr($arg, $i));
                    $stop = true;
                }
                
                $nargs = $option->nargs;
                if (count($rargs) < $nargs)
                    $this->error("{$opt} option requires {$nargs} argument(s)");
                else if ($nargs === 1)
                    $value = array_shift($rargs);
                else
                    $value = array_splice($rargs, 0, $nargs);
            } else {
                $value = null;
            }
            
            $option->process($opt, $value, $values, $this);
            
            if ($stop)
                break;
        }
    }
    
    function matchLongOpt($opt) {
        return match_abbrev($opt, $this->long_opt);
    }
    
    // formatting methods
    
    function expandProgName($s) {
        return str_replace('%prog', $this->getProgName(), $s);
    }
    
    function formatOptionHelp($formatter=null) {
        if (null === $formatter)
            $formatter = $this->formatter;
        $formatter->storeOptionStrings($this);
        $result = array();
        $result[] = $formatter->formatHeading('Options');
        $formatter->indent();
        if (!empty($this->option_list)) {
            $result[] = parent::formatOptionHelp($formatter);
            $result[] = "\n";
        }
        foreach ($this->option_groups as $group) {
            $result[] = $group->formatHelp($formatter);
            $result[] = "\n";
        }
        $formatter->dedent();
        
        array_pop($result);
        return implode('', $result);
    }
    
    function formatEpilog($formatter=null) {
        if (null === $formatter)
            $formatter = $this->formatter;
        return $formatter->formatEpilog($this->epilog);
    }
    
    function formatHelp($formatter=null) {
        if (null === $formatter)
            $formatter = $this->formatter;
        $result = array();
        if ($this->usage)
            $result[] = $this->getUsage() . "\n";
        if ($this->description)
            $result[] = $this->formatDescription($formatter) . "\n";
        $result[] = $this->formatOptionHelp($formatter);
        $result[] = $this->formatEpilog($formatter);
        return implode('', $result);
    }
    
    // internal
    
    function createOptionList() {
        $this->option_list = array();
        $this->option_groups = array();
        $this->createOptionMappings();
    }
    
    function populateOptionList($option_list, $add_help=true) {
        if (!empty(static::$standard_option_list))
            $this->addOptions(static::$standard_option_list);
        if (!empty($option_list))
            $this->addOptions($option_list);
        if ($this->version)
            $this->addVersionOption();
        if ($add_help)
            $this->addHelpOption();
    }
    
    function initParsingState() {
        $this->rargs = null;
        $this->largs = null;
        $this->values = null;
    }
    
    function addHelpOption() {
        $this->addOption('-h', '--help', array(
            'action' => 'help',
            'help' => 'show this help message and exit',
        ));
    }
    
    function addVersionOption() {
        $this->addOption('--version', array(
            'action' => 'version',
            'help' => 'show program\'s version number and exit',
        ));
    }
}

function match_abbrev($s, $wordmap) {
    if (array_key_exists($s, $wordmap))
        return $s;
    
    $possibilities = array_filter(array_keys($wordmap), function($word) use ($s) {
        return $word{0} === $s;
    });
    
    if (count($possibilities) === 1)
        return $possibilities[0];
    
    if (empty($possibilities))
        throw new BadOptionError($s);
    
    sort($possibilities);
    throw new AmbiguousOptionError($s, $possibilities);
}

