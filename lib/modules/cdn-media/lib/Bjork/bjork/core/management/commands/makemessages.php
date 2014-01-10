<?php

namespace bjork\core\management\commands\makemessages;

use fnmatch;
use strutils;
use optparse;
use os;

use bjork\conf\settings,
    bjork\core\management\utils,
    bjork\core\management\base,
    bjork\core\management\base\CommandError;

const PLURAL_FORMS_RE = '/^(?P<value>"Plural-Forms.+?\\\n")\s*$/ms';

function _popen($cmd) {
    exec($cmd, $msgs, $errors);
    if (0 !== $errors) $errors = true;
    else $errors = false;
    return array(implode("\n", $msgs), $errors);
}

function walk($root, $followlinks=false, $ignore_patterns=null, $verbosity=0, $stdout=null) {
    if ($stdout === null)
        $stdout = STDOUT;
    
    if ($ignore_patterns === null)
        $ignore_patterns = array();
    
    $dir_suffix = DIRECTORY_SEPARATOR . '*';
    $norm_patterns = array_map(function($p) use ($dir_suffix) {
        return strutils::endswith($p, $dir_suffix)
            ? mb_substr($p, 0, strlen($dir_suffix))
            : $p;
    }, $ignore_patterns);
    
    $nodes = array();
    
    foreach (os::walk($root) as $node) {
        list($dirpath, $dirnames, $filenames) = $node;
        $remove_dirs = array();
        foreach ($dirnames as $dirname) {
            $path = realpath($dirpath . DIRECTORY_SEPARATOR . $dirname);
            if (is_ignored($path, $norm_patterns))
                $remove_dirs[] = $dirname;
        }
        foreach ($remove_dirs as $dirname) {
            $dirnames = array_diff($dirnames, array($dirname));
            if ($verbosity > 1)
                fwrite($stdout, "ignoring directory {$dirname}\n");
        }
        
        $nodes[] = array($dirpath, $dirnames, $filenames);
        
        if ($followlinks) {
            foreach ($dirnames as $d) {
                $p = $dirpath . DIRECTORY_SEPARATOR . $d;
                if (is_link($p)) {
                    foreach (os::walk($p) as $node)
                        $nodes[] = $node;
                }
            }
        }
    }
    
    return $nodes;
}

/**
* Helper function to check if the given path should be ignored or not.
*/
function is_ignored($path, $ignore_patterns) {
    foreach ($ignore_patterns as $pattern) {
        if (fnmatch::matchcase($path, $pattern))
            return true;
    }
    return false;
}

/**
* Helper function to get all files in the given root.
*/
function find_files($root, $ignore_patterns, $verbosity, $symlinks=false, $stdout=null) {
    if (null === $stdout)
        $stdout = STDOUT;
    
    $all_files = array();
    foreach (walk($root, $symlinks, $ignore_patterns, $verbosity, $stdout) as $node) {
        list($dirpath, $dirnames, $filenames) = $node;
        foreach ($filenames as $filename) {
            $norm_filepath = realpath($dirpath . DIRECTORY_SEPARATOR . $filename);
            if (is_ignored($norm_filepath, $ignore_patterns)) {
                if ($verbosity > 1)
                    fwrite($stdout, "ignoring file {$filename} in {$dirpath}\n");
            } else {
                $all_files[] = array($dirpath, $filename);
            }
        }
    }
    array_multisort($all_files);
    return $all_files;
}

/**
* Copies plural forms header contents from a Django catalog of locale to
* the msgs string, inserting it at the right place. msgs should be the 
* contents of a newly created .po file.
*/
function copy_plural_forms($msgs, $locale, $domain, $verbosity, $stdout=null) {
    if (null === $stdout)
        $stdout = STDOUT;
    
    $bjork_dir = BJORK_ROOT . DIRECTORY_SEPARATOR . 'bjork';
    
    if ($domain == 'bjorkjs')
        $domains = array('bjorkjs', 'bjork');
    else
        $domains = array('bjork');
    
    foreach ($domains as $domain) {
        $bjork_po = implode(DIRECTORY_SEPARATOR, array(
            $bjork_dir, 'conf', 'locale', $locale, 'LC_MESSAGES', $domain . '.po',
        ));
        if (file_exists($bjork_po)) {
            $matched = preg_match(PLURAL_FORMS_RE, file_get_contents($bjork_po), $m);
            if ($matched > 0) {
                if ($verbosity > 1)
                    fwrite($stdout, "copying plural forms: {$m['value']}\n");
                $lines = array();
                $seen = false;
                $v = $m['value'];
                foreach (explode("\n", $msgs) as $line) {
                    if (!$seen && (empty($line) || false !== strpos($line, '"Plural-Forms'))) {
                        $line = $v;
                        $seen = true;
                    }
                    $lines[] = $line;
                }
                $msgs = implode("\n", $lines);
                break;
            }
        }
    }
    return $msgs;
}

