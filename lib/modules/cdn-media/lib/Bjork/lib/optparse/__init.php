<?php

namespace {

require_once __DIR__ . '/errors.php';
require_once __DIR__ . '/option.php';
require_once __DIR__ . '/option_parser.php';
require_once __DIR__ . '/help.php';

use optparse\Option;

final class optparse {
    
    public static function make_option(/* $opt1, $opt2, ..., array $attrs=null*/) {
        list($opts, $attrs) = self::parse_funcargs(func_get_args());
        return new Option($opts, $attrs);
    }
    
    /* internal */ static function parse_funcargs(array $funcargs) {
        if (count($funcargs) > 1 && is_array(end($funcargs))) {
            $kwargs = array_pop($funcargs);
            $args = $funcargs;
        } else {
            $args = $funcargs;
            $kwargs = array();
        }
        return array($args, $kwargs);
    }
}

}
