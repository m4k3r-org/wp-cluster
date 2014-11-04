<?php
/**
 * WP CLI utilities to work with GCS and media management.
 *
 * Plugin Name: GCS Sync
 * Plugin URI: http://usabilitydynamics.com/plugins/
 * Description: Syncs media to GCS
 * Author: Usability Dynamics, Inc.
 * Version: 0.0.1
 * Author URI: http://usabilitydynamics.com
 *
 * @uses _gcs_sync_bucket, option key to specify the bucket name for sync
 * @uses _gcs_sync_enabled, option key to determine if the sync process is enabled or disabled
 *
 * @note For use with MAMP: export PATH="/Applications/MAMP/bin/php/php5.3.29/bin:$PATH"
 */


/**
 * Returns an object with "code" and "output"
 *
 * @param string $_input
 *
 * @example runBinaryCommand( "/Users/andy.potanin/devtools/google-cloud-sdk/bin/gsutil cp -a public-read /storage/discodonniepresents.com/media/2014/11/7eb12a48b76de1fb694e1d312add079a38.jpg gs://media-test.discodonniepresents.com/" )->output
 *
 * @author potanin@UD
 * @return array
 */
function runBinaryCommand( $_input = "whoami" ) {

	$_lines = array_merge( array( 'export PATH=$PATH' ), (array) $_input );

	ob_start();
	passthru( implode( " && \\ \n", $_lines ) . " 2>&1", $return_code );
	$var = ob_get_contents();
	ob_end_clean();

	return (object) array(
		"code" => $return_code,
		"output" => $var
	);

}

// $_result = runBinaryCommand( "/Users/andy.potanin/devtools/google-cloud-sdk/bin/gsutil cp -a public-read /Users/andy.potanin/Libraries/www.discodonniepresents.com/storage/public/discodonniepresents.com/media/2014/11/7eb12a48b76de1fb694e1d312add079a38.jpg gs://media-test.discodonniepresents.com/" );
// die( '<pre>' . print_r( $_result->output , true ) . '</pre>');

/**
 * Create our class that'll be used outside of the CLI
 */
if( !class_exists( 'GCS_SYNC' ) ){
  class GCS_SYNC{

    const BUCKET_KEY = '_gcs_sync_bucket';
    const ENABLED_KEY = '_gcs_sync_enabled';
    const WP_TIMESTAMP_KEY = '_gcs_sync_timestamp';

    /**
     * Constructor, we're just going to set our initial options
     */
    function __construct(){

      $this->bucket = get_option( self::BUCKET_KEY );
      $this->enabled = get_option( self::ENABLED_KEY );
      $this->media_dir = wp_upload_dir();
      $this->media_dir = trailingslashit( $this->media_dir[ 'basedir' ] );

    }

    /**
     * Maybe echo out
     */
    function _echo( $line ){
      if( is_callable( 'WP_CLI::line' ) ){
        WP_CLI::line( $line );
      }
    }

    /**
     * Handles actually syncing to GCS
     */
    function _upload_to_gce( $file ){
      /** Setup the command */
      $file = ltrim( $file, '/' );
      $temp_file = tempnam( sys_get_temp_dir(), 'gsutil_' );
      $cmd = "/Users/andy.potanin/devtools/google-cloud-sdk/bin/gsutil cp -a public-read {$this->media_dir}{$file} gs://{$this->bucket}/{$file} > {$temp_file} 2>&1";

      /** Ok, if we're here, run the command */
      $this->_echo( "Running command:" );
      $this->_echo( "{$cmd}" );

      /** Run our command */
      passthru( 'whoami' );

      passthru( $cmd );
      $results = file_get_contents( $temp_file );

      unlink( $temp_file );

      /** @todo This is where we're running into issues, as 'results', doesn't contain the expected output due to path differences */
    }

    function _update_gce_headers( $file, $headers ){
      /** Setup the command */
      $file = ltrim( $file, '/' );
      $cmd = "/Users/andy.potanin/devtools/google-cloud-sdk/bin/gsutil setmeta";
      foreach( (array) $headers as $header => $value ){
        $cmd .= ' -h "' . addcslashes( $header, '"' ) . ':' . addcslashes( $value, '"' ) . '"';
      }
      $cmd .= " gs://{$this->bucket}/{$file} > /dev/null 2>&1";

      /** Ok, if we're here, run the command */
      $this->_echo( "Running command:" );
      $this->_echo( "{$cmd}" );

      /** Run our command */
      exec( $cmd );
    }

    /**
     * This is our function that handles uploading to GCS on request
     *
     * [file] => /Users/andy.potanin/Libraries/www.discodonniepresents.com/storage/public/discodonniepresents.com/media/2014/11/7eb12a48b76de1fb694e1d312add079a38.jpg
     * [url] => http://media.discodonniepresents.com/media/7eb12a48b76de1fb694e1d312add079a38.jpg
     * [type] => image/jpeg
     *
     */
    function handle_upload( $upload, $context ){

      /** Ok, so determine our relative path, and upload the thing */
      $file = str_ireplace( $this->media_dir, '', $upload[ 'file' ] );
      $this->_upload_to_gce( $file );

      /** Ok, now set it's meta */
      $this->_update_gce_headers( $file, array(
        'x-goog-meta-blog-id' => get_current_blog_id(),
        'x-goog-meta-updated-at' => time(),
        'x-goog-meta-post-type' => 'attachment'
      ) );

      /** Just return the upload */
      return $upload;

    }

  }

  /** Create an instance of our object */
  $gcs_sync = new GCS_SYNC();
  /** Go ahead and add our filter to handle auto-uploading to GCS */
  add_filter( 'wp_handle_upload', array( $gcs_sync, 'handle_upload' ), 10, 2 );

}