/**
* Creates of updates the $pofile PO file for $domain and $locale. Uses
* contents of the existing $potfile.
* 
* Uses mguniq, msgmerge, and msgattrib GNU gettext utilities.
*/
function write_po_file($pofile, $potfile, $domain, $locale, $verbosity, $stdout,
                       $copy_pforms, $wrap, $location, $no_obsolete)
{
    list($msgs, $errors) = _popen("msguniq {$wrap} --to-code=utf-8 '{$potfile}'");
    if ($errors) {
        unlink($potfile);
        throw new CommandError("errors happened while running msguniq\n$msgs");
    }
    if (is_file($pofile)) {
        file_put_contents($potfile, $msgs);
        list($msgs, $errors) = _popen(
            "msgmerge {$wrap} {$location} -q '{$pofile}' '{$potfile}'");
        if ($errors) {
            unlink($potfile);
            throw new CommandError("errors happened while running msgmerge\n$msgs");
        }
    } else if ($copy_pforms) {
        $msgs = copy_plural_forms($msgs, $locale, $domain, $verbosity, $stdout);
    }
    $msgs = str_replace(
        "#. #-#-#-#-#  {$domain}.pot (PACKAGE VERSION)  #-#-#-#-#\n", "",
        $msgs);
    file_put_contents($pofile, $msgs);
    unlink($potfile);
    if ($no_obsolete) {
        list($msgs, $errors) = _popen(
            "msgattrib {$wrap} {$location} -o '{$pofile}' --no-obsolete '{$pofile}'");
        if ($errors)
            throw new CommandError("errors happened while running msgattrib\n$msgs");
    }
}

/**
* Write the $potfile POT file with the $msgs contents, previously making
* sure its format is valid.
*/
function write_pot_file($potfile, $msgs, $file, $work_file, $is_templatized) {
    if ($is_templatized) {
        $old = '#:' . mb_substr($work_file, 2);
        $new = '#:' . mb_substr($file, 2);
        $msgs = str_replace($old, $new, $msgs);
    }
    
    if (is_file($potfile)) {
        // strip the header
        $m = explode("\n", $msgs);
        $c = count($m);
        $i = 0;
        while ($i < $c) {
            if (empty($m[$i])) {
                $m = array_slice($m, $i);
                break;
            }
            $i++;
        }
        $msgs = implode("\n", $m);
    } else {
        $msgs = str_replace('charset=CHARSET', 'charset=UTF-8', $msgs);
    }
    
    file_put_contents($potfile, $msgs, FILE_APPEND);
}

