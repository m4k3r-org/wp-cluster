<?php

namespace bjork\template\loaders\app_directories;

use os\path;

use bjork\conf\settings,
    bjork\core\loading,
    bjork\template\TemplateDoesNotExist,
    bjork\template\loader\BaseLoader;

function get_app_template_dirs() {
    static $dircache = null;
    if (null === $dircache) {
        $apps = loading::get_apps();
        $dirs = array();
        foreach ($apps as $app)
            $dirs[] = $app->getFullPath() . DIRECTORY_SEPARATOR . 'templates';
        $dircache = $dirs;
    }
    return $dircache;
}

class Loader extends BaseLoader {
    
    /**
    * Returns the absolute paths to "template_name", when appended to each
    * directory in "template_dirs". Any paths that don't lie inside one of the
    * template dirs are excluded from the result set, for security reasons.
    */
    function getTemplateSources($template_name, $template_dirs=null) {
        if (!$template_dirs)
            $template_dirs = get_app_template_dirs();
        $paths = array();
        foreach ($template_dirs as $dir)
            $paths[] = path::join($dir, $template_name);
        return $paths;
    }
    
    function loadTemplateSource($template_name, $template_dirs=null) {
        foreach ($this->getTemplateSources($template_name, $template_dirs) as $filepath) {
            try {
                return array(file_get_contents($filepath), $filepath);
            } catch (\ErrorException $e) {
                // pass
            }
        }
        throw new TemplateDoesNotExist($template_name);
    }
}
