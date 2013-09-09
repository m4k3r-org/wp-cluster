<?php

namespace bjork\core {

use os\path;

use strutils;

use bjork,
    bjork\conf\settings,
    bjork\core\management\ImportError,
    bjork\core\management\ManagementUtility,
    bjork\core\management\base\CommandError,
    bjork\utils\importlib;

require_once 'bjork/bjork.php';

final class management {
    
    protected static $commands = null;
    
    public static function get_version() {
        return bjork::get_version();
    }
    
    /**
    * Given a path to a management directory, returns a list of all the command
    * names that are available.
    * 
    * Returns an empty list if no commands are defined.
    */
    public static function find_commands($management_dir) {
        $command_dir = $management_dir . DIRECTORY_SEPARATOR . 'commands';
        $commands = array();
        
        if ($handle = opendir($command_dir)) {
            try {
                while (false !== ($entry = readdir($handle))) {
                    if (is_file($command_dir . DIRECTORY_SEPARATOR . $entry)) {
                        if ($entry{0} != '_' && substr($entry, -4) == '.php')
                            $commands[] = substr($entry, 0, -4);
                    }
                }
                closedir($handle);
            } catch (\ErrorException $e) {
                closedir($handle);
                return array();
            }
        }
        
        return $commands;
    }
    
    /**
    * Determines the path to the management module for the given app_name,
    * without actually importing the application or the management module.
    * 
    * Raises ImportError if the management module cannot be found for any
    * reason.
    */
    public static function find_management_module($app_name) {
        $path = str_replace('\\', DIRECTORY_SEPARATOR, "{$app_name}\management");
        $found_path = null;
        
        foreach (importlib::get_include_paths() as $dir) {
            $p = path::join($dir, $path);
            if (is_dir($p)) {
                $found_path = $p;
                break;
            }
        }
        
        if (!$found_path)
            throw new ImportError($path);
        return $found_path;
    }
    
    /**
    * Given a command name and an application name, returns the Command
    * class instance. All errors raised by the import process
    * (ImportError, AttributeError) are allowed to propagate.
    */
    public static function load_command_class($app_name, $name) {
        $command_file = str_replace('\\', DIRECTORY_SEPARATOR,
            "{$app_name}\management\commands\\{$name}.php");
        $command_class = "{$app_name}\management\commands\\{$name}\Command";
        try {
            require_once $command_file;
        } catch (\ErrorException $e) {
            throw new ImportError($command_class);
        }
        
        if (class_exists($command_class))
            return new $command_class();
        
        throw new ImportError($command_class);
    }
    
    /**
    * Returns a dictionary mapping command names to their callback
    * applications.
    * 
    * This works by looking for a management.commands package in django.core,
    * and in each installed application -- if a commands package exists, all
    * commands in that package are registered.
    * 
    * Core commands are always included. If a settings module has been
    * specified, user-defined commands will also be included.
    * 
    * The dictionary is in the format {command_name: app_name}. Key-value
    * pairs from this dictionary can then be used in calls to
    * load_command_class(app_name, command_name)
    * 
    * If a specific version of a command must be loaded (e.g., with the
    * startapp command), the instantiated module can be placed in the
    * dictionary in place of the application name.
    * 
    * The dictionary is cached on the first call and reused on subsequent
    * calls.
    */
    public static function get_commands() {
        if (null === self::$commands) {
            $commands = array();
            foreach (self::find_commands(__DIR__) as $name)
                $commands[$name] = 'bjork\core';
            
            try {
                $apps = settings::get('INSTALLED_APPS');
            } catch (\Exception $e) {
                $apps = array();
            }
            
            foreach ($apps as $app_name) {
                try {
                    $path = self::find_management_module($app_name);
                    $c = array();
                    foreach (self::find_commands($path) as $name)
                        $c[$name] = $app_name;
                    $commands = array_merge($commands, $c);
                } catch (ImportError $e) {
                    // pass -- no management module
                }
            }
            
            self::$commands = $commands;
        }
        
        return self::$commands;
    }
    
    /**
    * Calls the given command, with the given options and args/kwargs.
    *
    * This is the primary API you should use for calling specific commands.
    *
    * Some examples:
    *     call_command('syncdb')
    *     call_command('shell', plain=True)
    *     call_command('sqlall', 'myapp')
    */
    public static function call_command($name, array $args=null, array $options=null) {
        if (null === $args) $args = array();
        if (null === $options) $options = array();
        
        $commands = self::get_commands();
        if (!array_key_exists($name, $commands))
            throw new CommandError("Unknown command: {$name}");
        
        try {
            $app_name = $commands[$name];
            if ($app_name instanceof BaseCommand)
                // If the command is already loaded, use it directly.
                $klass = $app_name;
            else
                $klass = self::load_command_class($app_name, $name);
        } catch (ImportError $e) {
            throw new CommandError("Unknown command: {$name}");
        }
        
        $defaults = array();
        foreach ($klass->getOptionList() as $opt)
            $defaults[$opt->destination] = $opt->default;
        $defaults = array_merge($defaults, $options);
        
        return $klass->execute($args, $defaults);
    }
    
    /**
    * A simple method that runs a ManagementUtility.
    */
    public static function execute_from_command_line($argv=null) {
        $utility = new ManagementUtility($argv);
        $utility->execute();
    }
    
    /**
    * Include any default options that all commands should accept here
    * so that ManagementUtility can handle them before searching for
    * user commands.
    */
    public static function handle_default_options($options) {
        if (!empty($options['settings']))
            putenv("BJORK_SETTINGS_MODULE={$options['settings']}");
        if (!empty($options['includepath']))
            set_include_path(get_include_path() . PATH_SEPARATOR .
                $options['includepath']);
    }
}

}

