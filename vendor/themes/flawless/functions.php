<?php
/**
 * Flawless - Premium WordPress Theme - functions and definitions.
 *
 * @version 0.60.0
 * @package Flawless - Premium WordPress Theme
 */


if( version_compare( phpversion(), 5.3 ) < 0 ) {
  switch_theme( WP_DEFAULT_THEME, WP_DEFAULT_THEME );
  wp_die( sprintf( __( 'Your version of PHP, %1s, is old, and this theme cannot support it, so it has been disabled. <a href="%2s">Back to Safety.</a>', HDDP ), phpversion(), admin_url() ) );
}


define( 'Flawless_Version', '0.60.0' );

include_once( untrailingslashit( TEMPLATEPATH ) . '/core-assets/class_ud.php' );

//** Ran before init so cannot be called by init function */
add_action( 'after_setup_theme',  array( 'flawless_theme', 'after_setup_theme' ));


/**
 * Main class for Flawless theme options
 *
 * @since Flawless 0.2.3
 */
class flawless_theme extends Flawless_F {

  /**
   * Setups up core theme functions
   *
   * Adds image header section and default headers
   *
   * @todo flawless::theme_settings_loaded filter should verify that there is a valid return - potanin@UD
   * @todo $flawless[ 'deregister_empty_widget_areas' ] is staticly set right now, need to add menu to configure. - potanin@UD
   * @action after_setup_theme ( 10 )
   * @since Flawless 0.2.3
   */
  static function after_setup_theme() {
    global $flawless, $wpdb;

    add_action( 'init', array( 'flawless_theme', 'init_upper' ), 0 );
    add_action( 'init', array( 'flawless_theme', 'init_lower' ), 500 );

    $flawless = get_option( 'flawless_settings' );

    //** In case serialize string was broken during export/import */
    if( !is_array( $flawless ) || empty( $flawless ) ) {

      $flawless = self::repair_serialized_array( $wpdb->get_var( "SELECT option_value FROM {$wpdb->options} WHERE option_name = 'flawless_settings' " ) );

      if( is_array( $flawless ) && !empty( $flawless ) ) {
        update_option( 'flawless_settings', $flawless );

      } elseif ( file_exists( STYLESHEETPATH . '/default-configuration.json' ) && $default_configuration = file_get_contents( STYLESHEETPATH . '/default-configuration.json' ) ) {
        update_option( 'flawless_settings', $flawless = json_decode( $default_configuration, true ) );
      }
    }

    $flawless = apply_filters( 'flawless::theme_settings_loaded', stripslashes_deep( $flawless ) );

    flawless_theme::console_log( 'P: Theme settings loaded.' );

    $flawless[ 'have_static_home' ] = ( get_option( 'show_on_front' ) == 'page' ? true : false );
    $flawless[ 'using_permalinks' ] = ( get_option( 'permalink_structure' ) != '' ? true : false );
    $flawless[ 'have_blog_home' ] = ( $flawless[ 'have_static_home' ] ? ( get_option( 'page_for_posts' ) ? true : false ) : false );
    $flawless[ 'protocol' ] = ( is_ssl() ? 'https://' : 'http://' );
    $flawless[ 'deregister_empty_widget_areas' ] = false;

    $flawless[ 'asset_directories' ] = apply_filters( 'flawless_asset_location', array(
      untrailingslashit( get_template_directory() ) => untrailingslashit( get_template_directory_uri() ),
      untrailingslashit( get_stylesheet_directory() ) => untrailingslashit( get_stylesheet_directory_uri() )
    ));

    $flawless[ 'default_header' ][ 'style' ] = array(
      'name' => __( 'Name', 'flawless_theme' ),
      'description' => __( 'Description', 'flawless_theme' ),
      'media' => __( 'Media', 'flawless_theme' ),
      'version' => __( 'Version', 'flawless_theme' )
    );

    $flawless[ 'default_header' ][ 'color_schemes' ] = array(
      'name' => __( 'Name','flawless_theme' ),
      'description' => __( 'Description','flawless_theme' ),
      'author' => __( 'Author','flawless_theme' ),
      'version' => __( 'Version','flawless_theme' ),
      'tags' => __( 'Tags','flawless_theme' ),
      'thumbnail' => __( 'Thumbnail','flawless_theme' )
    );

    $flawless[ 'default_header' ][ 'extra_resources' ] = array(
      'name' => __( 'Name','flawless_theme' ),
      'description' => __( 'Description','flawless_theme' ),
      'author' => __( 'Author','flawless_theme' ),
      'version' => __( 'Version','flawless_theme' ),
      'theme_feature' => __( 'Theme Feature','flawless_theme' )
    );

    //** Load theme's core assets */
    $flawless = flawless_theme::load_core_assets( $flawless );

    //** Load extra functionality */
    $flawless = flawless_theme::load_extra_functions( $flawless );

    //** Have to be run on after_setup_theme() level. */
    $flawless = flawless_theme::setup_theme_features( $flawless );

    //** Figure out which Widget Area Sections ( WAS ) are available for use in the theme */
    $flawless = flawless_theme::define_widget_area_sections( $flawless );

    do_action( 'flawless_theme_setup', $flawless );
    do_action( 'flawless::theme_setup::after', $flawless );

		wp_register_script('jquery-cycle', 'http://cdnjs.cloudflare.com/ajax/libs/jquery.cycle/3.03/jquery.cycle.all.min.js', array('jquery'), '1.0');

  }


  /**
   * Run on init hook, loads all other hooks and filters
   *
   * Ran as early as possible, before:
   * - widgets_init ( 1 )
   *
   * @WPA init ( 0 )
   * @since Flawless 0.2.3
   *
   */
  static function init_upper() {
    global $flawless;

    flawless_theme::console_log( 'P: Executed: flawless_theme::init();' );

    //** JS Assets - loaded early since they are not expected to be overwritten  */
    wp_register_script( 'bootstrap',  get_bloginfo( 'template_url' ) . '/js/bootstrap.min.js', array( 'jquery' ), '2.0.1', true );
    wp_register_script( 'jquery-lazyload',  get_bloginfo( 'template_url' ) . '/js/jquery.lazyload.min.js', array( 'jquery' ), '1.7.0', true );
    wp_register_script( 'jquery-touch-punch',  get_bloginfo( 'template_url' ) . '/js/jquery.ui.touch-punch.min.js', array( 'jquery' ), '0.2.2', true );
    wp_register_script( 'jquery-fancybox',  get_bloginfo( 'template_url' ) . '/js/jquery.fancybox.pack.js', array( 'jquery' ), '2.0.4', true );
    wp_register_script( 'jquery-placeholder',  get_bloginfo( 'template_url' ) . '/js/jquery.placeholder.min.js', array( 'jquery' ), '2.0.4',true );
    wp_register_script( 'google-pretify',  get_bloginfo( 'template_url' ) . '/js/google-prettify.js', array( 'jquery' ), Flawless_Version, true );

    //** Admin Only Actions */
    add_action( 'admin_menu', array( 'flawless_theme', 'admin_menu' ));
    add_action( 'admin_init', array( 'flawless_theme', 'admin_init' ));

    //** Front-end Actions */
    add_action( 'template_redirect', array( 'flawless_theme', 'template_redirect' ), 0 );

    //** Admin AJAX Handler */
    add_action( 'wp_ajax_flawless_action', create_function( '', ' die( json_encode( flawless_theme::ajax_actions() )); ' ));
    add_action( 'wp_ajax_nopriv_flawless_action', create_function( '', ' die( json_encode( flawless_theme::ajax_actions() )); ' ));

    //** Frontend AJAX Handler */
    add_action( 'wp_ajax_frontend_ajax_handler', create_function( '', ' die( json_encode( flawless_theme::frontend_ajax_handler() )); ' ));
    add_action( 'wp_ajax_nopriv_frontend_ajax_handler', create_function( '', ' die( json_encode( flawless_theme::frontend_ajax_handler() )); ' ));

    add_action( 'wp_ajax_flawless_signup_field_check', array( 'flawless_theme', 'flawless_signup_field_check' ),10,3 );

    //** Change login page logo URL */
    add_action( 'login_headerurl', create_function( '', ' return home_url(); ' ));
    add_action( 'login_headertitle', create_function( '', ' return get_bloginfo( "name" ); ' ));

    //** Add custom logo to login screen */
    add_action( 'login_head', array( 'flawless_theme','login_head' ));

    //** Register Navigation Menus */
    register_nav_menus(
      array(
        'header-menu' => __( 'Header Menu' , 'flawless' ),
        'header-sub-menu' => __( 'Header Sub-Menu' , 'flawless' ),
        'footer-menu' => __( 'Footer Menu' , 'flawless' ),
        'bottom_of_page_menu' => __( 'Bottom of Page Menu' , 'flawless' )
      )
    );

    $flawless = $flawless = $flawless = flawless_theme::setup_content_types( $flawless );

    //** Check if updates should be disabled */
    flawless_theme::disable_updates();

    /** Handles ( search ) request */
    add_filter( 'request', array( 'flawless_theme', 'request_filter' ), 0 );

    add_filter('widget_text', 'do_shortcode');

    //** Process Navbar ( has to be called early in case it needs to deregister the admin bar */
    if( !is_admin() ) {
      flawless_theme::prepare_navbars();
    }

    do_action( 'flawless::init_upper' );

  }


  /**
   * Run on init hook, intended to load functionality towards the end of init.  Scripts are loaded here so they can be overwritten by regular init.
   *
   * 500 priority is ran pretty much after everything, to include widgets_init, which is ran @level 1 of init
   *
   * @filter init ( 500 )
   * @since Flawless 0.2.3
   */
  static function init_lower() {
    global $flawless;

    //** Register scripts that may be used on front, back or via child theme */
    wp_register_script( 'jquery-cookie',  get_bloginfo( 'template_url' ) . '/js/jquery.smookie.js',  array( 'jquery' ), Flawless_Version, true );

    //** Bundled jQuery UI Effects.  Individual scripts can be loaded as well, as they are shipped with WordPress */
    wp_register_script( 'jquery-ui-effects',  get_bloginfo( 'template_url' ) . '/js/jquery.ui.effects.min.js',  array( 'jquery-ui-core' ), '1.8.17', true );

    wp_register_script( 'jquery-ud-dynamic_filter',  get_bloginfo( 'template_url' ) . '/js/jquery.ud.dynamic_filter-1.1.3.js',  array( 'jquery-ui-core' ), '1.1.3', true );

    //** UD jQuery Plugins - for now all unminified */
    wp_register_script( 'jquery-ud-dynamic_filter', get_bloginfo( 'template_url' ) . '/js/jquery.ud.dynamic_filter-1.1.3.js', array( 'jquery-ui-core' ), '1.1.3', true );
    wp_register_script( 'jquery-ud-form_helper', get_bloginfo( 'template_url' ) . '/js/jquery.ud.form_helper-1.0.8.js', array( 'jquery-ui-core' ), '1.0.8', true );
    wp_register_script( 'jquery-ud-smart_buttons', get_bloginfo( 'template_url' ) . '/js/jquery.ud.smart_buttons-0.6.js', array( 'jquery-ui-core' ), '0.6', true );
    wp_register_script( 'jquery-ud-social', get_bloginfo( 'template_url' ) . '/js/jquery.ud.social-0.3.js', array( 'jquery-ui-core' ), '0.3', true );
    wp_register_script( 'jquery-ud-execute_triggers', get_bloginfo( 'template_url' ) . '/js/jquery.ud.execute_triggers-0.2.0.js', array( 'jquery-ui-core' ), '0.2.0', true );

    //** Flawless Scripts */
    wp_register_script( 'flawless',  get_bloginfo( 'template_url' ) . '/js/flawless.js',  array( 'jquery' ), Flawless_Version, true );
    wp_register_script( 'flawless-frontend',  get_bloginfo( 'template_url' ) . '/js/flawless.frontend.js',  array( 'flawless', 'bootstrap', 'jquery-placeholder' ), Flawless_Version, true );
    wp_register_script( 'flawless-admin-global',  get_bloginfo( 'template_url' ) . '/js/flawless.admin.global.js', array( 'flawless' ), Flawless_Version, true );
    wp_register_script( 'flawless-admin',  get_bloginfo( 'template_url' ) . '/js/flawless.admin.js', array( 'flawless', 'flawless-admin-global' ), Flawless_Version, true );

    //** Styles */
    wp_register_style( 'twitter-bootstrap', get_bloginfo( 'template_url' ) . '/css/bootstrap.css', array(), '2.0.3', 'screen' );
    wp_register_style( 'jquery-fancybox',  get_bloginfo( 'template_url' ) . '/css/jquery.fancybox.css',  array(), '2.0.4', 'screen' );
    wp_register_style( 'google-pretify', get_bloginfo( 'template_url' ) . '/css/prettify.css' );
    wp_register_style( 'flawless-admin-styles', get_bloginfo( 'template_url' ) . '/css/flawless-admin.css', array(), Flawless_Version, 'screen' );

    //** Load assets automatically */
    add_action( 'admin_enqueue_scripts', array( 'flawless_theme', 'auto_load_assets' ), 50 );

    //** Enqueue front-end assets */
    add_action( 'wp_enqueue_scripts', array( 'flawless_theme', 'wp_enqueue_scripts' ), 100 );

    add_action( 'script_loader_src', array( 'flawless_theme', 'script_loader_src' ), 10, 2 );

    add_action( 'flawless::init_lower', array( 'flawless_theme', 'create_views' ), 10 );

    add_action( 'admin_footer', array( 'flawless_theme', 'log_stats' ), 10 );
    add_action( 'wp_footer', array( 'flawless_theme', 'log_stats' ), 10 );
    add_action( 'wp_footer', array( 'flawless_theme', 'wp_footer' ), 500 );

    //** Extra front-end assets ( such as Fancybox ) */
    add_action( 'flawless::extra_local_assets', array( 'flawless_theme', 'extra_local_assets' ), 5 );

    //** Load Special assets that are conditional based on current request and environment */
    add_action( 'flawless::conditional_assets', array( 'flawless_theme', 'conditional_assets' ), 5 );

    add_filter( 'wp_print_footer_scripts', array( 'flawless_theme', 'wp_print_footer_scripts' ), 100 );

    //** Add console log JavaScript in admin footer */
    add_filter( 'admin_print_footer_scripts', array( 'flawless_theme', 'render_console_log' ));

    add_filter( 'post_link', array( 'flawless_theme', 'filter_post_link' ), 10, 2 );
    add_filter( 'post_type_link', array( 'flawless_theme', 'filter_post_link' ), 10, 2 );

    if( current_theme_supports( 'extended-taxonomies' ) ) {
      add_action( 'wp_insert_post', array( 'flawless_theme', 'term_updated' ), 9, 2 );
      add_action( 'created_term', array( 'flawless_theme', 'term_updated' ), 9 );
      add_action( 'edit_term', array( 'flawless_theme', 'term_updated' ), 9 );
      add_action( 'delete_term', array( 'flawless_theme', 'delete_term' ), 9, 3 );
      add_action( 'load-edit-tags.php', array( 'flawless_theme', 'term_editor_loader' ) );
      add_action( 'load-post.php', array( 'flawless_theme', 'post_editor_loader' ) );
    }

    add_action( 'get_footer', function() {
      global $wp_query, $flawless;
      $wp_query->query_vars[ 'flawless' ] = $flawless;
    });

    /* Check if .htaccess file is not there, and re-creates it */
    if( $flawless[ 'using_permalinks' ] ) {
      self::save_mod_rewrite_rules();
    }

    do_action( 'flawless::init_lower' );

  }


  /**
   * Load extra front-end assets
   *
   * Assets are registered in flawless::init_upper();
   *
   * @since Flawless 0.3.1
   */
  function extra_local_assets() {
    global $flawless;

    //** Fancybox Scripts and Styles - enabled by default */
    if( $flawless[ 'disable_fancybox' ] != 'true' ) {
      wp_enqueue_style( 'jquery-fancybox' );
      wp_enqueue_script( 'jquery-fancybox' );
    }

    //** Twitter Bootstrap - enabled by default*/
    if( $flawless[ 'disable_bootstrap' ] != 'true' ) {
      wp_enqueue_script( 'bootstrap' );
    }

    //** UD Form Helper - enabled on default */
    if( $flawless[ 'disable_form_helper' ] != 'true' ) {
      wp_enqueue_script( 'jquery-ud-form_helper' );
    }

    /* Dynamic Filter - disabled on default */
    if( $flawless[ 'enable_dynamic_filter' ] == 'true' ) {
      wp_enqueue_script( 'jquery-ud-dynamic_filter' );
    }

    /* Google Code Pretification - disabled on default */
    if( $flawless[ 'enable_google_pretify' ] == 'true' ) {
      wp_enqueue_script( 'google-pretify' );
      wp_enqueue_style( 'google-pretify' );
    }

    /* Lazyload for Images - disabled on default */
    if( $flawless[ 'enable_lazyload' ] == 'true' ) {
      wp_enqueue_script( 'jquery-lazyload' );
    }

  }


  /**
   * Load Special assets that are conditional based on current request and environment
   *
   * Example conditional styles:
   *
   * - IE - /css/conditional-ie.css
   * - lte IE 8 - /css/conditional-lte-ie-8.css
   * - !IE - /css/conditional-!ie.css
   * - IE 7 /css/conditional-ie-7.css
   *
   * @since Flawless 0.3.2
   */
  function conditional_assets() {
    global $flawless, $wp_styles;

    //** Load IE HTML5 fix */
    if( isset( $is_IE ) && $is_IE ) {
      wp_enqueue_script( 'html5shim', 'http://html5shim.googlecode.com/svn/trunk/html5.js' );
    }

    //** Load scripts for handling comments */
    if ( is_singular() && get_option( 'thread_comments' ) ) {
      wp_enqueue_script( 'comment-reply' );
    }

    //** Check for and load conditional browser styles */
    foreach( ( array ) apply_filters( 'flawless::conditional_asset_types', array( 'IE' , 'lte IE 7', 'lte IE 8',  'IE 7', 'IE 8', 'IE 9', '!IE' ) ) as $type ) {

      //** Fix slug for URL - remove white space and lowercase */
      $url_slug = strtolower( str_replace( ' ', '-', $type ));

      foreach( (array) $flawless[ 'asset_directories' ] as $assets_path => $assets_url ) {

        if ( file_exists( $assets_path . "/css/conditional-{$url_slug}.css" ) ) {
          wp_register_style( 'conditional-'. $url_slug, $assets_url . "/css/conditional-{$url_slug}.css",   array() , Flawless_Version );
          $wp_styles->add_data( 'conditional-'. $url_slug, 'conditional', $type );
          wp_enqueue_style( 'conditional-'. $url_slug );

        }

      }

    }

  }


  /**
   * Disables update notifications if set.
   *
   * @action after_setup_theme( 10 )
   * @since Flawless 0.2.3
   */
  static function log_stats() {
    flawless_theme::console_log( 'P: End of request, total execution: ' . timer_stop() . ' seconds.' );
  }


  /**
   * {}
   *
   * @since Flawless 0.6.0
   */
  function wp_footer() {
    global $wpdb;
    echo '<span class="flawless_page_stats hidden">' . timer_stop() . ' seconds | ' . ( $wpdb->num_queries ? $wpdb->num_queries . ' queries | ' : '' ) . round( ( memory_get_peak_usage() / 1048576 ) ) . ' mb' . '</span>';
  }


  /**
   * Disables update notifications if set.
   *
   * @source Update Notifications Manager ( http://www.geekpress.fr/ )
   * @action after_setup_theme( 10 )
   * @since Flawless 0.2.3
   */
  static function disable_updates() {
    global $flawless;

    if( isset( $flawless[ 'disable_updates' ][ 'plugins' ] ) && $flawless[ 'disable_updates' ][ 'plugins' ] == 'true' ) {
      remove_action( 'load-update-core.php', 'wp_update_plugins' );
      add_filter( 'pre_site_transient_update_plugins', create_function( '', "return null;" ));
      wp_clear_scheduled_hook( 'wp_update_plugins' );
    }

    if( isset( $flawless[ 'disable_updates' ][ 'core' ] ) && $flawless[ 'disable_updates' ][ 'core' ] == 'true' ) {
      add_filter( 'pre_site_transient_update_core', create_function( '', "return null;" ));
      wp_clear_scheduled_hook( 'wp_version_check' );
    }

    if( isset( $flawless[ 'disable_updates' ][ 'theme' ] ) && $flawless[ 'disable_updates' ][ 'theme' ] == 'true' ) {
      remove_action( 'load-update-core.php', 'wp_update_themes' );
      add_filter( 'pre_site_transient_update_themes', create_function( '', "return null;" ));
      wp_clear_scheduled_hook( 'wp_update_themes' );
    }

  }


