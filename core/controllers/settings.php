<?php
/**
 * Settings Access Controller
 *
 * @module Varnish
 * @author potanin@UD
 */
namespace Varnish {

  /**
   * Class Settings
   *
   * @module Varnish
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