<?php
  /**
   * Class WinterFantasy
   *
   * @class WinterFantasy
   * @author potanin@UD
   */
  class WinterFantasy {

    /**
     * Version of child theme
     *
     * @public
     * @property version
     * @var string
     */
    public static $version = '1.1.2';

    /**
     * Textdomain String
     *
     * @public
     * @property text_domain
     * @var string
     */
    public static $text_domain = 'WinterFantasy';

    /**
     * Initialize WinterFantasy Class
     *
     * @public
     * @for WinterFantasy
     * @method __construct
     * @constructor
     *
     * @author potanin@UD
     */
    public function __construct() {
      add_action( 'init', array( $this, 'init' ), 0 );
      add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
      add_filter( 'sanitize_file_name', array( $this, 'sanitize_file_name' ), 10 );
      add_theme_support( 'post-thumbnails' );
      add_filter( 'excerpt_length',  array( $this, 'custom_excerpt_length' ) );
      add_action( 'init', array( $this, 'custom_navigation_menus' ) );
    }

    /**
     * WordPress "init" action
     *
     * @public
     * @for WinterFantasy
     * @method init
     *
     * @author potanin@UD
     */
    public function init() {
      wp_register_style( 'google-reset', 'https://reset5.googlecode.com/hg/reset.min.css' );
      wp_register_style( 'app-style', get_stylesheet_directory_uri() . '/style.css', array( 'google-reset' ), WinterFantasy::$version, 'all' );
      wp_register_script( 'app-script', get_stylesheet_directory_uri() . '/js/app.js', array( 'jquery' ), WinterFantasy::$version, true );
    }

    /**
     * Enqueue Styles
     *
     * @public
     * @method wp_enqueue_scripts
     * @for WinterFantasy
     *
     * @author potanin@UD
     */
    public function wp_enqueue_scripts() {
      wp_enqueue_script( 'app-script' );
      wp_enqueue_style( 'app-style' );
    }

    /**
     * Rename uploaded files as the hash of their original.
     *
     * @public
     * @method sanitize_file_name
     * @for WinterFantasy
     *
     * @author sopp@ID
     */
    public function sanitize_file_name( $filename ) {
      $info = pathinfo( $filename );
      $ext  = empty( $info[ 'extension' ] ) ? '' : '.' . $info[ 'extension' ];
      $rnd  = rand( 0, 99 );
      $name = basename( $filename, $ext );

      return md5( $name ) . $rnd . $ext;
    }
    
    /**
     * Register navigation menus.
     *
     * @public
     * @method custom_navigation_menus
     * @for WinterFantasy
     *
     * @author sopp@ID
     */
    public function custom_navigation_menus() {
	    $locations = array(
	    	'footer-menu' => __( 'Footer Menu', 'text_domain' ),
	    );
	    register_nav_menus($locations);
    }
    
    /**
     * Control length of post_excerpt()
     *
     * @public
     * @method custom_excerpt_length
     * @for WinterFantasy
     *
     * @author sopp@UD
     */
    function custom_excerpt_length( $length ) {
		return 20;
	}

  }

  // Initialize Child Theme class.
  new WinterFantasy();