  /**
   * Defined which "Widget Area Sections" available for use in the Theme.
   *
   * These sections can have different Widget Areas associated with them, based on content type, home page, or blog page.
   * Definitions here are only configurable via API.
   *
   * @todo Add "Attention Grabber" via Feature
   *
   * @action after_setup_theme( 10 )
   * @since Flawless 0.2.3
   */
  static function define_widget_area_sections( $flawless ) {

    $flawless[ 'widget_area_sections' ][ 'left_sidebar' ] = array(
      'placement' => __( 'left', 'flawless' ),
      'class' => 'c6-12 sidebar-left span4 first',
      'label' => __( 'Left Sidebar', 'flawless' )
    );

    $flawless[ 'widget_area_sections' ][ 'right_sidebar' ] = array(
      'placement' => __( 'right', 'flawless' ),
      'class' => 'c6-56 sidebar-right span4 last',
      'label' => __( 'Right Sidebar', 'flawless' )
    );

    $flawless[ 'widget_area_sections' ] = apply_filters( 'flawless_widget_area_sections', $flawless[ 'widget_area_sections' ] );

    do_action( 'flawless_define_widget_area_sections' );

    return $flawless;

  }


  /**
   * Generates all views, registers Flawless widget areas, and unregisters any unsued widget areas.
   *
   * Unregistered widget areas are loaded into [widget_areas] array so they can be displayed on the Flawless settings page
   * for WAS association.
   *
   * Generates dynamic settings on every page load.
   *
   * @creates [widget_areas]
   * @creates [views]
   *
   * @action init ( 500 )
   * @action flawless::init_lower ( 10 )
   *
   * @todo: Add check for "custom" views, i.e. search result page. -potanin@UD
   * @todo: Add custom description generation based on views a widget area is used in. -potanin@UD
   *
   * @since Flawless 0.2.3
   */
  static function create_views( $current, $args = false ) {
    global $wp_registered_sidebars, $flawless, $post;

    $widget_areas = array();
    $views = array();

    //** Create a default Flawless sidebar */
    if( !isset( $flawless[ 'flawless_widget_areas' ] ) ) {

      $flawless[ 'flawless_widget_areas' ][ 'global_sidebar' ] = array (
        'label' => __( 'Global Sidebar', 'flawless' ),
        'class' => 'my_global_sidebar',
        'description' => __( 'Our default sidebar.', 'flawless' ),
        'id' => 'global_sidebar'
      );

    }

    //** Create custom widget areas */
    foreach( (array) $flawless[ 'flawless_widget_areas' ] as $sidebar_id => $wa_data ) {

      //** Register this widget area with some basic information */
      register_sidebar( array(
        'name' => isset( $wa_data[ 'label' ] ) ? $wa_data[ 'label' ] : '',
        'description' => isset( $wa_data[ 'description' ] ) ? $wa_data[ 'description' ] : '',
        'class' => isset( $wa_data[ 'class' ] ) ? $wa_data[ 'class' ] : '',
        'id' => $sidebar_id,
        'before_widget' => '<div id="%1$s"  class="flawless_widget theme_widget widget  %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h5 class="widgettitle widget-title">',
        'after_title' => '</h5>'
      ));

      $wp_registered_sidebars[$sidebar_id][ 'flawless_widget_area' ] = true;

    }

    //** Build views from all used widget areas, update widget area info based on location and usage */
    foreach( ( array ) $flawless[ 'post_types' ] as $post_type => $post_type_data ) {

      //** Load post type configuration ( not essential, just in case ) */
      $views[ 'post_types' ][$post_type][ 'settings' ] = $post_type_data;
      $views[ 'post_types' ][$post_type][ 'widget_areas' ] = array();

      flawless_theme::add_post_type_option( array(
        'post_type' => $post_type,
        'position' => 50,
        'meta_key' => 'hide_page_title',
        'label' => sprintf( __( 'Hide Page Title.' , 'flawless' ) )
      ));

      /** If breadcrumbs are not globally hidden, show an option to hide them */
      if( $flawless[ 'hide_breadcrumbs' ] != 'true' ) {
        flawless_theme::add_post_type_option( array(
          'post_type' => $post_type,
          'position' => 70,
          'meta_key' => 'hide_breadcrumbs',
          'label' => sprintf( __( 'Hide Breadcrumbs.' , 'flawless' ) )
        ));
      }

      if( post_type_supports( $post_type, 'author' ) && isset( $post_type_data[ 'disable_author' ] ) && $post_type_data[ 'disable_author' ] != 'true' ) {
        flawless_theme::add_post_type_option( array(
          'post_type' => $post_type,
          'position' => 100,
          'meta_key' => 'hide_post_author',
          'label' => sprintf( __( 'Hide Author.' , 'flawless' ) )
        ));
      }

      //** Load used widget areas into array */
      if ( isset( $post_type_data[ 'widget_areas' ] ) ) {
        foreach( ( array ) $post_type_data[ 'widget_areas' ] as $was_slug => $these_widget_areas ) {

          flawless_theme::add_post_type_option( array(
            'post_type' => $post_type,
            'position' => 200,
            'meta_key' => 'disable_' . $was_slug,
            'label' => sprintf( __( 'Disable %1s.' , 'flawless' ), $flawless[ 'widget_area_sections' ][$was_slug][ 'label' ] )
          ));

          $views[ 'post_types' ][$post_type][ 'widget_areas' ][$was_slug] = array_filter( ( array ) $these_widget_areas );

          $widget_areas[ 'used' ] = array_merge( isset( $widget_areas[ 'used' ] ) ? ( array ) $widget_areas[ 'used' ] : array(), ( array ) $these_widget_areas );

        }
      }

    }

    //** Build views from all used widget areas, update widget area info based on location and usage */
    foreach( ( array ) $flawless[ 'taxonomies' ] as $taxonomy => $taxonomy_data ) {

      //** Load post type configuration ( not essential, just in case ) */
      $views[ 'taxonomies' ][$taxonomy][ 'settings' ] = $taxonomy_data;
      $views[ 'taxonomies' ][$taxonomy][ 'widget_areas' ] = array();

      //** Load used widget areas into array */
      foreach( ( array ) $taxonomy_data[ 'widget_areas' ] as $was_slug => $these_widget_areas ) {

        $views[ 'taxonomies' ][$taxonomy][ 'widget_areas' ][$was_slug] = array_filter( ( array )  $these_widget_areas );

        $widget_areas[ 'used' ] = array_merge( ( array ) $widget_areas[ 'used' ], ( array ) $these_widget_areas );

      }

    }

    //** Create array of all sidebars */
    $widget_areas[ 'all' ] = $wp_registered_sidebars;

    ksort( $wp_registered_sidebars );

    ksort( $widget_areas[ 'all' ] );

    //** Unregister any WAs not placed into a WAS */
    foreach( ( array ) $wp_registered_sidebars as $sidebar_id => $sidebar_data ) {

      //** If there are no active sidebars, we leave our default global sidebar active */
      if( count( $widget_areas[ 'used' ] ) == 0 && $sidebar_id == 'global_sidebar' ) {
        continue;
      }

      if( !in_array( $sidebar_id, ( array ) $widget_areas[ 'used' ] ) ) {

        $widget_areas[ 'unused' ][$sidebar_id] = $wp_registered_sidebars[$sidebar_id];

        if( $flawless[ 'deregister_empty_widget_areas' ] ) {
          unset( $wp_registered_sidebars[$sidebar_id] );
        }

      }

    }


    //** Update descriptions of all used widget areas */
    foreach( ( array ) $widget_areas[ 'used' ] as $sidebar_id ) {

      //$wp_registered_sidebars[$sidebar_id][ 'description' ] = 'Modified! ' . $wp_registered_sidebars[$sidebar_id][ 'description' ];

    }


    //** Load settings into global variable */
    $flawless[ 'widget_areas' ] = $widget_areas;
    $flawless[ 'views' ] = $views;

    do_action( 'flawless::create_views' );

  }


  /**
   * Determines the currently requested page type.
   *
   * Returns information about curent view:
   * - type: The general type of request, typically corresponding with the type of template WP would load
   * - view: The specific view type, such as 'post_type', 'taxonomy', 'home', etc. that are used by Flawless to display custom elements such as sidebars
   * - group: The "group" this view belongs to, such as post types, taxonomies, etc.
   *
   * @todo Ensure $wp_query->query_vars work with other permalink structures. - potanin@UD
   * @since Flawless 0.2.3
   * @author potanin@UD
   */
  function this_request() {
    global $wp_query, $post;

    $t = array();

    switch ( true ) {

      /**
       * The home page, when a page is used.  In this instance, we treate it just like any other page.
       *
       */
      case is_page() && is_front_page():
        $t[ 'view' ] = 'single';
        $t[ 'group' ] = 'post_types';
        $t[ 'type' ] = 'page'; /* WP only allows pages to be set as home page */
        $t[ 'note' ] = 'Static Home Page';

        flawless_theme::console_log( 'P: Current View: Home page with static page.' );

      break;

      /**
       * The home page, when no page is set, so displayed as archive
       *
       */
      case !is_page() && is_front_page():
        $t[ 'view' ] = 'archive';
        $t[ 'type' ] = 'home';
        $t[ 'note' ] = 'Non-Static ( Archive ) Home Page';

        flawless_theme::console_log( 'P: Current View: Home page, default posts archive.' );

      break;

      /**
       * If this is the Blog Posts index page.
       *
       * By default posts page is never rendered NOT being attached to a page, therefore always 'single'
       */
      case $wp_query->is_posts_page:
        $t[ 'view' ] = 'single';
        $t[ 'group' ] = 'posts_page';
        $t[ 'type' ] = $wp_query->query_vars[ 'post_type' ];
        $t[ 'note' ] = 'Posts Page ( Archive )';

        flawless_theme::console_log( 'P: Current View: Blog Posts Index page.' );

      break;

      /**
       * If viewing a root of a post type, when the post type allows for a root archive
       * Note, default WP post types such as post and page do not have a post type archive
       *
       */
      case $wp_query->is_post_type_archive:
        $t[ 'view' ] = 'archive';
        $t[ 'group' ] = 'post_types';
        $t[ 'type' ] = $wp_query->query_vars[ 'post_type' ];


        flawless_theme::console_log( sprintf( 'P: Current View: Post Type Archive ( %1s ).', $wp_query->query_vars[ 'post_type' ] ));

      break;

      /**
       * If this is a single page, just as a post, page or custom post type single view
       *
       * Developer Notice: BuddyPress Pages are recognized as this ( page ).
       * Could create custom "BuddyPress" content type and modify wp_dropdown_pages filter to make them selectable.
       */
      case is_singular():
        $t[ 'view' ] = 'single';
        $t[ 'group' ] = 'post_types';
        $t[ 'type' ] = $post->post_type;

        flawless_theme::console_log( sprintf( 'P: Current View: Single post-type page ( %1s ).', $post->post_type ));

      break;

      /**
       * For search results.
       *
       */
      case is_search():
        $t[ 'view' ] = 'search';
        $t[ 'group' ] = 'post_types';
        $t[ 'type' ] = 'page';

        flawless_theme::console_log( 'P: Current View: Search Results page.' );

      break;

      /**
       * For taxonomy archives ( not taxonomy roots )
       * Template Load: ( category.php | tag.php | taxonomy-{$taxonomy} ) -> ( archive.php )
       * Although category and tag are taxonomies, WP has special templates for them.
       */
      case is_tax() || is_category() || is_tag():
        $t[ 'view' ] = 'archive';
        $t[ 'group' ] = 'taxonomies';
        $t[ 'type' ] = $wp_query->tax_query->queries[0][ 'taxonomy' ];

        flawless_theme::console_log( sprintf( 'P: Current View: Taxonomy archive ( %1s ) - ( non-root ). ', $wp_query->tax_query->queries[0][ 'taxonomy' ] ));

      break;

      /**
       * Taxonomy Root, by default results in 404.  WordPress does not support root pages for taxonomies, i.e. .com/category/ or .com/genre/
       * We check that the queried name is for a valid taxonomy, yet no taxonomy nor page is detece
       * Theoretically such a request should show all the objects associated with the taxonomy, perhaps uncategorized or ideally a Tagcloud
       */
      case taxonomy_exists( $wp_query->query_vars[ 'name' ] ) && !is_archive() && !is_singular():
        $t[ 'view' ] = 'archive';
        $t[ 'group' ] = 'taxonomies';
        $t[ 'type' ] = $wp_query->query_vars[ 'name' ];

        flawless_theme::console_log( 'P: Current View: Taxonomy root archive.' );

      break;


      default:
        $t[ 'view' ] = 'search';
        $t[ 'group' ] = 'post_types';
        $t[ 'type' ] = 'page';

        flawless_theme::console_log( 'P: Current View: Unknown - rendering same as Page.' );

      break;


    }

    $t = apply_filters( 'flawless_request_type', $t );

    return $t;

  }


  /**
   * Return array of sidebars that the current page needs to display
   *
   * Used to load CSS classes early on into the <body> element, as well as others
   *
   * Determines:
   * - widget_areas - removes any widget areas that do not have any widgets
   * - body_classes
   * - block_classes - the primary content container, class varies depending on number of sidebars
   *
   * @filter template_redirect ( 0 )
   * @since Flawless 0.2.3
   * @author potanin@UD
   */
  function set_current_view() {
    global $post, $wp_query, $flawless;

    //** Typically $flawless[ 'current_view' ] would be blank, but in case it was set by another function via API we do not override */
    $flawless[ 'current_view' ] = array_merge( ( array ) $flawless[ 'current_view' ], flawless_theme::this_request() );

    $flawless[ 'current_view' ][ 'body_classes' ] = ( array ) $flawless[ 'current_view' ][ 'body_classes' ];

    //** Load view data if it exists ( Widget areas, etc. )
    if( $flawless[ 'views' ][$flawless[ 'current_view' ][ 'group' ]] ) {
      $flawless[ 'current_view' ] = array_merge( ( array ) $flawless[ 'current_view' ] , ( array ) $flawless[ 'views' ][$flawless[ 'current_view' ][ 'group' ]][$flawless[ 'current_view' ][ 'type' ]] );
    }

    //** Get body classes from active widget sections */
    foreach( ( array ) $flawless[ 'current_view' ][ 'widget_areas' ]  as $was_slug => $wa_sidebars ) {

      //** If widget area sections and widget areas are loaded, make sure widget areas are active */
      foreach( (array) $wa_sidebars as $this_key => $sidebar_id ) {
       if( !flawless_theme::is_active_sidebar( $sidebar_id ) || apply_filters( 'flawless_exclude_sidebar', false, $sidebar_id ) ) {
        unset( $flawless[ 'current_view' ][ 'widget_areas' ][$was_slug][$this_key] );
       }
      }

      $flawless[ 'current_view' ][ 'widget_areas' ] = array_filter( (array) $flawless[ 'current_view' ][ 'widget_areas' ] );

      //** Check if we have any active sidebars left - if not, leave.  */
      if( empty( $flawless[ 'current_view' ][ 'widget_areas' ][$was_slug] ) ) {
        continue;
      }

      if( get_post_meta( $post->ID, 'disable_' . $was_slug, true ) ) {
        unset( $flawless[ 'current_view' ][ 'widget_areas' ][ $was_slug ] );
      }

    }

    if( count( $flawless[ 'current_view' ][ 'widget_areas' ]  ) === 0 ) {
      $flawless[ 'current_view' ][ 'block_classes' ] = array( 'c6-123456 span12' );
      $flawless[ 'current_view' ][ 'body_classes' ][] = 'no_wp_sidebar';
    }

    if( count( $flawless[ 'current_view' ][ 'widget_areas' ]  ) == 1 ) {
      $flawless[ 'current_view' ][ 'body_classes' ][] = 'have_sidebar';

      if( array_key_exists( 'right_sidebar', $flawless[ 'current_view' ][ 'widget_areas' ] ) ) {
        $flawless[ 'current_view' ][ 'block_classes' ][] = 'c6-1234 span8 first';
        $flawless[ 'current_view' ][ 'body_classes' ][] = 'have_right_sidebar';

      }

      if( array_key_exists( 'left_sidebar', $flawless[ 'current_view' ][ 'widget_areas' ] ) ) {
        $flawless[ 'current_view' ][ 'block_classes' ][] = 'c6-3456 span8 last';
        $flawless[ 'current_view' ][ 'body_classes' ][] = 'have_left_sidebar';
      }

    }

    if( count( $flawless[ 'current_view' ][ 'widget_areas' ]  ) == 2 ) {
      $flawless[ 'current_view' ][ 'block_classes' ][] = 'c6-45 span4';
    }

    //** If navbar is active */
    if( is_array( $flawless[ 'navbar' ][ 'html' ] ) ) {
      $flawless[ 'current_view' ][ 'body_classes' ][] = 'have-navbar';
    }

    if( hide_page_title() ) {
      $flawless[ 'current_view' ][ 'body_classes' ][] = 'no-title-wrapper';
    }

    if( $flawless[ 'developer_mode' ] == 'true' ) {
      $flawless[ 'current_view' ][ 'body_classes' ][] = 'developer_mode';
    }

    if( current_user_can( 'manage_options' ) ) {
      $flawless[ 'current_view' ][ 'body_classes' ][] = 'user_is_admin';
    }

    $flawless[ 'current_view' ][ 'body_classes' ] = array_unique( $flawless[ 'current_view' ][ 'body_classes' ] );
    $flawless[ 'current_view' ][ 'block_classes' ] = array_unique( $flawless[ 'current_view' ][ 'block_classes' ] );

    $flawless[ 'current_view' ] = apply_filters( 'set_current_view', $flawless[ 'current_view' ] );

    unset( $flawless[ 'current_view' ][ 'settings' ] );

    flawless_theme::console_log( 'P: Executed: flawless_theme::set_current_view();' );
    flawless_theme::console_log( $flawless[ 'current_view' ] );

  }


  /**
   * Return array of sidebars that the current page needs to display
   *
   * Used to load CSS classes early on into the <body> element, as well as others
   * Whether sidebars are active or not is already checked in set_current_view();
   *
   *
   * @since Flawless 0.2.3
   * @author potanin@UD
   */
  function get_current_sidebars( $widget_area_type = false ) {
    global $post, $flawless;

    if( !$widget_area_type ) {
      return array();
    }

    foreach( ( array ) $flawless[ 'current_view' ][ 'widget_areas' ][$widget_area_type] as $sidebar_id ) {

      $response[] = array(
        'sidebar_id' => $sidebar_id,
        'class' => $flawless[ 'widget_area_sections' ][$widget_area_type][ 'class' ]
      );

    }

    flawless_theme::console_log( 'P: Executed: flawless_theme::get_current_sidebars();' );
    flawless_theme::console_log( $response );

    return $response;

  }


  /**
   * Get Widget Titles and Instances in an area
   *
   * Currently not used, Denali 3.0 port.
   *
   * @since Flawless 0.2.3
   */
  function widget_area_tabs( $widget_area = false ) {
    global $wp_registered_widgets;

    //** Check if widget are is active before doing anything else */
    if( !flawless_theme::is_active_sidebar( $widget_area ) ) {
      return false;
    }

    $sidebars_widgets = wp_get_sidebars_widgets();

    if( empty( $sidebars_widgets ) ) {
      return false;
    }

    $load_options = array();

    if( empty( $sidebars_widgets[$widget_area] ) || !is_array( $sidebars_widgets[$widget_area] ) ) {
      return false;
    }

    foreach( (array) $sidebars_widgets[$widget_area] as $count=> $id ) {

      if ( !isset( $wp_registered_widgets[$id] ) ) {
        continue;
      }

      $callback = $wp_registered_widgets[$id][ 'callback' ];
      $number = $wp_registered_widgets[$id][ 'params' ][0][ 'number' ];
      $option_name = $callback[0]->option_name;
      $type =  $wp_registered_widgets[$id][ 'name' ];
      $params = array( '', ( array ) $wp_registered_widgets[$id][ 'params' ] );
      $name = trim( $wp_registered_widgets[$id][ 'name' ] );


      if( !isset( $load_options[$option_name] ) ) {
        $all_options = get_option( $option_name );
        $load_options[$option_name] = $all_options;
      }

      $these_settings = $load_options[$option_name][$number];

      $title = trim( $these_settings[ 'title' ] );

      $return[$count][ 'title' ] = ( !empty( $title ) ? $title : $name );
      $return[$count][ 'id' ] = $wp_registered_widgets[$id][ 'id' ];

      if ( is_callable( $callback ) ) {
        $return[$count][ 'callable' ] = true;
      }

    }

    if( is_array( $return ) ) {
      return $return;
    }

    return false;

  }


