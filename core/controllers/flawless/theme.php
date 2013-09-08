<?php
/**
 * Presentation
 *
 * @namespace Flawless
 * @class Flawless\Shortcode
 *
 * @user: potanin@UD
 * @date: 8/31/13
 * @time: 10:33 AM
 */
namespace Flawless {

  /**
   * Shortcode Management
   *
   * -
   *
   * @module Flawless
   * @class Theme
   */
  class Theme {

    /**
     * Something like constructor
     *
     */
    public function __construct( $params = array() ) {

    }

    /**
     *
     * @return mixed
     */
    public function get_theme_directories() {
      global $wp_theme_directories;
      return $wp_theme_directories;
    }

  }

}