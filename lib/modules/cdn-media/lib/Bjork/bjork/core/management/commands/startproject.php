<?php

namespace bjork\core\management\commands\startproject;

use bjork\conf\settings,
    bjork\core\management\base\CommandError,
    bjork\core\management\templates\TemplateCommand,
    bjork\utils\crypto;

class Command extends TemplateCommand {
    static
        $help = 'Creates a Bjork project directory structure for the given project name in the current directory or optionally in the given directory.';
    
    function handle(array $args, $options) {
        if (count($args) === 2) {
            $target = array_pop($args);
            $project_name = array_pop($args);
        } else if (count($args) === 1) {
            $target = null;
            $project_name = array_pop($args);
        } else {
            throw new CommandError('you must provide a project name');
        }
        
        // Create a random SECRET_KEY hash to put it in the main settings.
        $chars = 'abcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*(-_=+)';
        $options['secret_key'] = crypto::get_random_string(50, $chars);
        
        parent::handle(array('project', $project_name, $target), $options);
    }
}
