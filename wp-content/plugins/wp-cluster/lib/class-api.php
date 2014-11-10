<?php
/**
 * Our Cluster API handler - it should do things like ... @todo
 *
 * @author Reid Williams
 * @class UsabilityDynamics\Cluster\API
 */
namespace UsabilityDynamics\Cluster {

  if( !class_exists( 'UsabilityDynamics\Cluster\API' ) ){
    /**
     * Class API
     *
     * @module Cluster
     */
    class API extends \UsabilityDynamics\API{

      /** Setup our namespace */
      public static $namespace = 'wp-cluster';

      /**
       * Initialize API
       *
       * @version 0.1.5
       * @for API
       */
      public function __construct() {
        if( is_callable( array( 'parent', '__construct' ) ) ){
          parent::__construct();
        }
        /** @todo This may be depreciated */
        $this->actual_url = admin_url( 'admin-ajax.php' );
        /** Let's go ahead and add our routes */
        add_action( 'plugins_loaded', array( $this, 'create_routes' ) );
        /** Return this */
        return $this;
      }

      /**
       * Creates our routes
       */
      public function create_routes(){
        if( is_callable( array( 'parent', 'create_routes' ) ) ){
          parent::create_routes();
        }
        self::define( '/v1/sites', array( $this, 'listSites' ) );
        self::define( '/v1/site', array( $this, 'getSite' ) );
      }

      /**
       * List Sites
       *
       * @url http://discodonniepresents.com/api/v1/cluster/sites
       */
      public function listSites() {
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
        self::send( array(
          'data' => $_sites
        ) );
      }

      /**
       * Gets details on the current site
       *
       * @url http://umesouthpadre.com/api/v1/cluster/site
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
        self::send( array(
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

      /**
       * List Sites
       *
       * @todo MIGRATE THIS FROM OLD FUNCTIONALITY
       * @url http://discodonniepresents.com/api/v1/sites/add
       */
      public function addSites() {
        if ( ! current_user_can( 'manage_sites' ) ) {}

        check_admin_referer( 'add-blog', '_wpnonce_add-blog' );

        if ( ! is_array( $_POST['blog'] ) )
          wp_die( __( 'Can&#8217;t create an empty site.' ) );

        $blog = $_POST['blog'];
        $domain = '';
        if ( preg_match( '|^([a-zA-Z0-9-])+$|', $blog['domain'] ) )
          $domain = strtolower( $blog['domain'] );

        // If not a subdomain install, make sure the domain isn't a reserved word
        if ( ! is_subdomain_install() ) {
          /** This filter is documented in wp-includes/ms-functions.php */
          $subdirectory_reserved_names = apply_filters( 'subdirectory_reserved_names', array( 'page', 'comments', 'blog', 'files', 'feed' ) );
          if ( in_array( $domain, $subdirectory_reserved_names ) )
            wp_die( sprintf( __('The following words are reserved for use by WordPress functions and cannot be used as blog names: <code>%s</code>' ), implode( '</code>, <code>', $subdirectory_reserved_names ) ) );
        }

        $email = sanitize_email( $blog['email'] );
        $title = $blog['title'];

        if ( empty( $domain ) )
          wp_die( __( 'Missing or invalid site address.' ) );
        if ( empty( $email ) )
          wp_die( __( 'Missing email address.' ) );
        if ( !is_email( $email ) )
          wp_die( __( 'Invalid email address.' ) );

        if ( is_subdomain_install() ) {
          $newdomain = $domain . '.' . preg_replace( '|^www\.|', '', $current_site->domain );
          $path      = $current_site->path;
        } else {
          $newdomain = $current_site->domain;
          $path      = $current_site->path . $domain . '/';
        }

        $password = 'N/A';
        $user_id = email_exists($email);
        if ( !$user_id ) { // Create a new user with a random password
          $password = wp_generate_password( 12, false );
          $user_id = wpmu_create_user( $domain, $password, $email );
          if ( false == $user_id )
            wp_die( __( 'There was an error creating the user.' ) );
          else
            wp_new_user_notification( $user_id, $password );
        }

        $wpdb->hide_errors();
        $id = wpmu_create_blog( $newdomain, $path, $title, $user_id , array( 'public' => 1 ), $current_site->id );
        $wpdb->show_errors();
        if ( !is_wp_error( $id ) ) {
          if ( !is_super_admin( $user_id ) && !get_user_option( 'primary_blog', $user_id ) ){
            update_user_option( $user_id, 'primary_blog', $id, true );
          }
          $content_mail = sprintf( __( "New site created by %1$s\r\n\r\nAddress: %2$s\r\nName: %3$s" ), $current_user->user_login , get_site_url( $id ), wp_unslash( $title ) );
          wp_mail( get_site_option('admin_email'), sprintf( __( '[%s] New Site Created' ), $current_site->site_name ), $content_mail, 'From: "Site Admin" <' . get_site_option( 'admin_email' ) . '>' );
          wpmu_welcome_notification( $id, $user_id, $password, $title, array( 'public' => 1 ) );
          wp_redirect( add_query_arg( array( 'update' => 'added', 'id' => $id ), 'site-new.php' ) );
          exit;
        } else {
          wp_die( $id->get_error_message() );
        }

      }

    }
  }
}