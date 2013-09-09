<?php

namespace bjork\core\management\base;

use optparse,
    optparse\OptionGroup,
    optparse\OptionParser;

use bjork,
    bjork\core\exceptions\BjorkException,
    bjork\core\exceptions\ImproperlyConfigured,
    bjork\core\loading,
    bjork\core\management,
    bjork\utils\translation;

/**
* Exception class indicating a problem while executing a management
* command.
* 
* If this exception is raised during the execution of a management
* command, it will be caught and turned into a nicely-printed error
* message to the appropriate output stream (i.e., stderr); as a
* result, raising this exception (with a sensible description of the
* error) is the preferred way to indicate that something has gone
* wrong in the execution of a command.
*/
class CommandError extends BjorkException {}

/**
* The base class from which all management commands ultimately
* derive.
* 
* Use this class if you want access to all of the mechanisms which
* parse the command-line arguments and work out what code to call in
* response; if you don't need to change any of that behavior,
* consider using one of the subclasses defined in this file.
* 
* If you are interested in overriding/customizing various aspects of
* the command-parsing and -execution behavior, the normal flow works
* as follows:
* 
* 1. ``django-admin.py`` or ``manage.py`` loads the command class
*    and calls its ``run_from_argv()`` method.
* 
* 2. The ``run_from_argv()`` method calls ``create_parser()`` to get
*    an ``OptionParser`` for the arguments, parses them, performs
*    any environment changes requested by options like
*    ``pythonpath``, and then calls the ``execute()`` method,
*    passing the parsed arguments.
* 
* 3. The ``execute()`` method attempts to carry out the command by
*    calling the ``handle()`` method with the parsed arguments; any
*    output produced by ``handle()`` will be printed to standard
*    output and, if the command is intended to produce a block of
*    SQL statements, will be wrapped in ``BEGIN`` and ``COMMIT``.
* 
* 4. If ``handle()`` raised a ``CommandError``, ``execute()`` will
*    instead print an error message to ``stderr``.
* 
* Thus, the ``handle()`` method is typically the starting point for
* subclasses; many built-in commands and command types either place
* all of their logic in ``handle()``, or perform some additional
* parsing work in ``handle()`` and then delegate from it to more
* specialized methods as needed.
* 
* Several attributes affect behavior at various steps along the way:
* 
* ``args``
*     A string listing the arguments accepted by the command,
*     suitable for use in help messages; e.g., a command which takes
*     a list of application names might set this to '<appname
*     appname ...>'.
* 
* ``can_import_settings``
*     A boolean indicating whether the command needs to be able to
*     import Django settings; if ``True``, ``execute()`` will verify
*     that this is possible before proceeding. Default value is
*     ``True``.
* 
* ``help``
*     A short description of the command, which will be printed in
*     help messages.
* 
* ``option_list``
*     This is the list of ``optparse`` options which will be fed
*     into the command's ``OptionParser`` for parsing arguments.
*/
abstract class BaseCommand {
    
    static $help = '',
           $args = '';
    
    static $canImportSettings = true;
    
    protected $stdout, $stderr;
    
    public static function getDefaultOptionList() {
        return array(
            optparse::make_option('-v', '--verbosity', array(
                'action'     => 'store',
                'dest'       => 'verbosity',
                'default'    => '1',
                'type'       => 'choice',
                'choices'    => array('0', '1', '2', '3'),
                'help'       => 'Verbosity level; 0=minimal output, 1=normal '.
                                'output, 2=verbose output, 3=very verbose output'
            )),
            
            optparse::make_option('--settings', array(
                'help'       => 'The path to a settings module, e.g. '.
                                '"myproject/settings/main.php". If this isn\'t '.
                                'provided, the BJORK_SETTINGS_MODULE environment '.
                                'variable will be used.'
            )),
            
            optparse::make_option('--includepath', array(
                'help'       => 'A directory to add to the include path, e.g. '.
                                '"/home/bjorkprojects/myproject".'
            )),
            
            optparse::make_option('--traceback', array(
                'action'     => 'store_true',
                'help'       => 'Print traceback on exception'
            )),
        );
    }
    
    /**
    * Return a brief description of how to use this command, by
    * default from the attribute ``self.help``.
    */
    public function getUsage($subcommand) {
        $args = static::$args;
        $usage = "%prog {$subcommand} [options] {$args}";
        $help = static::$help;
        if (!empty($help))
            return "{$usage}\n\n{$help}";
        return $usage;
    }
    
    /**
    * Create and return the ``OptionParser`` which will be used to
    * parse the arguments to this command.
    */
    public function createParser($prog_name, $subcommand) {
        $parser = new OptionParser(array(
            'prog' => $prog_name,
            'usage' => $this->getUsage($subcommand),
            'version' => $this->getVersion(),
            'option_list' => self::getDefaultOptionList(),
        ));
        
        $command_options = static::getOptionList();
        if (!empty($command_options)) {
            $command_option_group = new OptionGroup($parser, 'Command options');
            $command_option_group->addOptions($command_options);
            $parser->addOptionGroup($command_option_group);
        }
        
        return $parser;
    }
    
    /**
    * Print the help message for this command, derived from
    * ``self.usage()``.
    */
    public function printHelp($prog_name, $subcommand) {
        $parser = $this->createParser($prog_name, $subcommand);
        $parser->printHelp();
    }
    
