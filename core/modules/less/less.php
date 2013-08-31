<?php
/**
 * Name: Flawless LESS Handler
 * Version: 1.0
 * Description: Extends lessphp.
 * Author: Usability Dynamics, Inc.
 * Theme Feature: less-css
 *
 *
 * =Documentation=
 * If the feature is enabled, on every page load the Flawless::build_compiled_css() is called which in turn calls flawless_class,
 * which processes /less/bootstrap.less, files references form within, as well as all enqueued front-end CSS files.
 * A reference to all includes files is saved in option "flawless::compiled_css_files"
 *
 * If one of the files has a a newer modification time than the compiled CSS files, then the compiled file is re-generated.
 * which are saved to the stylesheet directory, which is either the child theme, or the parent theme.
 *
 * When succssful, Flawless generates two files, bootstrap-compiled.css and bootstrap-compiled.min.css.
 *
 * =Developer Notes=
 * The file checking occurs at the wp_print_styles (0) action, since all the enqued styles must be known at that point.
 *
 * $flawless[ '_bootstrap_compiled_url' ] - URL to compiled CSS file.
 * $flawless[ '_bootstrap_compiled_path' ] - URL to compiled CSS file.
 * $flawless[ '_bootstrap_compiled_minified_path' ] - File path to minified compiled CSS file
 * $flawless[ '_bootstrap_compiled_minified_url' ] - URL to minified CSS file.
 *
 */

Flawless::load( 'lessc.inc' );

if ( !class_exists( 'lessc' ) ) {
  return;
}

class Flawless_LESS extends lessc {

  /**
   * Initializer.
   *
   * @since Flawless 0.6.1
   */
  function __construct() {

    //** On initial call, we need to initialize the feature via hooks */
    if ( !did_action( 'flawless::available_theme_features' ) ) {
      return Flawless_LESS::initialize();
    }

  }

  /**
   * Generates a compiled CSS file from multiple CSS and LESS files
   *
   * @since 0.0.3
   */
  function build_compiled_css( $styles, $args = array() ) {
    global $flawless;

    $args = wp_parse_args( $args, array(
      'minified_output_path' => $flawless[ '_bootstrap_compiled_minified_path' ],
      'output_path' => $flawless[ '_bootstrap_compiled_path' ]
    ));

    //** We do not ensure that feature is supported, but the class is required */
    if ( !class_exists( 'Flawless_LESS' ) ) {
      //return new WP_Error( 'error', Flawless::console_log( sprintf( __( 'CSS Compiling Error: Library not found.', 'flawless' ) ), 'error' ));
    }

    //** Verify that the target directory is writable.
    if ( !is_writable( dirname( $flawless[ '_bootstrap_compiled_minified_path' ] ) ) ) {
      return new WP_Error( 'error', Flawless::console_log( sprintf( __( 'CSS Compiling Error: Directory %1s is not writable', 'flawless' ), dirname( $flawless[ '_bootstrap_compiled_minified_path' ] ) ), 'error' ));
    }

    foreach ( (array) $styles as $handle => $style_data ) {
      $_handles[ ] = dirname( $style_data[ 'path' ] ) . '/' . $style_data[ 'file_name' ];
      $_paths[ ] = $style_data[ 'path' ];
    }

    if ( empty( $_handles ) ) {
      return;
    }

    //$_Flawless_LESS = new Flawless_LESS();

    //** Cycle through each CSS file and check for validation */
    foreach ( (array) $_paths as $path ) {
      //if ( is_wp_error( $_validation = $_Flawless_LESS->compile( $path ) ) ) {
      // $_validation_errors[ ] = $path . ' - ' . $_validation->get_error_message();
      //}
    }

    if ( $_validation_errors && is_array( $_validation_errors ) && count( $_validation_errors ) > 0 ) {
      return new WP_Error( 'compile_error', Flawless::console_log( sprintf( __( 'CSS Compiling Errors:<br /> %1s ', 'flawless' ), implode( '<br />', $_validation_errors ) ), 'error' ));
    }

    //** Pass CSS array for real complication. */
    //if ( is_wp_error( $output = $_Flawless_LESS->compile( $_paths ) ) ) {
    //return new WP_Error( 'compile_error', Flawless::console_log( sprintf( __( 'LESS Compiling Error: %1s ', 'flawless' ), $output->get_error_message() ), 'error' ));
    //}

    $_header = apply_filters( 'flawless::compiled_css::css_header', array(
      '/**',
      ' * Name: ' . get_bloginfo() . ' Screen Styles',
      ' * Generated: ' . date( get_option( 'date_format' ) ) . ' at ' . date( get_option( 'time_format' ) ),
      ' * Compilation Time: ' . round( timer_stop() ) . ' seconds',
      ' * Source Files: ',
      ' * - ' . implode( " \n * - ", (array) $_handles ),
      ' * ',
      ' */'
    ));

    if ( empty( $output[ 'parsed' ] ) ) {
      //return new WP_Error( 'compile_error', Flawless::console_log( sprintf( __( 'CSS Compiling Error: Compiled file empty after attempting to compile (%1s) CSS files.', 'flawless' ), count( (array) $_handles ) ), 'error' ));
    }

    //** @todo Is there a way to catch warnings? - potanin@UD 6/10/12 */
    if ( WP_DEBUG ) {
      file_put_contents( $args[ 'output_path' ], implode( "\n", (array) $_header ) . "\n\n" . $output[ 'parsed' ] );
      file_put_contents( $args[ 'minified_output_path' ], $output[ 'minified' ] );
    } else {
      @file_put_contents( $args[ 'output_path' ], implode( "\n", (array) $_header ) . "\n\n" . $output[ 'parsed' ] );
      @file_put_contents( $args[ 'minified_output_path' ], $output[ 'minified' ] );
    }

    if ( !file_exists( $args[ 'minified_output_path' ] ) ) {
      return new WP_Error( 'saving_error', Flawless::console_log( sprintf( __( 'CSS Compiling Error: Compiled file (%1s) could not be saved to disk.', 'flawless' ), $args[ 'minified_output_path' ] ), 'error' ));
    }

    Flawless::console_log( sprintf( __( 'CSS Compiling: - Compiled file created from (%1s) files. Minified version is %2s and the uncompressed version is %3s.', 'flawless' ), count( $_handles ), self::format_bytes( filesize( $args[ 'output_path' ] ) ), self::format_bytes( filesize( $args[ 'minified_output_path' ] ) ) ));

    update_option( 'flawless::compiled_css_files', $styles );

    return true;

  }

