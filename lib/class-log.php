<?php
/**
 * Log Access Controller
 *
 * @module Cluster
 * @author potanin@UD
 */
namespace UsabilityDynamics\Cluster {

  use Monolog\Logger;
  use Monolog\Handler\RotatingFileHandler;
  use Monolog\ErrorHandler;

  if( !class_exists( 'UsabilityDynamics\Cluster\Log' ) ) {
    /**
     * Class Log
     *
     * @module Cluster
     */
    class Log {

      /**
       * Initialize Log
       *
       * @for Log
       */
      public function __construct() {
//        /** Bring in a copy of the wp cluster object */
//        global $wp_cluster;
//        $this->cluster =& $wp_cluster;
//        /** Setup the logger */
//        $this->logger = new Logger( uniqid() );
//        /** Determine the path to our logs */
//        $this->_log_location = rtrim( WP_LOGS_DIR, '/' ) . '/' . $this->cluster->domain;
//        /** Try to create the log directory in case it doesn't exist */
//        if( !is_dir( $this->_log_location ) ){
//          if( !( mkdir( $this->_log_location, 0777, true ) ) ){
//            die( 'Could not enable logging.' );
//          }
//        }
//        /** Setup the file name */
//        $this->_log_file = $this->_log_location . '/log';
//        /** Ok, now we can create/add our handler */
//        $this->handler = new RotatingFileHandler( $this->_log_file, 30, Logger::DEBUG );
//        /** Now, bring in our file handler */
//        $this->logger->pushHandler( $this->handler );
//        /** Register the new error handler */
//        ErrorHandler::register( $this->logger );
//        /** Log something */
//        $this->logger->addInfo( 'Logging successfully started.' );
      }

    }
  }
}