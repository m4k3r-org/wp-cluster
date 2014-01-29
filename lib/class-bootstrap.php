<?php
/**
 * Festival Bootstrap
 *
 * @version 0.1.0
 * @author Usability Dynamics
 * @namespace UsabilityDynamics
 */
namespace UsabilityDynamics\Festival {

  /**
   * Festival Bootstrap
   *
   * @author Usability Dynamics
   */
  final class Bootstrap {

    /**
     * Instance.
     *
     * @var $instance
     */
    static private $instance;

    /**
     * Class Initializer
     *
     * @author Usability Dynamics
     * @since 0.1.0
     */
    public function __construct() {
      
      if( !class_exists( '\UsabilityDynamics\Festival' ) ) {
        wp_die( '<h1>Fatal Error</h1><p>Festival Theme not found.</p>' );
      }

      // Instantaite Disco.
      $this->theme = new \UsabilityDynamics\Festival;

      // Load all helper files from functions directory.
      \UsabilityDynamics\Utility::load_files( get_template_directory() . '/functions' );
      
    }

    /**
     * Determine if instance already exists and Return Theme Instance
     *
     */
    public static function get_instance() {
      return null === self::$instance ? self::$instance = new self() : self::$instance->theme;
    }

  }

}