  /**
   * Feature Initializer
   *
   * @since Flawless 0.6.1
   */
  function initialize() {

    /**
     * Add Frontend Editor as a Theme Feature. If already declared as Available Theme Feature, the exiting value takes presedence.
     *
     * @since Flawless 0.6.1
     */
    add_filter( 'flawless::available_theme_features', function ( $features ) {
      return array_merge( array( 'less-css' => true ), (array)$features );
    } );

    /**
     * {missing description}
     *
     * @todo admin_bar_menu should be using Flawless::add_to_navbar() which should then add the Editor link to the appropriate Navbar. - potanin@UD 6/9/2012
     * @since Flawless 0.6.1
     */
    add_action( 'flawless::theme_setup::after', function () {

      if ( !current_theme_supports( 'less-css' ) ) {
        return;
      }

    }, 200 );

  }

  /**
   * Compile LESS
   *
   * @param $paths
   * @param array $args
   * @return string|WP_Error
   */
  public function compile( $paths, $args = array() ) {
    global $flawless;

    $args = wp_parse_args( $form_data, array(
      'formatter' => false,
      'debug' => WP_DEBUG,
      'tidy' => true,
      'minify' => true
    ) );

    foreach ( (array)$paths as $path ) {
      $_contents .= file_get_contents( $path );
    }

    try {
      $_less = new lessc();

      $_less->importDir[ ] = untrailingslashit( get_stylesheet_directory() ) . '/less';
      $_less->importDir[ ] = untrailingslashit( get_template_directory() ) . '/less';
      $_less->importDir[ ] = untrailingslashit( get_stylesheet_directory() ) . '/css';
      $_less->importDir[ ] = untrailingslashit( get_template_directory() ) . '/css';

      if ( $args[ 'formatter' ] ) {
        $_less->setFormatter( $args[ 'formatter' ] );
      }

      foreach ( (array)$flawless[ 'css_options' ] as $name => $option ) {
        $_less_variables[ '@' . $name ] = $option[ 'value' ] . ';';
      }

      if ( $args[ 'debug' ] ) {
        $code[ 'parsed' ] = $_less->parse( $_contents, $_less_variables );
      } else {
        $code[ 'parsed' ] = @$_less->parse( $_contents, $_less_variables );
      }

    } catch ( exception $ex ) {
      return new WP_Error( 'compile_error', $ex->getMessage() );
    }

    if ( $args[ 'tidy' ] && ( class_exists( 'csstidy' ) || file_exists( untrailingslashit( get_template_directory() ) . '/core/libs/csstidy/class.csstidy.php' ) ) ) {

      include_once untrailingslashit( get_template_directory() ) . '/core/libs/csstidy/class.csstidy.php';

      if ( class_exists( 'csstidy' ) ) {
        $css = new csstidy();
        $css->load_template( untrailingslashit( get_template_directory() ) . '/core/libs/csstidy/template.tpl' );
        $data = $css->parse( $code[ 'parsed' ] );
        $code[ 'formatted' ] = $css->print->plain();
      }
    }

    if ( $args[ 'minify' ] && ( class_exists( 'CssMin' ) || Flawless::load( 'CssMin' ) ) ) {
      $code[ 'minified' ] = CssMin::minify( $code[ 'parsed' ] );
    }

    return $code;

  }

}

$Flawless_LESS = new Flawless_LESS();