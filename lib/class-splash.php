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
    public static $version = '0.1.0';

    /**
     * Textdomain String
     *
     * @public
     * @property text_domain
     * @var string
     */
    public static $text_domain = 'Splash';

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
      add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ), 20, 0 );
      add_action( 'the_post', array( $this, 'the_post' ), 20, 0 );
      add_action( 'wp_print_styles', array( $this, 'wp_print_styles' ), 20, 0 );

      add_action( 'wp_head', array( $this, 'wp_head' ), 20, 0 );
      add_action( 'wp_print_footer_scripts', array( $this, 'wp_print_footer_scripts' ), 20, 0 );

      add_action( 'shutdown', array( $this, 'shutdown' ), 100 );

      if( file_exists( dirname( __DIR__ ) . '/vendor/libraries/autoload.php' ) ) {
        include_once( dirname( __DIR__ ) . '/vendor/libraries/autoload.php' );
      }

      if( class_exists( 'zz\Html\HTMLMinify' ) ) {
        ob_start( array( $this, 'minify' ) );
      }

    }
    
    public function wp_head() {
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

    public function admin_init() {

      if( is_wp_error( validate_plugin( 'siteorigin-panels/siteorigin-panels.php' ) ) ) {
        wp_die( 'siteorigin invalid, cant use theme' );
      }

      if( !is_plugin_active( 'siteorigin-panels/siteorigin-panels.php' ) ) {

        if( is_wp_error( activate_plugin( 'siteorigin-panels/siteorigin-panels.php' ) ) ) {
          wp_die( 'cannot activate siteorigin' );
        }

      }

      remove_action( 'siteorigin_panels_before_interface', 'siteorigin_panels_update_notice' );

    }

    /**
     * Register Assets
     *
     */
    public function init() {

      if( !class_exists( 'UsabilityDynamics\LayoutEngine\Core' ) ) {
        wp_die( 'UsabilityDynamics\LayoutEngine\Core not found.' );
      }

      if( !class_exists( 'UsabilityDynamics\UI\Panel' ) ) {
        wp_die( 'UsabilityDynamics\UI\Panel not found.' );
      }

      add_filter('the_content', array( $this, 'the_content' ) );
      add_filter('siteorigin_panels_settings', array( $this, '_panels_settings' ) );
      add_filter('siteorigin_panels_row_styles', array( $this, '_panels_row_styles' ) );
      add_filter('siteorigin_panels_row_style_fields', array( $this, '_panels_row_style_fields' ) );
      add_filter('siteorigin_panels_row_style_attributes', array( $this, '_panels_row_style_attributes' ), 20, 2 );
      add_filter('siteorigin_panels_row_attributes', array( $this, '_panels_row_attributes' ), 20, 2 );

      remove_action( 'wp_head', 'siteorigin_panels_print_inline_css', 12 );
      remove_action( 'wp_footer', 'siteorigin_panels_print_inline_css' );

    }

    /**
     * @param $buffer
     *
     * @return string
     */
    function minify( &$buffer ) {

      $buffer = \zz\Html\HTMLMinify::minify( $buffer, array(
        'optimizationLevel' => 1,
        'emptyElementAddSlash' => true,
        'removeComment' => true
      ));

      return $buffer;
    }

    /**
     * Configure the SiteOrigin page builder settings.
     *
     * @param $settings
     * @return mixed
     */
    function _panels_settings($settings){
      $settings['home-page'] = false;
      $settings['margin-bottom'] = 35;
      $settings['responsive'] = true;
      return $settings;
    }

    /**
     * Add row styles.
     *
     * @param $styles
     * @return mixed
     */
    function _panels_row_styles($styles) {
      $styles['wide-grey'] = __('Wide Grey', 'wp-splash');
      $styles['sexy-blue'] = __('Sexy Blue', 'wp-splash');
      return $styles;
    }

    function _panels_row_style_fields($fields) {

      $fields['top_border'] = array(
        'name' => __('Top Border Color', 'wp-splash'),
        'type' => 'color',
      );

      $fields['bottom_border'] = array(
        'name' => __('Bottom Border Color', 'wp-splash'),
        'type' => 'color',
      );

      $fields['background'] = array(
        'name' => __('Background Color', 'wp-splash'),
        'type' => 'color',
      );

      $fields['background_image'] = array(
        'name' => __('Background Image', 'wp-splash'),
        'type' => 'media',
      );

      $fields['background_image_repeat'] = array(
        'name' => __('Repeat Background Image', 'wp-splash'),
        'type' => 'checkbox',
      );

      $fields['no_margin'] = array(
        'name' => __('No Bottom Margin', 'wp-splash'),
        'type' => 'checkbox',
      );

      return $fields;
    }

    function _panels_row_attributes($attr, $row) {

      if(!empty($row['style']['no_margin'])) {
        if(empty($attr['style'])) $attr['style'] = '';
        $attr['style'] .= 'margin-bottom: 0px;';
      }

      return $attr;
    }

    function _panels_row_style_attributes($attr, $style) {
      $attr['style'] = '';

      if(!empty($style['top_border'])) $attr['style'] .= 'border-top: 1px solid '.$style['top_border'].'; ';
      if(!empty($style['bottom_border'])) $attr['style'] .= 'border-bottom: 1px solid '.$style['bottom_border'].'; ';
      if(!empty($style['background'])) $attr['style'] .= 'background-color: '.$style['background'].'; ';
      if(!empty($style['background_image'])) $attr['style'] .= 'background-image: url('.esc_url($style['background_image']).'); ';
      if(!empty($style['background_image_repeat'])) $attr['style'] .= 'background-repeat: repeat; ';

      if(empty($attr['style'])) unset($attr['style']);

      return $attr;
    }

    /**
     *
     * * Enqueue CSS
     *
     * @param $wp_query
     * @return string
     */
    public function the_content( $wp_query ) {

      if( is_front_page() ) {
        return siteorigin_panels_render( null, true, get_theme_mod( 'sop:splash-home' ) );
      }

      if( is_404() ) {
        return siteorigin_panels_render( null, true, get_theme_mod( 'sop:splash-404' ) );
      }

    }

    public function admin_menu() {

      add_pages_page( __( 'Site Home' ), __( 'Site Home' ), 'edit_theme_options', 'splash-home-editor', array( $this, 'editor' ) );
      add_pages_page( __( '404 Page' ), __( '404 Page' ), 'edit_theme_options', 'splash-error-editor', array( $this, 'editor' ) );
      add_pages_page( __( 'Login Page' ), __( 'Login Page' ), 'edit_theme_options', 'splash-error-editor', array( $this, 'editor' ) );

      remove_menu_page( 'edit.php' );
      remove_menu_page( 'edit.php?post_type=artist' );
      remove_menu_page( 'edit.php?post_type=event' );
      remove_menu_page( 'edit.php?post_type=tour' );
      remove_menu_page( 'edit.php?post_type=venue' );
      remove_menu_page( 'upload.php' );
      remove_menu_page( 'users.php' );
      remove_menu_page( 'edit-comments.php' );
      remove_menu_page( 'tools.php' );

      $this->save_settings();
    }

    private function save_settings() {

      if(!isset($_POST['_sopanels_home_nonce']) || !wp_verify_nonce($_POST['_sopanels_home_nonce'], 'save')) return;
      if ( empty($_POST['panels_js_complete']) ) return;
      if(!current_user_can('edit_theme_options')) return;

      $_data = siteorigin_panels_get_panels_data_from_post( $_POST );

      // @todo Save error/home whatever into correct theme mod location.
      set_theme_mod( 'sop:splash-home', $_data );

      exit( wp_safe_redirect( admin_url( 'edit.php?post_type=page&page=splash-home-editor&updated=true' ) ) );

    }

    public function editor() {
      global $wp_meta_boxes;

      if( !$wp_meta_boxes ) {
        add_settings_error( 'wp-splash', 'editor-broke', __( 'No metaboxes found for editor.' ) );
      }

      include( dirname( __DIR__ ) . '/static/templates/splash-editor.php' );

    }

    public function admin_print_scripts() {

      if( get_current_screen()->base === 'pages_page_splash-home-editor' ) {
        $panels_data = get_theme_mod( 'sop:splash-home', null );

        if( is_null( $panels_data  ) ) {
          $layouts = apply_filters( 'siteorigin_panels_prebuilt_layouts', array() );
          $panels_data = !empty($layouts['default_home']) ? $layouts['default_home'] : current($layouts);
        }

        $panels_data = apply_filters( 'siteorigin_panels_data', $panels_data, 'home');

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
      wp_register_style( 'app', content_url( 'themes/wp-splash/static/styles/app.css' ), array( 'twitter-bootstrap' ), Splash::$version, 'all' );
      wp_register_style( 'twitter-bootstrap', 'http://netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css', array(), Splash::$version, 'all' );

      wp_register_script( 'udx-requires', '//cdn.udx.io/udx.requires.js', array(), '3.1.2', true );
      wp_register_script( 'app',  content_url( 'themes/wp-splash/static/scripts/app.js' ), array( 'udx-requires' ), Splash::$version, true );
    }

    /**
     * Enqueue Style
     *
     * @author potanin@UD
     * @method wp_enqueue_scripts
     */
    public function wp_enqueue_scripts() {
      wp_deregister_style( 'siteorigin-panels-front' );
      wp_enqueue_style( 'app');
      wp_enqueue_script( 'app');
    }


    /**
     *
     * SiteOrigin Panel only needs to be laoded on the backend since templates are saved into regular post content.
     *
     */
    public function wp_print_footer_scripts() {
      // echo '<script data-main="/themes/wp-splash/static/scripts/app" src="//cdn.udx.io/udx.requires.js"></script>';
    }

    public function wp_print_styles() {
      // echo siteorigin_panels_print_inline_css();
    }

    public function shutdown() {

    }

  }

}