/**
* Extract translatable literals from $file for $domain creating or updating
* the $potfile POT file.
* 
* Uses the xgettext GNU gettext utility.
*/
function process_file($file, $dirpath, $potfile, $domain, $verbosity,
                      $extensions, $wrap, $location, $stdout=null)
{
    if (null === $stdout)
        $stdout = STDOUT;
    
    if ($verbosity > 1)
        fwrite($stdout, "processing file {$file} in {$dirpath}\n");
    
    $pathinfo = pathinfo($file);
    if (isset($pathinfo['extension']))
        $file_ext = '.' . $pathinfo['extension'];
    else
        $file_ext = '';
    
    if ($domain == 'bjorkjs' && in_array($file_ext, $extensions)) {
        $is_templatized = true;
        $orig_file = $dirpath . DIRECTORY_SEPARATOR . $file;
        $thefile = "{$file}.c";
        $work_file = $dirpath . DIRECTORY_SEPARATOR . $thefile;
        copy($orig_file, $thefile);
        $cmd = "xgettext -d {$domain} -L C {$wrap} {$location} " .
            "--keyword=gettext_noop " .
            "--keyword=pgettext:1c,2 " .
            "--keyword=npgettext:1c,2,3 " .
            "--from-code UTF-8 " .
            "--add-comments=Translators -o - '{$work_file}'";
    } else if ($domain == 'bjork' && ($file_ext == '.php' || in_array($file_ext, $extensions))) {
        $thefile = $file;
        $orig_file = $dirpath . DIRECTORY_SEPARATOR . $file;
        $is_templatized = in_array($file_ext, $extensions);
        if ($is_templatized) {
            $thefile = "{$file}.php";
            copy($orig_file, $thefile);
        }
        $work_file = $dirpath . DIRECTORY_SEPARATOR . $thefile;
        $cmd = "xgettext -d {$domain} -L PHP {$wrap} {$location} " .
            "--keyword=gettext_noop " .
            "--keyword=pgettext:1c,2 " .
            "--keyword=npgettext:1c,2,3 " .
            "--keyword=trans ".
            "--keyword=ftrans ".
            "--keyword=ntrans:1,2 " .
            "--keyword=ptrans:1c,2 ".
            "--keyword=nptrans:1c,2,3 " .
            "--from-code UTF-8 " .
            "--add-comments=Translators -o - '{$work_file}'";
    } else {
        return;
    }
    
    list($msgs, $errors) = _popen($cmd);
    if ($errors) {
        if ($is_templatized)
            unlink($work_file);
        if (is_file($potfile))
            unlink($potfile);
        throw new CommandError(
            "errors happened while running xgettext on {$file}\n$msgs");
    }
    if (!empty($msgs))
        write_pot_file($potfile, $msgs, $orig_file, $work_file, $is_templatized);
    if ($is_templatized)
        unlink($work_file);
}

/**
* Uses the 'locale/' directory from the Bjork tree or an application/project
* to process all files with translatable literals for $domain and $locale.
*/
function make_messages($locale=null, $domain='bjork', $verbosity=1,
                       $all=false, $extensions=null, $symlinks=false,
                       $ignore_patterns=null, $no_wrap=false, $no_location=false,
                       $no_obsolete=false, $stdout=null)
{
    if (null === $stdout)
        $stdout = STDOUT;
    
    if (null === $ignore_patterns)
        $ignore_patterns = array();
    
    // Need to ensure that the i18n framework is enabled
    if (settings::is_configured())
        settings::$settings->_wrapped['USE_I18N'] = true;
    else
        settings::configure(null, array('USE_I18N' => true));
    
    $invoked_for_bjork = false;
    if (is_dir('conf' . DIRECTORY_SEPARATOR . 'locale')) {
        $localedir = realpath('conf' . DIRECTORY_SEPARATOR . 'locale');
        $invoked_for_bjork = true;
        // Ignoring all contrib apps
        $ignore_patterns[] = 'contrib/*';
    } else if (is_dir('locale')) {
        $localedir = realpath('locale');
    } else {
        throw new CommandError(
            'This script should be run from the Bjork root directory or your '.
            'project or app tree. If you did indeed run it from the SVN '.
            'checkout or your project or application, maybe you are just '.
            'missing the conf/locale (in the bjork root) or locale (for '.
            'project and application) directory? It is not created '.
            'automatically, you have to create it by hand if you want to '.
            'enable i18n for your project or application.');
    }
    
    if (!in_array($domain, array('bjork', 'bjorkjs')))
        throw new CommandError(
            "Currently makemessages only supports domains 'bjork' and 'bjorkjs'");
    
    if (($locale === null && !$all) || $domain === null)
        throw new CommandError(sprintf(
            "Type '%s help %s' for usage information.",
            basename($_SERVER['argv'][0]),
            $_SERVER['argv'][1]));
    
    $locales = array();
    if ($locale !== null)
        $locales[] = $locale;
    else if ($all) {
        $locale_dirs = array_filter(glob("{$localedir}/*"), 'is_dir');
        $locales = array_map('basename', $locale_dirs);
    }
    
    $wrap = $no_wrap ? '--no-wrap' : '';
    $location = $no_location ? '--no-location' : '';
    
    foreach ($locales as $locale) {
        if ($verbosity > 0)
            fwrite($stdout, "processing language {$locale}\n");
        $basedir = $localedir . DIRECTORY_SEPARATOR . $locale .
            DIRECTORY_SEPARATOR . 'LC_MESSAGES';
        if (!is_dir($basedir))
            mkdir($basedir, 0777, true); // recursive
        
        $pofile = $basedir . DIRECTORY_SEPARATOR . "{$domain}.po";
        $potfile = $basedir . DIRECTORY_SEPARATOR . "{$domain}.pot";
        
        if (file_exists($potfile))
            unlink($potfile);
        
        foreach (find_files('.', $ignore_patterns, $verbosity, $symlinks, $stdout) as $node) {
            list($dirpath, $file) = $node;
            process_file($file, $dirpath, $potfile, $domain,
                         $verbosity, $extensions, $wrap, $location,
                         $stdout);
        }
        
        if (file_exists($potfile)) {
            write_po_file($pofile, $potfile, $domain, $locale,
                          $verbosity, $stdout, !$invoked_for_bjork,
                          $wrap, $location, $no_obsolete);
        }
    }
}

