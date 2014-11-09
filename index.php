<?php
/**
 * WordPress Site Loader
 */
try {

  header( 'PageSpeed: off' );

  /** Make sure we have a proper wp-config file */
  if( !file_exists( __DIR__ . '/wp-config.php' ) ) {
    throw new Exception( 'Site not installed.' );
  }

} catch( Exception $e ) {

  /** There was an issue, we need to bail */
  header( 'HTTP/1.1 500 Internal Server Error' );
  echo '<h1>Site Error</h1>';
  echo '<p>' . $e->getMessage() . '</p>';
  die();

}
