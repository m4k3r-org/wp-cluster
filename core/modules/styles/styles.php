<?php
/**
 * Flawless
 *
 * @author potanin@UD
 * @version 0.0.1
 * @namespace Flawless
 */
namespace Flawless {

  /**
   * Styles
   *
   * -
   *
   * @author potanin@UD
   * @version 0.1.0
   * @class Styles
   */
  class Styles {

    // Class Version.
    public $version = '0.1.1';

    /**
     * Constructor for the Styles class.
     *
     * @author potanin@UD
     * @version 0.0.1
     * @method __construct
     *
     * @constructor
     * @for Styles
     *
     * @param array options
     */
    public function __construct( $options = array() ) {

      add_action( 'flawless::theme_setup', array( __CLASS__, 'theme_setup' ) );

      add_action( 'flawless::wp_print_styles', array( __CLASS__, 'wp_print_styles' ) );

    }

    /**
     * Setup Theme Styles
     *
     * @method theme_setup
     * @for Styles
     *
     * @param $flawless
     *
     * @return mixed
     */
    static function theme_setup( &$flawless ) {

      //** Get default LESS options */
      __SELF__::parse_css_option_defaults( $flawless );

      return $flawless;

    }

    /**
     * Get default LESS variables from a file. These are later overwritten by theme settings.
     *
     * @todo Include a way of grouping variables together based on the CSS comments. - potanin@UD 6/11/12
     * @since 0.0.6
     * @author potanin@UD
     */
    static function parse_css_option_defaults( &$flawless, $file = 'variables.less' ) {

      $flawless[ 'css_options' ] = array();

      if ( $_variables_path = Asset::load( $file, 'less', array( 'return' => 'path' ) ) ) {
        $_content = file_get_contents( $_variables_path );
        $_lines = explode( "\n", $_content );
      }

      function de_camel( $str ) {
        $str[ 0 ] = strtolower( str_replace( '@', '', $str[ 0 ] ) );
        return ucwords( trim( preg_replace( '/([A-Z])/e', "' ' . strtolower('\\1')", $str ) ) );
      }

      foreach ( (array) $_lines as $line => $_line_string ) {

        if ( strpos( trim( $_line_string ), '@' ) === 0 ) {

          @list( $name, $value ) = (array) explode( ':', $_line_string );
          @list( $value, $description ) = explode( '//', $value );

          $name = str_replace( '@', '', $name );

          switch ( true ) {
            case strpos( $name, 'FontFamily' ):
              $type = 'font';
              break;
            case strpos( $name, 'fluidGrid' ):
              $type = 'percentage';
              break;
            case strpos( $name, 'Width' ):
              $type = 'pixels';
              break;
            case strpos( $name, 'Text' ):
            case strpos( $name, 'Color' ):
            case strpos( $name, 'Border' ):
            case strpos( $name, 'Background' ):
              $type = 'color';
              break;
            case strpos( $name, 'zindex' ):
              $type = 'hidden';
              break;
            case strpos( $name, 'Path' ):
              $type = 'url';
              break;
          }

          $flawless[ 'css_options' ][ $name ] = array_filter( array(
            'label' => str_replace( 'Btn', __( 'Button', 'flawless' ), de_camel( $name ) ),
            'value' => str_replace( ';', '', trim( $value ) ),
            'name' => trim( $name ),
            'type' => $type ? $type : false,
            'description' => trim( $description )
          ) );

        }

      }

      $flawless[ 'css_options' ] = apply_filters( 'flawless::css_options', array_filter( $flawless[ 'css_options' ] ) );

      Log::add( sprintf( __( 'Flawless::parse_css_option_defaults() completed i n %2s seconds', 'flawless' ), timer_stop() ) );

      return $flawless;

    }

