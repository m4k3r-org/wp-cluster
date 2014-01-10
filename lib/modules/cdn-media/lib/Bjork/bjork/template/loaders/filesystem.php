<?php

namespace bjork\template\loaders\filesystem;

use os\path;

use bjork\conf\settings,
    bjork\template\TemplateDoesNotExist,
    bjork\template\loader\BaseLoader;

class Loader extends BaseLoader {
    
    /**
    * Returns the absolute paths to "template_name", when appended to each
    * directory in "template_dirs". Any paths that don't lie inside one of the
    * template dirs are excluded from the result set, for security reasons.
    */
    function getTemplateSources($template_name, $template_dirs=null) {
        if (!$template_dirs)
            $template_dirs = settings::get('TEMPLATE_DIRS');
        $paths = array();
        foreach ($template_dirs as $dir)
            $paths[] = path::join($dir, $template_name);
        return $paths;
    }
    
    function loadTemplateSource($template_name, $template_dirs=null) {
        $tried = array();
        foreach ($this->getTemplateSources($template_name, $template_dirs) as $filepath) {
            try {
                return array(file_get_contents($filepath), $filepath);
            } catch (\ErrorException $e) {
                $tried[] = $filepath;
            }
        }
        if (!empty($tried))
            $error_msg = 'Tried '.implode(', ', $tried);
        else
            $error_msg = 'Your TEMPLATE_DIRS setting is empty. Change it '.
                         'to point to at least one template directory.';
        throw new TemplateDoesNotExist($error_msg);
    }
}
