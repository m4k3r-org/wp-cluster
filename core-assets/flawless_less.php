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
 * If the feature is enabled, on every page load the flawless_theme::build_compiled_css() is called which in turn calls flawless_class,
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

flawless_theme::load( 'lessc.inc' );

if( !class_exists( 'lessc' ) ) {
  return;
}

class flawless_less extends lessc {

  /**
    * Initializer.
    *
    * @since Flawless 0.6.1
    */
  function __construct() {

    //** On initial call, we need to initialize the feature via hooks */
    if( !did_action( 'flawless::available_theme_features' ) ) {
      return flawless_less::initialize();
    }

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
    add_filter( 'flawless::available_theme_features', function( $features ) {
      return array_merge( array( 'less-css' => true ), (array) $features );
    });


    /**
      * {missing description}
      *
      * @todo admin_bar_menu should be using flawless_theme::add_to_navbar() which should then add the Editor link to the appropriate Navbar. - potanin@UD 6/9/2012
      * @since Flawless 0.6.1
      */
    add_action( 'flawless::theme_setup::after', function() {

      if( !current_theme_supports( 'less-css' ) ) {
        return;
      }

    }, 200 );

  }

  // compile $in to $out if $in is newer than $out
  // returns true when it compiles, false otherwise
  // now takes multiple arguments for $in

  public static function compile( $paths, $args = array() ) {
    global $flawless;

    $args = wp_parse_args( $form_data, array(
      'formatter' => false,
			'debug' => WP_DEBUG,
      'tidy' => true,
      'minify' => true
    ));

    foreach( (array) $paths as $path ) {
      $_contents .= file_get_contents( $path );
    }

    try {
      $_less = new lessc();

      $_less->importDir[] = untrailingslashit( get_stylesheet_directory() ) . '/less';
      $_less->importDir[] = untrailingslashit( get_template_directory() ) . '/less';
      $_less->importDir[] = untrailingslashit( get_stylesheet_directory() ) . '/css';
      $_less->importDir[] = untrailingslashit( get_template_directory() ) . '/css';

      if( $args[ 'formatter' ] ) {
        $_less->setFormatter( $args[ 'formatter' ] );
      }

      foreach( (array) $flawless[ 'css_options' ] as $name => $option ) {
        $_less_variables[ '@' . $name ] = $option[ 'value' ] . ';';
      }

			if( $args[ 'debug' ] ) {
				$code[ 'parsed' ] = $_less->parse( $_contents, $_less_variables );
			} else {
				$code[ 'parsed' ] = @$_less->parse( $_contents, $_less_variables );
			}

    } catch ( exception $ex ) {
      return new WP_Error( 'compile_error', $ex->getMessage() );
    }

    if( $args[ 'tidy' ] && ( class_exists( 'csstidy' ) || file_exists( untrailingslashit( get_template_directory() ) . '/libs/csstidy/class.csstidy.php' ) ) ) {

      include_once untrailingslashit( get_template_directory() ) . '/libs/csstidy/class.csstidy.php';

      if( class_exists( 'csstidy' ) ) {
        $css = new csstidy();
        $css->load_template( untrailingslashit( get_template_directory() ) . '/libs/csstidy/template.tpl' );
        $data = $css->parse( $code[ 'parsed' ] );
        $code[ 'formatted' ] = $css->print->plain();				
      }
    }

    if( $args[ 'minify' ] && ( class_exists( 'CssMin' ) || flawless_theme::load( 'CssMin' ) ) ) {
      $code[ 'minified' ] = CssMin::minify( $code[ 'parsed' ] );
    }

    return $code;

  }

}


$flawless_less = new flawless_less();