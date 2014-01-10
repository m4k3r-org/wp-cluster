<?php

namespace bjork\conf;

use bjork\conf\settings,
    bjork\core\exceptions\ImproperlyConfigured,
    bjork\core\urlresolvers\RegexURLPattern,
    bjork\core\urlresolvers\RegexURLResolver,
    bjork\core\urlresolvers\LocaleRegexURLResolver;

final class ImportedURLConf {
    public $urlconf_name, $params;
    
    function __construct($urlconf_name, $params=null) {
        $this->urlconf_name = $urlconf_name;
        $this->params = $params;
    }
}

final class urls {
    
    const handler404 = 'bjork\views\defaults\page_not_found';
    const handler500 = 'bjork\views\defaults\server_error';
    
    public static function import($urlconf_name, $params=null) {
        return new ImportedURLConf($urlconf_name, $params);
    }
    
    public static function patterns($prefix) {
        $pattern_list = array();
        $args = func_get_args();
        array_shift($args); // remove $prefix
        
        foreach ($args as $t) {
            if (is_array($t)) {
                $name = null;
                if (isset($t['name'])) {
                    $name = $t['name'];
                    unset($t['name']);
                }
                $regex = $t[0];
                $view = $t[1];
                $params = array();
                if (count($t) > 2)
                    $params = $t[2];
                $t = self::url($regex, $view, $params, $name, $prefix);
            } else if ($t instanceof RegexURLPattern) {
                $t->addPrefix($prefix);
            }
            $pattern_list[] = $t;
        }
        
        return $pattern_list;
    }
    
    public static function i18n_patterns($prefix) {
        $args = func_get_args();
        $fn = sprintf('%s::%s', __CLASS__, 'patterns');
        $pattern_list = call_user_func_array($fn, $args);
        if (!settings::get('USE_I18N'))
            return $pattern_list;
        return array(new LocaleRegexURLResolver($pattern_list));
    }
    
    public static function url($regex, $view, $params=null, $name=null, $prefix='') {
        if ($view instanceof ImportedURLConf) {
            // For import(...) processing.
            $resolver = new RegexURLResolver($regex, $view->urlconf_name, $view->params);
            return $resolver;
        }
        if (is_string($view)) {
            if (empty($view))
                throw new ImproperlyConfigured(
                    "Empty URL pattern view name not permitted ".
                    "(for pattern {$regex})");
            if (!empty($prefix))
                $view = "{$prefix}\\{$view}";
        }
        $pattern = new RegexURLPattern($regex, $view, $params, $name);
        return $pattern;
    }
}
