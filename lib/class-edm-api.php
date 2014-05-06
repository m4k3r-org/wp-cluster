<?php
/**
 * API Handler
 *
 */
namespace wpCloud\Vertical\EDM {

  use \UsabilityDynamics\Utility;
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
       * @url http://discodonniepresents.com/api/system/upgrade
       */
      static public function systemUpgrade() {

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
          'intelligence',
        );

        // @todo Flush transients.

        // @todo Flush whatever-the-fuck caches themes / theme locations.

        // @todo Register and re-activate plugins, after plugin path has changed from modules to vendor/modules.

        // @todo Register and network re-enable themes, after theme path has changed from themes to vendor/themes.

        // @todo Re-activate themes given location change.

        wp_send_json(array(
          'ok' => true,
          'message' => __( 'systemUpgrade ran' )
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