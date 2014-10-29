<?php
  /**
   * Class FreaksBeatsTreats
   *
   * @class FreaksBeatsTreats
   * @author potanin@UD
   */
  class FreaksBeatsTreats {

    /**
     * Version of child theme
     *
     * @public
     * @property version
     * @var string
     */
    public static $version = '1.0.0';

    /**
     * Textdomain String
     *
     * @public
     * @property text_domain
     * @var string
     */
    public static $text_domain = 'FreaksBeatsTreats';

    /**
     * Initialize FreaksBeatsTreats Class
     *
     * @public
     * @for FreaksBeatsTreats
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
    }

    /**
     * WordPress "init" action
     *
     * @public
     * @for FreaksBeatsTreats
     * @method init
     *
     * @author potanin@UD
     */
    public function init() {
      wp_register_style( 'google-reset', 'https://reset5.googlecode.com/hg/reset.min.css' );
      wp_register_style( 'app-style', get_stylesheet_directory_uri() . '/style.css', array( 'google-reset' ), FreaksBeatsTreats::$version, 'all' );
      wp_register_script( 'app-script', get_stylesheet_directory_uri() . '/js/app.js', array( 'jquery' ), FreaksBeatsTreats::$version, true );
    }

    /**
     * Enqueue Styles
     *
     * @public
     * @method wp_enqueue_scripts
     * @for FreaksBeatsTreats
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
     * @for FreaksBeatsTreats
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
     * Control length of post_excerpt()
     *
     * @public
     * @method custom_excerpt_length
     * @for FreaksBeatsTreats
     *
     * @author sopp@UD
     */
    function custom_excerpt_length( $length ) {
		return 20;
	}

  }

  // Initialize Child Theme class.
  new FreaksBeatsTreats();