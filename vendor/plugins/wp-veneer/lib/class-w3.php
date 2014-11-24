<?php
/**
 * W3 Extension.
 *
 */
namespace UsabilityDynamics\Veneer {

  if( !class_exists( 'UsabilityDynamics\Veneer\W3' ) ) {

    class W3 {

      /**
       * W3 Total Cache Instance
       *
       * @public
       * @property $root
       * @type {Object}
       */
      public $root = null;
      /**
       * Instantiate.
       *
       */
      function __construct() {

        if( !file_exists( WP_PLUGIN_DIR . '/w3-total-cache/inc/define.php' ) ) {
          return;
        }

        if( !defined( 'WP_CACHE' ) || !WP_CACHE ) {
          return;
        }

        return;

        add_action( 'admin_menu', array( $this, 'admin_menu' ), 15 );
        add_action( 'network_admin_menu', array( $this, 'network_admin_menu' ), 15 );

        add_action( 'admin_init', array( $this, 'admin_init' ) );
        add_filter( 'w3tc_extensions', array( $this, 'extension' ), 10, 2 );
        add_action( 'w3tc_extensions_page-veneer', array( $this, 'extension_header' ) );

        // Already Setup somehow.
        if( !defined( 'W3TC' ) ) {

          // Enable Database Clustering.
          define( 'W3TC_ENTERPRISE', true );

          // Disable Upgrade Nag.
          define( 'W3TC_PRO', true );

          define( 'W3TC_CACHE_CONFIG_DIR', WP_CONTENT_DIR  . '/application/static/etc/w3/cache' );
          define( 'W3TC_CONFIG_DIR', WP_CONTENT_DIR . '/application/static/etc/w3/config' );
          define( 'W3TC_CACHE_DIR', WP_CONTENT_DIR . '/storage/static/cache' );

          define( 'W3TC_CACHE_MINIFY_DIR', W3TC_CACHE_DIR  . '/minify' );
          define( 'W3TC_CACHE_PAGE_ENHANCED_DIR', W3TC_CACHE_DIR  . '/enhanced' );
          define( 'W3TC_CACHE_TMP_DIR', W3TC_CACHE_DIR . '/tmp' );
          define( 'W3TC_CACHE_BLOGMAP_FILENAME', W3TC_CACHE_DIR . '/blogs.php' );
          define( 'W3TC_WP_LOADER', ( defined( 'WP_PLUGIN_DIR' ) ? WP_PLUGIN_DIR : WP_CONTENT_DIR . '/plugins' ) . '/w3tc-wp-loader.php' );

          // define( 'W3TC_ADDIN_FILE_ADVANCED_CACHE', WP_VENDOR_DIR . '/usabilitydynamics/wp-cluster/lib/local/advanced-cache.php');
          // define( 'W3TC_ADDIN_FILE_OBJECT_CACHE', WP_VENDOR_DIR . '/usabilitydynamics/wp-cluster/lib/local/object-cache.php');
          // define( 'W3TC_ADDIN_FILE_DB', WP_VENDOR_DIR . '/usabilitydynamics/wp-cluster/lib/local/db.php');

          // define( 'W3TC_FILE_DB_CLUSTER_CONFIG', WP_VENDOR_DIR . '/usabilitydynamics/wp-cluster/lib/local/db-cluster-config.php');
          // define( 'W3TC_PLUGINS_DIR', WP_CONTENT_DIR . '/static/etc' );
          // define( 'W3TC_INSTALL_DIR', W3TC_DIR . '/static/etc' );
          // define( 'W3TC_INSTALL_MINIFY_DIR', W3TC_CACHE_DIR . '/min');
          // define( 'W3TC_INSTALL_FILE_ADVANCED_CACHE', W3TC_INSTALL_DIR . '/advanced-cache.php');
          // define( 'W3TC_INSTALL_FILE_DB', W3TC_INSTALL_DIR . '/db.php');
          // define( 'W3TC_INSTALL_FILE_OBJECT_CACHE', W3TC_INSTALL_DIR . '/object-cache.php');
          // define( 'W3TC_EXTENSION_DIR', W3TC_DIR . '/static/etc' );

          // Load W3 Total Cache and Initialize.
          @include_once WP_PLUGIN_DIR . '/w3-total-cache/inc/define.php';

          $this->root = w3_instance( 'W3_Root' );
          $this->root->run();

          // Forces W3 to not load natively.
          define( 'W3TC_IN_MINIFY', true );

          // @todo Use to modify settings.
          if( is_callable( 'w3_instance' ) ) {
            $this->instance = w3_instance( 'W3_Config' );
          }

        }

      }

      /**
       * Display if caching or not.
       */
      function extension_header() {
        $config   = w3_instance( 'W3_Config' );
        $settings = w3tc_get_extension_config( 'veneer' );
        $caching  = false;
        foreach( $settings as $setting => $value ) {
          if( strpos( $setting, 'reject' ) === false && $value == '1' ) {
            $caching = true;
            break;
          }
        }
        echo '<p>';
        printf( __( 'The Genesis Framework extension is currently %s ', 'w3-total-cache' ),
          ( $caching ? '<span class="w3tc-enabled">' . __( 'enabled', 'w3-total-cache' ) . '</span>' :
            '<span class="w3tc-disabled">' . __( 'disabled', 'w3-total-cache' ) . '</span>' ) );
        if( $caching )
          printf( __( 'and caching via <strong>%s</strong>', 'w3-total-cahe' ), $config->get_string( 'fragmentcache.engine' ) );
        echo '.</p>';
      }

      /**
       * Rmove Unnecessary Menus
       *
       */
      function admin_menu() {

        if( !current_user_can( 'manage_network' ) ) {
          return remove_menu_page( 'w3tc_dashboard' );
        }

        remove_submenu_page( 'w3tc_dashboard', 'w3tc_dashboard' );
        remove_submenu_page( 'w3tc_dashboard', 'w3tc_support' );
        remove_submenu_page( 'w3tc_dashboard', 'w3tc_faq' );
        remove_submenu_page( 'w3tc_dashboard', 'w3tc_install' );
        remove_submenu_page( 'w3tc_dashboard', 'w3tc_about' );

      }

      /**
       * Rmove Unnecessary Menus
       *
       */
      function network_admin_menu() {

        if( !current_user_can( 'manage_network' ) ) {
          return remove_menu_page( 'w3tc_dashboard' );
        }

        remove_submenu_page( 'w3tc_dashboard', 'w3tc_support' );
        remove_submenu_page( 'w3tc_dashboard', 'w3tc_dashboard' );
        remove_submenu_page( 'w3tc_dashboard', 'w3tc_about' );
        remove_submenu_page( 'w3tc_dashboard', 'w3tc_faq' );
        remove_submenu_page( 'w3tc_dashboard', 'w3tc_install' );

      }

      /**
       * Setups sections
       */
      function admin_init() {

        w3_require_once( W3TC_INC_FUNCTIONS_DIR . '/extensions.php' );

        // Register our settings field group
        w3tc_add_settings_section(
          'header', // Unique identifier for the settings section
          'Header', // Section title
          '__return_false', // Section callback (we don't want anything)
          'veneer' // extension id, used to uniquely identify the extension;
        );

        w3tc_add_settings_section(
          'content',
          'Content',
          '__return_false',
          'veneer'
        );

        w3tc_add_settings_section(
          'sidebar',
          'Sidebar',
          '__return_false',
          'veneer'
        );

        w3tc_add_settings_section(
          'footer',
          'Footer',
          '__return_false',
          'veneer'
        );

        w3tc_add_settings_section(
          'exclusions',
          'Disable fragment cache',
          '__return_false',
          'veneer'
        );

        $settings = $this->settings();

        foreach( $settings as $setting => $meta ) {
          /**
           * @var $label
           * @var $description
           * @var $section
           * @var $type
           */
          extract( $meta );
          w3tc_add_settings_field( $setting, $label,
            array( $this, 'print_setting' ), 'veneer', $section,
            array( 'label_for'   => $setting, 'type' => $type,
                   'description' => $description ) );
        }
      }

      /**
       *
       * @param $setting
       * @param $args
       */
      function print_setting( $setting, $args ) {
        // w3_require_once( W3TC_INC_FUNCTIONS_DIR . '/extensions.php' );
        // $saved_roles = w3tc_get_extension_config( 'veneer', $setting );
        // w3tc_get_name_and_id( 'veneer', $setting );
        echo 'wp-veneer settings';
      }

      /**
       * @param $extensions
       *
       * @param $config
       *
       * @return mixed
       */
      function extension( $extensions, $config ) {
        //$activation_enabled = $config->get_boolean( 'fragmentcache.enabled' );

        //$activation_enabled = $activation_enabled && defined( 'PARENT_THEME_NAME' ) && PARENT_THEME_NAME == 'Genesis' &&
        $activation_enabled = true;

        defined( 'PARENT_THEME_VERSION' ) && version_compare( PARENT_THEME_VERSION, '1.9.0' ) >= 0;
        $message = array();

        if( is_network_admin() ) {
          w3_require_once( W3TC_INC_FUNCTIONS_DIR . '/themes.php' );
          $themes = w3tc_get_themes();
          $exists = false;
          foreach( $themes as $theme ) {
            if( strtolower( $theme->Template ) == 'genesis' )
              $exists = true;
          }
          if( !$exists )
            $message[ ] = 'Veneer Framework';
        } elseif( !( defined( 'PARENT_THEME_NAME' ) && PARENT_THEME_NAME == 'Genesis' ) )
          $message[ ] = 'Genesis Framework version >= 1.9.0';
        if( !$config->get_boolean( 'fragmentcache.enabled' ) )
          $message[ ] = 'Fragment Cache (W3 Total Cache Pro)';

        $extensions[ 'veneer' ] = array(
          'name'          => 'Veneer Framework',
          'author'        => 'Usability Dynamics, Inc.',
          'description'   => 'Veneer Total Cache stuff..',
          'author uri'    => 'http://usabilitydynamics.com/',
          'extension uri' => 'http://usabilitydynamics.com/',
          'extension id'  => 'veneer',
          'version'       => '0.1',
          'enabled'       => $activation_enabled,
          'requirements'  => implode( ', ', $message ),
          'path'          => __FILE__
        );

        return $extensions;
      }

      /**
       * @return array
       */
      function settings() {
        return
          array(
            'wp_head'                                 =>
              array(
                'type'        => 'checkbox',
                'section'     => 'header',
                'label'       => __( 'Cache wp_head loop:', 'w3-total-cache' ),
                'description' => __( 'Cache wp_head. This includes the embedded CSS, JS etc.', 'w3-total-cache' )
              ),
            'genesis_header'                          =>
              array(
                'type'        => 'checkbox',
                'section'     => 'header',
                'label'       => __( 'Cache header:', 'w3-total-cache' ),
                'description' => __( 'Cache header loop. This is the area where the logo is located.', 'w3-total-cache' )
              ),
            'genesis_do_nav'                          =>
              array(
                'type'        => 'checkbox',
                'section'     => 'header',
                'label'       => __( 'Cache primary navigation:', 'w3-total-cache' ),
                'description' => __( 'Caches the navigation filter; per page.', 'w3-total-cache' )
              ),
            'genesis_do_subnav'                       =>
              array(
                'type'        => 'checkbox',
                'section'     => 'header',
                'label'       => __( 'Cache secondary navigation:', 'w3-total-cache' ),
                'description' => __( 'Caches secondary navigation filter; per page.', 'w3-total-cache' ),
              ),
            'loop_front_page'                         =>
              array(
                'type'        => 'checkbox',
                'section'     => 'content',
                'label'       => __( 'Cache front page post loop:', 'w3-total-cache' ),
                'description' => __( 'Caches the front page post loop, pagination is supported.', 'w3-total-cache' )
              ),
            'loop_single'                             =>
              array(
                'type'        => 'checkbox',
                'section'     => 'content',
                'label'       => __( 'Cache single post / page:', 'w3-total-cache' ),
                'description' => __( 'Caches the single post / page loop, pagination is supported.', 'w3-total-cache' )
              ),
            'loop_single_excluded'                    =>
              array(
                'type'        => 'textarea',
                'section'     => 'content',
                'label'       => __( 'Excluded single pages / posts:', 'w3-total-cache' ),
                'description' => __( 'List of pages / posts that should not have the single post / post loop cached. Specify one page / post per line.', 'w3-total-cache' )
              ),
            'loop_single_genesis_comments'            =>
              array(
                'type'        => 'checkbox',
                'section'     => 'content',
                'label'       => __( 'Cache comments:', 'w3-total-cache' ),
                'description' => __( 'Caches the comments loop, pagination is supported.', 'w3-total-cache' )
              ),
            'loop_single_genesis_pings'               =>
              array(
                'type'        => 'checkbox',
                'section'     => 'content',
                'label'       => __( 'Cache pings:', 'w3-total-cache' ),
                'description' => __( 'Caches the ping loop, pagination is supported. One per line.', 'w3-total-cache' )
              ),
            'sidebar'                                 =>
              array(
                'type'        => 'checkbox',
                'section'     => 'sidebar',
                'label'       => __( 'Cache sidebar:', 'w3-total-cache' ),
                'description' => __( 'Caches sidebar loop, the widget area.', 'w3-total-cache' )
              ),
            'sidebar_excluded'                        =>
              array(
                'type'        => 'textarea',
                'section'     => 'sidebar',
                'label'       => __( 'Exclude pages:', 'w3-total-cache' ),
                'description' => __( 'List of pages that should not have sidebar cached. Specify one page / post per line.', 'w3-total-cache' )
              ),
            'genesis_footer'                          =>
              array(
                'type'        => 'checkbox',
                'section'     => 'footer',
                'label'       => __( 'Cache footer:', 'w3-total-cache' ),
                'description' => __( 'Caches footer loop.', 'w3-total-cache' )
              ),
            'wp_footer'                               =>
              array(
                'type'        => 'checkbox',
                'section'     => 'footer',
                'label'       => __( 'Cache footer:', 'w3-total-cache' ),
                'description' => __( 'Caches wp_footer loop.', 'w3-total-cache' )
              ),
            'fragment_reject_logged_roles'            =>
              array( 'type'        => 'checkbox',
                     'section'     => 'exclusions',
                     'label'       => __( 'Disable fragment cache:', 'w3-total-cache' ),
                     'description' => 'Don\'t use fragment cache with the following hooks and for the specified user roles.'
              ),
            'fragment_reject_logged_roles_on_actions' =>
              array( 'type'        => 'custom',
                     'section'     => 'exclusions',
                     'label'       => __( 'Select hooks:', 'w3-total-cache' ),
                     'description' => __( 'Select hooks from the list that should not be cached if user belongs to any of the roles selected below.', 'w3-total-cache' )
              ),
            'fragment_reject_roles'                   =>
              array( 'type'        => 'custom',
                     'section'     => 'exclusions',
                     'label'       => __( 'Select roles:', 'w3-total-cache' ),
                     'description' => __( 'Select user roles that should not use the fragment cache.', 'w3-total-cache' )

              )
          );
      }
    }

  }

}

