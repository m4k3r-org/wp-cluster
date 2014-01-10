<?php

namespace bjork\core\management\commands\startapp;

use optparse;

use bjork\conf\settings,
    bjork\core\management\base\CommandError,
    bjork\core\management\templates\TemplateCommand,
    bjork\utils\crypto;

class Command extends TemplateCommand {
    static
        $help = 'Creates a Bjork app directory structure for the given app name in the current directory or optionally in the given directory.';
    
    public static function getOptionList() {
        return array_merge(parent::getOptionList(), array(
            optparse::make_option('--namespace', array(
                'action' => 'store', 'dest' => 'namespace', 'default' => null,
                'help' => 'The app namespace. Default is the app name.'
            )),
        ));
    }
    
    function handle(array $args, $options) {
        if (count($args) === 2) {
            $target = array_pop($args);
            $app_name = array_pop($args);
        } else if (count($args) === 1) {
            $target = null;
            $app_name = array_pop($args);
        } else {
            throw new CommandError('you must provide an app name');
        }
        if (!$options['namespace'])
            $options['namespace'] = $app_name;
        else
            $options['namespace'] = "{$options['namespace']}\\{$app_name}";
        parent::handle(array('app', $app_name, $target), $options);
    }
}