    /**
    * Set up any environment changes requested (e.g., include path
    * and Bjork settings), then run this command.
    */
    public function runFromArgv($argv) {
        $prog_name = array_shift($argv);
        $subcommand = array_shift($argv);
        $parser = $this->createParser($prog_name, $subcommand);
        list($options, $args) = $parser->parseArgs($argv);
        management::handle_default_options($options);
        $this->execute($args, $options);
    }
    
    /**
    * Try to execute this command. If the command raises a
    * ``CommandError``, intercept it and print it sensibly to
    * stderr.
    */
    public function execute(array $args, $options) {
        $show_traceback = isset($options['traceback']) ? $options['traceback'] : false;
        
        // Switch to English but only do this if we can assume we
        // have a working settings file, because bjork\utils\translation
        // requires settings.
        $saved_lang = null;
        if (static::$canImportSettings) {
            try {
                $saved_lang = translation::get_language();
                translation::activate('en-us');
            } catch (\ErrorException $e) {
                // If settings should be available, but aren't,
                // raise the error and quit.
                if ($show_traceback)
                    $this->printTraceback($e);
                else
                    fwrite($this->stderr, "Error: {$e->getMessage()}\n");
                exit(1);
            }
        }
        
        $this->stdout = array_key_exists('stdout', $options)
            ? $options['stdout']
            : STDOUT;
        $this->stderr = array_key_exists('stderr', $options)
            ? $options['stderr']
            : STDERR;
        
        try {
            $output = $this->handle($args, $options);
            if ($output)
                fwrite($this->stdout, $output);
        } catch (CommandError $e) {
            if ($show_traceback)
                $this->printTraceback($e);
            else
                fwrite($this->stderr, "Error: {$e->getMessage()}\n");
            exit(1);
        }
        
        if ($saved_lang !== null)
            translation::activate($saved_lang);
    }
    
    //-- subclassing
    
    public static function getOptionList() {
        return array();
    }
    
    /**
    * Return the Bjork version, which should be correct for all
    * built-in Bjork commands. User-supplied commands should
    * override this method.
    */
    public function getVersion() {
        return bjork::get_version();
    }
    
    function printTraceback($e) {
        $errs = array();
        
        do {
            $errs[] = $e;
        } while ($e = $e->getPrevious());
        
        foreach (array_reverse($errs) as $e) {
            fwrite($this->stderr, "\nError: {$e->getMessage()}\n");
            fwrite(STDERR, $e->getTraceAsString() . "\n");
        }
    }
    
    /**
    * The actual logic of the command. Subclasses must implement
    * this method.
    */
    abstract function handle(array $args, $options);
}

/**
* A management command which takes one or more installed application
* names as arguments, and does something with each of them.
*
* Rather than implementing ``handle()``, subclasses must implement
* ``handle_app()``, which will be called once for each application.
*/
abstract class AppCommand extends BaseCommand {
    
    static $args = '<appname appname ...>';
    
    function handle(array $args, $options) {
        if (empty($args))
            throw new CommandError('Enter at least one appname.');
        
        try {
            $app_list = loading::get_apps();
        } catch (ImproperlyConfigured $e) {
            throw new CommandError("{$e->getMessage()}. ".
                "Are you sure your INSTALLED_APPS setting is correct?");
        } catch (\ErrorException $e) {
            throw new CommandError("{$e->getMessage()}. ".
                "Are you sure your INSTALLED_APPS setting is correct?");
        }
        
        $output = array();
        foreach ($app_list as $app) {
            $app_output = $this->handleApp($app, $options);
            if (!empty($app_output))
                $output[] = $app_output;
        }
        
        return implode("\n", $output);
    }
    
    /**
    * Perform the command's actions for ``app``, which will be the
    * file system path corresponding to an application name given on
    * the command line.
    */
    abstract function handleApp($app, $options);
}

/**
* A management command which takes one or more arbitrary arguments
* (labels) on the command line, and does something with each of
* them.
* 
* Rather than implementing ``handle()``, subclasses must implement
* ``handle_label()``, which will be called once for each label.
* 
* If the arguments should be names of installed applications, use
* ``AppCommand`` instead.
*/
abstract class LabelCommand extends BaseCommand {
    
    static $args = '<label label ...>',
           $label = 'label';
    
    function handle(array $labels, $options) {
        if (empty($labels))
            throw new CommandError('Enter at least one '.static::$label.'.');
        
        $output = array();
        foreach ($labels as $label) {
            $label_output = $this->handleLabel($label, $options);
            if (!empty($label_output))
                $output[] = $label_output;
        }
        
        return implode("\n", $output);
    }
    
    /**
    * Perform the command's actions for ``label``, which will be the
    * string as given on the command line.
    */
    abstract function handleLabel($label, $options);
}

/**
* A command which takes no arguments on the command line.
* 
* Rather than implementing ``handle()``, subclasses must implement
* ``handle_noargs()``; ``handle()`` itself is overridden to ensure
* no arguments are passed to the command.
* 
* Attempting to pass arguments will raise ``CommandError``.
*/
abstract class NoArgsCommand extends BaseCommand {
    
    static $args = '';
    
    function handle(array $args, $options) {
        if (!empty($args))
            throw new CommandError('Command doesn\'t accept any arguments.');
        return $this->handleNoArgs($options);
    }
    
    /**
    * Perform this command's actions.
    */
    abstract function handleNoArgs($options);
}
