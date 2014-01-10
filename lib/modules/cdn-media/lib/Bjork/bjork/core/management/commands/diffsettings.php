<?php

namespace bjork\core\management\commands\diffsettings;

use pprint;

use bjork\conf\settings,
    bjork\core\management\base;

class Command extends base\NoArgsCommand {
    static
        $help = "Displays differences between the current settings.php and Bjork's default settings. Settings that don't appear in the defaults are followed by \"###\".";
    
    function handleNoArgs($options) {
        
        // Because settings are imported lazily, we need to
        // explicitly load them. The simplest way to do this
        // is as a side-effect of getting a setting.
        settings::get('DEBUG');
        
        $user_settings = (array)settings::$settings->_wrapped;
        $default_settings = include settings\DEFAULTS_MODULE_PATH;
        
        $output = array();
        $keys = array_keys($user_settings);
        sort($keys);
        
        foreach ($keys as $key) {
            $value = pprint::dump($user_settings[$key]);
            if (!array_key_exists($key, $default_settings))
                $output[] = "### {$key} = {$value}";
            else if ($user_settings[$key] !== $default_settings[$key])
                $output[] = "{$key} = {$value}";
        }
        
        return implode("\n", $output) . "\n";
    }
}
