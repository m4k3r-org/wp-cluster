<?php
/**
 * Network Splash Theme
 *
 * @version 2.0.0
 * @author potanin@UD
 * @namespace Network
 */
namespace UsabilityDynamics\Theme {

  /**
   * Class Splash
   *
   * @property mixed init
   * @property mixed wp_enqueue_scripts
   *
   * @author potanin@UD
   * @class Splash
   * @package Network\Theme
   */
  class Splash {

    /**
     * Version of child theme
     *
     * @public
     * @property version
     * @var string
     */
    public static $version = '2.0.0';

    /**
     * Textdomain String
     *
     * @public
     * @property text_domain
     * @var string
     */
    public static $text_domain = 'Splash';

    public static $_errors = null;

    /**
     * Class Initializer
     *
     * @author potanin@UD
     * @for Splash
     */
    public function __construct() {
      add_action( 'admin_init', array( $this, 'admin_init' ) );
      add_action( 'admin_menu', array( $this, 'admin_menu' ) );
      add_action( 'init', array( $this, 'init' ) );
      add_action( 'wp_loaded', array( $this, 'loaded' ), 5, 0 );

      add_action( 'admin_print_scripts', array( $this, 'admin_print_scripts' ) );
      add_action( 'admin_print_styles', array( $this, 'admin_print_styles' ) );
      add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ), 20, 0 );
      //add_action( 'the_post', array( $this, 'the_post' ), 20, 0 );
      add_action( 'wp_print_styles', array( $this, 'wp_print_styles' ), 20, 0 );

      add_action( 'wp_head', array( $this, 'wp_head' ), 20, 0 );
      add_action( 'wp_print_footer_scripts', array( $this, 'wp_print_footer_scripts' ), 20, 0 );

      add_action( 'template_redirect', array( $this, 'template_redirect' ), 100 );
      add_action( 'after_setup_theme', array( $this, 'after_setup_theme' ), 100 );

