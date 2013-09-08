<?php
/**
 *
 * @note Ported over from WPP 2.0 - needs a serious clean-up.
 *
 * @author potanin@UD
 * @version 0.0.1
 * @namespace Flawless
 */
namespace Flawless {

  /**
   * Class Settings
   *
   * Migrated from WPP 2.0.
   *
   * @class Settings
   */
  class Settings extends \UsabilityDynamics\Settings {

    // Class Version.
    public $version = '0.1.1';

    /**
     *
     * @method __construct
     * @for Settings
     */
    public function __construct( $options = array() ) {
      add_action( 'flawless::admin_init', array( __CLASS__, 'admin_init' ) );

    }

    /**
     * Update Flawless Theme Setting
     *
     * @since 0.0.6
     */
    static function update_option( $key = false, $value = '' ) {
      global $flawless;

      if ( !$key ) {
        return false;
      }

      if ( empty( $value ) ) {
        $flawless_settings = get_option( 'flawless_settings' );
        unset( $flawless_settings[ $key ] );
      } else {
        $flawless_settings = self::extend( get_option( 'flawless_settings' ), array( $key => $value ) );
      }

      if ( update_option( 'flawless_settings', $flawless_settings ) ) {

        if ( !empty( $value ) ) {
          $flawless[ $key ] = $flawless_settings[ $key ];
        } else {
          unset( $flawless[ $key ] );
        }

        return true;
      }

    }

    static function admin_init() {

      //** Check for special actions and nonce, a nonce must always be set. */
      if ( !empty( $_REQUEST[ '_wpnonce' ] ) /* && isset( $_REQUEST[ 'flawless_action' ] ) */ ) {

        if ( wp_verify_nonce( $_REQUEST[ '_wpnonce' ], 'flawless_settings' ) ) {

          $args = array();

          //** Handle Theme Backup Upload */
          if ( $backup_file = $_FILES[ 'flawless_settings' ][ 'tmp_name' ][ 'settings_from_backup' ] ) {
            $backup_contents = file_get_contents( $backup_file );

            if ( !empty( $backup_contents ) ) {
              $decoded_settings = json_decode( $backup_contents, true );

              if ( !empty( $decoded_settings ) ) {
                $_REQUEST[ 'flawless_settings' ] = $decoded_settings;
                $args[ 'message' ] = 'backup_restored';
              } else {
                $args[ 'message' ] = 'backup_failed';
              }

            }
          }

          //** Handle Theme Options updating */
          if ( $redirect = Settings::save_settings( $_REQUEST[ 'flawless_settings' ], $args ) ) {
            $redirect = add_query_arg( 'flush_rewrite_rules', 'true', $redirect );
            wp_redirect( $redirect );
            die();
          }

        }

        //** Download back up configuration */
        if ( wp_verify_nonce( $_REQUEST[ '_wpnonce' ], 'download-flawless-backup' ) ) {

          header( 'Cache-Control: public' );
          header( 'Content-Description: File Transfer' );
          header( 'Content-Disposition: attachment; filename=' . sanitize_key( get_bloginfo( 'name' ) ) . '-flawless.' . date( 'Y-m-d' ) . '.json' );
          header( 'Content-Transfer-Encoding: binary' );
          header( 'Content-Type: text/plain; charset=' . get_option( 'blog_charset' ), true );

          die( json_encode( $flawless ) );
        }

      }


    }

    /**
     * Save Theme Options
     *
     * Called after nonce is verified.
     *
     * @example
     *
     *        // Save Settings
     *        Settings::save_settings( array() );
     *
     * @method save_settings
     * @for Settings
     *
     * @since 0.0.2
     *
     * @param $flawless
     * @param array $args
     *
     * @return string or false.  If string, a URL to be used for redirection.
     */
    static function save_settings( $flawless = array(), $args = array() ) {

      $current_settings = stripslashes_deep( get_option( 'flawless_settings' ) );

      $args = wp_parse_args( $args, array(
        'message' => 'settings_updated'
      ) );

      //** Set logo */
      if ( !empty( $_FILES[ 'flawless_logo' ][ 'name' ] ) ) {

        $file = wp_handle_upload( $_FILES[ 'flawless_logo' ], array( 'test_form' => false ) );

        if ( !$file[ 'error' ] && $file[ 'url' ] && $image_size = getimagesize( $file[ 'file' ] ) ) {

          $post_id = wp_insert_attachment( array(
            'post_mime_type' => $file[ 'type' ],
            'guid' => $file[ 'url' ],
            'post_title' => sprintf( __( '%1s Logo', 'flawless' ), get_bloginfo( 'name' ) )
          ), $file[ 'file' ] );

          if ( !is_wp_error( $post_id ) ) {
            $flawless[ 'flawless_logo' ][ 'post_id' ] = $post_id;

            //** Delete old logo */
            if ( is_numeric( $current_settings[ 'flawless_logo' ][ 'post_id' ] ) ) {
              wp_delete_attachment( $current_settings[ 'flawless_logo' ][ 'post_id' ], true );
            }

            update_post_meta( $flawless[ 'flawless_logo' ][ 'post_id' ], '_wp_attachment_metadata', array( 'width' => $image_size[ 0 ], 'height' => $image_size[ 1 ] ) );
          }

        } else {
          unset( $flawless[ 'flawless_logo' ] );

        }

      }

      //** Cycle through settings and copy over any special keys */
      foreach ( (array) apply_filters( 'flawless_preserved_setting_keys', array( 'flex_layout' ) ) as $key ) {
        $flawless[ $key ] = !empty( $flawless[ $key ] ) ? $flawless[ $key ] : $current_settings[ $key ];
      }

      $flawless = apply_filters( 'flawless::update_settings', $flawless );

      update_option( 'flawless_settings', $flawless );

      delete_option( 'flawless::compiled_css_files' );

      flush_rewrite_rules();

      //** Redirect page to default Theme Settings page */
      return add_query_arg( 'message', $args[ 'message' ], Flawless_Admin_URL );

    }

  }

}