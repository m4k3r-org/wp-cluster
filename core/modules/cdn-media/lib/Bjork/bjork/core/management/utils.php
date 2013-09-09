<?php

namespace bjork\core\management;

final class utils {
    
    /**
    * Organizes multiple extensions that are separated with commas or passed by
    * using --extension/-e multiple times. Note that the .py extension is ignored
    * here because of the way non-*.py files are handled in make_messages() (they
    * are copied to file.ext.py files to trick xgettext to parse them as Python
    * files).
    * 
    * For example: running 'manage.php makemessages -e js,txt -e xhtml -a'
    * would result in an extension list: ['.js', '.txt', '.xhtml']
    * 
    *   >>> handle_extensions(['.html', 'html,js,py,py,py,.py', 'py,.py'])
    *   set(['.html', '.js'])
    *   >>> handle_extensions(['.html, txt,.tpl'])
    *   set(['.html', '.tpl', '.txt'])
    */
    public static function handle_extensions(array $extensions=array('html'),
                                             array $ignored=array('php'))
    {
        $ext_list = array();
        foreach ($extensions as $ext) {
            $ext_list = array_merge($ext_list, array_map(function($e) {
                return trim($e);
            }, explode(',', $ext)));
        }
        for ($i=0; $i < count($ext_list); $i++) { 
            $ext = $ext_list[$i];
            if ($ext{0} !== '.')
                $ext_list[$i] = ".{$ext_list[$i]}";
        }
        return array_unique(array_filter($ext_list, function($ext) use ($ignored) {
            return !in_array(trim($ext, '.'), $ignored);
        }));
    }
    
    /**
    *   >>> handle_csv_option(['html', 'html,js,py,py,py', 'py,py'])
    *   set(['html', 'js'])
    *   >>> handle_csv_option(['html, txt,tpl'])
    *   set(['html', 'tpl', 'txt'])
    */
    public static function handle_csv_option(array $option, array $ignored=array())
    {
        $list = array();
        foreach ($option as $opt) {
            $list = array_merge($list, array_map(function($e) {
                return trim($e);
            }, explode(',', $opt)));
        }
        return array_unique(array_filter($list, function($opt) use ($ignored) {
            return !in_array($opt, $ignored);
        }));
    }
}
