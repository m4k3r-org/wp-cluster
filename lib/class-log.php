<?php
/**
 * Log Access Controller
 *
 * @module Cluster
 * @author potanin@UD
 */
namespace UsabilityDynamics\Cluster {

  use Monolog\Logger;
  use Monolog\Handler\SyslogHandler;
  use Monolog\Formatter\LineFormatter;
  use Monolog\ErrorHandler;

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
      const CHANNEL = 'wpcloud';
      const FACILITY = 'local6';

      /**
       * Holds our required Monolog objects
       */
      private $handler;
      private $logger;
      private $formatter;
      private $guid;

      /**
       * The line format we'll be using
       */
      private $line_format = '%channel% - %level_name% - %datetime% - %message%';

      /**
       * Initialize Log
       *
       * @for Log
       */
      public function __construct() {
        /** Bring in a copy of the wp cluster object */
        global $wp_cluster;
        $this->cluster =& $wp_cluster;
        /** Setup the GUID */
        $this->guid = $this->create_guid();
        /** Setup the logger */
        $this->logger = new Logger( $this->guid );
        /** Build our line formatter */
        $this->line_format = $this->cluster->domain . ' - ' . $this->line_format . PHP_EOL;
        /** Setup the formatter */
        $this->formatter = new LineFormatter( $this->line_format );
        /** Ok, now we can create/add our handler */
        $this->handler = new SyslogHandler( self::CHANNEL, self::FACILITY, Logger::DEBUG );
        /** Implement the formatter */
        $this->handler->setFormatter( $this->formatter );
        /** Now, bring in our file handler */
        $this->logger->pushHandler( $this->handler );
        /** Register the new error handler */
        ErrorHandler::register( $this->logger );
        /** Default info */
        $this->logger->addDebug( 'GUID: ' . $this->guid );
        $this->logger->addDebug( 'Logging initialized...' );
      }

      /**
       * This function generates a guid for logging purposes
       * @link http://php.net/manual/en/function.com-create-guid.php
       */
      function create_guid() {
        if ( function_exists( 'com_create_guid' ) ) {
          return strtolower( com_create_guid() );
        } else {
          mt_srand( (double)microtime() * 10000 ); //optional for php 4.2.0 and up.
          $charid = strtoupper( md5( uniqid( rand(), true ) ) );
          $hyphen = chr( 45 );
          $uuid = substr( $charid, 0, 8 ) . $hyphen . substr( $charid, 8, 4 ) . $hyphen . substr( $charid, 12, 4 ) . $hyphen . substr( $charid, 16, 4 ) . $hyphen . substr( $charid, 20, 12 );
          return strtolower( $uuid );
        }
      }

    }
  }
}