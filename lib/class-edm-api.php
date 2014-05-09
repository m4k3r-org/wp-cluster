<?php
/**
 * API Handler
 *
 */
namespace wpCloud\Vertical\EDM {

  use \UsabilityDynamics\Veneer;

  if( !class_exists( 'wpCloud\Vertical\EDM\API' ) ) {

    /**
     * Class Feed
     *
     * @package wpCloud\Vertical\EDM\API
     */
    class API extends Veneer\API {

      /**
       * Perform System Upgrade
       *
       * @url http://discodonniepresents.com/api/v1/system/upgrade
       */
      static public function systemUpgrade() {
        global $wpdb, $current_site, $current_blog;

        if( !current_user_can( 'manage_options' ) ) {
          wp_send_json(array( 'ok' => false, 'message' => __( 'Invalid capabilities.' ) ));
          return;
        }

        $activePlugins = array(
          'gravityforms',
          'brightcove-video-cloud',
          'google-analytics-for-wordpress',
          'real-user-monitoring-by-pingdom',
          'wordpress-seo',
          'wp-pagenavi',
          'public-post-preview',
          'wpmandrill',
          'regenerate-thumbnails',
          'video',
          'jetpack',
          'widget-css-classes',
          'w3-total-cache',
          'wp-rpc',
          'wp-elastic',
          'sitepress-multilingual-cms',
          'wpml-translation-management'
        );

        $_results = array();

        delete_site_transient( 'update_plugins' );

        foreach( $activePlugins as $plugin ) {
          $_results[ $plugin ] = Utility::install_plugin( $plugin );
        }

        // Remove the stupid "upgrade" directory.
        if( is_dir( $_SERVER[ 'DOCUMENT_ROOT' ] . '/upgrade' ) ) {
          @rmdir( $_SERVER[ 'DOCUMENT_ROOT' ] . '/upgrade' );
        }

        wp_send_json(array(
          'ok' => true,
          'message' => __( 'Sstem upgrade ran.' ),
          'results' => $_results
        ));

      }

      /**
       * Install a Specific Plugin.
       *
       * http://umesouthpadre.com/v1/install/plugin
       * http://umesouthpadre.com/v1/install/plugin&name=wp-property
       */
      static public function pluginInstall() {

        if( !current_user_can( 'install_plugins' ) || !current_user_can( 'activate_plugins' ) ) {
          return;
        }

        $args = Utility::parse_args( $_GET, array(
          'name' => '',
          'version' => ''
        ));

        $_result = Utility::install_plugin( $args->name );

        wp_send_json(array(
          'ok' => !is_wp_error( $_result->result ) ? true : false,
          'data' => $_result
        ));

      }

      static public function getArtists() {}

      static public function getArtist() {}

      static public function getVenues() {}

      static public function getVenue() {}

      /**
       * Google Merchant product feed.
       *
       */
      static public function getList() {

        // Extend request args with defaults.
        $args = Utility::defaults( $_GET, array(
          'limit' => 100,
          'category' => 'available-online',
          'format' => 'json',
          'type' => 'google-merchant',
          'fields' => '*'
        ));

        // For example purposes only.
        if( $args->type === 'ebay' ) {
          return self::send( new \WP_Error( 'Ebay feed is not yet supported.' ) );
        }

        // Send response.
        self::send( array(
          'ok'  => true,
          'message'  => 'Product list query.',
          'args'  => $args
        ));

      }

      /**
       * Get Single Product
       *
       */
      static public function getSingle() {

        // Extend request args with defaults.
        $args = Utility::defaults( $_GET, array(
          'id' => null,
          'fields' => 'title,summary'
        ));

        // Send temp response.
        self::send( array(
          'ok'  => true,
          'message'  => 'Single product query.',
          'req'  => $args
        ));

      }

    }

  }

}