  /**
   * Setup theme features using the WordPress API as much as possible.
   *
   * This function must run after all the post types are created and initialized to have effect.
   *
   * This function may be called more than once at different action levels ( ALs ) since taxonomy and post types may be added by plugins,
   * yet we want the admin to have full control over all the post types and taxonomies in one UI.
   *
   * @todo Need to update all labels for taxonomoies. - potanin@UD
   * @action init (0)
   * @since Flawless 0.2.3
   *
   */
  static function setup_content_types( $flawless = false ) {
    global $wp_post_types, $wp_taxonomies;

    if( !$flawless ) {
      global $flawless;
    }

    flawless_theme::console_log( 'P: Executed: flawless_theme::setup_content_types();' );

    do_action( 'flawless::content_types', $flawless );

    //** Create any new post types that are in our settings array, but not in the global $wp_post_types variable*/
    foreach( ( array ) $flawless[ 'post_types' ] as $type => $data ) {

      if( $data[ 'flawless_post_type' ] != 'true' ) {
        continue;
      }

      flawless_theme::console_log( sprintf( __( 'P: Adding custom post type: %1s', 'flawless' ), $type ));

      $post_type_settings = array(
        'label' => $data[ 'name' ],
        'menu_position'  => ( $data[ 'hierarchical' ] == "true" ? 21 : 6 ),
        'public'  => true,
        'exclude_from_search' => $data[ 'exclude_from_search' ],
        'hierarchical' => $data[ 'hierarchical' ],
        'has_archive' => is_numeric( $data[ 'root_page' ] ) && $data[ 'root_page' ] > 0 ? false : true,
        'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments', 'revisions', 'post-formats', 'author' ),
      );

      if( !empty( $data[ 'rewrite_slug' ] ) ) {
        $post_type_settings[ 'rewrite' ] = array(
          'slug' => $data[ 'rewrite_slug' ],
          'with_front' => true
        );
      }

      //** 'has_archive' allows post_type entries list korotkov@ud */
      register_post_type( $type, $post_type_settings );

      do_action( 'flawless_content_type_added', array( 'type' => $type, 'data' => $data ));

    }

    //** Create any Flawless taxonomoies an create them, or update existing ones with custom settings  */
    foreach( ( array ) $flawless[ 'taxonomies' ] as $type => $data ) {
      if( $data[ 'flawless_taxonomy' ] == 'true' ) {

        flawless_theme::console_log( sprintf( __( 'P: Adding custom flawless_taxonomy: %1s', 'flawless' ), $type ));

        $flawless[ 'taxonomies' ][ $type ][ 'rewrite_tag' ] =  '%' . $type . '%';

        if( !taxonomy_exists( $type ) ) {

          $taxonomy_settings = array(
            'label' => $data[ 'label' ],
            'exclude_from_search' => $data[ 'exclude_from_search' ],
            'hierarchical' => $data[ 'hierarchical' ]
          );

          $taxonomy_settings[ 'rewrite' ] = array(
            'slug' => $data[ 'rewrite_slug' ] ? $data[ 'rewrite_slug' ] : $type,
            'with_front' => true
          );

          register_taxonomy( $type, '', $taxonomy_settings );
        }

        add_rewrite_tag( $flawless[ 'taxonomies' ][ 'rewrite_tag' ],  '([^/]+)' );

        do_action( 'flawless_content_type_added', array( 'type' => $type, 'data' => $data ));

      }

      //** Check to see if a taxonomy has disappeared ( i.e. plugin deactivated that was adding it ) */
      if( !in_array( $type, array_keys( $wp_taxonomies ) ) ) {
        unset( $flawless[ 'taxonomies' ][ $type ] );
      }

      if( current_theme_supports( 'extended-taxonomies' ) && !post_type_exists( $type ) ) {

        register_post_type( '_tp_' . $type, array(
          'label' => $data[ 'label' ],
          'public' => false,
          'rewrite' => false,
          'labels' => array(
            'name' => $data[ 'label' ],
            'edit_item' => 'Edit Term: ' . $data[ 'label' ]
          ),
          'supports' => array( 'title', 'editor' )
        ));

        if( !empty( $data[ 'allow_term_thumbnail' ] ) ) {
          add_post_type_support( '_tp_' . $type, 'thumbnail' );
          add_filter( 'manage_edit-' . $type . '_columns', create_function( '$c', ' return flawless_theme::array_insert_after( $c, "cb", array( "term_thumbnail" => "" ) ); ' ) );
          add_filter( 'manage_' . $type . '_custom_column', function( $null, $column, $term_id ) {
            if( $column == 'term_thumbnail' ) { echo wp_get_attachment_image( get_post_thumbnail_id( get_post_for_extended_term( $term_id )->ID ), array( 75, 75 ) ); };
          }, 10, 3);
        }

      }

      //** Save our custom settings to global taxononmy object */
      $wp_taxonomies[ $type ]->hierarchical = isset( $data[ 'hierarchical' ] ) && $data[ 'hierarchical' ] == 'true' ? true : false;
      $wp_taxonomies[ $type ]->exclude_from_search = isset( $data[ 'exclude_from_search' ] ) && $data[ 'exclude_from_search' ] == 'true' ? true : false;
      //$wp_taxonomies[ $type ]->show_tagcloud = $data[ 'show_tagcloud' ] == 'true' ? true : false;

      $wp_taxonomies[ $type ]->label= $data[ 'label' ] ? $data[ 'label' ] : $wp_taxonomies[ $type ]->label;

      //** Automatically try to get singular form if not set ( experimental ) */
      $wp_taxonomies[ $type ]->labels->singular_name = isset( $data[ 'singular_label' ] ) && $data[ 'singular_label' ] ? $data[ 'singular_label' ] : self::depluralize( $data[ 'label' ] );
      $wp_taxonomies[ $type ]->labels->name = $data[ 'label' ] ? $data[ 'label' ] : $wp_taxonomies[ $type ]->label;

      //** Set singular labels */
      $wp_taxonomies[ $type ]->labels->add_new_item = sprintf( __( 'New %1s', 'flawless' ),  $wp_taxonomies[ $type ]->labels->singular_name );
      $wp_taxonomies[ $type ]->labels->new_item = sprintf( __( 'New %1s', 'flawless' ),  $wp_taxonomies[ $type ]->labels->singular_name );
      $wp_taxonomies[ $type ]->labels->edit_item = sprintf( __( 'Edit %1s', 'flawless' ),  $wp_taxonomies[ $type ]->labels->singular_name );
      $wp_taxonomies[ $type ]->labels->update_item = sprintf( __( 'Update %1s', 'flawless' ),  $wp_taxonomies[ $type ]->labels->singular_name );
      $wp_taxonomies[ $type ]->labels->view_item = sprintf( __( 'No %1s found.', 'flawless' ),  $wp_taxonomies[ $type ]->labels->singular_name );
      $wp_taxonomies[ $type ]->labels->new_item_name = sprintf( __( 'New %1s Name.', 'flawless' ),  $wp_taxonomies[ $type ]->labels->singular_name );
      $wp_taxonomies[ $type ]->labels->not_found = sprintf( __( 'Add New %1s', 'flawless' ),  $wp_taxonomies[ $type ]->labels->singular_name );
      $wp_taxonomies[ $type ]->labels->not_found_in_trash = sprintf( __( 'Add New %1s', 'flawless' ),  $wp_taxonomies[ $type ]->labels->singular_name );

      //** Plural Labels */
      $wp_taxonomies[ $type ]->labels->search_items = sprintf( __( 'Search %1s', 'flawless' ),  $wp_taxonomies[ $type ]->labels->name );
      $wp_taxonomies[ $type ]->labels->not_found_in_trash = sprintf( __( 'No %1s found in trash.', 'flawless' ),  $wp_taxonomies[ $type ]->labels->name );
      $wp_taxonomies[ $type ]->labels->popular_items = sprintf( __( 'Popular %1s', 'flawless' ),  $wp_taxonomies[ $type ]->labels->name );
      $wp_taxonomies[ $type ]->labels->add_or_remove_items = sprintf( __( 'Add ore remove %1s', 'flawless' ),  $wp_taxonomies[ $type ]->labels->name );
      $wp_taxonomies[ $type ]->labels->choose_from_most_used = sprintf( __( 'Choose from most used %1s', 'flawless' ),  $wp_taxonomies[ $type ]->labels->name );
      $wp_taxonomies[ $type ]->labels->all_items = sprintf( __( 'All %1s', 'flawless' ),  $wp_taxonomies[ $type ]->labels->name );
      $wp_taxonomies[ $type ]->labels->menu_name = $wp_taxonomies[ $type ]->labels->name;

    }

    //** Cycle through all existing taxonomies, and load their settings into FS settings */
    foreach( (array) $wp_taxonomies as $type => $data ) {

      //** We do not do anything with non displayed taxononomies */
      if( !$data->show_ui ) {
        continue;
      }

      if( $flawless[ 'taxonomies' ][ $type ][ 'flawless_taxonomy' ] == 'true' ) {
        $flawless[ 'taxonomies' ][ $type ][ 'rewrite_slug' ] = $data->rewrite[ 'slug' ];
      }

      $flawless[ 'taxonomies' ][ $type ][ 'label' ] = $wp_taxonomies[ $type ]->labels->name;
      $flawless[ 'taxonomies' ][ $type ][ 'label' ] = $wp_taxonomies[ $type ]->labels->name;
      $flawless[ 'taxonomies' ][ $type ][ 'hierarchical' ] = $wp_taxonomies[ $type ]->hierarchical ? 'true' : 'false';
      $flawless[ 'taxonomies' ][ $type ][ 'exclude_from_search' ] = $wp_taxonomies[ $type ]->exclude_from_search ? 'true' : 'false';
      //$flawless[ 'taxonomies' ][ $type ][ 'show_tagcloud' ] = $wp_taxonomies[ $type ]->show_tagcloud ? 'true' : 'false';

      //** If Term Meta is supported, add callback function to render any UI we want to show on the taxonomy pages */
      if( current_theme_supports( 'term-meta' ) || current_theme_supports( 'extended-taxonomies' ) ) {
        add_action( $type . '_edit_form_fields', array( 'flawless_theme_ui', 'taxonomy_edit_form_fields' ), 5, 2 );
        add_action( $type . '_pre_add_form', array( 'flawless_theme_ui', 'taxonomy_pre_add_form' ), 5 );
      }

    }

    //** Loop through post types and update the $flawless array */
    foreach( (array) $wp_post_types as $type => $data ) {

      //** We don't do anything with any post types that are not displayed */
      if( !$data->public ) {
        continue;
      }

      $defaults = get_object_taxonomies( $type );

      //** Configure special settings if they are set, or use default settings */
      $flawless[ 'post_types' ][ $type ][ 'name' ] = ( isset( $flawless[ 'post_types' ][ $type ][ 'name' ] ) ? $flawless[ 'post_types' ][ $type ][ 'name' ] : $data->labels->name );
      $flawless[ 'post_types' ][ $type ][ 'hierarchical' ] = ( isset( $flawless[ 'post_types' ][ $type ][ 'hierarchical' ] ) ? $flawless[ 'post_types' ][ $type ][ 'hierarchical' ] : ( $data->hierarchical ? 'true' : false ));

      //** Cycle through all available taxonomies and add them back to post type. */
      foreach( ( array ) $flawless[ 'taxonomies' ] as $tax => $tax_data ) {

        $flawless[ 'post_types' ][ $type ][ 'taxonomies' ][$tax] = ( isset( $flawless[ 'post_types' ][ $type ][ 'taxonomies' ][$tax] ) ? $flawless[ 'post_types' ][ $type ][ 'taxonomies' ][$tax] : ( in_array( $tax, $defaults ) ?  'enabled' : '' ));

        if( $flawless[ 'post_types' ][ $type ][ 'taxonomies' ][$tax] == 'enabled' ) {
          register_taxonomy_for_object_type( $tax, $type );
        }

        //** Remove blank values added as placeholders when FL taxonomies are initially registered */
        $wp_taxonomies[ $tax ]->object_type = array_filter( $wp_taxonomies[ $tax ]->object_type );
      }

      $flawless[ 'post_types' ][ $type ][ 'rewrite_slug' ] = $data->rewrite[ 'slug' ];

      @ksort( $flawless[ 'post_types' ][ $type ][ 'taxonomies' ] );

      if( $flawless[ 'post_types' ][ $type ][ 'hierarchical' ] == 'true' ) {
        $wp_post_types[ $type ]->hierarchical = true;
        add_post_type_support( $type, 'page-attributes' );
      }

      if( isset( $flawless[ 'post_types' ][ $type ][ 'disable_comments' ] ) && $flawless[ 'post_types' ][ $type ][ 'disable_comments' ] == 'true' ) {
        remove_post_type_support( $type, 'comments' );
      }

      if( isset( $flawless[ 'post_types' ][ $type ][ 'custom_fields' ] ) && $flawless[ 'post_types' ][ $type ][ 'custom_fields' ] != 'true' ) {
        remove_post_type_support( $type, 'custom-fields' );
      }

      if( isset( $flawless[ 'post_types' ][ $type ][ 'disable_author' ] ) && $flawless[ 'post_types' ][ $type ][ 'disable_author' ] == 'true' ) {
        remove_post_type_support( $type, 'author' );
      }

      if( isset( $flawless[ 'post_types' ][ $type ][ 'exclude_from_search' ] ) && $flawless[ 'post_types' ][ $type ][ 'exclude_from_search' ] == 'true' ) {
        $wp_post_types[ $type ]->exclude_from_search = true;
      }

      //** Rename post types. Do special stuff for post and page since they are built in, and Menu is hardcoded for some reason. */
      if( $flawless[ 'post_types' ][ $type ][ 'name' ] != $data->labels->name || $flawless[ 'post_types' ][ $type ][ 'flawless_post_type' ] == 'true' ) {

        if( $flawless[ 'post_types' ][ $type ][ 'name' ] != $data->labels->name ) {
          flawless_theme::console_log( sprintf( __( 'P: Changing labels for post type: %1s, from %2s to %3s', 'flawless' ), $type, $data->labels->name, $flawless[ 'post_types' ][ $type ][ 'name' ] ));
        }

        $original_labels = ( !empty( $wp_post_types[ $type ]->labels ) ? ( array ) $wp_post_types[ $type ]->labels : array() );

        //** Update Post Type Labels */
        if( empty( $flawless[ 'post_types' ][ $type ][ 'singular_name' ] ) ) {
          $flawless[ 'post_types' ][ $type ][ 'singular_name' ] = self::depluralize( $flawless[ 'post_types' ][ $type ][ 'name' ] );
        }

        $wp_post_types[ $type ]->labels = ( object ) array_merge( $original_labels,  array(
          'name' => $flawless[ 'post_types' ][ $type ][ 'name' ], /* plural */
          'singular_name' => ucfirst( $flawless[ 'post_types' ][ $type ][ 'singular_name' ] ),
          'add_new_item' => sprintf( __( 'Add New %1s', 'flawless' ),  $flawless[ 'post_types' ][ $type ][ 'singular_name' ] ),
          'new_item' => sprintf( __( 'New %1s', 'flawless' ),  $flawless[ 'post_types' ][ $type ][ 'singular_name' ] ),
          'edit_item' => sprintf( __( 'Edit %1s', 'flawless' ),  ucfirst( $flawless[ 'post_types' ][ $type ][ 'singular_name' ] ) ),
          'search_items' => sprintf( __( 'Search %1s', 'flawless' ),  $flawless[ 'post_types' ][ $type ][ 'name' ] ),
          'view_item' => sprintf( __( 'View %1s', 'flawless' ),  $flawless[ 'post_types' ][ $type ][ 'singular_name' ] ),
          'search_items' => sprintf( __( 'Search %1s', 'flawless' ),  $flawless[ 'post_types' ][ $type ][ 'name' ] ),
          'not_found' => sprintf( __( 'No %1s found.', 'flawless' ),  strtolower( $flawless[ 'post_types' ][ $type ][ 'singular_name' ] ) ),
          'not_found_in_trash' => sprintf( __( 'No %1s found in trash.', 'flawless' ),  strtolower( $flawless[ 'post_types' ][ $type ][ 'name' ] ) )
        ));

        switch ( $type ) {
          case 'post':
            add_action( 'admin_menu', create_function( '', ' global $menu, $submenu, $flawless; $menu[5][0] = $flawless["post_types"]["post"]["name"]; $submenu["edit.php"][5][0] = "All " . $flawless["post_types"]["post"]["name"];  ' ));
          break;
          case 'page':
            add_action( 'admin_menu', create_function( '', ' global $menu, $submenu, $flawless; $menu[20][0] = $flawless["post_types"]["page"]["name"]; $submenu["edit.php?post_type=page"][5][0] = "All " . $flawless["post_types"]["page"]["name"];  ' ));
          break;
        }

      }

      //** If this post type can have an archive, we determine the URL */
      //** @todo This nees work, we are guessing that the permalink will be top level, need to check other factors */
      if( $wp_post_types[ $type ]->has_archive ) {

        add_filter( 'nav_menu_items_' . $type, array( 'flawless_theme', 'add_archive_checkbox' ), null, 3 );

        $flawless[ 'post_types' ][ $type ][ 'archive_url' ] = get_bloginfo( 'url' ) . '/' . $type . '/';

      }

      //** Disable post type, and do work-around for built-in types since they are hardcoded into menu.*/
      if( isset( $flawless[ 'post_types' ][ $type ][ 'disabled' ] ) && $flawless[ 'post_types' ][ $type ][ 'disabled' ] == 'true' ) {
        switch ( $type ) {
          case 'post':
            add_action( 'admin_menu', create_function( '', 'global $menu; unset( $menu[5] );' ));
          break;
          case 'page':
            add_action( 'admin_menu', create_function( '', 'global $menu; unset( $menu[20] );' ));
          break;
        }
        unset( $wp_post_types[ $type ] );
      }

    }

