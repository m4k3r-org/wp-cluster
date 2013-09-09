<?php

namespace bjork\conf {

use bjork\conf\settings\LazySettings;

final class settings {
    
    public static $settings = null;
    
    public static function is_configured() {
        return self::setup()->isConfigured();
    }
    
    public static function configure(array $default_settings=null, array $options=null) {
        self::setup()->configure($default_settings, $options);
    }
    
    public static function get($name /*, $default=null*/) {
        $settings = self::setup();
        $args = func_get_args();
        return count($args) > 1
            ? $settings->get($args[0], $args[1])
            : $settings->get($args[0]);
    }
    
    /* private */ static function &setup() {
        if (null === self::$settings)
            self::$settings = new LazySettings();
        return self::$settings;
    }
}

}

namespace bjork\conf\settings {

use bjork\core\exceptions\ImproperlyConfigured,
    bjork\utils\functional\LazyObject;

const ENVIRONMENT_VARIABLE = 'BJORK_SETTINGS_MODULE';
const DEFAULTS_MODULE_PATH = 'bjork/conf/global_settings.php';

/**
* A lazy proxy for either global Bjork settings or a custom settings object.
* The user can manually configure settings prior to using them. Otherwise,
* Bjork uses the settings module pointed to by BJORK_SETTINGS_MODULE.
*/
class LazySettings extends LazyObject {
    
    /**
    * Load the settings module pointed to by the environment variable. This
    * is used the first time we need any settings at all, if the user has not
    * previously configured the settings manually.
    */
    function setup() {
        $settings_module = getenv(ENVIRONMENT_VARIABLE);
        if (!$settings_module)
            throw new ImproperlyConfigured(
                'Settings cannot be imported, because environment variable '.
                ENVIRONMENT_VARIABLE . ' is undefined.');
        $this->_wrapped = new Settings($settings_module);
    }
    
    /**
    * Called to manually configure the settings. The 'default_settings'
    * parameter sets where to retrieve any unspecified values from.
    */
    public function configure(array $default_settings=null, array $options=null) {
        if ($this->isConfigured())
            throw new \RuntimeException('Settings already configured.');
        if (null === $default_settings)
            $default_settings = include DEFAULTS_MODULE_PATH;
        if (null === $options)
            $options = array();
        $holder = new UserSettingsHolder($default_settings);
        foreach ($options as $key => $value)
            $holder->set($key, $value);
        $this->_wrapped = $holder;
    }
    
    /**
    * Returns True if the settings have already been configured.
    */
    public function isConfigured() {
        return $this->_wrapped !== self::EMPTY_SENTINEL;
    }
}

class BaseSettings extends \ArrayObject {
    public $SETTINGS_MODULE = null;
    
    public function get($k /*, $default=null*/) {
        try {
            return $this[$k];
        } catch (\ErrorException $e) {
            $args = func_get_args();
            if (count($args) > 1)
                return $args[1];
            throw $e;
        }
    }
    
    public function set($k, $value) {
        $this[$k] = $value;
    }
    
    public function delete($k) {
        unset($this[$k]);
    }
}

class Settings extends BaseSettings {
    function __construct($settings_module) {
        $defaults = include DEFAULTS_MODULE_PATH;
        parent::__construct($defaults);
        
        $this->SETTINGS_MODULE = $settings_module;
        
        $error = null;
        
        try {
            $settings = include $settings_module;
        } catch (\ErrorException $e) {
            $error = $e;
        }
        
        if (null !== $error)
            throw new ImproperlyConfigured(
                "Could not import settings '{$settings_module}' ".
                "(Is it on include_path?). Error was: {$error->getMessage()}");
        if (!is_array($settings))
            throw new ImproperlyConfigured(
                "'{$settings_module}' must return an associative array of settings.");
        
        foreach ($settings as $key => $value)
            $this[$key] = $value;
        
        if (!$this['SECRET_KEY'])
            throw new ImproperlyConfigured(
                'The SECRET_KEY setting must not be empty.');
        
        // error reporting
        if ($this['DEBUG']) {
            ini_set('display_errors', 'on');
            ini_set('log_errors', 'off');
            ini_set('html_errors', 'on');
        } else {
            ini_set('display_errors', 'off');
            ini_set('log_errors', 'on');
            ini_set('html_errors', 'off');
        }
        
        if (php_sapi_name() == 'cli') {
            ini_set('html_errors', 'off');
        }
        
        // assertions
        if ($this['DEBUG']) {
            assert_options(ASSERT_ACTIVE, 1);
            assert_options(ASSERT_WARNING, 1);
            assert_options(ASSERT_BAIL, 0);
        } else {
            assert_options(ASSERT_ACTIVE, 0);
            assert_options(ASSERT_WARNING, 0);
            assert_options(ASSERT_BAIL, 0);
        }
        
        // i18n
        mb_internal_encoding($this['DEFAULT_CHARSET']);
        
        // timezone
        putenv("TZ={$this['TIME_ZONE']}");
        date_default_timezone_set($this['TIME_ZONE']);
        
        // logging
    }
}

class UserSettingsHolder extends BaseSettings {
    function __construct($default_settings) {
        parent::__construct($default_settings);
        
        // SETTINGS_MODULE doesn't make much sense in the manually configured
        // (standalone) case.
        $this->SETTINGS_MODULE = null;
    }
}

}
