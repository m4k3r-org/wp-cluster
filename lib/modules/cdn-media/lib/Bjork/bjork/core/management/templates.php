<?php

namespace bjork\core\management\templates;

use os,
    os\path;
use optparse;
use strutils;

use bjork\conf\settings,
    bjork\core\management\utils,
    bjork\core\management\base\BaseCommand,
    bjork\core\management\base\CommandError,
    bjork\template\Template,
    bjork\template\context\Context;

/**
* Copies either a Bjork application layout template or a Bjork project
* layout template into the specified directory.
* 
* app_or_project:   The string 'app' or 'project'.
* name:             The name of the application or project.
* directory:        The directory to which the template should be copied.
* options:          The additional variables passed to project or app templates
*/
class TemplateCommand extends BaseCommand {
    static
        $args = '[name] [optional destination directory]',
        
        // Can't import settings during this command, because they haven't
        // necessarily been created.
        $canImportSettings = false,
        
        // The supported URL schemes.
        $url_schemes = array('http', 'https', 'ftp');
    
    var $app_or_project,
        $paths_to_remove,
        $verbosity;
    
    public static function getOptionList() {
        return array(
            optparse::make_option('--template', array(
                'action' => 'store', 'dest' => 'template',
                'help' => 'The dotted import path to load the template from'
            )),
            optparse::make_option('--extension', '-e', array(
                'action' => 'append', 'dest' => 'extensions', 'default' => array('php'),
                'help' => 'The file extension(s) to render (default: "php"). '.
                          'Separate multiple extensions with commas, or use '.
                          '-e multiple times.'
            )),
            optparse::make_option('--name', '-n', array(
                'action' => 'append', 'dest' => 'files', 'default' => array(),
                'help' => 'The file name(s) to render. '.
                          'Separate multiple names with commas, or use '.
                          '-n multiple times.'
            )),
        );
    }
    