    return $flawless;

  }


  /**
   * Setup theme features using the WordPress API as much as possible.
   *
   * @since Flawless 0.2.3
   *
   */
  static function setup_theme_features( $flawless ) {
    global $wpdb;

    //** Load styles to be used by editor */
    add_editor_style( array(
      'css/bootstrap.css',
      'css/flawless-content.css',
      'css/content.css',
      'css/editor-style.css'
    ));

    define( 'HEADER_TEXTCOLOR', '000' );
    define( 'HEADER_IMAGE', apply_filters( 'flawless::header_image', '' ) );
    define( 'HEADER_IMAGE_WIDTH', apply_filters( 'flawless::header_image_width',  isset($flawless[ 'header_image_width' ]) ? $flawless[ 'header_image_width' ]  : 1090 ));
    define( 'HEADER_IMAGE_HEIGHT', apply_filters( 'flawless::header_image_height',  isset($flawless[ 'header_image_height' ]) ? $flawless[ 'header_image_height' ] : 314 ));

    add_custom_background( array( 'flawless_theme', 'custom_background' ),'',array( 'flawless_theme', 'admin_image_div_callback' ));
    add_custom_image_header( false, array( 'flawless_theme','flawless_admin_header_style' ), array( 'flawless_theme','flawless_admin_header_image' ));

    //** All Available Theme Features */
    $flawless[ 'available_theme_features' ][ 'custom-skins' ] = true;
    $flawless[ 'available_theme_features' ][ 'post-thumbnails' ] = true;
    $flawless[ 'available_theme_features' ][ 'custom-background' ] = true;
    $flawless[ 'available_theme_features' ][ 'custom-header' ] = true;
    $flawless[ 'available_theme_features' ][ 'automatic-feed-links' ] = true;
    $flawless[ 'available_theme_features' ][ 'header-dropdowns' ] = true;
    $flawless[ 'available_theme_features' ][ 'header-logo' ] = true;
    $flawless[ 'available_theme_features' ][ 'header-navbar' ] = true;
    $flawless[ 'available_theme_features' ][ 'header-search' ] = true;
    $flawless[ 'available_theme_features' ][ 'header-text' ] = true;
    $flawless[ 'available_theme_features' ][ 'mobile-navbar' ] = true;
    $flawless[ 'available_theme_features' ][ 'footer-copyright' ] = true;
    $flawless[ 'available_theme_features' ][ 'extended-taxonomies' ] = true;
    $flawless[ 'available_theme_features' ][ 'term-meta' ] = true;

    $flawless[ 'available_theme_features' ] = apply_filters( 'flawless::available_theme_features', $flawless[ 'available_theme_features' ] );

    //** Load all Available Theme featurse */
    foreach( (array) $flawless[ 'available_theme_features' ] as $feature => $always_true ) {
      add_theme_support( $feature );
    }

    //** Remove any explicitly disabled Features */
    if ( !empty( $flawless[ 'disabled_theme_features' ] ) ) {
      foreach( (array) $flawless[ 'disabled_theme_features' ] as $feature => $not_false ) {

        if( $not_false !== 'false' ) {
          return;
        }

        remove_theme_support( $feature );

        if ( in_array( $feature, array( 'custom-background', 'custom-header', 'editor-style', 'widgets', 'menus' ) ) ) {

          switch ($feature ) {

            case 'custom-background':
              remove_custom_background();
            break;

            case 'custom-header':
              remove_custom_image_header();
            break;

          }

        }

      }
    }


    if( current_theme_supports( 'term-meta' ) ) {
      $wpdb->taxonomymeta = $wpdb->prefix . 'taxonomymeta';
    }

    do_action( 'flawless::setup_theme_features::after' );

    return $flawless;

  }


  /**
   * Styles the header image displayed on the Appearance > Header admin panel.
   *
   * Referenced via add_custom_image_header() in flawless_setup().
   *
   */
  function flawless_admin_header_style() {
  ?>
    <style type="text/css">

    <?php if ( get_header_textcolor() != HEADER_TEXTCOLOR ) : ?>
      #site-title a,
      #site-description {
        color: #<?php echo get_header_textcolor(); ?>;
      }
    <?php endif; ?>

    </style>
  <?php
  }


  /**
   * Custom header image markup displayed on the Appearance > Header admin panel.
   *
   * Referenced via add_custom_image_header() in flawless_setup().
   *
   */
  function flawless_admin_header_image() { ?>
    <div id="headimg">
      <?php
      if ( 'blank' == get_theme_mod( 'header_textcolor', HEADER_TEXTCOLOR ) || '' == get_theme_mod( 'header_textcolor', HEADER_TEXTCOLOR ) )
        $style = ' style="display:none;"';
      else
        $style = ' style="color:#' . get_theme_mod( 'header_textcolor', HEADER_TEXTCOLOR ) . ';"';
      ?>
      <h1><a id="name"<?php echo $style; ?> onclick="return false;" href="<?php echo esc_url( home_url( '/' )); ?>"><?php bloginfo( 'name' ); ?></a></h1>
      <div id="desc"<?php echo $style; ?>><?php bloginfo( 'description' ); ?></div>
      <?php $header_image = get_header_image();
      if ( ! empty( $header_image ) ) : ?>
        <img src="<?php echo esc_url( $header_image ); ?>" alt="" />
      <?php endif; ?>
    </div>
  <?php
  }


  /**
   * Loads core assets of the theme
   *
   * Loaded after theme_features have been configured.
   *
   * @since Flawless 0.2.3
   */
  static function load_core_assets() {
    global $flawless;

    //** Load logo if set */
    if( is_numeric( $flawless[ 'flawless_logo' ][ 'post_id' ] ) && $image_attributes = wp_get_attachment_image_src( $flawless[ 'flawless_logo' ][ 'post_id' ], 'full' ) ) {
      $flawless[ 'flawless_logo' ][ 'url' ] = $image_attributes[0];
      $flawless[ 'flawless_logo' ][ 'width' ] = $image_attributes[1];
      $flawless[ 'flawless_logo' ][ 'height' ] = $image_attributes[2];
    }

    foreach( (array) $flawless[ 'asset_directories' ] as $path => $url ) {

      $path = $path . '/core-assets';

      if( !is_dir( $path ) || !$resource = opendir( $path  ) ) {
        continue;
      }

      while ( false !== ( $file_name = readdir( $resource ) ) ) {

        if( substr( strrchr( $file_name, '.' ), 1 ) != 'php' ) {
          continue;
        }

        $file_data = @get_file_data( $path . '/' . $file_name, $flawless[ 'default_header' ][ 'extra_resources' ], 'flawless_extra_assets' );

        $file_data[ 'location' ] = 'theme_functions';
        $file_data[ 'file_name' ] = $file_name;

        $flawless[ 'core_assets' ][ $file_data[ 'name' ] ] = $file_data;
        include_once( $path . '/' . $file_name );

      }

    }

    return $flawless;

  }


  /**
   * Loads extra function files.
   *
   * @since Flawless 0.2.3
   */
  static function load_extra_functions( $flawless ) {

    $required_file_data = apply_filters( 'flawless::required_extra_resource_file_data', array( 'name', 'version' ));

    foreach( (array) $flawless[ 'asset_directories' ] as $path => $url ) {

      $path = $path . '/functions';

      if( !is_dir( $path ) ) {
        return $flawless;
      }

      if( !$functions_resource = opendir( $path ) ) {
        return $flawless;
      }

      while ( false !== ( $file_name = readdir( $functions_resource ) ) ) {

        $fail_check = false;

        if( substr( strrchr( $file_name, '.' ), 1 ) != 'php' ) {
          continue;
        }

        $file_data = array_filter( (array) @get_file_data( $path . '/' . $file_name, $flawless[ 'default_header' ][ 'extra_resources' ], 'flawless_extra_assets' ) );

        if( empty( $file_data ) ) {
          continue;
        }

        foreach( (array) $required_file_data as $req_field ) {
          if( !in_array( $req_field, array_keys( $file_data ) ) ) {
            $fail_check = true;
          }
        }

        if( $fail_check ) {
          continue;
        }

        $file_data[ 'location' ] = 'theme_functions';
        $file_data[ 'file_name' ] = $file_name;

        $flawless[ 'extra_resources' ][$file_data[ 'name' ]] = $file_data;

        include_once( $path . '/' . $file_name );

      }

    }

    return $flawless;

  }


   /**
    * Automatically load global assets, ran on front and back-end
    *
    * @filter admin_enqueue_scripts ( 50 )
    * @since Flawless 0.2.3
    */
  static function auto_load_assets() {
    global $flawless;

    foreach( ( array ) $flawless[ 'asset_directories' ] as $this_directory => $this_url ) {

      if( is_dir( $this_directory . '/js/load-global' ) ) {
        $locations[ 'js' ][] = array(
          'path' => $this_directory . '/js/load-global',
          'scope' => 'global',
          'url' => $this_url .  '/js/load-global'
        );
      }

      if( is_dir( $this_directory . '/js/load-admin' ) ) {
        $locations[ 'js' ][] = array(
          'path' => $this_directory . '/js/load-admin',
          'scope' => 'admin',
          'url' => $this_url .  '/js/load-admin'
        );
      }

      if( is_dir( $this_directory . '/js/load' ) ) {
        $locations[ 'js' ][] = array(
          'path' => $this_directory . '/js/load',
          'scope' => 'front',
          'url' => $this_url .  '/js/load'
        );
      }

      if( is_dir( $this_directory . '/css/load-global' ) ) {
        $locations[ 'css' ][] = array(
          'path' => $this_directory . '/css/load-global',
          'scope' => 'global',
          'url' => $this_url .  '/css/load-global'
        );
      }

      if( is_dir( $this_directory . '/css/load-admin' ) ) {
        $locations[ 'css' ][] = array(
          'path' => $this_directory . '/css/load-admin',
          'scope' => 'admin',
          'url' => $this_url .  '/css/load-admin'
        );
      }

      if( is_dir( $this_directory . '/css/load' ) ) {
        $locations[ 'css' ][] = array(
          'path' => $this_directory . '/css/load',
          'scope' => 'front',
          'url' => $this_url .  '/css/load'
        );
      }

    }

    foreach( ( array ) $locations as $type => $type_locations ) {

      switch ( $type ) {

        case 'js':

          foreach( ( array ) $type_locations as $data ) {

            $this_dir = opendir( $data[ 'path' ] );

            //** Cycle through every JS file in directory */
            while ( false !== ( $file_name = readdir( $this_dir ) ) ) {

              if( strpos( $file_name, $type ) ) {

                switch ( $data[ 'scope' ] ) {

                  case 'admin':
                    if( is_admin() ) {
                      wp_enqueue_script( str_replace( '.' . $type, '', $file_name ), $data[ 'url' ] . '/' . $file_name, array(), Flawless_Version, true );
                    }
                  break;

                  case  'front':
                    if( !is_admin() ) {
                      wp_enqueue_script( str_replace( '.' . $type, '', $file_name ), $data[ 'url' ] . '/' . $file_name, array(), Flawless_Version, true );
                    }
                  break;

                  case 'global':
                    wp_enqueue_script( str_replace( '.' . $type, '', $file_name ), $data[ 'url' ] . '/' . $file_name, array(), Flawless_Version, true );
                  break;

                }

              }

            }

          }

        break;

        case 'css':

          foreach( ( array ) $type_locations as $data ) {

            $this_dir = opendir( $data[ 'path' ] );

            //** Cycle through every asset in directory */
            while ( false !== ( $file_name = readdir( $this_dir ) ) ) {

              if( strpos( $file_name, $type ) ) {

                $file_data = @get_file_data( $data[ 'path' ] . '/' . $file_name, $flawless[ 'default_header' ][ 'style' ], 'flawless_style_assets' );

                switch ( $data[ 'scope' ] ) {

                  case 'admin':
                    if( is_admin() ) {
                      wp_enqueue_style( str_replace( '.' . $type, '', $file_name ), $data[ 'url' ] . '/' . $file_name, array(), $file_data[ 'version' ] ? $file_data[ 'version' ] : Flawless_Version, $file_data[ 'media' ] ? $file_data[ 'media' ] : 'screen' );
                    }
                  break;

                  case  'front':
                    if( !is_admin() ) {
                      wp_enqueue_style( str_replace( '.' . $type, '', $file_name ), $data[ 'url' ] . '/' . $file_name, array(), $file_data[ 'version' ] ? $file_data[ 'version' ] : Flawless_Version, $file_data[ 'media' ] ? $file_data[ 'media' ] : 'screen' );
                    }
                  break;

                  case 'global':
                    wp_enqueue_style( str_replace( '.' . $type, '', $file_name ), $data[ 'url' ] . '/' . $file_name, array(), $file_data[ 'version' ] ? $file_data[ 'version' ] : Flawless_Version, $file_data[ 'media' ] ? $file_data[ 'media' ] : 'screen' );
                  break;

                }


              }

            }

          }

        break;

      }

    }

  }


  /**
   * Add minified argument to minified scripts to avoid minification by W3C
   *
   * Currently disabled since there is no option to enable 'add_minification_args'
   *
   * @filter script_loader_src ( 10 )
   * @since Flawless 0.2.3
   */
  static function script_loader_src( $src, $handle ) {
    global $flawless;

    if( isset( $flawless ) && isset( $flawless[ 'add_minification_args' ] ) && $flawless[ 'add_minification_args' ] != 'true' ) {
      return $src;
    }

    if( strpos( $src, '.min.' ) || apply_filters( 'flawless_minified_arg', false, array( 'src' => $src, 'handle' => $handle ) ) ) {
      $src = add_query_arg( 'minified', 'true', $src );
    }

    return $src;

  }


  /**
   * Front-end script loading
   *
   * Loads all local and remote assets, checks conditionally loaded assets, etc.
   * Modifies body class based on loaded assets.
   *
   * @filter wp_enqueue_scripts ( 100 )
   * @since Flawless 0.2.3
   * @todo Scripts should be registered here, but enqueved at a differnet level. - potanin@UD
   */
  static function wp_enqueue_scripts( $args = '' ) {
    global $flawless, $flawless, $wp_query, $is_IE;

    //** Do not load these styles if we are on admin side or the WP login page */
    if( strpos( $_SERVER[ 'SCRIPT_NAME' ], 'wp-login.php' ) ) {
      return;
    }

    do_action( 'flawless::wp_enqueue_scripts::start' );

    flawless_theme::console_log( 'P: Executed: flawless_theme::wp_enqueue_scripts();' );

    //** Always load bootstrap.css */
    wp_enqueue_style( 'twitter-bootstrap' );

    //** Load Automatic Scripts and Styles */
    flawless_theme::auto_load_assets();

    do_action( 'flawless::extra_local_assets' );

    //** Load main style.css if exists, even if child theme is used. */
    if( file_exists( TEMPLATEPATH . '/style.css' ) ) {
      wp_enqueue_style( 'flawless-style', get_bloginfo( 'template_url' ) . '/style.css', array( 'twitter-bootstrap' ), Flawless_Version, 'all' );
    }

    //** API Access */
    $remote_assets = apply_filters( 'flawless_remote_assets' , ( array ) $remote_assets );


    //** Check and Load Remote Styles */
    foreach( ( array ) $remote_assets[ 'css' ] as $asset_handle => $remote_asset ) {

      //** Remove prix if passed, we set them automatically */
      $remote_asset = str_replace( array( 'http://', 'https://' ), '', $remote_asset );
      if( flawless_theme::can_get_asset( $flawless[ 'protocol' ] . $remote_asset, array( 'handle' => $asset_handle ) ) ) {
        wp_enqueue_style( $asset_handle, $flawless[ 'protocol' ] . $remote_asset );
      } else {
        flawless_theme::console_log( sprintf( __( 'P: Could not load remote asset style: %1s.', 'flawless' ), $remote_asset ));
      }
    }

    //** Check and Load Remote Scripts */
    foreach( ( array ) $remote_assets[ 'script' ] as $asset_handle => $remote_asset ) {

      //** Remove prix if passed, we set them automatically */
      $remote_asset = str_replace( array( 'http://', 'https://' ), '', $remote_asset );

      if( flawless_theme::can_get_asset( $flawless[ 'protocol' ]. $remote_asset, array( 'handle' => $asset_handle ) ) ) {
        wp_enqueue_script( $asset_handle, $flawless[ 'protocol' ] . $remote_asset );

      } else {
        flawless_theme::console_log( sprintf( __( 'P: Could not load remote asset script: %1s.', 'flawless' ), $remote_asset ));
      }
    }

    //** Load any existing assets for active plugins */
    foreach ( apply_filters( 'flawless_active_plugins', ( array ) flawless_theme::get_active_plugins() ) as $plugin ) {

      //** Get a plugin name slug */
      $plugin = dirname( plugin_basename( trim( $plugin ) ));

      //** Look for plugin-specific scripts and load them */
      foreach( ( array ) $flawless[ 'asset_directories' ] as $this_directory => $this_url ) {

        if( file_exists( $this_directory . '/js/' . $plugin . '.js' ) ) {
          $asset_url = apply_filters( 'flawless-asset-url' , $this_url . '/js/' . $plugin . '.js', $plugin );
          wp_enqueue_script( 'flawless-asset-' . $plugin,  $asset_url , array(), Flawless_Version, true );
          flawless_theme::console_log( sprintf( __( 'P: JavaScript found for %1s plugin and loaded: %2s.', 'flawless' ), $plugin, $asset_url ));
        }

        if( file_exists( $this_directory . '/css/' . $plugin . '.css' ) ) {

          $asset_url = apply_filters( 'flawless-asset-url', $this_url . '/css/' . $plugin . '.css', $plugin );
          $file_data = @get_file_data( $this_directory . '/css/' . $plugin . '.css', $flawless[ 'default_header' ][ 'style' ], 'flawless_style_assets' );

          wp_enqueue_style( 'flawless-asset-' . $plugin, $asset_url, array( 'flawless-style' ), $file_data[ 'version' ] ? $file_data[ 'version' ] : Flawless_Version, $file_data[ 'media' ] ? $file_data[ 'media' ] : 'screen' );

          flawless_theme::console_log( sprintf( __( 'P: CSS found for %1s plugin and loaded: %2s.', 'flawless' ), $plugin, $asset_url ));
        }
      }
    }

    //** Load a custom color scheme if set last, so it supercedes all others */
    if ( !empty( $flawless[ 'color_scheme' ] ) ) {
      if( file_exists( STYLESHEETPATH . "/{$flawless[ 'color_scheme' ]}" ) ) {
        wp_enqueue_style( 'flawless-colors', get_bloginfo( 'stylesheet_directory' ) . "/{$flawless[ 'color_scheme' ]}", array( 'flawless-style' ), Flawless_Version );

      } elseif( file_exists( TEMPLATEPATH . "/{$flawless[ 'color_scheme' ]}" ) ) {
        wp_enqueue_style( 'flawless-colors', get_bloginfo( 'template_url' ) . "/{$flawless[ 'color_scheme' ]}", array( 'flawless-style' ), Flawless_Version );

      }

      //** Add color scheme class to body element */
      $flawless[ 'current_view' ][ 'body_classes' ][] = 'flawless_have_skin';
      $flawless[ 'current_view' ][ 'body_classes' ][] = 'flawless_' . str_replace( array( '.', '-', ' ' ), '_', $flawless[ 'color_scheme' ] );

    } else {
      //** Add body class to indiciate that we are not using a custom style  */
      $flawless[ 'current_view' ][ 'body_classes' ][] = 'flawless_no_skin';

    }

    //** Cycle through asset directories and look for specific CSS files ( this is for assets that can be overwritten by child theme ) */
    foreach( (array) $flawless[ 'asset_directories' ] as $assets_path => $assets_url ) {

      if( file_exists( $assets_path . '/css/flawless-content.css' ) ) {
        wp_enqueue_style( 'flawless-content', $assets_url . '/css/flawless-content.css' ,array( 'flawless-style' ) , Flawless_Version );
      }

      if( file_exists( $assets_path . '/css/content.css' ) ) {
        wp_enqueue_style( 'flawless-custom-content', $assets_url . '/css/content.css' ,array( 'flawless-style' ) , Flawless_Version );
      }

      if( $wp_query->query_vars[ 'splash_screen' ] && file_exists( $assets_path . '/css/flawless-maintanance.css' ) ) {
        wp_enqueue_style( 'flawless-maintanance', $assets_url . '/css/flawless-maintanance.css' ,array( 'flawless-style' ) , Flawless_Version );
      }

    }

    wp_enqueue_script( 'jquery-ui-widget',  get_bloginfo( 'template_url' ) . '/js/jquery.ui.widget.min.js', array( 'jquery' ), '2.0.4' );
    wp_enqueue_script( 'jquery-ui-mouse',  get_bloginfo( 'template_url' ) . '/js/jquery.ui.mouse.min.js', array( 'jquery' ), '2.0.4' );
    wp_enqueue_script( 'jquery-ui-slider',  get_bloginfo( 'template_url' ) . '/js/jquery.ui.slider.min.js', array( 'jquery' ), '2.0.4' );

    //** Load child theme style.css if exists, and only if child theme is active */
    if( STYLESHEETPATH != TEMPLATEPATH &&  file_exists( STYLESHEETPATH . '/style.css' ) ) {
      wp_enqueue_style( 'flawless-child-style' , get_bloginfo( 'stylesheet_directory' ) . '/style.css', array( 'twitter-bootstrap' ), Flawless_Version, 'all' );
    }

    wp_enqueue_script( 'flawless-frontend' );

    if ( wp_is_mobile() ) {
      wp_enqueue_script( 'jquery-touch-punch' );
    }

    //** Load assets that vary based on environment */
    do_action( 'flawless::conditional_assets' );

    $flawless_header_css = array();

    if( current_theme_supports( 'custom-background') && get_header_image() ) {
      $flawless_header_css[] = ' .background_header_image { background-image: url( ' . get_header_image() . ' );  height: 100%; max-height: ' . HEADER_IMAGE_HEIGHT . 'px; }';
    }

    if( $flawless[ 'layout_width' ] ) {
      $flawless_header_css[] = 'body .container { max-width: ' . $flawless[ 'layout_width' ] . 'px; }';
    }

    //** Included fixed image sizes for faster rendering, and masonry support */
    foreach( (array) self::image_sizes() as $size => $data ) {
      //$flawless_header_css[] = 'img.attachment-' . $size . ' { max-width: ' . $data[ 'width' ] . 'px; }';
      $flawless_header_css[] = '.gallery .gallery-item img.attachment-' . $size . ' { width: ' . $data[ 'width' ] . 'px; }';
      $flawless_header_css[] = 'img.fixed_size.attachment-' . $size . ' { width: ' . $data[ 'width' ] . 'px; }';
    }

    //** Print out header CSS */
    $flawless_header_css = apply_filters( 'flawless::header_css' , $flawless_header_css, $flawless );

    if( is_array( $flawless_header_css ) ) {
      echo '<style type="text/css">' . implode( '' , $flawless_header_css ) . ' </style>' . "\n";
    }

  }


  /**
   * Return array of active plugins for current instance
   *
   * Improvement over wp_get_active_and_valid_plugins() which doesn't return any plugins when in MS
   *
   * @since Flawless 0.2.3
   */
  function get_active_plugins() {

    $mu_plugins = ( array ) wp_get_mu_plugins();
    $regular_plugins = ( array ) wp_get_active_and_valid_plugins();

    if( is_multisite() ) {
      $network_plugins = ( array ) wp_get_active_network_plugins();
    } else {
      $network_plugins = array();
    }

    return array_merge( $regular_plugins, $mu_plugins, $network_plugins );

  }


  /**
    * Load global vars for header template part.
    *
    * @todo Not sure if this is necessary - is there a reason for the flawless_header_links filter to be applied here? - potanin@UD
    * @since Flawless 0.2.3
    */
  static function get_template_part_header( $slug, $name ) {
    global $flawless, $wp_query;

    //** $flawless_header_links from filter which was set by different sections that will be in header drpdowns */
    $flawless['header_links'] = apply_filters( 'flawless_header_links', false );

    return $current;

  }


  /**
   * Enqueue or print scripts in admin footer
   *
   * Renders json array of configuration.
   *
   * @since 0.2.3
   */
  static function admin_print_footer_scripts( $hook ) {
    global $flawless;
    echo '<script type="text/javascript">var flawless = jQuery.extend( true, jQuery.parseJSON( ' . json_encode( json_encode( $flawless ) ) . ' ), typeof flawless === "object" ? flawless : {} );</script>';
  }


  /**
   * Used for loading contextual help and back-end scripts. Only active on Theme Options page.
   *
   * @todo Should switch to WP 3.3 contextual help with UD live-help updater.
   * @uses $current_screen global variable
   * @since Flawless 0.2.3
   */
  static function admin_enqueue_scripts( $hook ) {
    global $current_screen, $flawless;

    //* Load Flawless Global Scripts */
    wp_enqueue_script( 'jquery-ud-smart_buttons' );
    wp_enqueue_script( 'flawless-admin-global' );
    wp_enqueue_style( 'flawless-admin-styles' );

    if( $current_screen->id != 'appearance_page_functions' ) {
      return;
    }

    $contextual_help[ 'content' ][] = '<h3>' . __( 'Flawless Theme Help' ) .'</h3>';
    $contextual_help[ 'content' ][] = '<p>' . __( 'Since version 3.0.0 much flexibility was added to page layouts by adding a number of conditional Tabbed Widget areas which are available on all the pages.', 'flawless' ) .'</p>';

    $contextual_help[ 'content' ][] = '<h3>' . __( 'Home & Posts Pages', 'flawless' ) .'</h3>';
    $contextual_help[ 'content' ][] = '<p>' . sprintf( __( '<b>Posts Page</b> is typically used to display the <b>blog</b> part of site when WordPress is used as a CMS. You can configure the posts page on the <a href="%1s">Settings -> Reading</a> settings page.', 'flawless' ), admin_url( "options-reading.php" ) ) .'</p>';
    $contextual_help[ 'content' ][] = '<p>' . __( 'The <b>Property Search</b> widget area is hidden automatically when no widget exists in the area.', 'flawless' ) .'</p>';

    $contextual_help[ 'content' ][] = '<h3>' . __( 'Color Schemes', 'flawless' ) .'</h3>';
    $contextual_help[ 'content' ][] = '<p>' . sprintf( __( 'If you want to customize colors, it is advisable to create a separate a color palette within a child theme. Please visit <a href="">WP-Property & Flawless Help</a> to learn more about this', 'flawless' ), 'http://usabilitydynamics.com/help/' ) .'</p>';

    $contextual_help[ 'content' ][] = '<h3>' . __( 'Content', 'flawless' ) .'</h3>';
    $contextual_help[ 'content' ][] = '<p>' . __( 'Content types, better known as Post Types, let you segragate your content into different types.', 'flawless' ) .'</p>';
    $contextual_help[ 'content' ][] = '<p>' . __( '<b>Root Page:</b> Your custom content pages will have a URL structure with the slug of the content type being at the root.  A root page will let you set a custom page to be displayed as a root of a content type.  A Root Page does not have to be a page, it can be of any <b>hierarchial</b> content type.', 'flawless' ) .'</p>';
    $contextual_help[ 'content' ][] = '<p>' . __( '<b>Show post meta:</b> When checked, will display the date posted, related taxonomies, and author in search results and on single pages of the content type.', 'flawless' ) .'</p>';

    $contextual_help[ 'content' ][] = '<h3>' . __( 'General Enhancements', 'flawless' ) .'</h3>';
    $contextual_help[ 'content' ][] = '<p>' . __( '<b>Automatically Hide Widgets:</b> After a page is rendered on the front-end, any widgets that do not have any text, excluding the title, will be hidden automatically.', 'flawless' ) .'</p>';

    $contextual_help[ 'content' ][] = '<h3>' . __( 'Other Theme Settings', 'flawless' ) .'</h3>';
    $contextual_help[ 'content' ][] = '<p>' . sprintf( __( 'If you would like to change the header images, please visit the <a href="%1s">Appearance -> Header</a> page.', 'flawless' ), admin_url( "themes.php?page=custom-header" ) ) .'</p>';
    $contextual_help[ 'content' ][] = '<p>' . sprintf( __( 'The navigational menus are configured are on the  <a href="%1s">Appearance -> Menus</a> page.', 'flawless' ), admin_url( "nav-menus.php" ) ) .'</p>';
    $contextual_help[ 'content' ][] = '<p>' . sprintf( __( 'And be sure to configure the widgets on the <a href="%1s">Appearance -> Widgets </a> page.', 'flawless' ), admin_url( "widgets.php" ) ) .'</p>';

    $contextual_help[ 'content' ][] = '<h3>' . __( 'Header Logo' ) .'</h3>';
    $contextual_help[ 'content' ][] = '<p>' . __( 'It is recommended the header logo is under 100 pixels tall.', 'flawless' ) .'</p>';
    $contextual_help[ 'content' ][] = '<p>' . __( 'Address: The address will be automatically converted into coordinates, and a Google Map will be displayed on the top of every page in the <b>Contact Us</b> dropdown.', 'flawless' ) .'</p>';

    $contextual_help[ 'content' ][] = '<h3>' . __( 'Header Property Search' ) .'</h3>';
    $contextual_help[ 'content' ][] = '<p>' . __( 'By default the header property search widget area, and the home page slideshow overlay area were the same widget area, <b>Header &amp; Home: Property Search</b>. This setting can be changed on within the Header tab, and two new widget areas will be created to differentiate between the two.', 'flawless' ) .'</p>';

    $contextual_help[ 'content' ][] = '<h3>' . __( 'Footer' ) .'</h3>';
    $contextual_help[ 'content' ][] = '<p>' . __( 'The footer includes several elements - <b>Footer: Bottom Left Block</b> widget, <b>Explore Block</b>, <b>phone number</b>, <b>site tagline</b>, <b>social media icons</b>, <b>Equal Housing Opportunity logo</b>, and <b>copyright notice.</b>, ', 'flawless' ) .'</p>';

    $contextual_help = apply_filters( 'wpp_contextual_help', array(
      'page' => $current_screen->id,
      'content' => $contextual_help[ 'content' ]
    ));

    add_contextual_help( $current_screen->id, implode( "\n", ( array ) $contextual_help[ 'content' ] ));

    //** Enque Scripts on Theme Options Page */
    wp_enqueue_script( 'jquery-ui-sortable' );
    wp_enqueue_script( 'jquery-ui-tabs' );
    wp_enqueue_script( 'jquery-cookie' );
    wp_enqueue_script( 'flawless-admin' );

  }


  /**
    * Adds Inline Cropping capability to an image.
    *
    * @todo Finish by initiating scripts when triggered. Right now causes a JS error because wp_image_editor() expects imageEdit() to already be loaded.  - potanin@UD
    * @since Flawless 0.3.4
    */
   static function inline_crop( $post_id ) {

    wp_enqueue_script( 'image-edit' );
    wp_enqueue_script( 'jcrop' );

    wp_enqueue_style( 'jcrop' );
    wp_enqueue_style( 'imgareaselect' );

    include_once( ABSPATH . 'wp-admin/includes/image-edit.php' );
    ?>
    <script type="text/javascript"> var imageEdit = {
    init: function() { jQuery( document ).ready( function() { imageEdit.init(); } );  }
    } ;</script>
    <?php

    echo wp_image_editor( $post_id );

   }



  /**
    * Add "Theme Options" link to admin bar.
    *
    *
    * @since Flawless 0.3.1
    */
   static function admin_bar_menu( $wp_admin_bar ) {

    if ( current_user_can( 'switch_themes' ) && current_user_can( 'edit_theme_options' ) ) {

      $wp_admin_bar->add_menu( array(
        'parent' => 'appearance',
        'id' => 'theme-options',
        'title' => __( 'Theme Settings', 'flawless' ),
        'href' => admin_url( 'themes.php?page=functions.php' )
      ));

    }

   }


  /**
    * Frontend AJAX Handler.
    *
    * @since Flawless 0.6.0
    */
  static function frontend_ajax_handler() {
    global $flawless, $wpdb;

    nocache_headers();

    $return = array( 'success' => false );

    ob_start();

    switch( $_REQUEST[ 'the_action' ] ) {

      /**
       *
       *
       * @todo Add  get_option('require_name_email') check / support.
       */
      case 'comment_submit':

        parse_str( $_POST[ 'form_data' ], $form_data );

        $args = wp_parse_args( $form_data, array(
          'comment_post_ID' => 0,
          'author' => null,
          'email' => null,
          'url' => null,
          'comment' => null,
          'comment_parent' => 0
        ));

        foreach( (array) $args as $key => $value ) {
          $args[ $key ] = trim( $value );
        }

        $args[ 'author' ] = strip_tags( $args[ 'author' ] );

        $post = get_post( $args[ 'comment_post_ID' ] );
        $status = get_post_status($post);
        $status_obj = get_post_status_object($status);

        if ( empty( $args[ 'comment'] ) ) {
          $return[ 'message' ] = __( 'Please enter a comment.', 'flawless' );
          break;
        }

        if ( empty( $post->comment_status ) || !comments_open( $args[ 'comment_post_ID' ] ) )  {
          do_action('comment_id_not_found', $args[ 'comment_post_ID' ]);
          $return[ 'message' ] = __('Sorry, comments are closed for this item.');
          break;
        } elseif ( 'trash' == $status ) {
          do_action('comment_on_trash', $args[ 'comment_post_ID' ]);
          break;
        } elseif ( !$status_obj->public && !$status_obj->private ) {
          do_action('comment_on_draft', $args[ 'comment_post_ID' ]);
          break;
        } elseif ( post_password_required($args[ 'comment_post_ID' ]) ) {
          do_action('comment_on_password_protected', $args[ 'comment_post_ID' ]);
          break;
        } else {
          do_action('pre_comment_on_post', $args[ 'comment_post_ID' ]);
        }

        $user = wp_get_current_user();

        if ( $user->ID ) {

          $args[ 'user_ID' ] = $user->ID;
          $args[ 'email' ] = $user->data->user_email;
          $args[ 'author' ] = $user->data->display_name;

        } else {

          if ( get_option('comment_registration') || 'private' == $status ) {
            $return[ 'message' ] = __('Sorry, you must be logged in to post a comment.');
            break;
          }

        }

        if ( !is_email( $args[ 'email'] ) ) {
          $return[ 'message' ] = __( 'Please enter a valid email address.' );
          break;
        }

        $comment_id = wp_new_comment( array(
          'comment_post_ID' => $args[ 'comment_post_ID'],
          'comment_author' => $args[ 'author'],
          'comment_author_email' => $args[ 'email' ],
          'comment_author_url' => $args[ 'url' ],
          'comment_content' => ( stripslashes( $args[ 'comment' ] ) ),
          'comment_parent' => $args[ 'comment_parent' ],
          'user_id' => $args[ 'user_id' ],
          'comment_type' => '',
        ));

        $comment = get_comment( $comment_id );

        if( $comment ) {

          if ( !$user->ID ) {
            $comment_cookie_lifetime = apply_filters('comment_cookie_lifetime', 30000000);
            setcookie('comment_author_' . COOKIEHASH, $comment->comment_author, time() + $comment_cookie_lifetime, COOKIEPATH, COOKIE_DOMAIN);
            setcookie('comment_author_email_' . COOKIEHASH, $comment->comment_author_email, time() + $comment_cookie_lifetime, COOKIEPATH, COOKIE_DOMAIN);
            setcookie('comment_author_url_' . COOKIEHASH, esc_url($comment->comment_author_url), time() + $comment_cookie_lifetime, COOKIEPATH, COOKIE_DOMAIN);
          }

          $return[ 'success' ] = true;

          $comments = get_comments( array(
            'post_id' => $args[ 'comment_post_ID' ],
            'status' => 'approve',
            'order' => 'ASC'
          ));

          if ( $user->ID ) {
            $comments = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d AND (comment_approved = '1' OR ( user_id = %d AND comment_approved = '0' ) )  ORDER BY comment_date_gmt", $args[ 'comment_post_ID' ], $args[ 'user_ID' ] ));
          } else if ( empty( $args['comment_author'] ) ) {
            $comments = get_comments( array('post_id' => $args[ 'comment_post_ID' ], 'status' => 'approve', 'order' => 'ASC') );
          } else {
            $comments = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d AND ( comment_approved = '1' OR ( comment_author = %s AND comment_author_email = %s AND comment_approved = '0' ) ) ORDER BY comment_date_gmt", $args[ 'comment_post_ID' ], wp_specialchars_decode( $args['comment_author'] ,ENT_QUOTES), $args['comment_author_email'] ));
          }

          $return[ 'comment_list' ] = $comments;
          $return[ 'comment_count' ] = count( $comments );

          ob_start();
          wp_list_comments( array( 'callback' => 'flawless_comment' ), $comments );
          $comment_html = ob_get_contents();
          ob_end_clean();

          $return[ 'comment_html' ] = $comment_html;

        }

      break;

    }

    $output = ob_get_contents();
    ob_end_clean();

    $return[ 'output' ] = $output;


    return $return;

  }



  /**
    * Admin Flawless-specific ajax actions.
    *
    * Called when AJAX call with action:flawless_action is used.
    * Must return array, which is automatically converted into JSON.
    *
    * @todo May want to update nonce verification to something more impressive since used on back and front-end calls.
    * @since Flawless 0.2.3
    */
  static function ajax_actions() {
    global $flawless, $wpdb;

    nocache_headers();

    if( !current_user_can( 'edit_theme_options' ) ) {
      die('0');
    }

    $flawless = stripslashes_deep( get_option( 'flawless_settings' ));

    switch( $_REQUEST[ 'the_action' ] ) {

      case 'delete_logo':

        //** Delete old logo */
        if( is_numeric( $flawless[ 'flawless_logo' ][ 'post_id' ] ) ) {
          wp_delete_attachment( $flawless[ 'flawless_logo' ][ 'post_id' ], true );
          unset( $flawless[ 'flawless_logo' ] );
        }

        update_option( 'flawless_settings', $flawless );
        $return = array( 'success' => 'true' );

      break;

      case 'clean_up_revisions':

        if( current_user_can( 'delete_posts' ) ) {

          $args['max_revisions'] = intval( defined( 'WP_POST_REVISIONS' ) ? WP_POST_REVISIONS : 3 );

          $revisions_over_limit = $wpdb->get_results( "SELECT post_parent, ID as revision_id, ( SELECT count(ID) FROM {$wpdb->posts} pp WHERE pp.post_parent = p.post_parent ) as revisions, ( SELECT post_date FROM {$wpdb->posts} last WHERE last.post_parent = p.post_parent ORDER BY last.post_date DESC LIMIT {$args[max_revisions]},1) as date_cutoff FROM {$wpdb->posts} p WHERE post_type = 'revision' AND post_date <= ( SELECT post_date FROM {$wpdb->posts} last WHERE last.post_parent = p.post_parent ORDER BY last.post_date DESC LIMIT " . ( $args[max_revisions] + 1 ) . ",1)");

          $args['revisions_over_limit'] = count( $revisions_over_limit );

          foreach( (array) $revisions_over_limit as $post_row ) {
            $args['deleted'][] = !is_wp_error( wp_delete_post_revision( $post_row->revision_id ) ) ? $post_row->revision_id : '';
          }

          $args['deleted'] = count( (array) array_filter( (array) $args['deleted'] ) );

          if( $args['deleted'] ) {
            $wpdb->query( "OPTIMIZE TABLE {$wpdb->posts}" );
            $wpdb->query( "OPTIMIZE TABLE {$wpdb->postmeta}" );

            $return = array( 'success' => 'true', 'message' => sprintf(__( 'Success! We removed %1s post revisions and optimized your MySQL tables. ', 'flawless' ), $args['deleted'], $args['max_revisions'] ) );
          } else {
            $return = array( 'success' => 'false', 'message' => __( 'Does not look like there were any revisions to remove.', 'flawless' ) );
          }

        }

      break;

      case 'delete_all_settings':

        delete_option( 'flawless_settings' );

        $return = array(
          'success' => 'true',
          'message' => __( 'All Flawless settings deleted.', 'flawless' )
        );
      break;

      case 'show_permalink_structure':

        $return = array(
          'success' => 'true',
          'message' => '<pre class="flawless_class_pre">' . print_r( get_option('rewrite_rules'), true) . '</pre>'
        );

      break;

      case 'show_flawless_configuration':

        $return = array(
          'success' => 'true',
          'message' => '<pre class="flawless_class_pre">' . print_r( $flawless, true ) . '</pre>'
        );

      break;

      default:
        $return = apply_filters( 'flawless_ajax_action', array( 'success' => $false ), $flawless );
      break;

    }

    if( empty( $return ) ) {

      $return = array(
        'success' => false,
        'message' => __( 'No action found.', 'flawless' )
      );

    }

    return $return;

  }


  /**
   * {need description}
   *
   *
   * @since Flawless 0.2.3
   *
   */
  function add_archive_checkbox( $posts, $args, $post_type ) {
    global $_nav_menu_placeholder, $wp_rewrite, $flawless;

    $_nav_menu_placeholder = ( 0 > $_nav_menu_placeholder ) ? intval( $_nav_menu_placeholder ) - 1 : -1;

    $archive_slug = $post_type[ 'args' ]->has_archive === true ? $post_type[ 'args' ]->rewrite[ 'slug' ] : $post_type[ 'args' ]->has_archive;

    if ( $post_type[ 'args' ]->rewrite[ 'with_front' ] ) {
      $archive_slug = substr( $wp_rewrite->front, 1 ) . $archive_slug;
    } else {
      $archive_slug = $wp_rewrite->root . $archive_slug;
    }

    array_unshift( $posts, ( object ) array(
      'ID' => 0,
      '_add_to_top' => true,
      'object_id' => $_nav_menu_placeholder,
      'post_content' => '',
      'post_excerpt' => '',
      'custom_thing' => 'hola',
      'post_title' => sprintf( __( '%1s Archive Root', 'flawless' ), $post_type[ 'args' ]->labels->all_items ),
      'post_type' => 'nav_menu_item',
      'type' => 'custom',
      'url' => site_url( $archive_slug ),
     ));

    return $posts;

  }


  /**
   * {need description}
   *
   * Adds a special class to menus that display descriptions for the individual menu items
   *
   * @since Flawless 0.2.3
   *
   */
  static function wp_nav_menu_args( $args ) {
    global $flawless;

    if( $flawless[ 'menus' ][$args[ 'theme_location' ]][ 'show_descriptions' ] == 'true' ) {
      $args[ 'menu_class' ] = $args[ 'menu_class' ] . ' menu_items_have_descriptions';
    }

    return $args;

  }


  /**
   * {need description}
   *
   * @since Flawless 0.2.3
   *
   */
  static function walker_nav_menu_start_el( $item_output, $item, $depth, $args ) {
    global $flawless;

    //** Do not add description if this is not a top level menu item */
    if( $item->menu_item_parent || $flawless[ 'menus' ][$args->theme_location][ 'show_descriptions' ] != 'true' ) {
      return $item_output;
    }

    $char_limit = 50;

    $description = substr( $item->description, 0, $char_limit ) . ( strlen( $item->description ) > $char_limit ? '...' : '' );

    $trigger =  '</a>' . $args->after;

    //** Inject description HTML by identifying the $args->after */
    $item_output = str_replace( $trigger, $trigger. ( $description ? '<span class="menu_item_description">' . $description . '</span>' : '' ), $item_output );

    return $item_output;

  }


  /**
  * Modified front-end menus and adds extra classes
  *
  * @todo Find way to inexpensively figure out if current item is last and add a class.
  * @since Flawless 0.2.3
  */
  static function nav_menu_css_class( $classes, $item, $args ) {
    global $post, $flawless, $wpdb;

    $total_items = $wpdb->get_var( "SELECT count FROM {$wpdb->term_relationships} tr LEFT JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id WHERE object_id = {$item->db_id} AND taxonomy = 'nav_menu' ");

    if( !$item->menu_item_parent ) {
      $classes[] = 'top_level_item';
    } else {
      $classes[] = 'sub_menu_level_item';
    }

    if( $item->menu_order == 1 ) {
      $classes[] = 'first';

    }
    if( $item->menu_order == $total_items ) {
      $classes[] = 'last';
    }

    //** Check if the currently rendered item is a child of this link */
    if( untrailingslashit( $item->url ) == untrailingslashit( $flawless[ 'post_types' ][$post->post_type][ 'archive_url' ] ) ) {

      $classes[] = 'current-page-ancestor current-menu-ancestor current-menu-parent current-page-parent current_page_parent flawless_ad_hoc_menu_parent';

      //** This menu item is an ad-hoc parent of something, we need to update parent elements as well */
      if( $item->menu_item_parent ) {

      }

    }

    return $classes;

  }


  /**
    * Handle upgrading the theme. Only displayed to users who can Update Themes.
    *
    * @since Flawless 0.2.3
    */
  static function handle_upgrade() {
    global $wpdb;

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    $installed_version = get_option( 'flawless_version' );

    //** If new install. */
    if( empty( $installed_version ) ) {
      $redirect = add_query_arg( 'admin_splash_screen', 'welcome', admin_url( 'themes.php?page=functions.php' ));
    }

    //** If upgrading from older version */
    if( version_compare( Flawless_Version, $installed_version, '>' ) ) {
      $redirect = add_query_arg( 'admin_splash_screen', 'updated', admin_url( 'themes.php?page=functions.php' ));
    }

    if( current_theme_supports( 'term-meta' ) && $wpdb->taxonomymeta ) {

      $sql = "CREATE TABLE {$wpdb->taxonomymeta} (
        meta_id bigint(20) unsigned NOT NULL auto_increment,
        taxonomy_id bigint(20) unsigned NOT NULL default '0',
        meta_key varchar(255) default NULL,
        meta_value longtext,
        PRIMARY KEY  (meta_id),
        KEY taxonomy_id (taxonomy_id),
        KEY meta_key (meta_key)
      ) $charset_collate;";

      dbDelta( $sql );

    }

    //** Run the update now in case we have a redirection */
    update_option( 'flawless_version', Flawless_Version );

    if( isset( $redirect ) ) {
      die( wp_redirect( $redirect ) );
    }

  }


  /**
   * Handles request
   *
   * Actually, handles empty search request:
   * Determine if search request exists but it's empty, -
   * we do 'hack' to show Search result page.
   *
   * @param $query_vars
   * @return unknown_type
   * @author peshkov@UD
   */
  function request_filter( $query_vars ) {

    if( isset( $_GET[ 's' ] ) && empty( $_GET[ 's' ] ) ) {
      $query_vars[ 's' ] = " ";
    }

    return $query_vars;
  }


  /**
    * Handles back-end theme configurations
    *
    * @since Flawless 0.2.3
    *
    */
  static function admin_menu() {
    global $flawless;

//die('admin');
    $flawless[ 'options_ui' ][ 'tabs' ] = apply_filters( 'flawless_option_tabs', array(
      'options_ui_general' => array(
        'label' => __( 'General','flawless' ),
        'id' => 'options_ui_general',
        'position' => 10,
        'callback' => array( 'flawless_theme_ui','options_ui_general' )
      ),
      'options_ui_post_types' => array(
        'label' => __( 'Content','flawless' ),
        'id' => 'options_ui_post_types',
        'position' => 20,
        'callback' => array( 'flawless_theme_ui','options_ui_post_types' )
      ),
      'options_ui_design' => array(
        'label' => __( 'Design','flawless' ),
        'id' => 'options_ui_design',
        'position' => 25,
        'callback' => array( 'flawless_theme_ui','options_ui_design' )
      ),
      'options_ui_advanced' => array(
        'label' => __( 'Advanced','flawless' ),
        'id' => 'options_ui_advanced',
        'position' => 200,
        'callback' => array( 'flawless_theme_ui','options_ui_advanced' )
      )
    ));

    //** Put the tabs into position */
    usort( $flawless[ 'options_ui' ][ 'tabs' ], create_function( '$a,$b', ' return $a["position"] - $b["position"]; ' ));

    //** QC Tabs Before Rendering */
    foreach( (array) $flawless[ 'options_ui' ][ 'tabs' ] as $tab_id => $tab ) {
      if( !is_callable( $tab[ 'callback' ] ) )  {
        unset( $flawless[ 'options_ui' ][ 'tabs' ][$tab_id] );
        continue;
      }
    }

    $flawless[ 'navbar_options' ] = array(
      'wordpress' => array(
        'label' => __( 'WordPress "Toolbar" ', 'flawless' )
      ));

    foreach( ( array ) wp_get_nav_menus() as $menu ) {
      $flawless[ 'navbar_options' ][ $menu->slug ] = array(
        'type' => 'wp_menu',
        'label' => $menu->name,
        'menu_slug' => $menu->slug
      );
    }

    $flawless[ 'navbar_options' ] = apply_filters( 'flawless::navbar_options', ( array ) $flawless[ 'navbar_options' ] );

    if( is_array( $flawless[ 'options_ui' ][ 'tabs' ] ) ) {
      $settings_page = add_theme_page( __( 'Settings', 'flawless' ), __( 'Settings', 'flawless' ), 'edit_theme_options', basename( __FILE__ ), array( 'flawless_theme', 'options_page' ));
    }

  }


    /**
     * Primary function for handling front-end actions
     *
     * @filter template_redirect ( 0 )
     * @since Flawless 0.2.3
     */
    static function template_redirect() {
      global $wp_styles, $is_IE, $flawless, $wp_query, $wp_rewrite;

      flawless_theme::set_current_view();

      add_action( 'wp_head', array( 'flawless_theme', 'wp_head' ));

      add_filter( 'wp_nav_menu_args', array( 'flawless_theme','wp_nav_menu_args' ), 5 );
      add_filter( 'walker_nav_menu_start_el', array( 'flawless_theme','walker_nav_menu_start_el' ), 5, 4 );
      add_filter( 'nav_menu_css_class', array( 'flawless_theme','nav_menu_css_class' ), 5, 3 );

      add_filter( 'post_class', array( 'flawless_theme', 'post_class' ), 10, 3 );
      add_filter( 'wp_title', array( 'flawless_theme', 'wp_title' ), 10, 3 );

      //** Load global variables into the "header" template_part
      add_filter( 'get_template_part_header-element', array( 'flawless_theme', 'get_template_part_header' ), 10, 2);
      add_filter( 'get_template_part_footer-element', array( 'flawless_theme', 'get_template_part_header' ), 10, 2);

      //** Load extra options into Admin Bar ( in header ) */
      add_action( 'admin_bar_menu', array( 'flawless_theme', 'admin_bar_menu' ), 200 );

      //** Disable default Gallery shortcode styles */
      add_filter( 'use_default_gallery_style', create_function( '', ' return false; ' ));

      //** Load denali into global var on all pages. */
      $wp_query->query_vars[ 'flawless' ] = &$flawless;

      if( $flawless[ 'maintanance_mode' ] == 'true' ) {
        $wp_query->query_vars[ 'splash_screen' ] = true;

        if( file_exists( STYLESHEETPATH . '/maintanance.php' ) ) {
          include STYLESHEETPATH . '/maintanance.php';
          die();
        } else {
          include TEMPLATEPATH . '/maintanance.php';
          die();
        }
      }

      add_action( 'body_class', array( 'flawless_theme', 'body_class' ), 200, 2 );

      flawless_theme::console_log( 'P: Executed: flawless_theme::template_redirect();' );
   }


  /**
   * Prevents direct editing of Extended Term post pages by redirecting user to term page.
   *
   * @since Flawless 0.5.0
   */
  static function post_editor_loader() {

    if( !is_numeric( $_GET[ 'post' ] ) ) {
      return;
    }

    $extended_term_id = get_post_meta( $_GET[ 'post' ], 'extended_term_id', true );
    $extended_term_taxonomy = get_post_meta( $_GET[ 'post' ], 'extended_term_taxonomy', true );

    if( $extended_term_id && $extended_term_taxonomy ) {
      die( wp_redirect( get_edit_term_link( $extended_term_id, $extended_term_taxonomy ) ) );
    }

  }


  /**
   * Pre-header loader for Term Editor, when Extended Taxonomies are enabled.
   *
   * @since Flawless 0.5.0
   */
  static function term_editor_loader() {
    global $taxnow, $wpdb;

    $tax = get_taxonomy( $taxnow );
    $tag_ID = (int) $_REQUEST['tag_ID'];

    if( $_GET['action'] == 'edit' && is_numeric( $tag_ID ) ) {
      flawless_theme::term_updated( $tag_ID );
    }

    if( $_POST['action'] == 'editedtag' && $_POST[ 'extended_post_id' ] ) {

      check_admin_referer( 'update-tag_' . $tag_ID );

      if ( !current_user_can( $tax->cap->edit_terms ) ) {
        wp_die( __( 'Cheatin&#8217; uh?' ) );
      }

      $post_id = $_POST[ 'extended_post_id' ];

      if( current_user_can( 'edit_post', $post_id ) ) {
        foreach( (array) $_POST[ 'post_data' ] as $meta_key => $meta_value ) {
          $wpdb->update( $wpdb->posts, array( $meta_key => $meta_value ), array( 'ID' => $post_id ) );
        }
      }

      foreach( (array) $_POST[ 'post_meta' ] as $meta_key => $meta_value ) {
        if( !empty( $meta_value ) ) {
          update_term_meta( $tag_ID, $meta_key, $meta_value );
        } else {
          delete_term_meta( $tag_ID, $meta_key );
        }

      }

    }

  }


  /**
   * Triggered on term update and creation when Extended Taxonomies are supported.
   *
   * Hooked into: wp_insert_post, edit_term, created_term, deleted_term.
   *
   * @since Flawless 0.5.0
   */
  static function term_updated( $term_id, $maybe_post = false, $maybe_taxonomy = false ) {
    global $wpdb;

    //** Determine if this is an post update */
    if( is_object( $maybe_post ) && is_numeric( $maybe_post->ID ) ) {

      //**  Verify if this is an auto save routine.  */
      if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $maybe_post ) ){
        return $term_id;
      }

      $post_term_id = get_post_meta( $maybe_post->ID, 'extended_term_id', true );

      if( !$post_term_id ) {
        return;
      }

      $term_update = wp_update_term( $post_term_id, str_replace( '_tp_' , '' , $maybe_post->post_type ), array(
        'name' => $maybe_post->post_title,
        'description' => $maybe_post->post_excerpt
      ));

      remove_filter( 'created_term',  array( 'flawless_theme', 'term_updated' ), 9 );
      remove_filter( 'edit_term',  array( 'flawless_theme', 'term_updated' ), 9 );

      return;

    }

    //** Must be a term creation / update */
    if( !$maybe_post && $_GET[ 'taxonomy'] ) {

      $term = $wpdb->get_row( "SELECT name, taxonomy, tt.description, tt.term_id, slug FROM {$wpdb->term_taxonomy} tt LEFT JOIN {$wpdb->terms} t on tt.term_id = t.term_id WHERE tt.term_id = '{$term_id}'" );
      $post = get_post_for_extended_term( $term_id, $_GET[ 'taxonomy' ] );

      //** Prevent the term_updated filter from running again (endless loop */
      remove_filter( 'wp_insert_post',  array( 'flawless_theme', 'term_updated' ), 9 );

      $post_id = wp_insert_post(array(
        'ID' => $post->ID,
        'post_status' => 'publish',
        'post_title' => wp_strip_all_tags( $term->name ),
        'post_type' => '_tp_' . $term->taxonomy,
        'post_excerpt' => $term->description,
        'post_content' => $post->post_content,
        'post_name' => $term->slug
      ), true );

      if( !is_wp_error( $post_id ) ) {
        update_post_meta( $post_id, 'extended_term_id', $term_id );
        update_post_meta( $post_id, 'extended_term_taxonomy', $term->taxonomy );
      }
    }

  }


  /**
   * Delete Extended Taxonomy post.
   *
   * @since Flawless 0.5.0
   */
  static function delete_term( $term_id, $tt_id, $taxonomy ) {
    global $wpdb;

    $post_id = $wpdb->get_var( "SELECT ID FROM {$wpdb->posts} WHERE post_type = '{$taxonomy}' AND post_name = '{$term_id}'" );

    if( $post_id ) {
      wp_delete_post( $post_id, true );
    }

  }


  /**
   * Determines if we have a Navbar, and if so, the type.
   *
   * Loads information into global variables, setups body classes, loads scripts, etc.
   * current_theme_supports() is ran within the function to give child themes the opportunity to remove support.
   *
   * @todo Navbar depth level of 1 is temporary - a callback must be added to render WP menus in TB format. - potanin@UD
   * @filter init ( 500 )
   * @author potanin@UD
   * @since Flawless 0.3.5
   */
  static function prepare_navbars() {
    global $flawless;

    if( !current_theme_supports( 'header-navbar' ) ) {
      return;
    }

    if( apply_filters( 'flawless::use_navbar' , $flawless[ 'disabled_theme_features' ][ 'header-navbar' ] ) ) {
      return;
    }

    //** Disable WordPress Toolbar unless it is selected */
    if( $flawless[ 'navbar' ][ 'type' ] != 'wordpress' ) {
      remove_action( 'init', '_wp_admin_bar_init' );
    }

    $flawless[ 'navbar' ][ 'html' ] = array();

    if( wp_get_nav_menu_object( $flawless[ 'navbar' ][ 'type' ] ) ) {
      $flawless[ 'navbar' ][ 'html' ][ 'left' ] = wp_nav_menu( array(
        'menu' => $flawless[ 'navbar' ][ 'type' ],
        'menu_class' => 'nav',
        'fallback_cb' => false,
        'echo' => false,
        'container' => false,
        'depth' => 1
      ));
    }

    /* Mobile Navbar is limited to one depth level */
    if( wp_get_nav_menu_object( $flawless[ 'mobile_navbar' ][ 'type' ] ) ) {
      $flawless[ 'mobile_navbar' ][ 'html' ][ 'left' ] = wp_nav_menu( array(
        'menu' => $flawless[ 'mobile_navbar' ][ 'type' ],
        'menu_class' => 'nav',
        'fallback_cb' => false,
        'echo' => false,
        'depth' => 1
      ));
    }

    $flawless[ 'navbar' ][ 'html' ] = apply_filters( 'flawless::navbar_html' , $flawless[ 'navbar' ][ 'html' ] );
    $flawless[ 'mobile_navbar' ][ 'html' ] = apply_filters( 'flawless::mobile_navbar_html' , $flawless[ 'mobile_navbar' ][ 'html' ] );

    if( $flawless[ 'navbar' ][ 'show_editlayout' ] == 'true' ) {

      /**Place edit layout in the navbar*/
     if ( current_user_can( 'manage_options' ) && !is_admin() ) {
       array_push( $flawless[ 'navbar' ][ 'html' ] , '<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">' . __( 'Manage', 'flawless' ) . ' <b class="caret"></b></a><ul class="dropdown-menu"><li><a class="flawless_edit_layout" href="#">' . __( 'Edit Layout', 'flawless' ) . '</a></li><li><a class="toggle_visual_debug" href="#">' . __( 'Toggle Visual Debug', 'flawless' ) . '</a></li></ul></li>' );
     }

   }

    if( is_array( $flawless[ 'navbar' ][ 'html' ] ) ) {
      foreach ( $flawless[ 'navbar' ][ 'html' ] as $key => &$value ) {

        if( empty( $value ) ){
          unset( $flawless[ 'navbar' ][ 'html' ][ $key ] );
        }

        if( is_array( $value ) ) {
          $value = implode( '' , $value );
        }

        $class = $key == "right" ? "pull-right" : "";
        $value = "<div class=\"nav-collapse {$class}\"><ul class=\"nav\">{$value}</ul></div>";

      }
    }

    if( is_array( $flawless[ 'mobile_navbar' ][ 'html' ] ) ) {
      foreach ( $flawless[ 'mobile_navbar' ][ 'html' ] as $key => &$value ) {

        if( empty( $value ) ){
          unset( $flawless[ 'mobile_navbar' ][ 'html' ][ $key ] );
        }

        if( is_array( $value ) ) {
          $value = implode( '' , $value );
        }

        $class = $key == "right" ? "pull-right" : "";
        $value = "<div class=\"nav-collapse nav-collapse-mobile {$class}\"><ul class=\"nav\">{$value}</ul></div>";

      }
    }

    //** Clean up Navbar */
    $flawless[ 'navbar' ][ 'html' ] = array_filter( ( array ) $flawless[ 'navbar' ][ 'html' ] );
    $flawless[ 'mobile_navbar' ][ 'html' ] = array_filter( ( array ) $flawless[ 'mobile_navbar' ][ 'html' ] );

    if( !empty( $flawless[ 'navbar' ][ 'html' ] ) ) {
      $flawless[ 'current_view' ][ 'body_classes' ][] = 'have-navbar';
    } else {
      unset( $flawless[ 'navbar' ][ 'html' ]  );

    }

    if( !empty( $flawless[ 'mobile_navbar' ][ 'html' ] ) ) {
      $flawless[ 'current_view' ][ 'body_classes' ][] = 'have-mobile-navbar';
    } else {
      unset( $flawless[ 'mobile_navbar' ][ 'html' ]  );
    }

    if( $flawless[ 'navbar' ][ 'html' ] || $flawless[ 'mobile_navbar' ][ 'html' ] ) {
      add_action( 'header-navbar', array( 'flawless_theme', 'render_navbars' ));
    }

  }


  /**
   * Not Used. Adds an item to the Navbar.
   *
   * Needs to be called before init ( 500 )
   *
   * @todo Finish function and update the way Navbar items are added. - potanin@UD 4/17/12
   * @since Flawless 0.5.0
   */
  function add_to_navbar( $html, $args = false ) {
    global $flawless;

    $args = wp_parse_args( $args, array(
      'order' => 100,
      'position' => 'left',
      'navbar' => array( 'navbar' )
    ));

    foreach( ( array ) $args[ 'navbar' ] as $navbar_type ) {
      $flawless[ $navbar_type ][ 'html' ] .= 'start' . $html . '|' ;
    }

  }


  /**
   * Renders the Navbar form the template part.
   *
   * @since Flawless 0.3.5
   */
  function render_navbars( $args = false ) {
    global $flawless;

    $args = wp_parse_args( $args, array(
      'echo' => true
    ));

    //** Prepare for rendering as a string. */
    $flawless[ 'navbar' ][ 'html' ] = implode( '', ( array ) $flawless[ 'navbar' ][ 'html' ] );
    $flawless[ 'mobile_navbar' ][ 'html' ] = implode( '', ( array ) $flawless[ 'mobile_navbar' ][ 'html' ] );

    ob_start();
    get_template_part( 'header-navbar' );
    $html = ob_get_contents();
    ob_end_clean();

    if( !$args[ 'echo' ] ) {
      return $html;
    }

    echo $html;

  }


  /**
   * Add all the body classes
   *
   * @since Flawless 0.2.5
   * @author potanin@UD
   */
  static function body_class( $classes, $class ) {
    global $flawless;

    //** Added classes to body */
    foreach( ( array ) $flawless[ 'current_view' ][ 'body_classes' ] as $class ) {
      $classes[] = $class;
    }

    if( $flawless[ 'visual_debug' ] == 'true' ) {
      $classes[] = 'flawless_visual_debug';
    }

    $classes = apply_filters( 'flawless::body_class', $classes, $class );

    return array_unique( $classes );

  }


  /**
   * Tweaks the default title. In most cases a specialty plugin will be used.
   *
   * @since Flawless 0.3.7
   */
  static function wp_title( $current_title, $sep, $seplocation ) {

    $title = array();

    if( is_home() || is_front_page() ) {
      $title[] = get_bloginfo( 'name' );

      if( get_bloginfo( 'description' ) ) {
        $title[] = get_bloginfo( 'description' );
      }

    } else {
      $title[] = $current_title;
      $title[] = get_bloginfo( 'name' );
    }

    return trim( implode( ' - ', $title ) );

  }


  /**
   * Filters a post permalink to replace the tag placeholder with the first
   * used term from the taxonomy in question.
   *
   * @source http://www.viper007bond.com/2011/10/07/code-snippet-helper-class-to-add-custom-taxonomy-to-post-permalinks/
   * @since Flawless 0.5.0
   */
  static function filter_post_link( $permalink, $post ) {
    global $flawless;

    foreach( (array) $flawless[ 'taxonomies' ] as $taxonomy => $data ) {

      if ( isset( $data[ 'rewrite_tag'] ) && false === strpos( $permalink, $data[ 'rewrite_tag'] ) ) {
        continue;
      }

      $terms = get_the_terms( $post->ID, $taxonomy );
      if ( empty( $terms ) ) {
        $permalink = str_replace( !empty( $data[ 'rewrite_tag'] ) ? $data[ 'rewrite_tag'] : '', $taxonomy, $permalink );
      } else {
        $first_term = array_shift( $terms );
        $permalink = str_replace( !empty( $data[ 'rewrite_tag'] ) ? $data[ 'rewrite_tag'] : '', $first_term->slug, $permalink );
      }

    }

    return $permalink;

  }


  /**
   * Adds content-specific classes
   *
   */
  static function post_class( $classes ) {

    if ( has_post_thumbnail() ) {
      $classes[] = 'has-img';
    } else {
      $classes[] = 'has-not-img';
    }

    return $classes;

  }


  /**
   * Front-end Header Things
   *
   * @since Flawless 0.2.3
   */
  static function wp_head() {
    global $flawless, $is_iphone, $is_IE;

    flawless_theme::console_log( 'P: Executed: flawless_theme::wp_head();' );

    //** Check for and load favico.ico */
    if( file_exists( STYLESHEETPATH . '/favicon.ico' ) ) {
      $html[] = '<link rel="shortcut icon" href="' . get_bloginfo( 'stylesheet_directory' ) . '/favicon.ico" type="image/x-icon" />';
    };

    //** Load JS Config */
    $js_config[ 'ajax_url' ] = admin_url( 'admin-ajax.php' );
    $js_config[ 'message_submission' ] = __( 'Thank you for your message.', 'flawless' );
    $js_config[ 'header' ] = $flawless[ 'header' ] ? $flawless[ 'header' ] : array();
    $js_config[ 'location_name' ] = !empty( $flawless[ 'name' ] ) ? $flawless[ 'name' ] : __( 'Our location.', 'flawless' );
    $js_config[ 'remove_empty_widgets' ] = $flawless[ 'do_not_remove_empty_widgets' ] == "true" ? false : true;
    $js_config[ 'location_coords' ] = array(
      'latitude' => $flawless[ 'latitude' ],
      'longitude' => $flawless[ 'longitude' ],
    );

    if( $is_iphone ) {
      $js_config[ 'is_iphone' ] = true;
    }

    if( $is_IE ) {
      $js_config[ 'is_ie' ] = true;
    }

    if( $flawless[ 'developer_mode' ] == 'true' ) {
      $js_config[ 'developer_mode' ] = true;
    };

    if( current_user_can( 'manage_options' ) ) {
      $js_config[ 'is_admin' ] = true;
      $js_config[ 'nonce' ] = wp_create_nonce( 'flawless_action' );
    };

    $js_config = apply_filters( 'flawless_theme_js_config' , $js_config );

    if( is_array( $js_config ) ) {
      $html[] = '<script type="text/javascript">var flawless = jQuery.extend( true, jQuery.parseJSON( ' . json_encode( json_encode( $js_config ) ) . ' ), typeof flawless === "object" ? flawless : {} ); </script>';
    };

    if( is_array( $html ) ) {
      echo implode( "\n", $html );
    };

    echo '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>';

    if( file_exists( untrailingslashit( get_stylesheet_directory() ) . '/apple-touch-icon.png' ) ) {
      echo '<link type="image/png" rel="apple-touch-icon" href="' . untrailingslashit( get_stylesheet_directory_uri() ) . '"/>';
    }

  }


  /**
   * Scans asset directories for available color schemes.
   *
   * Scans child theme first.
   *
   * @since Flawless 0.2.3
   */
  static function get_color_schemes() {
    global $flawless;

    //** Reverse so child theme gets scanned first */
    $skin_directories = apply_filters( 'flawless::skin_directories', array_reverse( $flawless[ 'asset_directories' ] ) );

    foreach( (array) $skin_directories as $path => $url ) {

      if( !is_dir( $path ) || !$resource = opendir( $path  ) ) {
        continue;
      }

      while ( false !== ( $file = readdir( $resource ) ) ) {

        if ( $file == "." || $file == ".." || strpos( $file, 'skin-' ) !== 0 || substr( strrchr( $file, '.' ), 1 ) != 'css' ) {
          continue;
        }

        $file_data = array_filter( (array) @get_file_data( $path . '/' . $file, $flawless[ 'default_header' ][ 'color_schemes' ], 'flawless_color_css' ) );

        if( empty( $file_data ) ) {
          continue;
        }

        $file_data[ 'css_path' ] = $path . '/' . $file;
        $file_data[ 'css_url' ] = $url . '/' . $file;

        $potential_thumbnails = array(
          str_replace( '.css', '.jpg', $file ),
          str_replace( '.css', '.png', $file )
        );

        if( !empty( $file_data[ 'thumbnail' ] ) ) {
          $potential_thumbnails[] = $file_data[ 'thumbnail' ];
          array_reverse( $potential_thumbnails );
        }

        foreach( (array) $potential_thumbnails as $thumbnail_filename ) {
          foreach( (array) $skin_directories as $thumb_path => $thumb_url ) {
            if( file_exists( trailingslashit( $thumb_path ) . '/' . $thumbnail_filename ) ) {
              $file_data[ 'thumb_url' ] =  $thumb_url  . '/' . $thumbnail_filename;
              break;
            }
          }
        }

        if( !isset( $files[ $file ] ) ) {
          $files[ $file ] = array_filter( ( array) $file_data );
        }

      }
    }

    $files = array_filter( (array) $files );

    if( empty( $files ) ) {
      return false;
    }

    return $files;

  }


  /**
   * Draw the custom site background
   *
   * Run on Flawless options update to validate blog owner's address for map on front-end.
   *
   * @todo Add function to check if background image actually exists and is reachable. - potanin@UD
   * @since Flawless 0.2.3
   */
  static function custom_background() {

    $background = get_background_image();
    $color = get_background_color();
    $position = get_theme_mod( 'background_position_x', 'left' );
    $attachment = get_theme_mod( 'background_attachment', 'scroll' );
    $repeat = get_theme_mod( 'background_repeat', 'no-repeat' );

    if ( ! $background && ! $color ) {
      return;
    }

    $style = array();

    if( $color ) {
      $style[] = "background-color: #$color;";
    }

    if( !empty( $background ) ) {
      $style[] = " background-image: url( '$background' );";

      if ( !in_array( $repeat, array( 'no-repeat', 'repeat-x', 'repeat-y', 'repeat' ) ) ) {
        $repeat = ' no-repeat ';
      }

      $style[] = " background-repeat: $repeat;";

      if ( ! in_array( $position, array( 'center', 'right', 'left' ) ) ) {
        $position = ' center ';
      }

      $style[] = " background-position: top $position;";

      if ( ! in_array( $attachment, array( 'fixed', 'scroll' ) ) ) {
        $attachment = ' scroll ';
      }

      $style[] = " background-attachment: $attachment;";

    }

    echo '<style type="text/css">body { ' . trim( implode( '', ( array ) $style ) ) . ' }</style>';

  }

  /**
   * Display area for background image in back-end
   *
   *
   * @since Flawless 0.2.3
   */
  function admin_image_div_callback() { ?>

    <h3><?php _e( 'Background Image' ); ?></h3>
    <table class="form-table">
    <tbody>
    <tr valign="top">
    <th scope="row"><?php _e( 'Preview' ); ?></th>
    <td>
    <?php
    $background_styles = '';
    if ( $bgcolor = get_background_color() )
      $background_styles .= 'background-color: #' . $bgcolor . ';';

    if ( get_background_image() ) {
      // background-image URL must be single quote, see below
      $background_styles .= ' background-image: url(\'' .  get_background_image() . '\' );'
        . ' background-repeat: ' . get_theme_mod( 'background_repeat', 'no-repeat' ) . ';'
        . ' background-position: top ' . get_theme_mod( 'background_position_x', 'left' );
    }
    ?>
    <div id="custom-background-image" style=" min-height: 200px;<?php echo $background_styles; ?>"><?php // must be double quote, see above ?>

    </div>
    <?php

  }


  /**
   * Adds a widget to a sidebar.
   *
   * Adds a widget to a sidebar, making sure that sidebar doesn't already have this widget.
   *
   * Example usage:
   * flawless_theme::add_widget_to_sidebar( 'global_property_search', 'text', array( 'title' => 'Automatically Added Widget', 'text' => 'This widget was added automatically' ));
   *
   * @todo Some might exist that adds widgets twice.
   * @todo Consider moving functionality to UD Class
   *
   * @since Flawless 0.2.3
   */
   static function add_widget_to_sidebar( $sidebar_id = false, $widget_id = false, $settings = array(), $args = '' ) {
    global $wp_registered_widget_updates, $wp_registered_widgets;

    extract( wp_parse_args( $args,  array(
      'do_not_duplicate' => 'true'
    ) ), EXTR_SKIP );

    require_once( ABSPATH . 'wp-admin/includes/widgets.php' );

    do_action( 'load-widgets.php' );
    do_action( 'widgets.php' );
    do_action( 'sidebar_admin_setup' );

    //** Need some validation here */
    if( !$sidebar_id ) {
      return false;
    }

     if( !$widget_id ) {
      return false;
    }

    if( empty( $settings ) ) {
      return false;
    }

    //** Load sidebars */
    $sidebars = wp_get_sidebars_widgets();

    //** Get widget ID */
    $widget_number  = next_widget_id_number( $widget_id );

    if( is_array( $sidebars[$sidebar_id] ) ) {
      foreach( (array) $sidebars[$sidebar_id] as $this_sidebar_id => $sidebar_widgets ) {

        //** Check if this sidebar already has this widget */
        if( strpos( $sidebar_widgets, $widget_id ) === false ) {
          continue;
        }

        $widget_exists = true;

      }
    }

    if( $do_not_duplicate == 'true' && $widget_exists ) {
      return true;
    }

    foreach ( ( array ) $wp_registered_widget_updates as $name => $control ) {

      if ( $name == $widget_id ) {
        if ( !is_callable( $control[ 'callback' ] ) ) {
          continue;
        }

        ob_start();
          call_user_func_array( $control[ 'callback' ], $control[ 'params' ] );
        ob_end_clean();
        break;
      }
    }

    //** May not be necessary */
    if ( $form = $wp_registered_widget_controls[$widget_id] ) {
      call_user_func_array( $form[ 'callback' ], $form[ 'params' ] );
    }

    //** Add new widget to sidebar array */
    $sidebars[$sidebar_id][] = $widget_id . '-' . $widget_number;

    //** Add widget to widget area */
    wp_set_sidebars_widgets( $sidebars );

    //** Get widget configuration */
    $widget_options = get_option( 'widget_' . $widget_id );

    //** Check if current widget has any settings ( it shouldn't ) */
    if( $widget_options[$widget_number] ) {
    }

    //** Update widget with settings */
    $widget_options[$widget_number] = $settings;

    //** Commit new widget data to database */
    update_option( 'widget_' . $widget_id, $widget_options );


    return true;

   }



   /**
    * Adds an option to post editor
    *
    * Must be called early, before admin_init
    *
    * @since Flawless 0.2.3
    */
    static function add_post_type_option( $args = array() ) {
      global $flawless;

      $args = wp_parse_args( $args, array(
        'post_type' => 'page',
        'label' => '',
        'input_class' => 'regular-text',
        'placeholder' => '',
        'meta_key' => '',
        'type' => 'checkbox'
      ));

      if( !is_array( $args[ 'post_type' ] ) ) {
        $args[ 'post_type' ] = array( $args[ 'post_type' ] );
      }

      foreach( (array) $args[ 'post_type' ] as $post_type ) {
        $flawless[ 'ui_options' ][ $post_type ][ $args[ 'meta_key' ] ] = $args;
      }

      //** Create filter to render input */
      add_action( 'save_post', array( 'flawless_theme', 'save_post' ), 10, 2 );

      //** Create filter to save / update */
      add_action( 'post_submitbox_misc_actions', array( 'flawless_theme', 'post_submitbox_misc_actions' ));

    }


   /**
    * Saves extra post information
    *
    * @since Flawless 0.2.3
    */
    function save_post( $post_id, $post ) {
      global $pagenow;

      if( isset( $_REQUEST[ 'flawless_option' ] ) ) {

        foreach( ( array ) $_REQUEST[ 'flawless_option' ] as $meta_key => $value ) {

          if( $value == 'false' || empty( $value ) ) {
            delete_post_meta( $post_id, $meta_key );
          } else {
            update_post_meta( $post_id, $meta_key, $value );
          }

        }

      }

      //** Verify that this is in the allowed pages array. */
      if ( ! in_array( $pagenow, flawless_theme::pt_allowed_pages() ) ){
        return;
      }

      //** Verify if this is an auto save routine.  */
      if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){
        return;
      }

      if ( wp_is_post_revision( $post ) ) {
        return;
      }

      //** Return if option box is not selected. */
      if ( ! isset( $_POST['cpt-nonce-select'] ) ){
        return;
      }

      //** Return if selected nonce was used within time limit.  */
      if ( ! wp_verify_nonce( $_POST['cpt-nonce-select'], 'post-type-selector' ) ){
        return;
      }

      //** Return if user cannot edit post. */
      if ( ! current_user_can( 'edit_post', $post_id ) ){
        return;
      }

      //** Return if new post type matches current post type. */
      if ( $_POST['flawless_cpt_post_type'] == $post->post_type ){
        return;
      }

      //** Return if post type slug returned null. */
      if ( ! $new_post_type_object = get_post_type_object( $_POST['flawless_cpt_post_type'] ) ){
        return;
      }

      //** Return if current user cannot publish posts. */
      if ( ! current_user_can( $new_post_type_object->cap->publish_posts ) ){
        return;
      }

      //** Updates the post type for the new post ID.  */
      set_post_type( $post_id, $new_post_type_object->name );

    }

    /**
    * @author odokienko@UD
    */
    function flawless_signup_field_check(  ) {
      global $wpdb;

      $field_name = $_REQUEST[ 'field_name' ];
      $field_value = $_REQUEST[ 'field_value' ];
      $field_type = $_REQUEST[ 'field_type' ];
      $response = array(
        'success' => 'true'
      );

      switch ( $field_name ){
        case "signup_username":
          $user_exists = $wpdb->get_row( "SELECT * FROM {$wpdb->users} WHERE `user_login` = '{$field_value}' limit 1" );

          if( !empty( $user_exists ) ){
            $response = array(
              'success' => 'false',
              'message' => __( 'Sorry, that username already exists!', 'flawless' )
            );
          }
          break;
        case "signup_email":
          $user_exists = $wpdb->get_row( "SELECT * FROM {$wpdb->users} WHERE `user_email` = '{$field_value}' limit 1" );

          if( !empty( $user_exists ) ){
            $response = array(
              'success' => 'false',
              'message' => __( 'Sorry, that email address is already used!', 'flawless' ),
              'setfocus' => '.flawless_login_form input[name=log]'
            );
          }
          break;

        default:

      }

      die( json_encode( $response ));
    }


   /**
    * Render any options for this post type on editor page
    *
    * @since Flawless 0.2.3
    */
    function post_submitbox_misc_actions() {
      global $post, $flawless, $pagenow;

      $cur_post_type_object =	get_post_type_object( $post->post_type );

      if( !$cur_post_type_object->public ) {
        return;
      }

      /** Create form for switching the post type */
      if ( current_user_can( $cur_post_type_object->cap->publish_posts ) && in_array( $pagenow, flawless_theme::pt_allowed_pages() ) ) { ?>

      <div class="misc-pub-section misc-pub-section-last change-post-type">
        <label for="flawless_cpt_post_type"><?php _e( 'Post Type:', 'flawless' ); ?></label>
        <span id="post-type-display" class="flawless_cpt_display"><?php echo $cur_post_type_object->labels->singular_name; ?></span>

        <a href="#" id="edit-post-type-change" class="hide-if-no-js"><?php _e( 'Edit' ); ?></a>
        <?php wp_nonce_field( 'post-type-selector', 'cpt-nonce-select' ); ?>
        <div id="post-type-select" class="flawless_cpt_select">
          <select name="flawless_cpt_post_type" id="flawless_cpt_post_type">
          <?php foreach ( (array) get_post_types( (array) apply_filters( 'flawless_cpt_metabox', array( 'public' => true, 'show_ui' => true ) ), 'objects' ) as $pt ) { if ( !current_user_can( $pt->cap->publish_posts ) ) { continue; }
            echo '<option value="' . esc_attr( $pt->name ) . '"' . selected( $post->post_type, $pt->name , false ) . '>' . $pt->labels->singular_name . "</option>\n";
          } ?>
          </select>
          <a href="#" id="save-post-type-change" class="hide-if-no-js button"><?php _e( 'OK' ); ?></a>
          <a href="#" id="cancel-post-type-change" class="hide-if-no-js"><?php _e( 'Cancel' ); ?></a>
        </div>
      </div>
      <?php
      }

      if( !is_array( $flawless[ 'ui_options' ][ $post->post_type ] ) ) {
        return;
      }

      usort( $flawless[ 'ui_options' ][$post->post_type], create_function( '$a,$b', ' return $a["position"] - $b["position"]; ' ));

      foreach( ( array ) $flawless[ 'ui_options' ][$post->post_type] as $option ) {

        switch ( $option[ 'type' ] ) {

          case 'checkbox':

          $html[] = '<li class="post_option_' . $option[ 'meta_key' ] . '">' . sprintf( '<input type="hidden" name="%1s" value="false" /><label><input type="checkbox" name="%2s" value="true" %3s /> %4s</label>',
            'flawless_option[' . $option[ 'meta_key' ] . ']',
            'flawless_option[' . $option[ 'meta_key' ] . ']',
            checked( 'true', get_post_meta( $post->ID, $option[ 'meta_key' ], true ), false ),
            $option[ 'label' ]
          ) . '</li>';

          break;

          case 'datetime':

            wp_enqueue_script( 'jquery-ui-datepicker' );

            $meta_value = trim( esc_attr( implode( ', ', ( array ) get_post_meta( $post->ID, $option[ 'meta_key' ] ) ) ) );

            if( is_numeric( $meta_value ) && (int) $meta_value == $meta_value && strlen( $value ) == 10 ) {
              $meta_value = date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) , $meta_value );
            }

            $html[] = '<li class="post_option_' . $option[ 'meta_key' ] . '">' . sprintf( '<label><span class="regular-text-label">%1s:</span> <input class="flawless_datepicker %2s" type="text" placeholder="%3s" name="%4s" value="' . $meta_value . '"  /></label>',
            $option[ 'label' ],
            $option[ 'input_class' ],
            $option[ 'placeholder' ] ? $option[ 'placeholder' ] : '',
            'flawless_option[' . $option[ 'meta_key' ] . ']', $meta_value ) . '</li>';

          break;

          case 'input':
          default:

          $meta_value = trim( esc_attr( implode( ', ', ( array ) get_post_meta( $post->ID, $option[ 'meta_key' ] ) ) ) );

          $html[] = '<li class="post_option_' . $option[ 'meta_key' ] . '">' . '<label><span class="regular-text-label">' .  $option[ 'label' ] . ':</span>
          <input class="' . $option[ 'input_class' ] . '" type="text" placeholder="' . esc_attr( $option[ 'placeholder' ] ) .'" name="flawless_option[' . esc_attr( $option[ 'meta_key' ] ) . ']" value="' . esc_attr( $meta_value ) . '"  /></label></li>';

          break;

        }

      }

      if( is_array( $html ) ) {
        echo '<ul class="flawless_post_type_options wp-tab-panel">' . implode( "\n", $html ) . '</ul>';
      }

    }


    /**
     * Return the allowed pages that $pagenow global is allowed to be.
     *
     */
    function pt_allowed_pages() {
      $pages = array(
        'post.php'
      );
      return $pages;
    }


   /**
    * Remove all instanced of a widget from a sidebar
    *
    * Adds a widget to a sidebar, making sure that sidebar doesn't already have this widget.
    *
    * @since Flawless 0.2.3
    */
   function remove_widget_from_sidebar( $sidebar_id, $widget_id ) {
     global $wp_registered_widget_updates;

    //** Load sidebars */
    $sidebars = wp_get_sidebars_widgets();

    //** Get widget ID */
    if( is_array( $sidebars[$sidebar_id] ) ) {
      foreach( (array) $sidebars[$sidebar_id] as $this_sidebar_id => $sidebar_widgets ) {

        //** Check if this sidebar already has this widget */

        if( strpos( $sidebar_widgets, $widget_id ) === 0 || $widget_id == 'all' ) {

          //** Remove widget instance if it exists */
          unset( $sidebars[$sidebar_id][$this_sidebar_id] );

        }

      }
    }


    //** Save new siebars */
    wp_set_sidebars_widgets( $sidebars );
   }

  /**
   * Displays first-time setup splash screen
   *
   *
   * @since Flawless 0.2.3
   */
  static function admin_init() {
    global $wp_registered_widget_updates, $wpdb, $flawless;

    //** Load defaults on theme activation */
    if( current_user_can( 'update_themes' ) ) {
      flawless_theme::handle_upgrade();
    }

    //** Load back-end JS and Contextual Help */
    add_action( 'admin_enqueue_scripts', array( 'flawless_theme', 'admin_enqueue_scripts' ), 10 );
    add_action( 'admin_print_footer_scripts', array( 'flawless_theme', 'admin_print_footer_scripts' ), 10 );

    //** Check if child thme exists and updates flawless_settings accordingly */
    flawless_theme::flawless_child_theme_exists();

    //** Check for special actions and nonce, a nonce must always be set. */
    if( !empty( $_REQUEST[ '_wpnonce' ] ) /* && isset( $_REQUEST[ 'flawless_action' ] ) */ ) {

      if( wp_verify_nonce( $_REQUEST[ '_wpnonce' ], 'flawless_settings' ) ) {

        $args = array();

        //** Handle Theme Backup Upload */
        if( $backup_file = $_FILES[ 'flawless_settings' ][ 'tmp_name' ][ 'settings_from_backup' ] ) {
          $backup_contents = file_get_contents( $backup_file );

          if( !empty( $backup_contents ) ) {
            $decoded_settings = json_decode( $backup_contents, true );

            if( !empty( $decoded_settings ) ) {
              $_REQUEST[ 'flawless_settings' ] = $decoded_settings;
              $args[ 'message' ] = 'backup_restored';
            } else {
              $args[ 'message' ] = 'backup_failed';
            }

          }
        }

        //** Handle Theme Options updating */
        if( $redirect = flawless_theme::save_settings( $_REQUEST[ 'flawless_settings' ], $args ) ) {
          $redirect = add_query_arg( 'flush_rewrite_rules', 'true', $redirect );
          wp_redirect( $redirect );
          die();
        }

      }

      //** Download back up configuration */
      if( wp_verify_nonce( $_REQUEST[ '_wpnonce' ], 'download-flawless-backup' ) ) {

        header( 'Cache-Control: public' );
        header( 'Content-Description: File Transfer' );
        header( 'Content-Disposition: attachment; filename=' . sanitize_key( get_bloginfo( 'name' ) ) . '-flawless.' . date( 'Y-m-d' ) . '.json' );
        header( 'Content-Transfer-Encoding: binary' );
        header( 'Content-Type: text/plain; charset=' . get_option( 'blog_charset' ), true );

        die( json_encode( $flawless ));
      }

    }


    //** Has to be run every time for custom taxonomy URLs to work, when permalinks are used. */
    if( isset( $_REQUEST[ 'flush_rewrite_rules' ] ) && $_REQUEST[ 'flush_rewrite_rules' ] == 'true' ) {
      flush_rewrite_rules( false, 'flawless' );
    } elseif( $flawless[ 'using_permalinks' ] ) {
      flush_rewrite_rules( false, 'flawless' );
    }

    do_action( 'flawless::admin_init' );

  }


  /**
   * Adds custom logo, if exists, to login screen.
   *
   * @since Flawless 0.2.3
   */
  static function login_head() {
    global $flawless;

    if( !flawless_theme::can_get_image( $flawless[ 'flawless_logo' ][ 'url' ] ) ) {
      return;
    }

    echo '<style type="text/css" media="screen">.login h1 a, #login { min-width: 300px; width: ' . $flawless[ 'flawless_logo' ][ 'width' ] . 'px; } .login h1 a { background-size:' . $flawless[ 'flawless_logo' ][ 'width' ] . 'px ' . $flawless[ 'flawless_logo' ][ 'height' ] . 'px; background-image: url( '.$flawless[ 'flawless_logo' ][ 'url' ].' ); margin-bottom: 10px;} </style>';

  }


  /**
   * Save Theme Options
   *
   * Called after nonce is verified.
   *
   * @return string or false.  If string, a URL to be used for redirection.
   * @since Flawless 0.2.3
   */
  static function save_settings( $flawless, $args = array() ) {

    $current_settings = stripslashes_deep( get_option( 'flawless_settings' ));

    $args = wp_parse_args( $args, array(
      'message' => 'settings_updated'
    ));

    //** Set logo */
    if( !empty( $_FILES[ 'flawless_logo' ][ 'name' ] ) ) {

      $file = wp_handle_upload( $_FILES[ 'flawless_logo' ], array( 'test_form' => false ));

      if( !$file[ 'error' ] && $file[ 'url' ] && $image_size = getimagesize( $file[ 'file' ] ) ) {

        $post_id = wp_insert_attachment( array(
          'post_mime_type' => $file[ 'type' ],
          'guid' => $file[ 'url' ],
          'post_title' => sprintf( __( '%1s Logo', 'flawless' ), get_bloginfo( 'name' ) )
        ), $file[ 'file' ] );

        if ( !is_wp_error( $post_id ) ) {
          $flawless[ 'flawless_logo' ][ 'post_id' ] = $post_id;

          //** Delete old logo */
          if( is_numeric( $current_settings[ 'flawless_logo' ][ 'post_id' ] ) ) {
            wp_delete_attachment( $current_settings[ 'flawless_logo' ][ 'post_id' ], true );
          }

          update_post_meta( $flawless[ 'flawless_logo' ][ 'post_id' ] , '_wp_attachment_metadata', array( 'width' => $image_size[0], 'height' => $image_size[1] ));
        }

      } else {
        unset( $flawless[ 'flawless_logo' ] );

      }

    }

    //** Cycle through settings and copy over any special keys */
    foreach( ( array ) apply_filters( 'flawless_preserved_setting_keys', array( 'flex_layout' ) ) as $key ) {
      $flawless[$key] = !empty( $flawless[$key] ) ? $flawless[$key] : $current_settings[$key];
    }

    $flawless = apply_filters( 'flawless_update_settings', $flawless );

    update_option( 'flawless_settings', $flawless );

    flush_rewrite_rules();

    //** Redirect page to default Theme Settings page */
    return admin_url( 'themes.php?page=functions.php&message=' . $args[ 'message' ] );

  }


  /**
   * Adds "Theme Options" page on back-end
   *
   * Used for configurations that cannot be logically placed into a built-in Settings page
   *
   * @todo Update 'auto_complete_done' message to include a link to the front-end for quick view of setup results.
   * @since Flawless 0.2.3
   */
  static function options_page() {
    global $flawless, $_wp_theme_features, $flawless;

    if( !empty( $_GET[ 'admin_splash_screen' ] ) ) {
      flawless_theme_ui::show_update_screen( $_GET[ 'admin_splash_screen' ] );
      return;
    }

    if( $_REQUEST[ 'message' ] == 'auto_complete_done' ) {
      $updated = __( 'Your site has been setup.  You may configure more advanced options here.', 'flawless' );
    }

    if( $_REQUEST[ 'message' ] ) {

      switch( $_REQUEST[ 'message' ] ) {

        case 'settings_updated':
        $updated = __( 'Theme settings updated.', 'flawless' );
        break;

        case 'backup_restored':
        $updated = __( 'Theme backup has been restored from uploaded file.', 'flawless' );
        break;

        case 'backup_failed':
        $updated = __( 'Could not restore configuration from backup, file data was not in valid JSON format.', 'flawless' );
        break;

      }
    }

    echo '<style type="text/css">' . implode( '', (array) $theme_feature_styles ) . '</style>';

    ?>

    <div id="flawless_settings_page" class="wrap flawless_settings_page">

      <h2 class="placeholder_title"></h2>

      <?php if( $updated ) { ?>
      <div class="updated fade"><p><?php echo $updated; ?></p></div>
      <?php } ?>

      <form action="<?php echo admin_url( 'themes.php?page=functions.php&flawless_action=update_settings' ); ?>" method="post" enctype="multipart/form-data">

        <input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'flawless_settings' ); ?>" />

        <div class="flawless_settings_tabs">

          <div class="icon32" id="icon-themes"><br></div>

          <ul class="tabs">
           <?php foreach( (array) $flawless[ 'options_ui' ][ 'tabs' ] as $tab ) {  ?>
            <li><a class="nav-tab" href="#flawless_tab_<?php echo $tab[ 'id' ]; ?>"><?php echo $tab[ 'label' ]; ?></a></li>
          <?php } ?>
          </ul>

        <?php foreach( (array) $flawless[ 'options_ui' ][ 'tabs' ] as $tab ) { ?>
          <div id="flawless_tab_<?php echo $tab[ 'id' ]; ?>" class="flawless_tab <?php echo $tab[ 'panel_class' ]; ?>">
            <?php call_user_func( $tab[ 'callback' ], $flawless ); ?>
          </div>
        <?php } ?>

        </div>

        <div class="flawless_below_tabs">
          <div class="submit_wrapper"><input type="submit" value="Save Changes" class="button-primary" name="Submit"/></div>
        </div>

      </form>
    </div>
    <?php }



    /**
     * Uses back-trace to figure out which sidebar was called from the sidebar.php file
     *
     * WordPress does not provide an easy way to figure out the type of sidebar that was called from within the sidebar.php file, so we backtrace it.
     *
     * @since Flawless 0.2.3
     * @author potanin@UD
     */
    function backtrace_sidebar_type() {

      $backtrace = debug_backtrace();

      if( !is_array( $backtrace ) ) {
        return false;
      }

      foreach( (array) $backtrace as $item ) {

        if( $item[ 'function' ] == 'flawless_widget_area' ) {
          return $item[ 'args' ][0];
        } elseif ( $item[ 'function' ] == 'get_sidebar' ) {
          return $item[ 'args' ][0];
        }

      }

      return false;

    }


  /**
   * Checks if script or style have been loaded.
   *
   * @todo Add handler for styles.
   * @since Flawless 0.2.0
   *
   */
  function is_asset_loaded( $handle = false ) {
    global $wp_scripts;

    if( empty( $handle ) ) {
      return;
    }

    $footer = ( array ) $wp_scripts->in_footer;
    $done = ( array ) $wp_scripts->done;

    $accepted = array_merge( $footer, $done );

    if( !in_array( $handle, $accepted ) ) {
      return false;
    }

    return true;

  }


  /**
   * PHP function to echoing a message to JS console
   *
   * @todo This needs to be improved.
   * @since Flawless 0.2.0
   */
  static function console_log( $entry = false ) {
    global $flawless;

    if( empty( $entry ) ) {
      return;
    }

    $new_entry = array(
      'entry' => $entry
    );

    if( function_exists( 'memory_get_peak_usage' ) && class_exists( 'Flawless_F' ) ) {
      $new_entry[ 'memory_usage' ] = self::format_bytes( memory_get_peak_usage() );
    }

    $flawless[ 'console_log' ][] = $new_entry;

  }


  /**
  * {}
  *
  * flawless_render_in_footer() depends on this to render any scripts / styles.
  *
  * @version 0.50.0
  */
  function wp_print_footer_scripts() {
    global $flawless;

    foreach( (array) $flawless[ 'runtime' ][ 'footer_scripts' ] as $script ) {
      echo $script;
    }

    echo flawless_theme::render_console_log();

  }


  /**
  * Prints JS for the console log when in debug mode in the footer.
  *
  * @version 0.25.0
  */
  static function render_console_log() {
    global $flawless;

    if( !isset( $flawless[ 'developer_mode' ] ) || $flawless[ 'developer_mode' ] != 'true' ) {
      return;
    }

    $html = array();

    $html[] = '<script type="text/javascript"> if( typeof console == "object" && typeof console.log == "function" )  {';
    foreach( ( array ) $flawless[ 'console_log' ] as $entry ) {

    if( is_array( $entry[ 'entry' ] ) || is_object( $entry[ 'entry' ] ) ) {
      $html[] = 'console.log( jQuery.parseJSON( ' . json_encode( json_encode( $entry[ 'entry' ] ) ) . ' ));';

    } else {
      $html[] = 'console.log( "' . $entry[ 'entry' ]. '" ); ';
    }

    }
    $html[] = '} </script>';

    echo implode( "\n" , ( array ) $html );

  }


  /**
  * Tests if remote script or CSS file can be opened prior to sending it to browser
  *
  *
  * @version 0.25.0
  */
  function can_get_asset( $url = false, $args = array() ) {
    global $flawless;

    if( empty( $url ) ) {
      return false;
    }

    $match = false;

    if( empty( $args ) ){
      $args[ 'timeout' ] = 10;
    }

    $result = wp_remote_get( $url, $args );

    if( is_wp_error( $result ) ) {
      return false;
    }

    $type = $result[ 'headers' ][ 'content-type' ];

    if( strpos( $type, 'javascript' ) !== false ) {
      $match = true;
    }

    if( strpos( $type, 'css' ) !== false ) {
      $match = true;
    }

    if( !$match || $result[ 'response' ][ 'code' ] != 200 ) {

      if( $flawless[ 'developer_mode' ] == 'true' ) {
        flawless_theme::console_log( "P: Remote asset ( $url ) could not be loaded, content type returned: ". $result[ 'headers' ][ 'content-type' ] );
      }

      return false;
    }

    return true;

  }

  /**
  * Tests if remote image can be loaded.  Returns URL to image if valid.
  *
  * @version 0.25.0
  */
  function can_get_image( $url = false ) {

    if( !is_string( $url ) ) {
      return false;
    }

    if( empty( $url ) ) {
      return false;
    }

    //** Test if post_id */
    if( is_numeric( $url ) && $image_attributes = wp_get_attachment_image_src( $url, 'full' ) ) {
      $url = $image_attributes[0];
    }

    $result = wp_remote_get( $url, array( 'timeout' => 10 ));

    if( is_wp_error( $result ) ) {
      return false;
    }

    //** Image content types should always begin with 'image' ( I hope ) */
    if( strpos( $result[ 'headers' ][ 'content-type' ], 'image' ) !== 0 ) {
      return false;
    }

    return $url;

  }


  /**
   * Installs a Flawless child theme.
   *
   * Copies files from /flawless-child folder into the them folder so denali child can be used.
   *
   * @todo Needs to be updated to select which files to copy, flawless-child directory no longer used.
   * @since Flawless 0.2.3
   */
  function install_child_theme() {
    global $user_ID, $wpdb, $wp_theme_directories;

    if( flawless_theme::flawless_child_theme_exists() ) {
      return true;
    }

    $destination_root = $wp_theme_directories[0];

    $original = TEMPLATEPATH . '/flawless-child';
    $original_images = TEMPLATEPATH . '/img';

     if( !file_exists( $original ) ) {
      return false;
     }

     if( !is_writable( $destination_root ) ) {
      return false;
     }

     $destination = $destination_root . '/flawless-child';
     $destination_images = $destination_root . '/flawless-child/img';

      //** Create destination folder */
      if ( !@mkdir( $destination, 0755 ) ) {
        return false;
      } else {
        @mkdir( $destination_images, 0755 );
      }


    //** Copy folders from denali/flawless-child into flawless-chlld
     if ( $original_handle = opendir( $original . '/' ) ) {
       while ( false !== ( $file = readdir( $original_handle ) ) ) {

        if ( $file != "." && $file != ".." ) {

          $file_path = $original . '/'. $file;

          /* Determine if it's directory, We don't copy it */
          if ( is_dir( $file_path ) ) {
            continue;
          }

          if( copy( $file_path, $destination . '/' . $file ) ) {
            $copied[] = $file;
          }  else {
            $not_copied[] = $file;
          }
        }

       }
     }

     //** Copy image files */
      if ( $images_handle = opendir( $original_images . '/' ) ) {
       while ( false !== ( $file = readdir( $images_handle ) ) ) {

        if ( $file == "." || $file == ".." ) {
          continue;
        }

        $file_path = $original_images . '/'. $file;

        /* Determine if it's directory, We don't copy it */
        if ( is_dir( $file_path ) ) {
          continue;
        }

        if( copy( $file_path, $destination_images . '/' . $file ) ) {
          $copied[] = $file;
        }  else {
          $not_copied[] = $file;
        }


       }
     }

    if( count( $copied ) > 0 ) {
      return true;
    }

    return false;

  }


  /**
   * Check if default denali child theme exists.
   *
   *
   * @since Flawless 0.2.3
   */
  static function flawless_child_theme_exists() {
    global $user_ID, $wpdb, $flawless;

    if( file_exists( ABSPATH . '/wp-content/themes/flawless-child' ) ) {
      $flawless[ 'install_flawless_child_theme' ] = 'true';
      update_option( 'flawless_settings', $flawless );
      return true;
    }

    return false;

  }


  /**
   * Checks if sidebar is active. Same as default function, but allows hooks
   *
   * @since Flawless 0.2.0
   */
  function is_active_sidebar( $sidebar ) {
    return is_active_sidebar( $sidebar );
  }


  /**
   * Draws a dropdown of objects, much like the regular wp_dropdown_objects() but with custom objects
   *
   * @todo Perhaps update function to return an auto-complete or ID input field when there are too many objects to render ina  dropdown.
   *
   */
  function wp_dropdown_objects( $args = '' ) {

    $defaults = array(
      'depth' => 0,
      'post_type' => 'page',
      'child_of' => 0,
      'selected' => 0,
      'echo' => 1,
      'name' => 'page_id',
      'id' => '',
      'show_option_none' => '',
      'show_option_no_change' => '',
      'option_none_value' => ''
    );

    $r = wp_parse_args( $args, $defaults );
    extract( $r, EXTR_SKIP );


    if( is_array( $post_type ) ) {
      $content_types = $post_type;
    } else {
      $content_types = array( $post_type );
    }

    foreach( (array) $content_types as $type ) {
      $post_type_obj = get_post_type_object( $type );
      $this_query = $r;
      $this_query[ 'post_type' ] = $type;

      $these_pages = get_pages( $this_query );

      if( $these_pages ) {
        $objects[$post_type_obj->labels->name] = $these_pages;
      }

    }

    if( empty( $objects ) ) {
      return false;
    }

    $output = array();

    $output[] = "<select name='" . esc_attr( $name ) . "' id='" . esc_attr( $id ) . "'>\n";

    if ( $show_option_no_change ) {
      $output[] = "\t<option value=\"-1\">$show_option_no_change</option>";
    }

    if ( $show_option_none ) {
      $output[] = "\t<option value=\"" . esc_attr( $option_none_value ) . "\">$show_option_none</option>\n";
    }

    foreach( (array) $objects as $object_type => $pages ) {

      if( count( $objects ) > 1 ) {
        $output[] =  '<optgroup label="' .  $object_type . '">';
      }

      $output[] = walk_page_dropdown_tree( $pages, $depth, $r );

      if( count( $objects ) > 1 ) {
        $output[] =  '</optgroup>';
      }

    }

    $output[] = "</select>\n";

    $output = apply_filters( 'wp_dropdown_pages', $output );

    if ( $echo ) {
      echo implode( ' ', $output );
    }

    return implode( ' ', $output );

  }


  /**
   * Modifies default WP Login form by adding extra classes
   *
   */
  function wp_login_form( $args = false ) {

    //* Must override */
    $args[ 'echo' ] = false;

    $form = wp_login_form( $args );

    //** Add our classes */
    $form = str_replace( 'name="log"', 'name="log" placeholder="Username"', $form );
    $form = str_replace( 'name="pwd"', 'name="pwd" placeholder="Password"', $form );

    echo $form;

  }


  /**
   * Returns false.
   *
   * This is used for add_filter() and remove_filter()
   *
   * @author potanin@UD
   */
  function return_false() {
    return false;
  }


  /**
   * Returns true.
   *
   * This is used for add_filter() and remove_filter()
   *
   * @author potanin@UD
   */
  function return_true() {
    return true;
  }


  /**
   * Loads 'third-party' library
   *
   * @param string $name.
   * @author peshkov@UD
   */
  function load( $name ) {

    //** Try to find the file. */
    $file = STYLESHEETPATH . "/libs/" . $name . ".php";

    if( ! file_exists( $file ) ) {
      $file = TEMPLATEPATH . "/libs/" . $name . ".php";
    }

    if( file_exists( $file ) ) {
      include_once( $file );
      return true;
    }

    return false;
  }


  /**
   * Utility Function in_array with a like comparison
   *
   * @author willis@UD
   */
  function in_array_like( $needle , $haystack ) {
    global $flawless;

    foreach ( $haystack as $key )
    {
        if( stripos( strtolower( $key ) , strtolower( $needle ) ) !== false )
        {
            return true;
        }
    }
    return false;
  }

}  /* end flawless_theme class */


do_action( 'flawless_loaded' );
