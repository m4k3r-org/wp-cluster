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

        if( !current_user_can( 'manage_options' ) ) {
          wp_send_json(array( 'ok' => false, 'message' => __( 'Invalid capabilities.' ) ));
          return;
        }

        include_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );
        include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

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

        foreach( $activePlugins as $plugin ) {
          $_results[ $plugin ] = Utility::install_plugin( $plugin );
        }

        // wp_clean_plugins_cache();
        // wp_clean_themes_cache();

        // delete_site_transient( 'theme_roots' );

        // update_option( 'upload_path', '/storage/public/' . $current_blog->domain );
        // update_option( 'template_root', '/vendor/themes' );
        // update_option( 'stylesheet_root', '/vendor/themes' );


        // @todo Flush transients.

        // @todo Flush whatever-the-fuck caches themes / theme locations.

        // @todo Register and re-activate plugins, after plugin path has changed from modules to vendor/modules.

        // @todo Register and network re-enable themes, after theme path has changed from themes to vendor/themes.

        // @todo Re-activate themes given location change.

        wp_send_json(array(
          'ok' => true,
          'message' => __( 'Sstem upgrade ran.' ),
          'results' => $_results
        ));

      }

      /**
       * List Sites
       *
       * * @url http://discodonniepresents.com/api/v1/sites
       * * @url http://discodonniepresents.com/api/v1/sites
       */
      static public function listSites() {
        global $wpdb, $current_site, $current_blog;

        if( !current_user_can( 'manage_options' ) ) {
          return;
        }

        $_sites = array();


        foreach( (array) $wpdb->get_results( "SELECT * FROM {$wpdb->blogs} WHERE site_id = {$current_blog->site_id} " ) as $site ) {

          switch_to_blog( $site->blog_id );

          $_sites[] = array(
            '_id' => $site->blog_id,
            'details' => get_blog_details(),
            'theme' => array(
              'template' => get_option( 'template' ),
              'stylesheet' => get_option( 'stylesheet' ),
              'template_root' => get_option( 'template_root' )
            )
          );

          restore_current_blog();

        }

        wp_send_json(array(
          'ok' => true,
          'data' => $_sites
        ));

      }

      /**
       * List Defined API Routes
       *
       * http://discodonniepresents.com/api/v1/routes
       */
      static public function listRoutes() {

        if( !current_user_can( 'read' ) ) {
          return;
        }

        wp_send_json(array(
          'ok' => true,
          'routes' => API::routes()
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

      /**
       *
       * http://umesouthpadre.com/manage/admin-ajax.php?action=/v1/site
       *
       */
      static public function getSite() {
        global $wpdb, $current_site, $current_blog, $wp_post_types, $_wp_post_type_features, $wp_plugin_paths, $wp_theme_directories, $_wp_theme_features;

        if( !current_user_can( 'manage_options' ) ) {
          return;
        }

        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

        $activePlugins = array();

        foreach( (array) get_option( 'active_plugins' ) as $plugin_path ) {
          $activePlugins[] = array(
            'plugin' => $plugin_path,
            'active' => is_plugin_active( $plugin_path ),
            'uninstallable' => is_uninstallable_plugin( $plugin_path ),
            'network_only' => is_network_only_plugin( $plugin_path ),
            'exists' => is_file( $plugin_path )
          );
        }

        wp_send_json(array(
          'ok' => true,
          'data' => array(
            'site' => $current_blog,
            'is_multisite' => is_multisite(),
            'urls' => array(
              'admin_url' => admin_url( 'wp-ajax.php' ),
              'admin_url:relative' => admin_url( 'wp-ajax.php', 'relative' ),
              'wp_logout_url' => wp_logout_url(),
              'wp_admin_css_uri' => wp_admin_css_uri(),
              'home_url' => get_home_url(),
              'login_url' => wp_login_url(),
              'site_url' => get_site_url(),
              'includes_url' => includes_url(),
              'content_url' => content_url(),
              'plugins_url' => plugins_url(),
              'network_site_url' => network_site_url(),
              'network_home_url' => network_home_url(),
              'network_admin_url' => network_admin_url(),
              'self_admin_url' => self_admin_url(),
              'user_admin_url' => user_admin_url(),
              'get_stylesheet_directory_uri' => get_stylesheet_directory_uri(),
              'get_template_directory_uri' => get_template_directory_uri(),
            ),
            'paths' => array(
              'plugins' => array(
                'path' => WP_PLUGIN_DIR,
                'exists' => is_dir( WP_PLUGIN_DIR )
              ),
              'content' => array(
                'path' => WP_CONTENT_DIR,
                'exists' => is_dir( WP_CONTENT_DIR )
              ),
              'template_directory' => array(
                'get_template_directory' => get_template_directory(),
                'exists' => is_dir( get_template_directory() )
              ),
              'stylesheet_directory' => array(
                'get_stylesheet_directory' => get_stylesheet_directory(),
                'exists' => is_dir( get_stylesheet_directory() )
              )
            ),
            'themes' => array(
              'mods' => get_theme_mods(),
              'roots' => get_theme_roots(),
              'features' => $_wp_theme_features,
              'current_theme' => get_option( 'current_theme' ),
              'is_child_theme' => is_child_theme(),
              'template' => get_option( 'template' ),
              'stylesheet' => get_option( 'stylesheet' ),
              'template_root' => get_option( 'template_root' ),
              'stylesheet_root' => get_option( 'stylesheet_root' ),
              'curentTheme' => array(
                'exists' => wp_get_theme()->exists(),
                'version' => wp_get_theme()->get( 'Version' ),
                'description' => wp_get_theme()->get( 'Description' ),
                'author' => wp_get_theme()->get( 'Author' ),
                'screenshot' => wp_get_theme()->get_screenshot(),
                'files' =>  wp_get_theme()->get_files(),
              ),
              'directories' => $wp_theme_directories,
              'allowedThemes' => array(
                'active_theme' => wp_get_theme(),
                'allowed_on_network' => \WP_Theme::get_allowed_on_network(),
                'allowed_on_site' => \WP_Theme::get_allowed_on_site(),
                'allowdThemes' => wp_get_themes( array( 'errors' => false, 'allowed' => true )),
                'brokenThemes' => wp_get_themes( array( 'errors' => true, 'allowed' => true ))
              )
            ),
            'plugins' => array(
              '$wp_plugin_paths' => $wp_plugin_paths,
              'get_mu_plugins' => get_mu_plugins(),
              'get_dropins' => get_dropins(),
              'get_plugins' => get_plugins(),
              'uninstall_plugins' => get_option( 'uninstall_plugins' ),
              'active_plugins' => get_option( 'active_plugins' ),
              'active_sitewide_plugins' => get_option( 'active_sitewide_plugins' )
            ),
            'plugin_report' => $activePlugins,
            'network' => array(
              'meta' => $current_site,
              'active_sitewide_plugins' => get_site_option( 'active_sitewide_plugins' ),
              'site_admins' => get_site_option( 'site_admins' ),
            ),
            'user' => array(
              'settings' => get_all_user_settings(),
              'current' => wp_get_current_user(),
              'user_roles' => get_option( $wpdb->prefix . 'user_roles' )
            ),
            'rewrite_rules' => get_option( 'rewrite_rules' ),
            'permalink_structure' => get_option( 'permalink_structure' ),
            'tag_base' => get_option( 'tag_base' ),
            'upload_path' => get_option( 'upload_path' ),
            'upload_url_path' => get_option( 'upload_url_path' ),
            'site_admins' => get_option( 'site_admins' ),
            'post_types' => get_post_types(),
            'post_types_supports' => get_all_post_type_supports(),
            'get_post_type_labels' => $_wp_post_type_features
          )
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