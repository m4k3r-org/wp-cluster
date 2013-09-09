<?php

namespace bjork\template {

use strutils;

use bjork\conf\settings,
    bjork\core\loading,
    bjork\core\exceptions\ImproperlyConfigured,
    bjork\template\Template,
    bjork\template\TemplateDoesNotExist,
    bjork\template\context\Context;

final class loader {
    
    static $template_source_loaders = null;
    
    /**
    * Loads the given template_name and renders it with the given dictionary as
    * context. The template_name may be a string to load a single template using
    * get_template, or it may be an array to use select_template to find one of
    * the templates in the list. Returns a string.
    */
    public static function render_to_string($template_name,
                                            array $dictionary=null,
                                            $context_instance=null)
    {
        if (is_array($template_name))
            $t = self::select_template($template_name);
        else
            $t = self::get_template($template_name);
        
        if (null === $dictionary)
            $dictionary = array();
        
        if (null === $context_instance)
            return $t->render(new Context($dictionary));
        
        // Add the dictionary to the context stack, ensuring it gets removed
        // again to keep the context_instance in the same state it started in.
        $context_instance->update($dictionary);
        try {
            return $t->render($context_instance);
        } catch (\Exception $e) {
            $context_instance->pop();
            throw $e;
        }
        $context_instance->pop();
    }
    
    /**
    * Given a list of template names, returns the first that can be loaded.
    */
    public static function select_template(array $template_name_list) {
        if (empty($template_name_list))
            throw new TemplateDoesNotExist('No template names provided');
        
        $not_found = array();
        foreach ($template_name_list as $template_name) {
            try {
                return self::get_template($template_name);
            } catch (TemplateDoesNotExist $e) {
                $errmsg = $e->getMessage();
                if (!in_array($errmsg, $not_found))
                    $not_found[] = $errmsg;
                continue;
            }
        }
        // If we get here, none of the templates could be loaded
        throw new TemplateDoesNotExist(strutils::join(', ', $not_found));
    }
    
    /**
    * Returns a compiled Template object for the given template name,
    * handling template inheritance recursively.
    */
    public static function get_template($template_name) {
        list($template, $origin) = self::find_template($template_name);
        if (!method_exists($template, 'render')) // if it quacks like a duck...
            $template = self::get_template_from_string($template, $origin, $template_name);
        return $template;
    }
    
    /**
    * Returns a compiled Template object for the given template code,
    * handling template inheritance recursively.
    */
    public static function get_template_from_string($source, $origin=null, $name=null) {
        return new Template($source, $origin, $name);
    }
    
    public static function find_template($name, array $dirs=null) {
        if (null === self::$template_source_loaders) {
            $loaders = array();
            foreach (settings::get('TEMPLATE_LOADERS') as $loader)
                $loaders[] = self::find_template_loader($loader);
            self::$template_source_loaders = $loaders;
        }
        
        foreach (self::$template_source_loaders as $loader) {
            try {
                list($source, $display_name) = $loader($name, $dirs);
                return array($source, loader\make_origin($display_name, $loader, $name, $dirs));
            } catch (TemplateDoesNotExist $e) {
                // pass
            }
        }
        
        throw new TemplateDoesNotExist($name);
    }
    
    static function find_template_loader($loader) {
        if (is_array($loader)) {
            $loader_cls = array_shift($loader);
            $args = $loader_cls;
        } else {
            $loader_cls = $loader;
            $args = array();
        }
        return new $loader_cls($args);
    }
    
    static function get_template_source_loaders() {
        return self::$template_source_loaders;
    }
}

}

namespace bjork\template\loader {

use bjork\conf\settings,
    bjork\template,
    bjork\template\loader,
    bjork\template\Origin,
    bjork\template\TemplateDoesNotExist;

function make_origin($display_name, $loader, $name, $dirs) {
    if (settings::get('TEMPLATE_DEBUG') && $display_name)
        return new LoaderOrigin($display_name, $loader, $name, $dirs);
    return null;
}

class LoaderOrigin extends Origin {
    var $loader, $loadname, $dirs;
    
    function __construct($display_name, $loader, $name, $dirs) {
        parent::__construct($display_name);
        $this->loader = $loader;
        $this->loadname = $name;
        $this->dirs = $dirs;
    }
    
    function reload() {
        $source = call_user_func_array($this->loader, array(
            $this->loadname, $this->dirs));
        return end($source);
    }
}

abstract class BaseLoader {
    
    function __construct(array $options=null) {}
    
    function __invoke($template_name, $template_dirs=null) {
        return $this->loadTemplate($template_name, $template_dirs);
    }
    
    function loadTemplate($template_name, $template_dirs=null) {
        list($source, $display_name) = $this->loadTemplateSource(
            $template_name, $template_dirs);
        $origin = make_origin($display_name,
            array($this, 'loadTemplateSource'),
            $template_name, $template_dirs);
        
        try {
            $template = loader::get_template_from_string($source, $origin, $template_name);
            return array($template, null);
        } catch (TemplateDoesNotExist $e) {
            // If compiling the template we found raises TemplateDoesNotExist,
            // back off to returning the source and display name for the
            // template we were asked to load. This allows for correct
            // identification (later) of the actual template that does
            // not exist.
            return array($source, $template_name);
        }
    }
    
    /**
    * Resets any state maintained by the loader instance (e.g., cached
    * templates or cached loader modules).
    */
    public function reset() {
        // pass
    }
    
    /**
    * Returns a tuple containing the source and origin for the given template
    * name.
    */
    abstract public function loadTemplateSource($template_name, $template_dirs=null);
}

}