namespace bjork\core\management {

use strutils;

use optparse\OptionParser,
    optparse\SystemExit,
    optparse\Values;

use bjork\core\management,
    bjork\core\management\base,
    bjork\core\management\base\BaseCommand;

class ImportError extends \Exception {}

/**
* An option parser that doesn't raise any errors on unknown options.
* 
* This is needed because the --settings and --includepath options affect
* the commands (and thus the options) that are available to the user.
*/
class LaxOptionParser extends OptionParser {
    
    function error($msg) {}
    
    function quit($status=0, $msg=null) {
        if ($msg)
            fwrite(STDERR, $msg);
        throw new \Exception($status);
    }
    
    /**
    * Output nothing.
    * 
    * The lax options are included in the normal option parser, so under
    * normal usage, we don't need to print the lax options.
    */
    function printHelp($file=null) {}
    
    /**
    * Output the basic options available to every command.
    * 
    * This just redirects to the default print_help() behavior.
    */
    function printLaxHelp() {
        parent::printHelp();
    }
    
    function processArgs(&$largs, &$rargs, Values $values) {
        while (!empty($rargs)) {
            $arg = $rargs[0];
            try {
                if (substr($arg, 0, 2) == '--' && strlen($arg) > 2) {
                    $this->processLongOpt($rargs, $values);
                } else if (substr($arg, 0, 1) == '-' && strlen($arg) > 1) {
                    $this->processShortOpts($rargs, $values);
                } else {
                    array_shift($rargs);
                    throw new \Exception();
                }
            } catch (\Exception $e) {
                $largs[] = $arg;
            }
        }
    }
}

/**
* A ManagementUtility has a number of commands, which can be manipulated
* by editing the self.commands dictionary.
*/
class ManagementUtility {
    
    var $argv, $prog_name;
    
    function __construct($argv=null) {
        if (null === $argv)
            $argv = $_SERVER['argv'];
        $this->argv = $argv;
        $this->prog_name = basename($this->argv[0]);
    }
    
    /**
    * Returns the script's main help text, as a string.
    */
    function getMainHelpText($commands_only=false) {
        if ($commands_only) {
            $usage = array_keys(management::get_commands());
            sort($usage);
        } else {
            $usage = array(
                '',
                "Type '{$this->prog_name} help <subcommand>' for help on a ".
                'specific subcommand.',
                '',
                'Available subcommands:',
            );
            $commands_dict = array();
            foreach (management::get_commands() as $name => $app) {
                if ($app == 'bjork\core') {
                    $app = 'bjork';
                } else {
                    $parts = strutils::rpartition($app, '\\');
                    $app = array_pop($parts);
                }
                $commands_dict[$app][] = $name;
            }
            foreach ($commands_dict as $app => $commands) {
                $usage[] = '';
                $usage[] = "[{$app}]";
                sort($commands);
                foreach ($commands as $name) {
                    $usage[] = "    {$name}";
                }
            }
        }
        return implode("\n", $usage);
    }
    
    /**
    * Tries to fetch the given subcommand, printing a message with the
    * appropriate command called from the command line (usually
    * "bjork-admin.php" or "manage.php") if it can't be found.
    */
    function fetchCommand($subcommand) {
        $commands = management::get_commands();
        if (!array_key_exists($subcommand, $commands)) {
            fwrite(STDERR, "Unknown command: {$subcommand}\n".
                           "Type '{$this->prog_name} help' for usage.\n");
            exit(1);
        }
        
        $app_name = $commands[$subcommand];
        if ($app_name instanceof BaseCommand)
            // If the command is already loaded, use it directly.
            $klass = $app_name;
        else
            $klass = management::load_command_class($app_name, $subcommand);
        
        return $klass;
    }
    
    /**
    * Given the command-line arguments, this figures out which subcommand is
    * being run, creates a parser appropriate to that command, and runs it.
    */
    function execute() {
        $parser = new LaxOptionParser(array(
            'usage' => '%prog subcommand [options] [args]',
            'version' => management::get_version(),
            'option_list' => BaseCommand::getDefaultOptionList()
        ));
        
        $options = array();
        $args = array();
        
        try {
            list($options, $args) = $parser->parseArgs($this->argv);
            management::handle_default_options($options);
        } catch (\Exception $e) {
            // Ignore any option errors at this point.
        }
        
        try {
            $subcommand = $this->argv[1];
        } catch (\ErrorException $e) {
            $subcommand = 'help'; // Display help if no arguments were given.
        }
        
        if ($subcommand == 'help') {
            if (count($args) <= 2) {
                $parser->printLaxHelp();
                fwrite(STDOUT, $this->getMainHelpText() . "\n");
            } else if ($args[2] == '--commands') {
                fwrite(STDOUT, $this->getMainHelpText(true) . "\n");
            } else {
                $this->fetchCommand($args[2])->printHelp($this->prog_name, $args[2]);
            }
        } else if ($subcommand == 'version') {
            fwrite(STDOUT, $parser->getVersion() . "\n");
        } else if (array_slice($this->argv, 1) === array('--version')) {
            // LaxOptionParser already takes care of printing the version.
        } else if (in_array(array_slice($this->argv, 1), array(array('--help'), array('-h')))) {
            $parser->printLaxHelp();
            fwrite(STDOUT, $this->getMainHelpText() . "\n");
        } else {
            $this->fetchCommand($subcommand)->runFromArgv($this->argv);
        }
    }
}

}
