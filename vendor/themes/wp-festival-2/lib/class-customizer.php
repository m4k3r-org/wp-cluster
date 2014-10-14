<?php
/**
 * Contains methods for customizing the theme customization screen.
 *
 * @link http://codex.wordpress.org/Theme_Customization_API
 * @since 0.1.0
 * author peshkov@UD
 */
namespace UsabilityDynamics\Festival2 {

  /**
   * Customizer.
   *
   */
  class Customizer extends \UsabilityDynamics\Theme\Customizer {

    public static $text_domain = NULL;

    /**
     * Create Customizer Instance
     *
     * Note: all specific hooks should be added here before object initialization.
     */
    public static function define( $args = array() ) {

      self::$text_domain = !empty( $args[ 'text_domain' ] ) ? $args[ 'text_domain' ] : '';

      //** Initialize Customizer with predefined settings stored in json */
      $settings = self::_get_system_settings();
      return new Customizer( $settings );

    }

    /**
     * Get default Settings from schema
     *
     */
    public static function _get_system_settings() {
      $short_path = '/static/schemas/schema.customizer.json';
      $file = get_stylesheet_directory() . $short_path;
      if( !file_exists( $file ) ) {
        $file = get_template_directory() . $short_path;
      }
      if( !file_exists( $file ) ) {
        return false;
      }
      return json_decode( file_get_contents( $file ), true );
    }

  }

}
