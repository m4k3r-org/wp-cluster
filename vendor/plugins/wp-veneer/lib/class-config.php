<?php
/**
 * Our custom wp-config bootstrapper, it looks at the environment, and then loads config files
 * based on those environment variables - typically environment set in .htaccess, see
 * .htaccess.tpl
 *
 * Configs are loaded based on the following hierarchy, and you can do both folders and files:
 *  1) ENVIRONMENT variables, these supercede anything else, and can also define 'ENVIRONMENT'
 *  2) application/etc/wp-config/{ENVIRONMENT}/{FILE_NAME}
 *  3) application/etc/wp-config/{FILE_NAME}
 *  4) All items defined in composer.json, in the settings object key
 *
 * @author Reid Williams
 * @class UsabilityDynamics\Veneer\Config
 */
namespace UsabilityDynamics\Veneer {

  if( !class_exists( 'UsabilityDynamics\Veneer\Config' ) ) {

    class Config {

      /**
       * Holds the arrayed location of our config files/folders
       */
      private $config_folders = array();

      /**
       * This variable defines the config files which will be autoloaded in the class, can be a
       * directory (of which all files will be loaded), or a specific file - scope is defined
       * such that you might have to use $uds_config->get_config() if the variables aren't declared
       * globally
       */
      private $autoload_files = array(
        'g:system', /** Holds variables such as 'path' or 'web host name' declarations */
        'g:constants', /** Our defines file, should hold all static declarations, replacement of old wp-config.php */
        'g:database', /** Our database settings */
        'g:debug', /** Any debug file, looks for 'debug.php' - g: prefix makes it global */
        'options', /** Looking for some options definition files in a directory (scans all files) */
      );

      /**
       * This variable will hold the config files that have already been included
       */
      private $loaded = array();

      /**
       * This variable holds protected config variables (they cannot be defined in the config files)
       */
      private $protected_variables = array(
        'slug',
        'file'
      );

      /**
       * The constants that should be dynamically generated
       */
      protected $protected_constants = array(
        'WP_BASE_DIR',
        'WP_BASE_DOMAIN',
        'WP_DEFAULT_PROTOCOL',
        'WP_BASE_URL',
        'WP_HOME',
        'WP_CACHE',
        'WP_ALLOW_MULTISITE',
        'MULTISITE',
        'SUBDOMAIN_INSTALL',
        'SUNRISE'
      );

      /**
       * On init, we're just going to setup and include all our config files
       *
       * @param string $base_dir Override the base dir to search for files (defaults to __DIR__)
       * @param bool   $do_stuff Whether we should actually do initialization( needed for 'init' )
       *
       * @throws \Exception Plain exception when there is an issue
       */
      public function __construct( $base_dir = __DIR__, $do_stuff = true ) {
        global $table_prefix, $wp_version;

        if( !( is_bool( $do_stuff ) && $do_stuff ) ) {
          return;
        }

        /** Make sure we have a valid http host */
        if( @isset( $_SERVER[ 'HTTP_HOST' ] ) ){
          $this->host = $_SERVER[ 'HTTP_HOST' ];
        }else{
          $_SERVER[ 'HTTP_HOST' ] = 'CLI';
        }

        /** Fix HTTPS if we're proxied */
        if( @isset( $_SERVER[ 'HTTP_X_FORWARDED_PROTO' ] ) && strtolower( $_SERVER[ 'HTTP_X_FORWARDED_PROTO' ] ) == 'https' ) {
          $_SERVER[ 'HTTPS' ] = 'on';
        }

        /** Set some local variables */
        $base_dir = dirname( dirname( dirname( dirname( $base_dir ) ) ) );

        /** Bring in our local-debug file if we have it */
        if( is_file( $base_dir . '/local-debug.php' ) ) {
          require_once( $base_dir . '/local-debug.php' );
        }

        // Normalize.
        foreach( (array) $_SERVER as $_key => $_value ) {

          if( $_value === 'true' )  {
            $_SERVER[ $_key ] = true;
          }

          if( $_value === 'false' )  {
            $_SERVER[ $_key ] = false;
          }

          if( is_int( $_value ) ) {
            $_SERVER[ $_key ] = intval( $_value );
          }

        }

        /** If we've got WP_CLI, we need to fix the base dir */
        if( defined( 'WP_CLI' ) && WP_CLI ) {
          $_SERVER[ 'DOCUMENT_ROOT' ] = $base_dir;

          if( !defined( 'WP_DEBUG' ) ) {
            define( 'WP_DEBUG', false );
          }

          if( !defined( 'WP_DEBUG_DISPLAY' ) ) {
            define( 'WP_DEBUG_DISPLAY', false );
          }

        }

        if( !defined( 'WP_ENV' ) && isset( $_SERVER[ 'WP_ENV' ] ) ) {
          define( 'WP_ENV', $_SERVER[ 'WP_ENV' ] );
        }

        if( !defined( 'PHP_ENV' ) && isset( $_SERVER[ 'PHP_ENV' ] ) ) {
          define( 'PHP_ENV', $_SERVER[ 'PHP_ENV' ] );
        }

        if( !defined( 'DB_HOST' ) && isset( $_SERVER[ 'DB_HOST' ] ) ) {
          define( 'DB_HOST', $_SERVER[ 'DB_HOST' ] );
        }

        if( !defined( 'DB_USER' ) && isset( $_SERVER[ 'DB_USER' ] ) ) {
          define( 'DB_USER', $_SERVER[ 'DB_USER' ] );
        }

        if( !defined( 'DB_PASSWORD' ) && isset( $_SERVER[ 'DB_PASSWORD' ] ) ) {
          define( 'DB_PASSWORD', $_SERVER[ 'DB_PASSWORD' ] );
        }

        if( !defined( 'DB_NAME' ) && isset( $_SERVER[ 'DB_NAME' ] ) ) {
          define( 'DB_NAME', $_SERVER[ 'DB_NAME' ] );
        }

        if( !defined( 'DB_PREFIX' ) && isset( $_SERVER[ 'DB_PREFIX' ] ) ) {
          define( 'DB_PREFIX', $_SERVER[ 'DB_PREFIX' ] );
        }

        if( !defined( 'WP_DEBUG' ) && isset( $_SERVER[ 'WP_DEBUG' ] ) ) {
          define( 'WP_DEBUG', $_SERVER[ 'WP_DEBUG' ] );
        }

        if( !defined( 'WP_DEBUG_DISPLAY' ) && isset( $_SERVER[ 'WP_DEBUG_DISPLAY' ] ) ) {
          define( 'WP_DEBUG_DISPLAY', $_SERVER[ 'WP_DEBUG_DISPLAY' ] );
        }

        // Just in case.
        if( !defined( 'WP_VERSION' ) && isset( $wp_version ) ) {
          define( 'WP_VERSION', $wp_version );
        }

        /** Check for any ENVIRONMENT variables first */
        foreach( (array) $_ENV as $key => $value ){
          if( !defined( strtoupper( $key ) ) ){
            define( strtoupper( $key ), $value );
          }
        }

        /** Bring in our environment file if we need to */
        if( !defined( 'ENVIRONMENT' ) && is_file( $base_dir . '/.environment' ) ) {
          $environment = @file_get_contents( $base_dir . '/.environment' );
          define( 'ENVIRONMENT', trim( $environment ) );
        }

        if( !defined( 'ENVIRONMENT' ) && defined( 'WP_ENV' ) && WP_ENV ) {
          define( 'ENVIRONMENT', WP_ENV );
        }

        if( !defined( 'ENVIRONMENT' ) && defined( 'PHP_ENV' ) && PHP_ENV ) {
          define( 'ENVIRONMENT', PHP_ENV );
        }

        /** For these variables, make sure they exist */
        if( defined( 'ENVIRONMENT' ) )  {
          $this->config_folders[ ] = rtrim( $base_dir, '/' ) . '/application/static/etc/wp-config/' . ENVIRONMENT . '/';
        }

        $this->config_folders[ ] = rtrim( $base_dir, '/' ) . '/application/static/etc/wp-config/';

        foreach( $this->config_folders as $key => $value ) {
          if( !is_dir( $value ) ) {
            unset( $this->config_folders[ $key ] );
          }
        }

        /** Renumber the array */
        $this->config_folders = array_values( $this->config_folders );

        /** If we don't have any config folders, bail */
        if( !( is_array( $this->config_folders ) && !count( $this->config_folders ) ) ) {
          /** Now, go through our autoloaded configs, and bring them in */
          foreach( $this->autoload_files as $autoload_file ) {
            /** See if it needs to be global or local */
            if( substr( $autoload_file, 0, 2 ) == 'g:' ) {
              $autoload_scope = 'global';
              $autoload_file  = substr( $autoload_file, 2, strlen( $autoload_file ) - 2 );
            } else {
              $autoload_scope = 'local';
            }
            /** Include the files then */
            $this->load_config( $autoload_file, $autoload_scope );
          }
        }

        /** Ensure protected constants are not defined before the configs */
        foreach( (array) $this->protected_constants as $protected_constant ){
          if( defined( $protected_constant ) && !isset( $_ENV[ $protected_constant ]) ) {
            throw new \Exception( 'The constant "' . $protected_constant . '" is defined - it\'s autogenerated. Please remove or comment out that define() line.' );
          }
        }

        /** Now declare our dynamically generated constants for any URLs */
        if( !defined( 'WP_BASE_DIR' ) ) {
          define( 'WP_BASE_DIR', $base_dir );
        }

        if( !defined( 'WP_BASE_DOMAIN' ) ) {
          define( 'WP_BASE_DOMAIN', $_SERVER[ 'HTTP_HOST' ] );
        }

        if( !defined( 'WP_DEFAULT_PROTOCOL' ) ) {
          define( 'WP_DEFAULT_PROTOCOL', @isset( $_SERVER[ 'HTTPS' ] ) && $_SERVER[ 'HTTPS' ] == 'on' ? 'https' : 'http' );
        }

        if( !defined( 'WP_BASE_URL' ) ) {
          define( 'WP_BASE_URL', WP_DEFAULT_PROTOCOL . '://' . WP_BASE_DOMAIN );
        }

        if( !defined( 'WP_HOME' ) ) {
          define( 'WP_HOME', rtrim( WP_BASE_URL, '/' ) );
        }

        /** Ok, we need to make some other determinations based on the file structure */
        if( file_exists( WP_BASE_DIR . '/db.php' ) ){
          define( 'IS_CLUSTER', true );
          define( 'IS_MULTISITE', false );
          define( 'IS_STANDALONE', false );
          define( 'WP_ALLOW_MULTISITE', true );
          define( 'MULTISITE', true );
          define( 'SUBDOMAIN_INSTALL', true );
          define( 'SUNRISE', 'on' );
        }elseif( file_exists( WP_BASE_DIR . '/sunrise.php' ) ){
          define( 'IS_CLUSTER', false );
          define( 'IS_MULTISITE', true );
          define( 'IS_STANDALONE', false );
          define( 'WP_ALLOW_MULTISITE', true );
          define( 'MULTISITE', true );
          define( 'SUBDOMAIN_INSTALL', true );
          define( 'SUNRISE', 'on' );
        }else{
          define( 'IS_CLUSTER', false );
          define( 'IS_MULTISITE', false );
          define( 'IS_STANDALONE', true );
          define( 'WP_ALLOW_MULTISITE', false );
          define( 'MULTISITE', false );
          define( 'SUBDOMAIN_INSTALL', false );
          define( 'SUNRISE', 'off' );
        }

        /** Ok, if we're on a production system, we should assume we're caching */
        if( defined( 'ENVIRONMENT' ) && ENVIRONMENT == 'production' && !defined( 'WP_CACHE' ) ) {
                  }

        /** Finally, go through the composer.json file and add all the configs there */
        if( is_file( $_SERVER[ 'DOCUMENT_ROOT' ] . '/composer.json' ) ) {
          $composer_file = $_SERVER[ 'DOCUMENT_ROOT' ] . '/composer.json';
        } else if( is_file( $base_dir . '/composer.json' ) ) {
          $composer_file = $base_dir . '/composer.json';
        }

        /** Pull in the settings */
        if( isset( $composer_file ) ) {
          $_settings = self::_parse_composer( $composer_file );
        } else {
          $_settings = array();
        }

        /** Check to see if we're of the new format */
        if( isset( $_settings[ 'default' ] ) ){
          $new_settings = array();
          foreach( $_settings as $key => $value ){
            $new_settings = array_merge( $new_settings, (array) $value );
          }
          $_settings = $new_settings;
        }

        /** Loop through them, declaring them if they don't already previously exist */
        foreach( (array) $_settings as $key => $value ) {
          if( !defined( strtoupper( $key ) ) ) {
            $matches = array();
            /** If we have a {}, we're looking for a constant */
            if( preg_match_all( '/{.*?}/i', $value, $matches ) ){
              $patterns = $matches[ 0 ];
              foreach( $patterns as $pattern ){
                $constant = strtoupper( trim( $pattern, '{}' ) );
                if( !defined( $constant ) ){
                  /** Trigger a notice */
                  trigger_error( 'The constant "' . $constant . '" is not defined, but it is used in our composer config between two {} characters for the variable "' . $key . '".', E_USER_NOTICE );
                }else{
                  /** Go ahead and string replace the value */
                  $value = str_ireplace( $pattern, constant( $constant ), $value );
                }
              }

            }
            define( strtoupper( $key ), $value );
          }else{
            /** Check to see if it's a protected constant */
            if( in_array( strtoupper( $key ), $this->protected_constants ) ){
              throw new \Exception( 'The constant "' . $this->protected_constants . '" is defined in composer.json - it\'s autogenerated. Please remove that line.' );
            }
          }
        }

        /** Is this needed? */
        if( !isset( $table_prefix ) ) {
          $table_prefix = defined( 'DB_PREFIX' ) ? DB_PREFIX : 'wp_';
        }

        /** Return this own object */
        return $this;

      }

      /**
       * This function lets us chain methods without having to instantiate first, YOU MUST COPY THIS TO ALL SUB CLASSES
       */
      static public function init() {
        return new self( __DIR__, false );
      }

      /**
       * This function looks through the configuration options that are stored and returns them
       *
       * @param string $config The config we're trying to load
       * @param mixed  $value Whether we want to get a specific value from this config, or the whole thing
       *
       * @return mixed False on failure, config array on success
       */
      private function get_config( $config, $value = false ) {
        if( isset( $this->loaded[ $config ] ) && is_array( $this->loaded[ $config ] ) && isset( $this->loaded[ $config ][ 'vars' ] ) ) {
          if( is_string( $value ) && !empty( $value ) && isset( $this->loaded[ $config ][ 'vars' ][ $value ] ) ) {
            return $this->loaded[ $config ][ 'vars' ][ $value ];
          } else {
            /** If there is only one item, return it directly */
            if( count( $this->loaded[ $config ][ 'vars' ] ) == 1 ) {
              return array_pop( array_values( $this->loaded[ $config ][ 'vars' ] ) );
            } else {
              return $this->loaded[ $config ][ 'vars' ];
            }
          }
        } else {
          return false;
        }
      }

      /**
       * Parse comoser.json file for settings and extra.settings
       *
       * @author potanin@UD
       * @param null $composer_file
       * @method _parse_composer
       *
       * @return array
       */
      private function _parse_composer( $composer_file = null ) {

        try {

          $_settings = array();

          $_composer = file_get_contents( $composer_file );
          $_composer = json_decode( $_composer, false, 512 );

        } catch( \Exception $error ) {
          // Most likely can't parse JSON file... Silently fail.
          return $_settings;
        }


        if( isset( $_composer->settings ) && is_object( $_composer->settings ) ) {
          foreach( (array) $_composer->settings as $key => $value ) {
            $_settings[ $key ] = (array) $value;
          }
        }

        if( isset( $_composer->extra ) && isset( $_composer->extra->settings ) && is_object( $_composer->extra->settings ) ) {
          foreach( (array) $_composer->extra->settings as $key => $value ) {
            $_settings[ $key ] = (array) $value;
          }

        }

        return (array) $_settings;
        
      }

      /**
       * This function basically looks for a way to load the specific config files, by first looking in the
       * current environment's folder, and then looking into the base config folder afterwards
       *
       * @param string $file The file we want to include
       * @param string $scope The scope for the variables, globally or locally, defaults to 'local'
       */
      private function load_config( $file, $scope = 'local' ) {
        /** Ok, make sure our variables are good */
        if( !( is_string( $scope ) && $scope == 'global' ) ) {
          $scope = 'local';
        }
        $files = array();
        /** Loop through our config folders, stopping at the first one we can find and include */
        foreach( $this->config_folders as $config_folder ) {
          if( is_dir( $config_folder . $file ) ) {
            // echo 'Directory: ' . $config_folder . $file . "\r\n";
            $config_folder = $config_folder . $file . DIRECTORY_SEPARATOR;
            /** Scan the directory */
            $possibles = scandir( $config_folder );
            /** Loop through the possibles and include them if you can */
            foreach( $possibles as $possible ) {
              /** Skip root folders */
              if( $possible == '.' || $possible == '..' ) {
                continue;
              }
              /** Remove the '.php' file from the name if it has it */
              if( substr( $possible, strlen( $possible ) - 4, 4 ) == '.php' ) {
                $possible = substr( $possible, 0, strlen( $possible ) - 4 );
              }
              /** Remove the '.json' file from the name if it has it */
              if( substr( $possible, strlen( $possible ) - 5, 5 ) == '.json' ) {
                $possible = substr( $possible, 0, strlen( $possible ) - 5 );
              }
              /** Ok, now call ourselves, so we'll recurse through directories */
              $this->load_config( $file . DIRECTORY_SEPARATOR . $possible, $scope );
            }
          } elseif( is_file( $config_folder . $file . '.php' ) ) {
            // echo 'File: ' . $config_folder . $file . '.php' . "\r\n";
            /** Try to include the file in our exclusions list, if not already included */
            if( !isset( $files[ $file ] ) ) {
              $files[ $file ] = array(
                'scope' => $scope,
                'file'  => $config_folder . $file . '.php'
              );
            }
          } elseif( is_file( $config_folder . $file . '.json' ) ) {
            // echo 'File: ' . $config_folder . $file . '.json' . "\r\n";
            /** Try to include the file in our exclusions list, if not already included */
            if( !isset( $files[ $file ] ) ) {
              $files[ $file ] = array(
                'scope' => $scope,
                'file'  => $config_folder . $file . '.json'
              );
            }
          }
        }
        /** If we have a files array that is not empty, go through and include them */
        if( is_array( $files ) && count( $files ) ) {
          /** Go ahead and require the file */
          foreach( $files as $slug => $file ) {
            /** Ok, call our function (so we don't have to do a bunch of unsets) */
            $this->_try_load_config_file( $slug, $file );
          }
        }
      }

      /**
       * This function actually does the requiring
       *
       * @param string $slug File's slug to store
       * @param array  $file File definition array as done in 'load_config'
       */
      private function _try_load_config_file( $slug, $file ) {
        if( !in_array( $slug, array_keys( $this->loaded ) ) ) {
          /** Now, require the file, base on the type it is */
          if( substr( $file[ 'file' ], strlen( $file[ 'file' ] ) - 4, 4 ) == '.php' ) {
            require_once( $file[ 'file' ] );
            $file[ 'vars' ] = get_defined_vars();
          } elseif( substr( $file[ 'file' ], strlen( $file[ 'file' ] ) - 5, 5 ) == '.json' ) {
            $file[ 'vars' ] = json_decode( file_get_contents( $file[ 'file' ] ), true );
            /** Loop through the items, and if they prefix with 'c:', they should be defined constants */
            foreach( $file[ 'vars' ] as $key => $value ) {
              if( substr( $key, 0, 2 ) == 'c:' ) {
                /** Let's go ahead and unset the key */
                unset( $file[ 'vars' ][ $key ] );
                /** Set the constant */
                define( substr( $key, 2, strlen( $key ) - 2 ), $value );
              }
            }
          }
          /** Go through and unset the protected variables */
          foreach( $this->protected_variables as $protected_variable ) {
            if( isset( $file[ 'vars' ][ $protected_variable ] ) ) {
              unset( $file[ 'vars' ][ $protected_variable ] );
            }
          }
          /** Now, determine what to do with the vars */
          if( isset( $file[ 'scope' ] ) && $file[ 'scope' ] == 'global' ) {
            foreach( $file[ 'vars' ] as $key => $value ) {
              $GLOBALS[ $key ] = $value;
            }
          }
          /** No, add it to our loaded array */
          $this->loaded[ $slug ] = $file;
        }
      }

    }

  }

  /**
   * If we don't have the following defined, we should assume that we're directly including this file,
   * so we should initialize it
   */
  if( !defined( 'WP_BASE_DOMAIN' ) && !defined( 'WP_DEBUG' ) && !defined( 'AUTH_KEY' ) ) {
    global $wp_veneer;

    /** Init our config object */
    if( !is_object( $wp_veneer ) ) {
      $wp_veneer = new \stdClass();
    }

    /** Add to our object, if we don't have the config object */
    if( !isset( $wp_veneer->config ) ) {
      $wp_veneer->config = new Config();
    }

    /** Now that we've done that, lets include our wp settings file, as per normal operations */
    require_once( ABSPATH . '/wp-settings.php' );
  }

}