class Command extends base\NoArgsCommand {
    static
        $canImportSettings = false,
        $help = "Runs over the entire source tree of the current directory and pulls out all strings marked for translation. It creates (or updates) a message file in the conf/locale (in the bjork tree) or locale (for projects and applications) directory.\n\nYou must run this command with one of either the --locale or --all options";
    
    public static function getOptionList() {
        return array(
            optparse::make_option('--locale', '-l', array(
                'default' => null, 'dest' => 'locale',
                'help' => 'Creates or updates the message files for the '.
                          'given locale (e.g. pt_BR).')),
            optparse::make_option('--domain', '-d', array(
                'default' => 'bjork', 'dest' => 'domain',
                'help' => 'The domain of the message files (default: '.
                          '"bjork").')),
            optparse::make_option('--all', '-a', array(
                'default' => false, 'action' => 'store_true', 'dest' => 'all',
                'help' => 'Updates the message files for all existing locales.')),
            optparse::make_option('--extension', '-e', array(
                'action' => 'append', 'dest' => 'extensions',
                'help' => 'The file extension(s) to examine (default: '.
                          '"html,txt", or "js" if the domain is "bjorkjs"). '.
                          'Separate multiple extensions with commas, or use '.
                          '-e multiple times.')),
            optparse::make_option('--symlinks', '-s', array(
                'default' => false, 'action' => 'store_true', 'dest' => 'symlinks',
                'help' => 'Follows symlinks to directories when examining '.
                          'source code and templates for translation strings.')),
            optparse::make_option('--ignore', '-i', array(
                'default' => array(), 'metavar' => 'PATTERN',
                'action' => 'append', 'dest' => 'ignore_patterns',
                'help' => 'Ignore files or directories matching this '.
                          'glob-style pattern. Use multiple times to ignore '.
                          'more.')),
            optparse::make_option('--no-default-ignore', array(
                'default' => true, 'action' => 'store_false',
                'dest' => 'use_default_ignore_patterns',
                'help' => "Don't ignore the common glob-style patterns ".
                          "'CVS', '.*' and '*~'.")),
            optparse::make_option('--no-wrap', array(
                'default' => false, 'action' => 'store_true', 'dest' => 'no_wrap',
                'help' => "Don't break long message lines into several lines")),
            optparse::make_option('--no-location', array(
                'default' => false, 'action' => 'store_true', 'dest' => 'no_location',
                'help' => "Don't write '#: filename:line' lines")),
            optparse::make_option('--no-obsolete', array(
                'default' => false, 'action' => 'store_true', 'dest' => 'no_obsolete',
                'help' => 'Remove obsolete message strings')),
        );
    }
    
    function handleNoArgs($options) {
        $locale = $options['locale'];
        $domain = $options['domain'];
        $verbosity = (int)$options['verbosity'];
        $all = $options['all'];
        $extensions = $options['extensions'];
        $symlinks = $options['symlinks'];
        $ignore_patterns = $options['ignore_patterns'];
        if ($options['use_default_ignore_patterns'])
            $ignore_patterns = array_merge(
                $ignore_patterns,
                array('CVS', '.*', '*~'));
        $ignore_patterns = array_unique($ignore_patterns);
        $no_wrap = $options['no_wrap'];
        $no_location = $options['no_location'];
        $no_obsolete = $options['no_obsolete'];
        if ($domain === 'bjorkjs')
            $exts = empty($extensions) ? array('js') : $extensions;
        else
            $exts = empty($extensions) ? array('html', 'txt') : $extensions;
        $extensions = utils::handle_extensions($exts);
        
        if ($verbosity > 1)
            fwrite($this->stdout,
                "examining files with extensions: " . implode(', ', $extensions) . "\n");
        
        make_messages($locale, $domain, $verbosity, $all, $extensions,
                      $symlinks, $ignore_patterns, $no_wrap, $no_location,
                      $no_obsolete, $this->stdout);
    }
}
