<?php

namespace optparse;

require_once __DIR__ . '/errors.php';
require_once __DIR__ . '/option_parser.php';

use optparse;

function check_builtin($option, $opt, $value) {
    $val = null;
    switch ($option->type) {
        case 'int':
        case 'long': return intval($value, 10); break;
        case 'float': return floatval($value); break;
        case 'complex': throw new \Exception('complex'); break;
        default:
            break;
    }
    
    throw new OptionValueError(
        "option {$opt}: invalid value type: {$option->type}");
}

function check_choice($option, $opt, $value) {
    if (in_array($value, $option->choices))
        return $value;
    $choices = implode(', ', $option->choices);
    throw new OptionValueError(
        "option {$opt}: invalid choice: {$value} (choose from {$choices})");
}

class Option extends \ArrayObject {
    
    // random value to serve as sentinel
    const NO_DEFAULT = 'ATvgcDYBC%RCtghBDR^CYTHBdeXTGYE%XctyhBDR%ESTX';
    
    static $_ATTRS = array(
        'action',
        'type',
        'dest',
        'default',
        'nargs',
        'const',
        'choices',
        'callback',
        'callback_args',
        // 'callback_kwargs',
        'help',
        'metavar');
    
    static $_ACTIONS = array(
        'store',
        'store_const',
        'store_true',
        'store_false',
        'append',
        'append_const',
        'count',
        'callback',
        'help',
        'version');
    
    static $_STORE_ACTIONS = array(
        'store',
        'store_const',
        'store_true',
        'store_false',
        'append',
        'append_const',
        'count');
    
    static $_TYPED_ACTIONS = array(
        'store',
        'append',
        'callback');
    
    static $_ALWAYS_TYPED_ACTIONS = array(
        'store',
        'append');
    
    static $_CONST_ACTIONS = array(
        'store_const',
        'append_const');
    
    static $_TYPES = array(
        'str',
        'string',
        'int',
        'long',
        'float',
        // 'complex',
        'choice');
    
    static $_TYPE_CHECKER = array(
        'int'       => 'optparse\check_builtin',
        'long'      => 'optparse\check_builtin',
        'float'     => 'optparse\check_builtin',
        // 'complex'   => 'optparse\check_builtin',
        'choice'    => 'optparse\check_choice');
    
    var $short_opts,
        $long_opts;
    
    public
        $action,
        $type,
        $dest,
        $default,
        $nargs,
        $const,
        $choices,
        $callback,
        $callback_args,
        $help,
        $metavar;
    
    function __construct(array $opts, array $attrs) {
        parent::__construct(array());
        
        $this->short_opts = array();
        $this->long_opts = array();
        
        $this->setOpts($opts);
        $this->setAttrs($attrs);
        
        $this->checkAction();
        $this->checkType();
        $this->checkChoice();
        $this->checkDest();
        $this->checkConst();
        $this->checkNargs();
        $this->checkCallback();
    }
    
    protected function setOpts(array $opts) {
        foreach ($opts as $opt) {
            if (strlen($opt) < 2) {
                throw new OptionError(
                    "invalid option string {$opt}: ".
                    "must be at least two characters long",
                    $this);
            } else if (strlen($opt) == 2) {
                if (!($opt{0} == '-' && $opt[1] != '-'))
                    throw new OptionError(
                        "invalid short option string {$opt}: ".
                        "must be of the form -x, (x any non-dash char)",
                        $this);
                $this->short_opts[] = $opt;
            } else {
                if (!($opt{0} == '-' && $opt[1] == '-' && $opt[2] != '-'))
                    throw new OptionError(
                        "invalid long option string {$opt}: ".
                        "must start with --, followed by non-dash",
                        $this);
                $this->long_opts[] = $opt;
            }
        }
    }
    
    protected function setAttrs(array $attrs) {
        foreach (self::$_ATTRS as $attr) {
            if (in_array($attr, array_keys($attrs))) {
                $this->$attr = $attrs[$attr];
                unset($attrs[$attr]);
            } else {
                if ($attr == 'default')
                    $this->$attr = self::NO_DEFAULT;
                else
                    $this->$attr = null;
            }
        }
        
        if (!empty($attrs)) {
            $attrs = array_keys($attrs);
            sort($attrs);
            throw new OptionError(
                'invalid attributes: ' . implode(', ', $attrs),
                $this);
        }
    }
    
    // Validation methods
    
    protected function checkAction() {
        if ($this->action === null)
            $this->action = 'store';
        else if (!in_array($this->action, self::$_ACTIONS))
            throw new OptionError(
                "invalid action: {$this->action}",
                $this);
    }
    