if( defined( 'WP_CLI' ) && class_exists( 'WP_CLI_Command' ) && !class_exists( 'GCS_SYNC_CLI' ) ) {
  class GCS_SYNC_CLI extends WP_CLI_Command {

    /**
     * In our constructor, all we're going to do is create an instance of the GCS_SYNC class
     */
    function __construct(){

      $this->gcs_sync = new GCS_SYNC();

      /** Clear all output buffers */
      while (ob_get_level()){
        ob_end_flush();
      }
    }

    /**
     * Gets/sets the bucket name in the site options, uses _gcs_sync_bucket
     *
     * @synopsis [--name=<bucket>]
     *
     * ## OPTIONS
     *
     * <bucket>
     * : The name of the GCS bucket to use
     *
     * ## EXAMPLES
     *
     * wp gcssync bucket
     * wp gcssync bucket --name="media.discodonniepresents.com"
     */
    function bucket( $args, $assoc_args ){

      /** If we don't have any args, we're just getting the current bucket */
      if( !in_array( 'name', array_keys( $assoc_args ) ) ){
        if( !$this->gcs_sync->bucket ){
          WP_CLI::error( "No bucket is currently configured. Please do so with 'wp gcssync bucket --name=\"{bucket}\"'." );
        } else{
          WP_CLI::line( "The currently configured bucket is: " . $this->gcs_sync->bucket );
        }
      }else{
        /** Ok, we're going to set the option */
        update_option( GCS_SYNC::BUCKET_KEY, $assoc_args[ 'name' ] );
        $this->gcs_sync->bucket = get_option( GCS_SYNC::BUCKET_KEY );
        /** Ok success, bail */
        WP_CLI::line( "Updated the bucket to: " . $this->gcs_sync->bucket );
      }

    }

    /**
     * Gets GCS Sync status
     *
     * @synopsis
     *
     * ## OPTIONS
     *
     * ## EXAMPLES
     *
     * wp gcssync status
     */
    function status( $args, $assoc_args ){

      /** Just update the option */
      if( !$this->gcs_sync->enabled ){
        WP_CLI::line( "GCS Sync is currently not enabled." );
      }else{
        WP_CLI::line( "GCS Sync is currently enabled." );
      }

    }

    /**
     * Enables GCS sync
     *
     * @synopsis
     *
     * ## OPTIONS
     *
     * ## EXAMPLES
     *
     * wp gcssync enable
     */
    function enable( $args, $assoc_args ){

      /** Just update the option */
      update_option( GCS_SYNC::ENABLED_KEY, '1' );
      /** Nice */
      WP_CLI::line( "Enabled GCS sync." );

    }

    /**
     * Disables GCS sync
     *
     * @synopsis
     *
     * ## OPTIONS
     *
     * ## EXAMPLES
     *
     * wp gcssync enable
     */
    function disable( $args, $assoc_args ){

      /** Just update the option */
      delete_option( GCS_SYNC::ENABLED_KEY );
      /** Nice */
      WP_CLI::line( "Disabled GCS sync." );

    }

    /**
     * Syncs all attachments
     *
     * @synopsis
     *
     * ## OPTIONS
     *
     * ## EXAMPLES
     *
     * wp gcssync sync
     */
    function sync( $args, $assoc_args ){
      global $wpdb;

      /** Ok, if we don't have bucket or if we aren't enable bail */
      if( !$this->gcs_sync->bucket ){
        WP_CLI::error( "No bucket is currently configured. Please do so with 'wp gcssync bucket --name=\"{bucket}\"'." );
      }
      if( !$this->gcs_sync->enabled ){
        WP_CLI::error( "Sync is currently disabled. Please enable sync= 'wp gcssync enable'." );
      }

      WP_CLI::line( "Determining file differences." );
      $different_files = array();
      /** Get our files list */
      $temp_file = tempnam( sys_get_temp_dir(), 'gsutil_' );
      $cmd = "gsutil -m rsync -nCR {$this->gcs_sync->media_dir} gs://{$this->gcs_sync->bucket}/ > {$temp_file} 2>&1";
      WP_CLI::line( "Getting list of all files to upload:" );
      WP_CLI::line( $cmd );
      passthru( $cmd );
      $raw_files_list = explode( "\n", file_get_contents( $temp_file ) );
      unlink( $temp_file );
      /** Ok loop through them all looking for possible copies */
      foreach( $raw_files_list as $row ){
        if( preg_match_all( '/Would copy file.* to gs:\/\/' . $this->gcs_sync->bucket . '\/(.*)/i', $row, $matches ) ){
          /** Ok, add the file to the array of files to copy */
          $different_files[ md5( $matches[ 1 ][ 0 ] ) ] = $matches[ 1 ][ 0 ];
        }
      }

      /** Ok, get all attachments that haven't been synced to this bucket */
      $query = "SELECT DISTINCT post_id FROM {$wpdb->postmeta} WHERE meta_key = '" . GCS_SYNC::BUCKET_KEY ."' AND meta_value = '" . $wpdb->escape( $this->gcs_sync->bucket ) . "'";
      $this_bucket_ids = $wpdb->get_col( $query );
      if( !count( $this_bucket_ids ) ){
        $this_bucket_ids = array( 0 );
      }

      /** Ok, get all attachments that are not already uploaded */
      $query = "SELECT * FROM {$wpdb->posts} WHERE post_type = 'attachment' AND ID NOT IN ( " . implode( $this_bucket_ids, ', ' ) .  " )";
      $pending_posts = $wpdb->get_results( $query, ARRAY_A );

      /** Ok, loop through our pending posts, and upload them if they're in the 'different' array */
      $to_sync = array();
      $i = 0;
      while( $i < count( $pending_posts ) ){
        $post = $pending_posts[ $i ];
        $i++;
        /** If the post file is in the 'different' array, we need to sync it */
        if( isset( $post[ '_meta' ] ) ){
          $meta = $post[ '_meta' ];
        }else{
          $meta = wp_get_attachment_metadata( $post[ 'ID' ] );
        }
        /** Ok, if we don't have a file specified, we look at a different meta keu */
        if( !$meta ){
          $meta = get_post_meta( $post[ 'ID' ] );
          $file = $meta[ '_wp_attached_file' ][ 0 ];
        }else{
          $file = $meta[ 'file' ];
        }
        /** Ok, so let's check the file now versus the 'different' array */
        WP_CLI::line( 'Checking file: ' . $file );
        if( in_array( $file, $different_files ) ){
          /** Let's upload */
          $this->gcs_sync->_upload_to_gce( $file );
          unset( $different_files[ md5( $file ) ] );
        }
        /** Ok, so we made it here, let's update the posts gce headers */
        $this->gcs_sync->_update_gce_headers( $file, array(
          'x-goog-meta-blog-id' => get_current_blog_id(),
          'x-goog-meta-updated-at' => time(),
          'x-goog-meta-post-type' => $post[ 'post_type' ]
        ) );
        /** Ok, if we have meta, we should check for thumbnails */
        if( is_array( $meta ) && isset( $meta[ 'sizes' ] ) && is_array( $meta[ 'sizes' ] ) && count( $meta[ 'sizes' ] ) ){
          $sizes = $meta[ 'sizes' ];
          $file = explode( '/', $meta[ 'file' ] );
          unset( $meta[ 'sizes' ] );
          foreach( $sizes as $size ){
            $new_post = $post;
            array_pop( $file );
            $file[] = $size[ 'file' ];
            $meta[ 'file' ] = implode( '/', $file );
            $new_post[ '_meta' ] = $meta;
            $new_post[ 'post_type' ] = 'thumbnail';
            /** Add it onto the currently processing array */
            $pending_posts[ count( $pending_posts ) ] = $new_post;
          }
        }
      }

      /** We are done */
      WP_CLI::line( "Done processing." );
    }
  }

  /** Add the new commands */
  WP_CLI::add_command( 'gcssync', 'GCS_SYNC_CLI' );
}