    /**
     * Compile Styles and Include Header Styles
     *
     * @method wp_print_styles
     * @for Styles
     */
    static function wp_print_styles() {
      global $flawless, $wp_styles;

      //** Analyze all Enqueued Styles for compiling */
      foreach ( (array) $wp_styles->queue as $key => $handle ) {

        $style_data = (array) $wp_styles->registered[ $handle ];

        $style_data[ 'file_name' ] = basename( $wp_styles->registered[ $handle ]->src );
        $style_data[ 'url' ] = $wp_styles->registered[ $handle ]->src;
        $style_data[ 'path' ] = untrailingslashit( apply_filters( 'flawless::root_path', ABSPATH ) ) . str_replace( untrailingslashit( home_url() ), '', $wp_styles->registered[ $handle ]->src );
        if ( !empty( $style_data[ 'args' ] ) && ( $style_data[ 'args' ] == 'print' ) ) {
          Log::add( sprintf( __( 'CSS Compiling: Excluding %1s because it is print only. ', 'flawless' ), $style_data[ 'file_name' ] ), 'info' );
          continue;
        }

        if ( defined( 'WP_PLUGIN_DIR' ) && $flawless[ 'do_not_compile_plugin_css' ] == 'true' && strpos( $style_data[ 'path' ], Utility::fix_path( WP_PLUGIN_DIR ) ) !== false ) {
          continue;
        }

        //** Add file to complication array if it is local and accessible*/
        if ( file_exists( $style_data[ 'path' ] ) ) {
          $flawless[ '_compilable_styles' ][ $handle ] = array_merge( $style_data, array( 'modified' => filemtime( $style_data[ 'path' ] ), 'file_size' => filesize( $style_data[ 'path' ] ) ) );
          $_modified_times[ $style_data[ 'file_name' ] ] = filemtime( $style_data[ 'path' ] );
        } else {

        }

      }

      if ( empty( $flawless[ '_compilable_styles' ] ) ) {
        Log::add( sprintf( __( 'CSS Compiling: No compilable styles were detected. ', 'flawless' ), $style_data[ 'file_name' ] ), 'error' );
      }

      //** If compiled CSS does not exist or is outdated, we re-generate */
      if ( !file_exists( $flawless[ '_bootstrap_compiled_path' ] ) ) {
        $_update_reason = 'initial';
      }

      if ( is_array( $_modified_times ) && !$_update_reason && file_exists( $flawless[ '_bootstrap_compiled_path' ] ) && ( filemtime( $flawless[ '_bootstrap_compiled_path' ] ) < max( (array) $_modified_times ) ) ) {
        $_update_reason = array_search( max( (array) $_modified_times ), $_modified_times );
      }

      if ( !$_update_reason && get_option( 'flawless::compiled_css_files' ) == '' ) {
        $_update_reason = 'system_trigger';
      }

      if ( $_update_reason ) {

        /*
       *
      // If compiled, enqueue the compiled CSS and remove the compiled styles
      if ( !is_wp_error( $_css_is_compiled = Flawless_LESS::build_compiled_css( $flawless[ '_compilable_styles' ] ) ) ) {

        if ( $_update_reason == 'initial' ) {
          flawless_add_notice( sprintf( __( 'Compiled CSS file has been successfully generated.', 'flawless' ), '#flawless_action#disable_notice=compiled_css_generation', 'hide' ));
        } elseif ( $_update_reason == 'system_trigger' ) {
          flawless_add_notice( sprintf( __( 'Compiled CSS file has been successfully generated, triggered by system.', 'flawless' ), '#flawless_action#disable_notice=compiled_css_generation', 'hide' ));
        } elseif ( !empty( $_update_reason ) ) {
          flawless_add_notice( sprintf( __( 'Compiled CSS automatically updated due to (%1s) having a more recent modified date than the compiled CSS.', 'flawless' ), array_search( max( (array) $_modified_times ), $_modified_times ), 'hide' ));
        }

      } else {

        if ( $flawless[ 'developer_mode' ] == 'true' ) {
          wp_die( '<h2>' . __( 'CSS Compile Error', 'flawless' ) . '</h2> ' . $_css_is_compiled->get_error_message(), '' );
        }

        Log::add( sprintf( __( 'CSS Compiling Error: %1s.', 'flawless' ), $_css_is_compiled->get_error_message() ? $_css_is_compiled->get_error_message() : __( 'Unknown Error.' ) ), 'error' );
      }

      */

      } else {
        Log::add( sprintf( __( 'CSS Compiling: Compiled file is up to date. ', 'flawless' ) ), 'info' );
      }

      //** We don't Enqueue this until now to exclude it from compiling */
      // wp_enqueue_style( 'flawless-compiled-css', $flawless[ 'developer_mode' ] == 'true' ? $flawless[ '_bootstrap_compiled_url' ] : $flawless[ '_bootstrap_compiled_minified_url' ], array(), Flawless_Version, 'screen' );

      foreach ( (array) $flawless[ '_compilable_styles' ] as $handle => $style_data ) {
        wp_dequeue_style( $handle );
      }

      $flawless_header_css = array();

      if ( current_theme_supports( 'custom-background' ) && get_header_image() ) {
        $flawless_header_css[ ] = ' .background_header_image { background-image: url( ' . get_header_image() . ' );  height: 100%; max-height: ' . HEADER_IMAGE_HEIGHT . 'px; }';
      }

      if ( $flawless[ 'layout_width' ] ) {
        $flawless_header_css[ ] = 'body div.container { max-width: ' . $flawless[ 'layout_width' ] . 'px; }';
      }

      //** Included fixed image sizes for faster rendering, and masonry support */
      foreach ( (array) Utility::all_image_sizes() as $size => $data ) {
        $flawless_header_css[ ] = '.gallery .gallery-item img.attachment-' . $size . ' { width: ' . $data[ 'width' ] . 'px; }';
        $flawless_header_css[ ] = 'img.fixed_size.attachment-' . $size . ' { width: ' . $data[ 'width' ] . 'px; }';
      }

      if ( is_array( $flawless_header_css ) ) {
        wp_add_inline_style( 'flawless_header_css', implode( '', (array) apply_filters( 'flawless::header_css', $flawless_header_css, $flawless ) ) );
      }

    }

  }

}