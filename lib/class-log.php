<?php
/**
 * Log Access Controller
 *
 * @module Veneer
 * @author potanin@UD
 */
namespace UsabilityDynamics\Veneer {

  use Monolog\Logger;
  use Monolog\Handler\FingersCrossedHandler;
  use Monolog\Handler\StreamHandler;
  use Monolog\Handler\SyslogHandler;
  use Monolog\Handler\RotatingFileHandler;
  use Monolog\Formatter\LineFormatter;
  use Monolog\ErrorHandler;
  use Graze\Monolog\Handler\RaygunHandler;
  use Raygun4php\RaygunClient;

  if( !class_exists( 'UsabilityDynamics\Cluster\Log' ) ) {
    /**
     * Class Log
     *
     * @module Cluster
     */
    class Log {

      /**
       * Declare some static constants
       */
      const CHANNEL  = 'veneer';

      /**
       * Facility for syslog, if that's what we're using
       */
      const FACILITY = 'local6';

      /**
       * Holds our required Monolog objects
       */
      private $handler;

      /**
       * @var \Monolog\Logger
       */
      private $logger;

      /**
       * Our log level
       */
      private $log_level = Logger::DEBUG;

      /**
       * @var \Monolog\Formatter\LineFormatter
       */
      private $formatter;
      /**
       * @var string
       */
      private $guid;

      /**
       * The line format we'll be using
       */
      private $line_format = '%channel% - %datetime% - %level_name% - %message% - %extra% - %context%';

      /**
       * Initialize Log
       *
       * @param boolean $do_stuff If we should actually process (used by 'init')
       * @throws \Exception on error
       * @returns Log $this
       */
      public function __construct( $do_stuff = true ) {
        global $current_blog;

        /** Make sure we've got valid data */
        if( !$do_stuff || !defined( 'WP_LOGS_DIR' ) ){
          return $this;
        }

        /** Verify Class Exists.  */
        if( !class_exists( 'Monolog\Logger' ) ) {
          return $this;
        }

        $this->logs_dir = rtrim( WP_LOGS_DIR, '/' ) . '/' . rtrim( WP_BASE_DOMAIN, '/' );
        /** Ok, if the directory doesn't exist, let's try to create it */
        if( !is_dir( $this->logs_dir ) ){
          if( !@mkdir( $this->logs_dir, 0755, true ) ){
            throw new \Exception( 'Could not create logs directory: ' . $this->logs_dir );
          }
          if( !@chmod( $this->logs_dir, 0755 ) ){
            throw new \Exception( 'Could not set proper permissions on logs directory: ' . $this->logs_dir );
          }
        }

        /** Setup the GUID */
        $this->guid = $this->create_guid();

        /** Setup the logger */
        $this->logger = new Logger( $this->guid );

        /** Build our line formatter */
        $this->line_format = ( is_object( $current_blog ) ? $current_blog->domain : null ) . ' - ' . $this->line_format . PHP_EOL;

        /** Setup the formatter */
        $this->formatter = new LineFormatter( $this->line_format );

        /** Add our handler */
        switch( true ) {
          case defined( 'WP_LOGS_HANDLER' ) && WP_LOGS_HANDLER == 'syslog':
            /** Syslog handler */
            $handler = new SyslogHandler( self::CHANNEL, self::FACILITY, $this->log_level );
            break;
          case defined( 'WP_LOGS_HANDLER' ) && WP_LOGS_HANDLER == 'file':
            /** File name to write to */
            $this->log_file = rtrim( $this->logs_dir, '/' ) . '/debug.log';
            /** Ok, attempt to create the file */
            if( !is_file( $this->log_file ) ){
              if( !@touch( $this->log_file ) ){
                throw new \Exception( 'Could not create logs file: ' . $this->log_file );
              }
              if( !@chmod( $this->log_file, 0644 ) ){
                throw new \Exception( 'Could not set proper permissions on logs file: ' . $this->log_file );
              }
            }
            /** File handler */
            $handler = new StreamHandler( $this->log_file, $this->log_level );
            break;
          case defined( 'RAYGUN_API_KEY' ) && ( ( defined( 'WP_LOGS_HANDLER' ) && WP_LOGS_HANDLER == 'raygun' ) || ( !defined( 'WP_LOGS_HANDLER' ) && ENVIRONMENT == 'production' ) ):
            /** Create the client for RayGun */

	          if( class_exists( 'RaygunClient' ) ) {
		          $client = new RaygunClient( RAYGUN_API_KEY );
	          }
            /** Create the handler */

	          if( class_exists( 'RaygunHandler' ) ) {
		          $handler = new RaygunHandler( $client );
	          }

            break;
          case !defined( 'WP_LOGS_HANDLER' ):
          default:
            /** File name to write to */
            $this->log_file = rtrim( $this->logs_dir, '/' ) . '/debug.log';
            /** Rotating file handler */
            $handler = new RotatingFileHandler( $this->log_file, 14, $this->log_level, true, 0644 );
            break;
        }

        /** Implement the formatter */
        $handler->setFormatter( $this->formatter );

        /** Setup our FingersCrossedHandler */
        $this->handler = new FingersCrossedHandler( $handler, Logger::ERROR );

        /** Now, bring in our file handler, depending on what kind of system we're in */
        if( defined( 'ENVIRONMENT' ) && ENVIRONMENT == 'production' ){
          $this->logger->pushHandler( $this->handler );
        }else{
          $this->logger->pushHandler( $handler );
        }

        /** Register the new error handler */
        ErrorHandler::register( $this->logger );

        /** Default info */
        $this->logger->addDebug( 'GUID: ' . $this->guid );
        $this->logger->addDebug( 'Logging initialized...' );

        /** If we made it here, we should go aheand and just kill any 'debug.log' files that might exist */
        @unlink( rtrim( WP_BASE_DIR, '/' ) . '/debug.log' );
        @unlink( rtrim( WP_SYSTEM_DIRECTORY, '/' ) . '/debug.log' );
        @unlink( rtrim( WP_SYSTEM_DIRECTORY, '/' ) . '/wp-admin/debug.log' );

        /** Return this */
        return $this;
      }

      /**
       * This function generates a guid for logging purposes
       *
       * @link http://php.net/manual/en/function.com-create-guid.php
       */
      function create_guid() {
        if( function_exists( 'com_create_guid' ) ) {
          return strtolower( com_create_guid() );
        } else {
          mt_srand( (double) microtime() * 10000 ); //optional for php 4.2.0 and up.
          $charid = strtoupper( md5( uniqid( rand(), true ) ) );
          $hyphen = chr( 45 );
          $uuid   = substr( $charid, 0, 8 ) . $hyphen . substr( $charid, 8, 4 ) . $hyphen . substr( $charid, 12, 4 ) . $hyphen . substr( $charid, 16, 4 ) . $hyphen . substr( $charid, 20, 12 );
          return strtolower( $uuid );
        }
      }

      /**
       * This function lets us chain methods without having to instantiate first, YOU MUST COPY THIS TO ALL SUB CLASSES
       */
      static public function init() {
        return new self( false );
      }

    }
  }

}