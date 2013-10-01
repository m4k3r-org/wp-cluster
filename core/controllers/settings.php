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
    public $data = stdClass;

    /**
     * Initialize Settings
     *
     * @for Settings
     */
    public function __construct() {
    }

    /**
     * Get Key
     *
     * @return mixed
     */
    public function get() {
      return $this->data;
    }

    /**
     * Set Key
     *
     * @return mixed
     */
    public function set() {
      return $this->data;
    }

  }
}