    protected function checkType() {
        if ($this->type === null) {
            if (in_array($this->action, self::$_ALWAYS_TYPED_ACTIONS)) {
                if ($this->choices !== null)
                    $this->type = 'choice';
                else
                    $this->type = 'string';
            }
        } else {
            if ($this->type == 'str')
                $this->type = 'string';
            if (!in_array($this->type, self::$_TYPES))
                throw new OptionError(
                    "invalid option type: {$this->type}",
                    $this);
            if (!in_array($this->action, self::$_TYPED_ACTIONS))
                throw new OptionError(
                    "must not supply a type for action {$this->action}",
                    $this);
        }
    }
    
    protected function checkChoice() {
        if ($this->type == 'choice') {
            if ($this->choices === null) {
                throw new OptionError(
                    "must supply a list of choices for type 'choice'",
                    $this);
            } else if (!is_array($this->choices)) {
                $type = gettype($this->choices);
                throw new OptionError(
                    "choices must be a list of strings ('{$type}' supplied)",
                    $this);
            }
        } else if ($this->choices !== null) {
            throw new OptionError(
                "must not supply choices for type {$this->type}",
                $this);
        }
    }
    
    protected function checkDest() {
        $takes_value = in_array($this->action, self::$_STORE_ACTIONS)
                    || $this->type !== null;
        
        if ($this->dest === null && $takes_value) {
            if (!empty($this->long_opts))
                // eg. "--foo-bar" -> "foo_bar"
                $this->dest = str_replace('-', '_',
                    substr($this->long_opts[0], 2));
            else
                $this->dest = $this->short_opts[0]{1};
        }
    }
    
    protected function checkConst() {
        if (!in_array($this->action, self::$_CONST_ACTIONS) && $this->const !== null)
            throw new OptionError(
                "'const' must not be supplied for action {$this->action}",
                $this);
    }
    
    protected function checkNargs() {
        if (in_array($this->action, self::$_TYPED_ACTIONS)) {
            if ($this->nargs === null)
                $this->nargs = 1;
        } else if ($this->nargs !== null) {
            throw new OptionError(
                "'nargs' must not be supplied for action {$this->action}",
                $this);
        }
    }
    
    protected function checkCallback() {
        if ($this->action == 'callback') {
            if (!is_callable($this->callback))
                throw new OptionError(
                    "callback not callable: {$this->callback}",
                    $this);
            if ($this->callback_args !== null && !is_array($this->callback_args))
                throw new OptionError(
                    "callback_args, if supplied, must be a list: ".
                    "not {$this->callback_args}",
                    $this);
        } else {
            if ($this->callback !== null)
                throw new OptionError(
                    "callback supplied ({$this->callback}) for non-callback option",
                    $this);
            if ($this->callback_args !== null)
                throw new OptionError(
                    "callback_args supplied for non-callback option",
                    $this);
        }
    }
    
    // Public
    
    public function __toString() {
        return implode('/', array_merge($this->short_opts, $this->long_opts));
    }
    
    public function takesValue() {
        return $this->type !== null;
    }
    
    public function getOptString() {
        if (!empty($this->long_opts))
            return $this->long_opts[0];
        else
            return $this->short_opts[0];
    }
    
    // internal
    
    function checkValue($opt, $value) {
        $checker = array_key_exists($this->type, self::$_TYPE_CHECKER)
            ? self::$_TYPE_CHECKER[$this->type]
            : null;
        return $checker ? $checker($this, $opt, $value) : $value;
    }
    
    function convertValue($opt, $value) {
        if (null !== $value) {
            if ($this->nargs === 1) {
                return $this->checkValue($opt, $value);
            } else {
                return array_map(function($val) use ($opt) {
                    return $this->checkValue($opt, $val);
                }, $value);
            }
        }
    }
    
    function process($opt, $value, $values, $parser) {
        $value = $this->convertValue($opt, $value);
        return $this->takeAction($this->action, $this->dest, $opt,
            $value, $values, $parser);
    }
    
    function takeAction($action, $dest, $opt, $value, $values, $parser) {
        if ($action === 'store') {
            $values[$dest] = $value;
        }
        else if ($action === 'store_const') {
            $values[$dest] = $this->const;
        }
        else if ($action === 'store_true') {
            $values[$dest] = true;
        }
        else if ($action === 'store_false') {
            $values[$dest] = false;
        }
        else if ($action === 'append') {
            $values->ensureValue($dest, array());
            $values[$dest][] = $value;
        }
        else if ($action === 'append_const') {
            $values->ensureValue($dest, array());
            $values[$dest][] = $this->const;
        }
        else if ($action === 'count') {
            $values[$dest] = $values->ensureValue($dest, 0) + 1;
        }
        else if ($action === 'callback') {
            $args = $this->callback_args ?: array();
            $this->callback($this, $opt, $values, $parser, $args);
        }
        else if ($action === 'help') {
            $parser->printHelp();
            $parser->quit();
        }
        else if ($action === 'version') {
            $parser->printVersion();
            $parser->quit();
        }
        else {
            throw new \Exception("unknown action {$this->action}");
        }
        
        return 1;
    }
}
