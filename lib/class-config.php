<?php
/**
 * Our custom wp-config bootstrapper, it looks at the environment, and then loads config files
 * based on those environment variables - typically environment set in .htaccess, see
 * .htaccess.tpl
 *
 * Configs are loaded based on the following hierarchy, and you can do both folders and files:
 *  1) application/etc/wp-config/{ENVIRONMENT}/{FILE_NAME}
 *  2) application/etc/wp-config/{FILE_NAME}
 *
 * @author Reid Williams
 * @class UsabilityDynamics\Cluster\Config
 */
namespace UsabilityDynamics\Cluster {
  if( !class_exists( 'UsabilityDynamics\Cluster\Config' ) ){
    class Config{

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
       * This function looks through the configuration options that are stored and returns them
       *
       * @param string $config The config we're trying to load
       * @param string $value Whether we want to get a specific value from this config, or the whole thing
       */
      function get_config( $config, $value = false ){
        if( isset( $this->loaded[ $config ] ) && is_array( $this->loaded[ $config ] ) && isset( $this->loaded[ $config ][ 'vars' ] ) ){
          if( is_string( $value ) && !empty( $value ) && isset( $this->loaded[ $config ][ 'vars' ][ $value ] ) ){
            return $this->loaded[ $config ][ 'vars' ][ $value ];
          }else{
            /** If there is only one item, return it directly */
            if( count( $this->loaded[ $config ][ 'vars' ] ) == 1 ){
              return array_pop( array_values( $this->loaded[ $config ][ 'vars' ] ) );
            }else{
              return $this->loaded[ $config ][ 'vars' ];
            }
          }
        }else{
          return false;
        }
      }

      /**
       * This function basically looks for a way to load the specific config files, by first looking in the
       * current environment's folder, and then looking into the base config folder afterwards
       *
       * @param string $file The file we want to include
       * @param string $scope The scope for the variables, globally or locally, defaults to 'local'
       */
      function load_config( $file, $scope = 'local' ){
        /** Ok, make sure our variables are good */
        if( !( is_string( $scope ) && $scope == 'global' ) ){
          $scope = 'local';
        }
        $files = array();
        /** Loop through our config folders, stopping at the first one we can find and include */
        foreach( $this->config_folders as $config_folder ){
          if( is_dir( $config_folder . $file ) ){
            // echo 'Directory: ' . $config_folder . $file . "\r\n";
            $config_folder = $config_folder . $file . DIRECTORY_SEPARATOR;
            /** Scan the directory */
            $possibles = scandir( $config_folder );
            /** Loop through the possibles and include them if you can */
            foreach( $possibles as $possible ){
              /** Skip root folders */
              if( $possible == '.' || $possible == '..' ){
                continue;
              }
              /** Remove the '.php' file from the name if it has it */
              if( substr( $possible, strlen( $possible ) - 4, 4 ) == '.php' ){
                $possible = substr( $possible, 0, strlen( $possible ) - 4 );
              }
              /** Remove the '.json' file from the name if it has it */
              if( substr( $possible, strlen( $possible ) - 5, 5 ) == '.json' ){
                $possible = substr( $possible, 0, strlen( $possible ) - 5 );
              }
              /** Ok, now call ourselves, so we'll recurse through directories */
              $this->load_config( $file . DIRECTORY_SEPARATOR . $possible, $scope );
            }
          }elseif( is_file( $config_folder . $file . '.php' ) ){
            // echo 'File: ' . $config_folder . $file . '.php' . "\r\n";
            /** Try to include the file in our exclusions list, if not already included */
            if( !isset( $files[ $file ] ) ){
              $files[ $file ] = array(
                'scope' => $scope,
                'file' => $config_folder . $file . '.php'
              );
            }
          }elseif( is_file( $config_folder . $file . '.json' ) ){
            // echo 'File: ' . $config_folder . $file . '.json' . "\r\n";
            /** Try to include the file in our exclusions list, if not already included */
            if( !isset( $files[ $file ] ) ){
              $files[ $file ] = array(
                'scope' => $scope,
                'file' => $config_folder . $file . '.json'
              );
            }
          }
        }
        /** If we have a files array that is not empty, go through and include them */
        if( is_array( $files ) && count( $files ) ){
          /** Go ahead and require the file */
          foreach( $files as $slug => $file ){
            /** Ok, call our function (so we don't have to do a bunch of unsets) */
            $this->_try_load_config_file( $slug, $file );
          }
        }
      }

      /**
       * This function actually does the requiring
       *
       * @param string $slug File's slug to store
       * @param array $file File definition array as done in 'load_config'
       */
      function _try_load_config_file( $slug, $file ){
        if( !in_array( $slug, array_keys( $this->loaded ) ) ){
          /** Now, require the file, base on the type it is */
          if( substr( $file[ 'file' ], strlen( $file[ 'file' ] ) - 4, 4 ) == '.php' ){
            require_once( $file[ 'file' ] );
            $file[ 'vars' ] = get_defined_vars();
          }elseif( substr( $file[ 'file' ], strlen( $file[ 'file' ] ) - 5, 5 ) == '.json' ){
            $file[ 'vars' ] = json_decode( file_get_contents( $file[ 'file' ] ), true );
          }
          /** Go through and unset the protected variables */
          foreach( $this->protected_variables as $protected_variable ){
            if( isset( $file[ 'vars' ][ $protected_variable ] ) ){
              unset( $file[ 'vars' ][ $protected_variable ] );
            }
          }
          /** Now, determine what to do with the vars */
          if( isset( $file[ 'scope' ] ) && $file[ 'scope' ] == 'global' ){
            foreach( $file[ 'vars' ] as $key => $value ){
              $GLOBALS[ $key ] = $value;
            }
          }
          /** No, add it to our loaded array */
          $this->loaded[ $slug ] = $file;
        }
      }

      /**
       * On init, we're just going to setup and include all our config files
       * @param string $base_dir Override the base dir to search for files (defaults to __DIR__)
       * @param bool $do_stuff Whether we should actually do initialization( needed for 'init' )
       */
      function __construct( $base_dir = __DIR__, $do_stuff = true ){
        if( !( is_bool( $do_stuff ) && $do_stuff ) ){
          return;
        }
        /** Set some local variables */
        $this->config_folders[] = rtrim( $base_dir, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR . 'wp-config' . DIRECTORY_SEPARATOR . ENVIRONMENT . DIRECTORY_SEPARATOR;
        $this->config_folders[] = rtrim( $base_dir, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR . 'wp-config' . DIRECTORY_SEPARATOR;
        /** For these variables, make sure they exist */
        foreach( $this->config_folders as $key => $value ){
          if( !is_dir( $value ) ){
            unset( $this->config_folders[ $key ] );
          }
        }
        /** Renumber the array */
        $this->config_folders = array_values( $this->config_folders );
        /** If we don't have any config folders, bail */
        if( is_array( $this->config_folders ) && !count( $this->config_folders ) ){
          return $this;
        }
        /** Now, go through our autoloaded configs, and bring them in */
        foreach( $this->autoload_files as $autoload_file ){
          /** See if it needs to be global or local */
          if( substr( $autoload_file, 0, 2 ) == 'g:' ){
            $autoload_scope = 'global';
            $autoload_file = substr( $autoload_file, 2, strlen( $autoload_file ) - 2 );
          }else{
            $autoload_scope = 'local';
          }
          /** Include the files then */
          $this->load_config( $autoload_file, $autoload_scope );
        }
        // echo "DONE\r\n" . print_r( $this->loaded, true ); die();
        /** Return this own object */
        return $this;
      }

      /**
       * This function lets us chain methods without having to instantiate first, YOU MUST COPY THIS TO ALL SUB CLASSES
       */
      static public function init(){
        return new self( false );
      }

    }
  }
}