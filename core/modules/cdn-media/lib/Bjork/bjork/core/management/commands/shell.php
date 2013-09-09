<?php

namespace bjork\core\management\commands\shell;

use bjork\conf\settings,
    bjork\core\management\base;

class Command extends base\NoArgsCommand {
    static
        $help = "Runs a PHP interactive interpreter.

Note that autoloading does not work during the session; you must include any files explicitly.";
    
    function handleNoArgs($options) {
        
        $descriptorspec = array(
            // 0 => array('pipe', 'r'),
            // 1 => array('pipe', 'w'),
            // 2 => array('pipe', 'a')
        );
        
        $shell = proc_open('/usr/bin/env BJORK_INTERACTIVE_SHELL=1 php '.
            '-d "include_path='.escapeshellarg(get_include_path()).'" '.
            '-a', $descriptorspec, $pipes);
        
        if (is_resource($shell)) {
            proc_close($shell);
            fwrite(STDOUT, "\n");
        }
    }
}