    function handle(array $args, $options) {
        $app_or_project = $args[0];
        $name = $args[1];
        $target = count($args) == 3 ? $args[2] : null;
        
        $this->app_or_project = $app_or_project;
        $this->paths_to_remove = array();
        $this->verbosity = (int)$options['verbosity'];
        
        // if it's not a valid directory name
        if (!preg_match('/^[_a-zA-Z]\w*$/', $name)) {
            // Provide an error message
            if (!preg_match('/^[_a-zA-Z]/', $name))
                $message = 'make sure the name begins with a letter or underscore';
            else
                $message = 'use only letters, numbers and underscores';
            throw new CommandError(
                "{$name} is not a valid {$app_or_project} name. ".
                "Please {$message}.");
        }
        
        // if some directory is given, make sure it's nicely expanded
        if ($target === null) {
            $top_dir = getcwd() . DIRECTORY_SEPARATOR . $name;
            try {
                mkdir($top_dir, 0777, true);
            } catch (\ErrorException $e) {
                throw new CommandError($e->getMessage());
            }
        } else {
            $top_dir = path::abspath($target);
            if (!is_dir($top_dir))
                throw new CommandError(
                    "Destination directory '{$top_dir}' does not exist, ".
                    "please create it first.");
        }
        
        $extensions = utils::handle_extensions($options['extensions'], array());
        $extra_files = array();
        foreach ($options['files'] as $file)
            $extra_files = array_merge($extra_files,
                array_map('trim', explode(',', $file)));
        if ($this->verbosity >= 2) {
            fwrite($this->stdout, "Rendering {$app_or_project} template files ".
                                  "with extensions: ".implode(', ', $extensions).
                                  "\n");
            fwrite($this->stdout, "Rendering {$app_or_project} template files ".
                                  "with filenames: ".implode(', ', $extra_files).
                                  "\n");
        }
        
        $base_name = "{$app_or_project}_name";
        $base_subdir = "{$app_or_project}_template";
        $base_directory = "{$app_or_project}_directory";
        
        $context = new Context(array_merge((array)$options, array(
            $base_name => $name,
            $base_directory => $top_dir,
        )));
        
        // Setup a stub settings environment for template rendering
        if (!settings::is_configured())
            settings::configure();
        
        $template_dir = $this->handleTemplate($options['template'], $base_subdir);
        $prefix_length = mb_strlen($template_dir) + 1;
        
        foreach (os::walk($template_dir) as $node) {
            list($root, $dirs, $files) = $node;
            
            $path_rest = mb_substr($root, $prefix_length);
            $relative_dir = str_replace($base_name, $name, $path_rest);
            
            if ($relative_dir) {
                $target_dir = $top_dir . DIRECTORY_SEPARATOR . $relative_dir;
                if (!is_dir($target_dir))
                    mkdir($target_dir, 0777, true);
            }
            
            $dirs = array_filter($dirs, function($dirname) {
                return $dirname{0} !== '.';
            });
            
            foreach ($files as $filename) {
                $old_path = $root . DIRECTORY_SEPARATOR . $filename;
                $new_path = strutils::join(DIRECTORY_SEPARATOR, array(
                    $top_dir, $relative_dir,
                    str_replace($base_name, $name, $filename)));
                if (is_file($new_path))
                    throw new CommandError(
                        "{$new_path} already exists, overlaying a project or app ".
                        "into an existing directory won't replace conflicting files");
                
                if (strutils::endswith($filename, $extensions) || in_array($filename, $extra_files)) {
                    $tplfile = $old_path;
                    if (!strutils::endswith($filename, '.php')) {
                        // Create a temporary file in the destination path
                        // adding the .php extension so it is parsed by PHP
                        $tplfile = "{$new_path}.php";
                        copy($old_path, $tplfile);
                        $this->paths_to_remove[] = $tplfile;
                    }
                    $template = new Template(file_get_contents($tplfile));
                    $content = $template->render($context);
                    $content = str_replace('<{?', '<?', $content);
                    file_put_contents($new_path, $content);
                } else {
                    copy($old_path, $new_path);
                }
                
                if ($this->verbosity >= 2)
                    fwrite($this->stdout, "Creating {$new_path}\n");
                try {
                    os::copymode($old_path, $new_path);
                    $this->makeWritable($new_path);
                } catch (\ErrorException $e) {
                    fwrite(STDERR,
                        "Notice: Couldn't set permission bits on {$new_path}. ".
                        "You're probably using an uncommon filesystem setup. ".
                        "No problem.\n");
                }
            }
        }
        
        if (!empty($this->paths_to_remove)) {
            if ($this->verbosity >= 2)
                fwrite($this->stdout, "Cleaning up temporary files.\n");
            $paths_to_remove = array_unique($this->paths_to_remove);
            foreach ($paths_to_remove as $path_to_remove) {
                if (is_file($path_to_remove))
                    unlink($path_to_remove);
                else
                    os::rmtree($path_to_remove);
            }
        }
    }
    
    /**
    * Determines where the app or project templates are.
    * Use BJORK_ROOT as the default because we don't
    * know into which directory Bjork has been installed.
    */
    function handleTemplate($template, $subdir) {
        if (null === $template)
            return implode(DIRECTORY_SEPARATOR,
                array(BJORK_ROOT, 'bjork', 'conf', $subdir));
        
        if (strutils::startswith($template, 'file://'))
            $template = mb_substr($template, 7);
        $expanded_template = realpath($template);
        if (is_dir($expanded_template))
            return $expanded_template;
        
        /* @TODO
        if ($this->isURL($template))
            $absolute_path = $this->download($template);
        else
            $absolute_path = realpath($template);
        if (file_exists($absolute_path))
            return $this->extract($absolute_path);
        */
        
        throw new CommandError(
            "couldn't handle {$this->app_or_project} template {$template}");
    }
    
    /**
    * Downloads the given URL and returns the file name.
    */
    /* @TODO
    function download($url) {
        $cleanup_url = function($url) {
            $tmp = rtrim($url, '/');
            $filename_parts = explode('/', $tmp);
            $filename = end($filename_parts);
            if (strutils::endswith($url, '/'))
                $display_url = $tmp . '/';
            else
                $display_url = $url;
            return array($filename, $display_url);
        };
        
        $prefix = "bjork_{$this->app_or_project}_template_";
        $tempdir = sys_get_temp_dir() . ;
    }
    */
    
    /**
    * Make sure that the file is writeable.
    * Useful if our source is read-only.
    */
    function makeWritable($filename) {
        if (!is_writable($filename)) {
            $st = stat($filename);
            $new_permissions = $stat['mode'] | 128; // writable by user
            chmod($filename, $new_permissions);
        }
    }
}