      if( file_exists( dirname( __DIR__ ) . '/vendor/libraries/autoload.php' ) ) {
        include_once( dirname( __DIR__ ) . '/vendor/libraries/autoload.php' );
      }

    }

    /**
     *
     */
    public function after_setup_theme() {

      // This theme uses wp_nav_menu() in one location.
      register_nav_menus( array(
        'footer-icons' => __( 'Footer Icons', 'wpp' )
      ) );

      register_nav_menus( array(
        'header-icons' => __( 'Header Icons', 'wpp' )
      ) );

      set_post_thumbnail_size( 402, 301, true );

      add_theme_support( 'html5', array(
        'search-form', 'comment-form', 'comment-list', 'gallery', 'caption'
      ) );

      add_theme_support( 'post-thumbnails' );

      add_theme_support( 'featured-content', array(
        'featured_content_filter' => 'twentyfourteen_get_featured_posts',
        'max_posts'               => 6,
      ) );

      add_theme_support( 'custom-background', array(
        'default-color'    => 'e8e8e8',
        //'default-image' => '',,
        'default-repeat'   => 'no-repeat',
        //'default-position-x'     => 'center',
        //'default-attachment'     => 'scroll',
        'wp-head-callback' => array( $this, 'render_background' ),
        //'admin-head-callback'    => '',
        //'admin-preview-callback' => '',
      ) );

    }

    /**
     *
     */
    public function render_background() {

      // $background is the saved custom image, or the default image.
      $background = set_url_scheme( get_background_image() );

      // $color is the saved custom color.
      // A default has to be specified in style.css. It will not be printed here.
      $color = get_background_color();

      if( $color === get_theme_support( 'custom-background', 'default-color' ) ) {
        $color = false;
      }

      if( !$background && !$color )
        return;

      $style = $color ? "background-color: #$color;" : '';

      if( $background ) {
        $image = " background-image: url('$background');";

        $repeat = get_theme_mod( 'background_repeat', get_theme_support( 'custom-background', 'default-repeat' ) );
        if( !in_array( $repeat, array( 'no-repeat', 'repeat-x', 'repeat-y', 'repeat' ) ) )
          $repeat = 'repeat';
        $repeat = " background-repeat: $repeat;";

        $position = get_theme_mod( 'background_position_x', get_theme_support( 'custom-background', 'default-position-x' ) );
        if( !in_array( $position, array( 'center', 'right', 'left' ) ) )
          $position = 'left';
        $position = " background-position: top $position;";

        $attachment = get_theme_mod( 'background_attachment', get_theme_support( 'custom-background', 'default-attachment' ) );
        if( !in_array( $attachment, array( 'fixed', 'scroll' ) ) )
          $attachment = 'scroll';
        $attachment = " background-attachment: $attachment;";

        $style .= $image . $repeat . $position . $attachment;
      }
      ?>
      <style type="text/css" id="custom-background-css">
        body {
        <?php echo trim( $style ); ?>
        }
      </style>
    <?php

    }

    /**
     *
     */
    private function save_settings() {

      if( !isset( $_POST[ '_sopanels_home_nonce' ] ) || !wp_verify_nonce( $_POST[ '_sopanels_home_nonce' ], 'save' ) ) return;
      if( empty( $_POST[ 'panels_js_complete' ] ) ) return;
      if( !current_user_can( 'edit_theme_options' ) ) return;

      $_data = siteorigin_panels_get_panels_data_from_post( $_POST );

      // @todo Save error/home whatever into correct theme mod location.
      set_theme_mod( 'sop:splash-home', $_data );

      exit( wp_safe_redirect( admin_url( 'edit.php?post_type=page&page=splash-home-editor&updated=true' ) ) );

    }

    /**
     *
     */
    public function wp_head() {
      /** This shouldn't be hard coded, need to update this with proper meta */
      return;
      echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
      echo '<meta property="fb:admins" content="632804651"/>';
      echo '<meta property="fb:app_id" content="680492461962274"/>';
      echo '<meta property="og:locale" content="en_US"/>';
      echo '<meta property="og:type" content="website"/>';
      echo '<meta property="og:title" content="The Day After Festival 2014 in Panama City, Panama"/>';
      echo '<meta property="og:description" content="The Day After Festival is an electronic dance music festival in Panama City, Panama on January 10-12, 2014."/>';
      echo '<meta property="og:url" content="http://dayafter.com/"/>';
      echo '<meta property="og:site_name" content="Day After Festival 2014"/>';
      echo '<meta property="article:publisher" content="https://www.facebook.com/TheDayAfterPanama"/>';
      echo '<meta property="og:image" content="http://dayafter.com/themes/dayafter/images/fb.jpg"/>';
      echo '<meta name="twitter:card" content="summary"/>';
      echo '<meta name="twitter:site" content="@tdapanama"/>';
      echo '<meta name="twitter:domain" content="Day After Festival 2014"/>';
      echo '<meta name="twitter:creator" content="@tdapanama"/>';
    }

    /**
     *
     */
    public function admin_init() {

      try {

        if( !is_plugin_active( 'siteorigin-panels/siteorigin-panels.php' ) ) {

          if( is_wp_error( validate_plugin( 'siteorigin-panels/siteorigin-panels.php' ) ) ) {
            $_errors[ ] = new \WP_Error( 'siteorigin not found, please download and activate for theme to work well' );
          } else {

            if( is_wp_error( activate_plugin( 'siteorigin-panels/siteorigin-panels.php' ) ) ) {
              $_errors[ ] = new \WP_Error( 'cannot activate siteorigin' );
            }

          }

        }

        if( !is_plugin_active( 'wp-amd/wp-amd.php' ) ) {

          if( is_wp_error( validate_plugin( 'wp-amd/wp-amd.php' ) ) ) {
            $_errors[ ] = new \WP_Error( 'wp-amd/ not found, please download and activate for theme to work well' );
          } else {

            if( is_wp_error( activate_plugin( 'wp-amd/wp-amd.php' ) ) ) {
              $_errors[ ] = new \WP_Error( 'cannot activate wp-amd' );
            }

          }

        }

        remove_action( 'siteorigin_panels_before_interface', 'siteorigin_panels_update_notice' );

      } catch( Exception $exception ) {
        $_errors[ ] = new \WP_Error( $exception->getMessage() );
      }

      // do something with any errors, such as display on backend
      if( $_errors ) {
        // wp_die( '<pre>' . print_r( $_errors, true ) . '</pre>' );
      }

    }

    /**
     * Register Assets
     *
     */
    public function init() {

	    if( is_admin() ) {
		    return;
	    }

	    try {

		    if( !class_exists( 'UsabilityDynamics\LayoutEngine\Core' ) ) {
			    throw new \Exception( 'UsabilityDynamics\LayoutEngine\Core not found.' );
		    }

		    if( !class_exists( 'UsabilityDynamics\UI\Panel' ) ) {
			    throw new \Exception( 'UsabilityDynamics\UI\Panel not found.' );
		    }

	    } catch( \Exception $error ) {
		    //wp_die( '<h1>Site Temporarily Unavailable</h1><p>Our aplogizes, but this site is currently not available.</p><p>The theme is currently being updated, please check back later.</p><!--' . $error->getMessage() . '-->' );
	    }

      add_filter( 'the_content',  array( $this, 'the_content' ) );
      add_filter( 'content_url',  array( $this, 'content_url' ), 20, 2 );
      // add_filter( 'body_class',   array( $this, 'body_class' ), 10 );

      add_filter( 'siteorigin_panels_settings', array( $this, '_panels_settings' ) );
      add_filter( 'siteorigin_panels_row_styles', array( $this, '_panels_row_styles' ) );
      add_filter( 'siteorigin_panels_row_style_fields', array( $this, '_panels_row_style_fields' ) );
      add_filter( 'siteorigin_panels_row_style_attributes', array( $this, '_panels_row_style_attributes' ), 20, 2 );
      add_filter( 'siteorigin_panels_row_attributes', array( $this, '_panels_row_attributes' ), 20, 2 );
      add_filter( 'siteorigin_panels_row_cell_attributes', array( $this, '_panels_row_cell_attributes' ), 20, 2 );

      //remove_action( 'wp_head', 'siteorigin_panels_print_inline_css', 12 );
      //remove_action( 'wp_footer', 'siteorigin_panels_print_inline_css' );

    }

    /**
     * Modify Body Class
     *
     * @todo May need to set response "vary" header to force Varnish to cache mobile and desktop seperatly.
     *
     * @param array $classes
     * @param null  $class
     *
     * @return array
     */
    public function body_class( $classes = array(), $class = null ) {

      if( function_exists( 'wp_is_mobile' ) && wp_is_mobile() ) {
        $classes[] = 'is_mobile';
      }

      if( function_exists( 'wp_is_mobile' ) && !wp_is_mobile() ) {
        $classes[] = 'is_desktop';
      }

      return $classes;

    }

    /**
     *
     * "panel-grid-cell" is a column
     *
     * @param $args
     * @param $data
     *
     * @return mixed
     */
    public function _panels_row_cell_attributes( $args, $data ) {
      $args[ 'class' ] = $args[ 'class' ] . ' column';

      return $args;
    }

    /**
     * @param $buffer
     *
     * @return string
     */
    public function optimize( &$buffer ) {

      $buffer = \zz\Html\HTMLMinify::minify( $buffer, array(
        'optimizationLevel'    => 1,
        'emptyElementAddSlash' => true,
        'removeComment'        => true
      ) );

      return $buffer;
    }

    /**
     * Configure the SiteOrigin page builder settings.
     *
     * @param $settings
     *
     * @return mixed
     */
    public function _panels_settings( $settings ) {
      $settings[ 'home-page' ]     = false;
      $settings[ 'margin-bottom' ] = 0;
      $settings[ 'responsive' ]    = true;

      return $settings;
    }

    /**
     * Add row styles.
     *
     * @param $styles
     *
     * @return mixed
     */
    public function _panels_row_styles( $styles ) {
      $styles[ 'visible-lg' ] = __( 'Large devices', 'wp-splash' );
      $styles[ 'hidden' ]     = __( 'Hidden', 'wp-splash' );

      return $styles;
    }

    /**
     * @param $fields
     *
     * @return mixed
     */
    public function _panels_row_style_fields( $fields ) {

      $fields[ 'row_title' ] = array(
        'name' => __( 'Title', 'wp-splash' ),
        'type' => 'text',
      );

      $fields[ 'scroll_reveal' ] = array(
        'name' => __( 'Scroll Reveal', 'wp-splash' ),
        'type' => 'text',
      );

      $fields[ 'color' ] = array(
        'name' => __( 'Text Color', 'wp-splash' ),
        'type' => 'color',
      );

      $fields[ 'background' ] = array(
        'name' => __( 'Background Color', 'wp-splash' ),
        'type' => 'color',
      );

      $fields[ 'background_image' ] = array(
        'name' => __( 'Background Image', 'wp-splash' ),
        'type' => 'media',
      );

      $fields[ 'center_text' ] = array(
        'name' => __( 'Center Text', 'wp-splash' ),
        'type' => 'checkbox',
      );

      return $fields;
    }

    /**
     * @param $attr
     * @param $row
     *
     * @return mixed
     */
    public function _panels_row_attributes( $attr, $row ) {

      $attr[ 'class' ] = $attr[ 'class' ] . ' row';

      if( !empty( $row[ 'style' ][ 'center_text' ] ) ) {
        $attr[ 'class' ] = $attr[ 'class' ] . ' text-center';
      }

      return $attr;
    }

    /**
     * @param $attr
     * @param $style
     *
     * @return mixed
     */
    public function _panels_row_style_attributes( $attr, $style ) {
      $attr[ 'style' ] = '';

      if( !empty( $style[ 'scroll_reveal' ] ) ) {
        $attr[ 'data-scroll-reveal' ] = $style[ 'scroll_reveal' ];
      }

      if( !empty( $style[ 'row_title' ] ) ) $attr[ 'data-title' ] .= $style[ 'row_title' ];

      if( !empty( $style[ 'visible-lg' ] ) ) $attr[ 'style' ] .= '';
      if( !empty( $style[ 'hidden' ] ) ) $attr[ 'style' ] .= 'display:none';

      if( !empty( $style[ 'background' ] ) ) $attr[ 'style' ] .= 'background-color: ' . $style[ 'background' ] . '; ';
      if( !empty( $style[ 'background_image' ] ) ) $attr[ 'style' ] .= 'background-image: url(' . esc_url( $style[ 'background_image' ] ) . '); ';
      if( !empty( $style[ 'background_image_repeat' ] ) ) $attr[ 'style' ] .= 'background-repeat: repeat; ';

      if( empty( $attr[ 'style' ] ) ) unset( $attr[ 'style' ] );

      return $attr;
    }

    /**
     *
     * * Enqueue CSS
     *
     * @param $content
     *
     * @return string
     */
    public function the_content( $content ) {

      // @note CSS being included for now, later on wwill figure out how to make it part of app.css

      if( is_front_page() && !is_null( get_theme_mod( 'sop:splash-home', null ) ) ) {
        return '<section class="container-fluid">' . siteorigin_panels_render( null, true, get_theme_mod( 'sop:splash-home' ) ) . '</section>';
      }

      if( is_404() && !is_null( get_theme_mod( 'sop:splash-404', null ) ) ) {
        return '<section class="container-fluid">' . siteorigin_panels_render( null, true, get_theme_mod( 'sop:splash-404' ) ) . '</section>';
      }
      
      /** By default we do want to return */
      return '<section class="container-fluid">' . $content . '</section>';

    }

    /**
     *
     * Fix so content_url( 'themes/wp-splash/static/scripts/app.js' ) can be used to get theme files.
     *
     * @param        $url
     * @param string $path
     *
     * @return mixed
     *
     */
    public function content_url( $url, $path = '' ) {

      // @temp hardcoded, use get_template_directory_uri() to figure out correct URL that should be replaced.

      $url = str_replace( 'themes/wp-splash/static/', 'vendor/themes/wp-splash/static/', $url );
      $url = str_replace( 'themes/wp-splash/vendor/', 'vendor/themes/wp-splash/vendor/', $url );

      return $url;

    }

    /**
     *
     */
    public function admin_menu() {

      // Add Home Page.
      if( get_option( 'show_on_front' ) !== 'page' ) {
        add_pages_page( __( 'Site Home' ), __( 'Site Home' ), 'edit_theme_options', 'splash-home-editor', array( $this, 'editor' ) );
      }

      if( get_theme_mod( 'admin:hide-post-menu', false ) ) {
        remove_menu_page( 'edit.php' );
        remove_menu_page( 'edit.php?post_type=artist' );
        remove_menu_page( 'edit.php?post_type=event' );
        remove_menu_page( 'edit.php?post_type=tour' );
        remove_menu_page( 'edit.php?post_type=venue' );
      }

      if( get_theme_mod( 'admin:hide-users-menu', false ) ) {
        remove_menu_page( 'users.php' );
      }

      if( get_theme_mod( 'admin:hide-tools-menu', false ) ) {
        remove_menu_page( 'tools.php' );
      }

      if( get_theme_mod( 'admin:hide-comments-menu', false ) ) {
        remove_menu_page( 'edit-comments.php' );
      }

      $this->save_settings();

    }

    /**
     *
     */
    public function editor() {
      global $wp_meta_boxes;

      if( !$wp_meta_boxes ) {
        add_settings_error( 'wp-splash', 'editor-broke', __( 'No metaboxes found for editor.' ) );
      }

      include( dirname( __DIR__ ) . '/static/templates/splash-editor.php' );

    }

    /**
     * http://2015.dayafter.com/vendor/usabilitydynamics/wp-splash/static/styles/app.css
     * http://2015.dayafter.com/vendor/usabilitydynamics/wp-splash/static/scripts/app.js
     */
    public function admin_print_styles() {

      /// Will move out of here when Laout Library is self-sufficient
      wp_enqueue_style( 'ud-layout', content_url( 'themes/wp-splash/vendor/libraries/usabilitydynamics/lib-layout-engine/static/styles/post-editor.css' ) );

    }

    /**
     *
     */
    public function admin_print_scripts() {

      if( get_current_screen()->base === 'pages_page_splash-home-editor' ) {
        $panels_data = get_theme_mod( 'sop:splash-home', null );

        if( is_null( $panels_data ) ) {
          $layouts     = apply_filters( 'siteorigin_panels_prebuilt_layouts', array() );
          $panels_data = !empty( $layouts[ 'default_home' ] ) ? $layouts[ 'default_home' ] : current( $layouts );
        }

        $panels_data = apply_filters( 'siteorigin_panels_data', $panels_data, 'home' );

      }

      if( get_current_screen()->base === 'pages_page_splash-error-editor' ) {
        $panels_data = get_theme_mod( 'sop:splash-error', null );
        $panels_data = apply_filters( 'siteorigin_panels_data', isset( $panels_data ) ? $panels_data : array(), 'home' );
      }

      if( isset( $panels_data ) ) {
        _siteorigin_panels_scripts( $panels_data );
        _siteorigin_panels_styles( $panels_data );
        add_meta_box( 'so-panels-panels', __( 'Page Builder', 'siteorigin-panels' ), 'siteorigin_panels_metabox_render', 'appearance_page_so_panels_home_page', 'advanced', 'high' );
      }

    }

    /**
     *
     */
    public function loaded() {
      wp_register_style( 'app', content_url( 'themes/wp-splash-v2.0/static/styles/app.css' ), array(), Splash::$version, 'all' );
      wp_register_script( 'udx-requires', '//cdn.udx.io/udx.requires.js', array(), '3.1.2', true );
      wp_register_script( 'app', content_url( 'themes/wp-splash-v2.0/static/scripts/app.js' ), array( 'udx-requires' ), Splash::$version, true );
    }

    /**
     * Enqueue Style
     *
     * @author potanin@UD
     * @method wp_enqueue_scripts
     */
    public function wp_enqueue_scripts() {
      //wp_deregister_style( 'siteorigin-panels-front' );
      wp_enqueue_style( 'app' );
      wp_enqueue_script( 'app' );
    }

    /**
     *
     * SiteOrigin Panel only needs to be laoded on the backend since templates are saved into regular post content.
     *
     */
    public function wp_print_footer_scripts() {
      // echo '<script data-main="/themes/wp-splash/static/scripts/app" src="//cdn.udx.io/udx.requires.js"></script>';
    }

    /**
     *
     */
    public function wp_print_styles() {
      // echo siteorigin_panels_print_inline_css();
    }

    /**
     *
     * @todo Add redirection to 404 page if "Splash" page has not yet been setup on a new theme activation.
     *
     */
    public function template_redirect() {

      if( class_exists( 'zz\Html\HTMLMinify' ) && !is_user_logged_in() ) {
        ob_start( array( $this, 'optimize' ) );
      }

    }

  }

}
