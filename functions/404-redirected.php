<?php
/**
 * Name: 404-Redirected Extensions
 * Version: 1.0
 * Supported Plugin Version: 1.2
 * Description: Adds 404-Redirected suggestion to 404 page.
 * Author: rrolfe (http://www.weberz.com), Usability Dynamics, Inc.
 *
 */

 if ( function_exists('wbz404_suggestions') ) {

  /**
   * Renders wbz404_suggestions() in 404 Page Content
   *
   * @author potanin@UD
   */
  add_action( 'flawless::404_page_content', function() {
    wbz404_suggestions();
  });


  if( !function_exists( 'shortcode_wbz404_suggestions' ) ) {
    /**
     * Allows wbz404_suggestions() to be called via shortcode.
     *
     * @author potanin@UD
     */
    function shortcode_wbz404_suggestions( $atts ) {
      global $wp_query;
      $wp_query->is_404 = true;
      ob_start(); wbz404_suggestions(); $content = ob_get_clean();
      return $content;
    }

    add_shortcode( 'wbz404_suggestions', 'shortcode_wbz404_suggestions' );

  }


}