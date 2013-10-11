<?php
/**
 * Settings Access Controller
 *
 * @module Veneer
 * @author potanin@UD
 */
namespace UsabilityDynamics\Veneer {

  /**
   * Class Settings
   *
   * @module Veneer
   */
  class Settings {

    /**
     * Actual Settings data.
     *
     * @static
     * @property $version
     * @type {Object}
     */
    public $data = null;

    /**
     * Initialize Settings
     *
     * @for Settings
     */
    public function __construct() {

      // Create new Object
      $this->data = $this->data ? $this->data : (object) array();

    }

    /**
     * Register Content Structure
     *
     * @param array|bool|string $data
     *
     * @return \Exception|\Veneer\Exception
     */
    public static function add_content_type( $data = false ) {

      // If a a file path is passed try to load
      if( $data && is_string( $data ) && file_exists( $data ) ) {
        $data = file_get_contents( $data );
      }

      try {

        // Convert into object
        $data = json_decode( $data, true );

        if( !$data ) {
          throw new Exception( 'Unreadable data.' );;
        }

        if( function_exists( 'register_post_type' ) ) {
          // register_post_type( $data );
        }

      } catch( Exception $e ) {
        return $e;
      }

    }

    /**
     * Get Key
     *
     * @param      $key
     * @param null $defaults
     *
     * @return mixed
     */
    public function get( $key, $defaults = null ) {
      return $this->data{$key} || $defaults;
    }

    /**
     * Set Key
     *
     * @param bool $key
     * @param bool $value
     *
     * @return mixed
     */
    public function set( $key = false, $value = false ) {

      // If no key provided
      if( $key === false ) {
        return $this->data;
      }

      // Delete key if no value.
      if( $value === false ) {
        return $this->data;
      }

      // Update data
      $this->data{$key} = $value;

      // Return data object
      return $this->data;

    }

  }
}