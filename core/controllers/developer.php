<?php
/**
 * Plugin Name: UD Developer
 * Plugin URI: http://usabilitydynamics.com
 * Description: Tools for development.
 * Author: Andy Potanin
 * Version: 1.0
 * Author URI: http://usabilitydynamics.com
 *
 */
namespace Veneer {

  /**
   * Class Developer
   *
   * @package Veneer
   */
  class Developer {

    function __construct() {
      add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ), 50 );
    }

    /**
     * {}
     *
     */
    function admin_menu() {

      if( current_user_can( 'manage_options' ) ) {
        // add_options_page( 'Developer', 'Developer', 'manage_options', 'ud', array( 'Veneer\Developer', 'page_ud' ) );
      }

    }

    /**
     * Add WP actions to be logged for debugging
     *
     * @ported Flawless 3.0
     */
    function add_filter_monitor() {

      add_action( 'after_setup_theme', create_function( "", "Developer::log('P: Action: after_setup_theme');" ) );
      add_action( 'set_current_user', create_function( "", "Developer::log('P: Action: set_current_user');" ) );

      add_action( 'init', create_function( "", "Developer::log('P: Action: init (10) ');" ) );
      add_action( 'widgets_init', create_function( "", "Developer::log('P: Action: widgets_init (10) ');" ) );

      /*
      add_action('init', create_function("", "Developer::log('P: Action: init (0)');"), 0);
      add_action('init', create_function("", "Developer::log('P: Action: init (2)');"), 2);

      add_action('widgets_init', create_function("", "Developer::log('P: Action: widgets_init (0) ');"), 0);
      */

      add_action( 'wp_loaded', create_function( "", "Developer::log('P: Action: wp_loaded');" ) );

      add_action( 'template_redirect', create_function( "", "Developer::log('P: Action: template_redirect');" ) );
      //add_action('wp_register_sidebar_widget', create_function("", "Developer::log('P: Action: wp_register_sidebar_widget');"));

      add_action( 'flawless_theme_setup', create_function( "", "Developer::log('P: Action: flawless_theme_setup');" ) );
      add_action( 'flawless_define_widget_area_sections', create_function( "", "Developer::log('P: Action: flawless_define_widget_area_sections');" ) );

      add_action( 'flawless::init_lower', create_function( "", "Developer::log('P: Action: flawless::init_lower');" ) );
      add_action( 'flawless::init_upper', create_function( "", "Developer::log('P: Action: flawless::init_upper');" ) );
      add_action( 'flawless::create_views', create_function( "", "Developer::log('P: Action: flawless::create_views');" ) );

      /* register_sidebar - ran after registration of each sidebar, called from register_sidebar() */
      //add_action('flawless::register_sidebar', create_function("", "Developer::log('P: Action: flawless::register_sidebar');"));
      add_action( 'register_sidebar', create_function( "", "Developer::log('P: Action: register_sidebar');" ) );

      //add_action('wp_loaded', create_function("", "Developer::log(debug_backtrace());"));
      //add_action('widgets_init', create_function("", "Developer::log(debug_backtrace());"));

      //add_action('register_sidebar', create_function("", "Developer::log(debug_backtrace());"));
      //add_action('register_sidebar', create_function("", "Developer::file_backtrace();"));

    }

    /**
     * PHP function to echoing a message to JS console
     *
     * @ported Flawless 3.0
     */
    function log( $text = false ) {
      global $flawless_settings;

      $text = maybe_serialize( $text );

      add_filter( 'wp_footer', create_function( '$nothing, $text = \'' . $text . '\' ', ' echo Developer::print_debug_js($text);  ' ) );
      add_filter( 'admin_footer', create_function( '$nothing, $text = \'' . $text . '\' ', ' echo Developer::print_debug_js($text);  ' ) );

    }

    /**
     * PHP function to echoing a message to JS console
     *
     * @ported Flawless 3.0
     */
    function file_backtrace( $text = false ) {
      global $flawless_settings;

      $backtrace = debug_backtrace();

      $parsed = array();

      foreach( $backtrace as $count => $step ) {

        if( $count == 0 ) {
          continue;
        }

        if( !$step[ 'file' ] ) {
          continue;
        }

        if( $step[ 'function' ] == 'call_user_func_array' ) {
          continue;
        }

        if( $step[ 'function' ] == 'do_action' ) {
          $step[ 'function' ] = 'Action: ' . $step[ 'args' ][ 0 ];
        }

        $parsed[ ] = ( $step[ 'class' ] ? $step[ 'class' ] . '::' : "" ) . "$step[function] " . ( $step[ 'file' ] ? '(' . $step[ 'file' ] . ', ' . $step[ 'line' ] . ')' : '' );

      }

      //die("<pre>" . print_r($backtrace,true) . "</pre>");

      $text = maybe_serialize( $parsed );

      add_filter( 'wp_footer', create_function( '$nothing, $text = \'' . $text . '\' ', ' echo Developer::print_debug_js($text);  ' ) );
      add_filter( 'admin_footer', create_function( '$nothing, $text = \'' . $text . '\' ', ' echo Developer::print_debug_js($text);  ' ) );

    }

    /**
     * Prints JS for the console info when in debug mode in the footer.
     *
     * @ported 1.26.0
     */
    function print_debug_js( $text ) {

      $text = maybe_unserialize( $text );

      if( is_array( $text ) ) {
        $text = 'jQuery.parseJSON(' . json_encode( json_encode( $text ) ) . ')';
      } else {
        $text = "'" . $text . "'";
      }

      ob_start();  ?>
      <script type="text/javascript">if ( typeof console == "object" && typeof console.info == "function" ) {
          console.info( <?php echo $text; ?> );
        }</script><?php

      $content = ob_get_contents();
      ob_end_clean();

      return $content;

    }

    function page_ud() {

      if( wp_verify_nonce( $_REQUEST[ '_wpnonce' ], 'ud_action' ) ) {

        if( $_REQUEST[ 'delete_all_posts' ] == 'Delete all Posts' ) {
          // $result = self::delete_all_posts( 'all' );
        }

        if( $_REQUEST[ 'delete_all_posts' ] == 'Delete all Properties' ) {
          // $result = self::delete_all_posts( 'property' );
        }

        if( $_REQUEST[ 'delete_all_images' ] == 'Delete all Images' ) {
          // $result = self::delete_all_images();
        }

      }


      ?>
      <style type="text/css">
      .ud_dev_actions li {
        -moz-border-bottom-colors: none;
        -moz-border-image: none;
        -moz-border-left-colors: none;
        -moz-border-right-colors: none;
        -moz-border-top-colors: none;
        background: none repeat scroll 0 0 #F7F7F7;
        border-color: #DADADA #DADADA #DADADA #F9A500;
        border-style: solid;
        border-width: 1px 1px 1px 5px;
        padding: 10px;
      }
    </style>

      <div class="wrap">
      <h2>Developer</h2>
      <div class="error">
        <p><b>Warning!</b> This is a development tool, and can do a lot of damage, use at your own risk.</p>
        <?php if( $result ): ?>
          <p><b><?php echo count( $result[ 'deleted_objects' ] ); ?></b> objects deleted.</p>
          <p><b><?php echo count( $result[ 'deleted_attachments_from_db' ] ); ?></b> attachments deleted from database.</p>
          <p><b><?php echo count( $result[ 'deleted_attachments_from_disk' ] ); ?></b> attachments deleted from disk.</p>
          <p><b>Time: <?php echo count( $result[ 'time' ] ); ?></b> seconds.</p>
        <?php endif; ?>
      </div>

      <form action="" method="POST">
        <input type="hidden" name='_wpnonce' value="<?php echo wp_create_nonce( 'ud_action' ); ?>"/>
        <ul class="ud_dev_actions">
          <li>
            <input type="submit" name="delete_all_posts" value="Delete all Posts"/>
            <span class="description">Delete all pages, posts, properties and their meta data.</span>
           </li>
          <li>
            <input type="submit" name="delete_all_posts" value="Delete all Properties"/>
            <span class="description">Delete properties and their meta data.</span>
           </li>

          <li>
            <input type="submit" name="delete_all_images" value="Delete all Images"/>
            <span class="description">Delete images from DB and disk.</span>
           </li>

          <li>
            <label for="">Resize all original property images that are over:</label>
            <input type="text" id="" name="ud_dev[resize_images][min_image_width]"/>px
            to
            <input type="text" id="" name="ud_dev[resize_images][image_width]"/>px,
            for Property Type:
            <input type="text" id="" name="ud_dev[resize_images][property_type]"/>
            <input type="submit" name="ud_dev_action" value="Resize Property Images"/>
           </li>
         </ul>
      </form>

     </div>


    <?php
    }

    function resize_images( $args ) {
      global $wpdb;

      $image_width     = $args[ 'image_width' ];
      $min_image_width = $args[ 'min_image_width' ];
      $property_type   = $args[ 'property_type' ];

      $upload_dir = wp_upload_dir();

      if( $property_type ) {

        $images = $wpdb->get_col( "
          SELECT p.ID
          FROM {$wpdb->posts} p
          LEFT JOIN {$wpdb->postmeta} pm ON pm.post_id = p.post_parent
          WHERE pm.meta_key = 'property_type' AND pm.meta_value = '{$property_type}'
          AND p.post_mime_type = 'image/jpeg'
          " );

        foreach( $images as $image_id ) {

          $image_data[ ] = $wpdb->get_var( "SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = $image_id AND meta_key = '_wp_attached_file' " );

        }
      }

      echo "<pre>";
      echo "Found " . count( $image_data ) . " images. <br />";

      foreach( $image_data as $image_path ) {

        $image_path = $upload_dir[ 'basedir' ] . '/' . $image_path;

        $image_size = getimagesize( $image_path );

        print_r( $image_size );

        echo " $image_path <br />";
        continue;

        $image = new ResizeImage();
        $image->load( $image_path );
        $image->resizeToWidth( 250 );
        $image->save( $image_path );

      }

      echo "</pre>";

    }

    function delete_all_images() {
      global $wpdb;

      set_time_limit( 0 );

      timer_start();
      $uploads_dir = wp_upload_dir();

      $wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_type = 'attachment'" );

      $all_attachments = $wpdb->get_results( "SELECT * FROM {$wpdb->postmeta} WHERE meta_key = '_wp_attached_file'" );

      foreach( $all_attachments as $data ) {

        $wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE post_id = '{$data->post_id}'" );

        @unlink( $uploads_dir[ 'basedir' ] . '/' . $data->meta_value );

      }

    }

    function delete_all_posts( $type ) {
      global $wpdb;

      set_time_limit( 0 );

      timer_start();
      $uploads_dir = wp_upload_dir();

      if( $type == 'property' ) {

        $types[ ] = 'property';

      } elseif( $type == 'all' ) {

        $types[ ] = 'property';
        $types[ ] = 'post';
        $types[ ] = 'page';

      }

      $limit = 200;

      $post_ids = array();

      // Get list of all objects we are going to dlete
      foreach( $types as $type ) {
        $these_ids = $wpdb->get_col( "SELECT ID FROM {$wpdb->posts} WHERE post_type = '{$type}'" );

        if( !empty( $these_ids ) ) {
          $post_ids = array_merge( $post_ids, $these_ids );
        }
      }

      foreach( $post_ids as $count => $post_id ) {

        if( $count == $limit ) {
          break;
        }

        // get images
        $attachment_ids = $wpdb->get_col( "SELECT ID FROM {$wpdb->posts} WHERE post_parent = $post_id AND post_type = 'attachment'" );

        if( !empty( $attachment_ids ) ) {

          foreach( $attachment_ids as $attachment_id ) {

            // Get image path
            $_wp_attached_file = get_post_meta( $attachment_id, '_wp_attached_file', true );

            // DOES NOT REMOVE RESIZED VERSIONS OF IMAGE

            // Delete image from system
            if( $_wp_attached_file ) {
              $attachment_path                                = $uploads_dir[ 'basedir' ] . '/' . $_wp_attached_file;
              $affected[ 'deleted_attachments_from_disk' ][ ] = unlink( $attachment_path );
            }

            // Delete image from DB
            $affected[ 'deleted_attachments_from_db' ][ ] = wp_delete_post( $attachment_id, true )->ID;

          }
        }

        // Delete post, meta and taxonomy associations
        $affected[ 'deleted_objects' ][ ] = wp_delete_post( $post_id, true )->ID;

      }

      $affected[ 'time' ] = timer_stop();

      return $affected;

    }

  }

}