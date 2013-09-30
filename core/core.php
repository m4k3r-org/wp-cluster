<?php
/**
 *
 */
namespace Varnish {

  /**
   *
   *
   */
  class Core {

    /**
     * {}
     *
     */
    function initialize() {

      /**
       * {}
       *
       */
      add_action( 'plugins_loaded', function () {
      } );

      /**
       * {}
       *
       */
      add_action( 'admin_bar_menu', function ( $wp_admin_bar ) {

        if ( !is_super_admin() || !is_multisite() ) {
          return;
        }

        $wp_admin_bar->add_menu( array(
          'parent' => 'network-admin',
          'id'     => 'network-themes',
          'title'  => __( 'Themes' ),
          'href'   => network_admin_url( 'themes.php' ),
        ) );

        $wp_admin_bar->add_menu( array(
          'parent' => 'network-admin',
          'id'     => 'network-plugins',
          'title'  => __( 'Plugins' ),
          'href'   => network_admin_url( 'plugins.php' ),
        ) );

        $wp_admin_bar->add_menu( array(
          'parent' => 'network-admin',
          'id'     => 'network-plugins',
          'title'  => __( 'VarnishCMS Settings' ),
          'href'   => network_admin_url( 'settings.php' ),
        ) );

      }, 21 );

      /**
       * {}
       *
       */
      add_action( 'admin_init', function () {

        /* Remove Akismet API Key Nag */
        remove_action( 'admin_notices', 'akismet_warning' );

        /* Disable BuddyPress Nag */
        remove_action( 'admin_notices', 'bp_core_update_nag', 5 );
        remove_action( 'network_admin_notices', 'bp_core_update_nag', 5 );

      } );

    }

    /**
     * Automatically fix MS URLs that get messed up
     *
     */
    function load_mu_plugins() {

      if ( file_exists( WPMU_PLUGIN_DIR . '/image-widget/image-widget.php' ) ) {
        include_once WPMU_PLUGIN_DIR . '/image-widget/image-widget.php';
      }

      if ( file_exists( WPMU_PLUGIN_DIR . '/wordpress-importer/wordpress-importer.php' ) ) {
        include_once WPMU_PLUGIN_DIR . '/wordpress-importer/wordpress-importer.php';
      }

      if ( file_exists( WPMU_PLUGIN_DIR . '/akismet/akismet.php' ) ) {
        include_once WPMU_PLUGIN_DIR . '/akismet/akismet.php';
      }

      if ( file_exists( WPMU_PLUGIN_DIR . '/configure-smtp/configure-smtp.php' ) ) {
        include_once WPMU_PLUGIN_DIR . '/configure-smtp/configure-smtp.php';
      }

    }

    /**
     * Automatically fix MS URLs that get messed up
     *
     */
    function fix_urls() {

      add_filter( 'network_site_url', function ( $url ) {
        //if( !strpos( $url, '/system' ) ) { return trailingslashit( $url ) . 'system/'; }
        return str_replace( 'wp-admin', 'system/wp-admin', $url );
      } );

      add_filter( 'blog_option_upload_path', function ( $url ) {

        if ( strpos( $url, 'wp-content/blogs.dir' ) !== false ) {
          return str_replace( 'wp-content/blogs.dir', 'media/sites', $url );
        }

        if ( strpos( $url, 'wp-content/uploads' ) !== false ) {
          return str_replace( 'wp-content/uploads', 'media/sites', $url );
        }

        return $url;

      } );

    }

    /**
     * Scan all blog file directories and look for /themes/ directory
     *
     * @todo Should probably have them automatically enabled for the respective blog.
     */
    function add_theme_directories() {
      global $wpdb, $_varnish;

      if ( is_dir( WP_CONTENT_DIR . '/themes-client' ) ) {
        $_varnish[ 'theme_directories' ][ ] = WP_CONTENT_DIR . '/themes-client';
      }

      foreach ( $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}" ) as $blog_id ) {

        $_upload_path = get_blog_option( $blog_id, 'upload_path' );

        $_blog_theme_directory = str_replace( 'files', 'themes', WP_BASE_DIR . '/' . $_upload_path );

        if ( is_dir( $_blog_theme_directory ) ) {
          $_varnish[ 'theme_directories' ][ ] = $_blog_theme_directory;
        }

      }

      foreach ( (array) $_varnish[ 'theme_directories' ] as $directory ) {
        register_theme_directory( $directory );
      }

    }

  }

}