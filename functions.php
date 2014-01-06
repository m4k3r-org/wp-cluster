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
      add_action( 'init', array( &$this, 'init' ) );
      add_action( 'wp_enqueue_scripts', array( &$this, 'wp_enqueue_scripts' ) );

      // Enable JavaScript Library Loading.
      $this->requires = new \UsabilityDynamics\Requires(array(
        'name' => 'splash.state',
        'scopes' => [ 'public' ],
        'debug' => true
      ));

      // $this->requires->add();

    }

    /**
     * Register Assets
     *
     */
    public function init() {
      wp_register_script( 'app', get_stylesheet_directory_uri() . '/scripts/app.js', array( 'jquery' ), Splash::$version, true );
      wp_register_style( 'app', get_stylesheet_directory_uri() . '/styles/app.css', array(), Splash::$version, 'all' );
    }

    /**
     * Enqueue Style
     *
     * @author potanin@UD
     * @method wp_enqueue_scripts
     */
    public function wp_enqueue_scripts() {
      wp_enqueue_script( 'app' );
      wp_enqueue_style( 'app' );
    }

  }

  new Splash;

}