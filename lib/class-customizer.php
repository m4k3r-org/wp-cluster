<?php
/**
 * Contains methods for customizing the theme customization screen.
 *
 * @link http://codex.wordpress.org/Theme_Customization_API
 * @since 0.1.0
 * author peshkov@UD
 */
namespace UsabilityDynamics\Festival {

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
      $short_path = '/static/schemas/default.customizer.json';
      $file = get_stylesheet_directory() . $short_path;
      if( !file_exists( $file ) ) {
        $file = get_template_directory() . $short_path;
      }
      if( !file_exists( $file ) ) {
        return false;
      }
      return self::_localize( json_decode( file_get_contents( $file ), true ) );
    }

    /**
     * Localization functionality.
     * Replaces array's l10n data.
     * Helpful for localization of data which is stored in JSON files ( see /schemas )
     *
     * @param type $data
     *
     * @return type
     * @author peshkov@UD
     */
    public static function _localize( $data ) {
      static $l10n;

      if ( !is_array( $data ) ) return $data;

      //** The Localization's list. */
      if( empty( $l10n ) ) {
        $l10n = apply_filters( 'ud::theme::festival::customizer', array(
          'colors' => __( 'Colors', self::$text_domain ),
          'background_images' => __( 'Background Images', self::$text_domain ),
          'header_banner_bg_color' => __( 'Header Banner Background', self::$text_domain ),
          'content_bg_color' => __( 'Content Background', self::$text_domain ),
          'footer_bg_color' => __( 'Footer Background', self::$text_domain ),
          'header_banner_bg_image' => __( 'Header Banner Image', self::$text_domain ),
          'sticky_bar_logo' => __( 'Sticky Bar Logo', self::$text_domain ),
          'styled_button_colors' => __( 'Styled Button Colors', self::$text_domain ),
          'button_background_color' => __( 'Background', self::$text_domain ),
          'button_font_color' => __( 'Text', self::$text_domain ),
          'button_border_color' => __( 'Border', self::$text_domain ),
          'button_background_color_hover' => __( 'Background (Hover)', self::$text_domain ),
          'button_font_color_hover' => __( 'Text (Hover)', self::$text_domain ),
          'button_border_color_hover' => __( 'Border (Hover)', self::$text_domain ),
          'images' => __( 'Images', self::$text_domain ),
          '404_page' => __( '404 page', self::$text_domain ),
        ));
      }

      //** Replace l10n entries */
      foreach ( $data as $k => $v ) {
        if ( is_array( $v ) ) {
          $data[ $k ] = self::_localize( $v );
        } elseif ( is_string( $v ) ) {
          if ( strpos( $v, 'l10n' ) !== false ) {
            preg_match_all( '/l10n\.([^\s]*)/', $v, $matches );
            if ( !empty( $matches[ 1 ] ) ) {
              foreach ( $matches[ 1 ] as $i => $m ) {
                if ( key_exists( $m, $l10n ) ) {
                  $data[ $k ] = str_replace( $matches[ 0 ][ $i ], $l10n[ $m ], $data[ $k ] );
                }
              }
            }
          }
        }
      }

      return $data;
    }

  }

}
