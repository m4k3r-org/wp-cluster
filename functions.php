<?php
/**
 * Author: Usability Dynamics, Inc.
 * Author URI: http://www.insidedesign.info/
 *
 * @version 0.10
 * @author Insidedesign
 * @subpackage Flawless
 * @package Flawless
 */

if( isset( $_SERVER[ 'HTTP_X_UDS_DEBUG' ] ) ) {
  header( "Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
  header( "Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

  add_action( 'wp_footer', function() {
    // echo "\n<!-- " . DB_HOST . "  -->\n";
    // echo "\n<!-- " . DB_NAME . "  -->\n";
    // echo "\n<!-- " . DB_USER . "  -->\n";
  }, 1000 );
}

if( !is_dir( TEMPLATEPATH ) ) {
	wp_die(TEMPLATEPATH);
	return;
}

include_once( untrailingslashit( TEMPLATEPATH ) . '/core-assets/class_ud.php' );
include_once( untrailingslashit( STYLESHEETPATH ) . '/core-assets/ud_saas.php' );
include_once( untrailingslashit( STYLESHEETPATH ) . '/core-assets/ud_functions.php' );
include_once( untrailingslashit( STYLESHEETPATH ) . '/core-assets/template_functions.php' );
include_once( untrailingslashit( STYLESHEETPATH ) . '/core-assets/microdata.php' );

include_once( untrailingslashit( STYLESHEETPATH ) . '/entities/entity.php' );
include_once( untrailingslashit( STYLESHEETPATH ) . '/entities/event.php' );
include_once( untrailingslashit( STYLESHEETPATH ) . '/entities/venue.php' );
include_once( untrailingslashit( STYLESHEETPATH ) . '/entities/artist.php' );
include_once( untrailingslashit( STYLESHEETPATH ) . '/entities/tour.php' );
include_once( untrailingslashit( STYLESHEETPATH ) . '/entities/imagegallery.php' );
include_once( untrailingslashit( STYLESHEETPATH ) . '/entities/videoobject.php' );
include_once( untrailingslashit( STYLESHEETPATH ) . '/entities/promoter.php' );
include_once( untrailingslashit( STYLESHEETPATH ) . '/entities/credit.php' );
include_once( untrailingslashit( STYLESHEETPATH ) . '/entities/event-taxonomy.php' );
include_once( untrailingslashit( STYLESHEETPATH ) . '/entities/venue-taxonomy.php' );
include_once( untrailingslashit( STYLESHEETPATH ) . '/entities/artist-taxonomy.php' );

$_package = json_decode( file_get_contents( __DIR__ . '/package.json' ));

/* Define Child Theme Version */
define( 'HDDP_Version', $_package->version );

/* Transdomain */
define( 'HDDP', 'HDDP' );

/* Initialize */
add_action( 'flawless::theme_setup::after', array( 'hddp', 'theme_setup' ) );
add_action( 'flawless::init_upper', array( 'hddp', 'init_upper' ) );
add_action( 'flawless::init_lower', array( 'hddp', 'init_lower' ) );
add_filter( 'elasticsearch_indexer_build_document', array( 'hddp', 'build_elastic_document' ), 10, 2 );
add_action( 'flawless::setup_theme_features::after', array( 'hddp', 'carrington_build' ) );

add_action( 'admin_menu', array( 'hddp', 'es_menu' ), 11 );

add_action( 'post_submitbox_misc_actions', array( 'hddp', 'autogenerator_submitbox_actions' ) );
add_action( 'save_post', array( 'hddp', 'autogenerate_post_elements' ), 1, 2 );

/**
 * fix for broken CSS
 */
if ( ! function_exists( 'flawless_module_class' ) ) {
  /**
   * Builds classes for the .hentry.cfct-module based on conditional elements.
   *
   * Called in templates intead of post_class(). On CB pages, the cfct-move is removed by flawless_carrington::module_class()
   *
   * @since Flawless 0.5.0
   * @author potanin@UD
   */
  function flawless_module_class( $custom_class = '' ) {
    global $flawless, $wp_query, $post;

    //** Load Post Classes if this is a post */
    $classes = get_post_class( '', $post->ID );

    $classes[] = $custom_class;
    if ( $post->post_type != 'page' ) {
      $classes[] = 'cfct-module';
    }

    $classes = apply_filters( 'flawless::module_class', $classes );

    echo implode( ' ', ( array ) $classes );

  }
}


/**
 * Functionality for Theme
 *
 * @author potanin@UD
 */
class hddp extends Flawless_F {

  /**
   * Primary Loader
   *
   * @author potanin@UD
   */
  static function theme_setup() {

	  remove_theme_support( 'header-dropdowns' );
    remove_theme_support( 'custom-header' );
    remove_theme_support( 'custom-background' );
    remove_theme_support( 'header-business-card' );
    remove_theme_support( 'frontend-editor' );
    remove_theme_support( 'custom-skins' );
    remove_theme_support( 'custom-background' );
    remove_theme_support( 'custom-header' );

    load_theme_textdomain( HDDP, get_template_directory() . '/lang' );

  }

	/**
	 *
	 */
	public static function carrington_build() {

		if( !file_exists( __DIR__ . '/vendor/usabilitydynamics/lib-carrington/lib/carrington-build.php' ) ) {
			return;
		}

		// Prevent Flawless' version of Carrington Build from being loaded.
		remove_action( 'flawless_theme_setup', 'flawless_carrington::flawless_theme_setup' );

		// Include our newer version of CB.
		include_once( __DIR__ . '/vendor/usabilitydynamics/lib-carrington/lib/carrington-build.php' );

		add_theme_support( 'carrington_build' );

		// Legacy filters still available in Flawless but commented out until needed.

		add_filter( 'cfct-module-dirs', array( 'flawless_carrington', 'cfct_module_dirs' ) );
		
		add_filter( 'cfct-build-display-class', array( 'flawless_carrington', 'cfct_build_display_class' ), 10, 4 );
		
		add_filter( 'cfct-generated-row-classes', array( 'flawless_carrington', 'cfct_generated_row_classes' ), 10, 4 );
		
		add_filter( 'cfct-block-c6-12-classes', function( $classes ) { return array_merge( array( 'span4', 'first' ), $classes ); }, 10, 2 );
		add_filter( 'cfct-block-c6-34-classes', function( $classes ) { return array_merge( array( 'span4' ), $classes ); }, 10, 2 );
		add_filter( 'cfct-block-c6-56-classes', function( $classes ) { return array_merge( array( 'span4', 'last' ), $classes ); }, 10, 2 );
		
		add_filter( 'cfct-block-c6-123-classes', function( $classes ) { return array_merge( array( 'span6', 'first' ), $classes ); }, 10, 2 );
		add_filter( 'cfct-block-c6-456-classes', function( $classes ) { return array_merge( array( 'span6', 'last' ), $classes ); }, 10, 2 );
		add_filter( 'cfct-block-c4-12-classes', function( $classes ) { return array_merge( array( 'span6', 'first' ), $classes ); }, 10, 2 );
		add_filter( 'cfct-block-c4-34-classes', function( $classes ) { return array_merge( array( 'span6', 'last' ), $classes ); }, 10, 2 );
		
		add_filter( 'cfct-block-c6-1234-classes', function( $classes ) { return array_merge( array( 'span8', 'first' ), $classes ); }, 10, 2 );
		add_filter( 'cfct-block-c6-3456-classes', function( $classes ) { return array_merge( array( 'span8', 'last' ), $classes ); }, 10, 2 );
		
		add_filter( 'cfct-block-c6-123456-classes', function( $classes ) { return array_merge( array( 'span12', 'first', 'full-width' ), $classes ); }, 10, 2 );
		add_filter( 'cfct-block-c4-1234-classes', function( $classes ) { return array_merge( array( 'span12', 'first', 'full-width' ), $classes ); }, 10, 2 );
		
		add_filter( 'cfct-build-module-class', array( 'flawless_carrington', 'cfct_module_wrapper_classes' ) , 10, 2 );
		
		add_filter( 'cfct-row-admin-html', array( 'flawless_carrington', 'cfct_row_admin_html' ), 10, 4 );
		add_filter( 'cfct-row-html', array( 'flawless_carrington', 'cfct_row_html' ), 10, 4 );
		
		add_filter( 'cfct-module-cfct-callout-admin-form', array( 'flawless_carrington', 'cfct_module_admin_theme_chooser' ) , 10, 2 );
		add_filter( 'cfct-module-cf-post-callout-module-admin-form', array( 'flawless_carrington', 'cfct_module_admin_theme_chooser' ) , 10, 2 );
		add_filter( 'cfct-module-cfct-heading-admin-form', array( 'flawless_carrington', 'cfct_module_admin_theme_chooser' ) , 10, 2 );
		add_filter( 'cfct-module-cfct-plain-text-admin-form', array( 'flawless_carrington', 'cfct_module_admin_theme_chooser' ) , 10, 2 );
		add_filter( 'cfct-module-cfct-rich-text-admin-form', array( 'flawless_carrington', 'cfct_module_admin_theme_chooser' ) , 10, 2 );
		add_filter( 'cfct-module-cfct-module-loop-admin-form', array( 'flawless_carrington', 'cfct_module_admin_theme_chooser' ) , 10, 2 );
		add_filter( 'cfct-module-cfct-module-loop-subpages-admin-form', array( 'flawless_carrington', 'cfct_module_admin_theme_chooser' ) , 10, 2 );
		add_filter( 'cfct-module-display', array( 'flawless_carrington', 'cfct_module_display' ) , 10, 3 );
		add_filter( 'cfct-build-module-url-unknown', array( 'flawless_carrington', 'module_url_unknown' ) , 10, 3 );
		
		add_action( 'cfct-modules-included', array( 'flawless_carrington', 'remove_loop_module' ) );

    // Add body class carrington_layout or non_carrington_layout
    add_filter( 'body_class', array( 'flawless_carrington', 'body_class' ), 200, 2 );

	}

  /**
   * Primary Loader.
   * Scripts and styles are registered here so they overwriten Flawless scripts if needed.
   *
   * @author potanin@UD
   */
  static function init_upper() {
    global $wpdb, $flawless;

    // fixes /vendor assets URLs for composer-isntalled libraries
    add_filter( 'includes_url', array( __CLASS__, 'includes_url' ), 5 );

    wp_register_script( 'knockout', get_stylesheet_directory_uri() . '/js/knockout.js', array(), '3.1.0', true );
    wp_register_script( 'jquery-ud-form_helper', get_stylesheet_directory_uri() . '/js/jquery.ud.form_helper.js', array( 'jquery-ui-core' ), '1.1.3', true );
    wp_register_script( 'jquery-ud-smart_buttons', 'http' . ( is_ssl() ? 's' : '' ) . '://cdn.usabilitydynamics.com/js/jquery.ud.smart_buttons/0.6/jquery.ud.smart_buttons.js', array( 'jquery-ui-core' ), '0.6', true );
    wp_register_script( 'jquery-ud-social', 'http' . ( is_ssl() ? 's' : '' ) . '://cdn.usabilitydynamics.com/js/jquery.ud.social/0.3/jquery.ud.social.js', array( 'jquery-ui-core' ), '0.3', true );
    wp_register_script( 'jquery-ud-execute_triggers', 'http' . ( is_ssl() ? 's' : '' ) . '://cdn.usabilitydynamics.com/js/jquery.ud.execute_triggers/0.2/jquery.ud.execute_triggers.js', array( 'jquery-ui-core' ), '0.2', true );

    wp_register_script( 'jquery-ud-elastic_filter', get_stylesheet_directory_uri() . '/js/jquery.ud.elastic_filter.js', array( 'jquery' ), HDDP_Version, true );

    wp_register_script( 'jquery-ud-date-slector', get_stylesheet_directory_uri() . '/js/jquery.ud.date_selector.js', array( 'jquery-ui-core' ), '0.1.1', true );
    wp_register_script( 'jquery-jqtransform', get_stylesheet_directory_uri() . '/js/jquery.jqtransform.js', array( 'jquery' ), HDDP_Version, true );
    wp_register_script( 'jquery-simplyscroll', get_stylesheet_directory_uri() . '/js/jquery.simplyscroll.min.js', array( 'jquery' ), HDDP_Version, true );
    wp_register_script( 'jquery-flexslider-1.8', get_stylesheet_directory_uri() . '/js/jquery.flexslider.1.8.js', array( 'jquery' ), '1.8', true );
    wp_register_script( 'jquery-flexslider', get_stylesheet_directory_uri() . '/js/jquery.flexslider.js', array( 'jquery' ), '2.2.2', true );
    wp_register_script( 'jquery-cookie', get_stylesheet_directory_uri() . '/js/jquery.cookie.js', array( 'jquery' ), '1.7.3', false );

    wp_register_script( 'hddp-frontend-js', get_stylesheet_directory_uri() . '/js/hddp.frontend.js', array( 'jquery', 'jquery-jqtransform', 'jquery-flexslider', 'flawless-frontend' ), HDDP_Version, true );
    wp_register_script( 'hddp-backend-js', get_stylesheet_directory_uri() . '/js/hddp.backend.js', array( 'jquery-ui-tabs', 'flawless-admin-global', 'jquery-ud-execute_triggers' ), HDDP_Version, true );
    wp_register_script( 'google-maps', 'https://maps.googleapis.com/maps/api/js?sensor=true' );

    wp_register_style( 'hddp-backend-css', get_stylesheet_directory_uri() . '/css/hddp.backend.css' );
    wp_register_style( 'jquery-jqtransform', get_stylesheet_directory_uri() . '/css/jqtransform.css' );
    wp_register_style( 'jquery-simplyscroll', get_stylesheet_directory_uri() . '/css/jquery.simplyscroll.css' );

    wp_register_script( 'jquery-fitvids', get_stylesheet_directory_uri() . '/js/jquery.fitvids.js', array( 'jquery' ), HDDP_Version, true );

    //** DDP Elastic */
    wp_register_script( 'jquery-ddp-elastic-suggest', get_stylesheet_directory_uri() . '/components/lib-js-elastic-filter/lib-js-elastic-filter-built.js', array( 'jquery', 'jquery-cookie' ), HDDP_Version, true );
    wp_register_script( 'elastic-dsl', get_stylesheet_directory_uri() . '/js/elastic.dsl.js', array(), HDDP_Version, true );
    wp_register_script( 'xmlhttpclient', get_stylesheet_directory_uri() . '/js/xmlhttpclient.js', array(), HDDP_Version, true );

    add_filter( 'the_content', array( 'hddp', 'featured_image_in_feed' ) );

    add_action( 'widgets_init', function () {
      if( class_exists( 'HDP_Latest_Posts_Widget' ) ) {
        register_widget( 'HDP_Latest_Posts_Widget' );
      }
    } );

    add_image_size( 'hd_large', 890, 460, true );
    add_image_size( 'hd_small', 230, 130, true );
    add_image_size( 'gallery', 200, 999 );
    add_image_size( 'sidebar_poster', 310, 999 );
    add_image_size( 'events_flyer_thumb', 140, 999 );
    add_image_size( 'tiny_thumbnail', 100, 80 );
    add_image_size( 'sidebar_thumb', 120, 100, true );

    set_post_thumbnail_size( 230, 130 );

    unregister_nav_menu( 'footer-menu' );

    //** Disable unsupported Carrington Build modules */
    add_action( 'cfct-modules-included', function () {
      cfct_build_deregister_module( 'cfct_module_hero' );
      cfct_build_deregister_module( 'cfct_module_notice' );
      cfct_build_deregister_module( 'cfct_module_pullquote' );
      cfct_build_deregister_module( 'cfct_module_loop_subpages' );
      cfct_build_deregister_module( 'cfct_module_plain_text' );
    } );

    flawless_set_color_scheme( 'skin-default.css' );

    /**
     * If Plugin exists add custom index handler for taxonomies
     */
    if ( class_exists('\elasticsearch\Indexer') ) {
      add_action( 'create_term', array( __CLASS__, "es_index_term" ), 10, 3 );
      add_action( 'edited_term', array( __CLASS__, "es_index_term" ), 10, 3 );
      add_action( 'delete_term', array( __CLASS__, "es_delete_term" ), 10, 4 );
      add_action( 'wp_ajax_reindex_taxonomies', array( __CLASS__, "es_index_taxonomies" ) );
      add_action( 'wp_ajax_remap_all', array( __CLASS__, "es_remap_all" ) );
    }

    // redirect unused post type archives
    add_action( 'template_redirect', array( __CLASS__, 'maybe_redirect_post_type_archive' ) );

    // Check whether the Meta-Box plugin is installed or not
    add_action( 'admin_notices', array( __CLASS__, 'check_for_metabox_plugin' ) );
    add_action( 'network_admin_notices', array( __CLASS__, 'check_for_metabox_plugin' ) );

  }

  /**
   * Inject Theme-specific Includes URL Rewrites
   *
   * includes_url( '/theme/img/placeholder.png' )
   *
   *
   * @todo Add test.
   *
   * @param null $url
   * @param null $path
   * @return mixed|null
   */
  static public function includes_url( $url = null, $path = null ) {

    // Get theme Images
    if( strpos( $url, '/theme/img/' ) ) {
      $url = str_replace( site_url( '/' . WPINC . '/theme/' ), trailingslashit( get_stylesheet_directory_uri() ), $url );
    }

    // Get theme JS
    if( strpos( $url, '/theme/js/' ) ) {
      $url = str_replace( site_url( '/' . WPINC . '/theme/' ), trailingslashit( get_stylesheet_directory_uri() ), $url );
    }

    // Get theme CSS
    if( strpos( $url, '/theme/css/' ) ) {
      $url = str_replace( site_url( '/' . WPINC . '/theme/' ), trailingslashit( get_stylesheet_directory_uri() ), $url );
    }

    if( strpos( $url, '/wp-includes/vendor/libraries' )) {
      $url = str_replace( '/wp-includes/vendor/libraries', '/vendor/libraries', $url );
    }

    return $url;

  }

  /**
   * Add custom menu item
   */
  static function es_menu( $wp_admin_bar ) {
    add_submenu_page( 'elastic_search', 'Additional ElasticSearch Options', 'Advanced [DDP]', 'manage_options', 'es_options', array( __CLASS__, 'es_options_ui' ) );
  }

  /**
   * Render reindex tax UI
   */
  static function es_options_ui() {
    include_once 'templates/admin.elastic_management.php';
  }

  /**
   * Delete term from es
   * @param $term_id
   * @param type $tt_id
   * @param type $taxonomy
   * @param type $deleted_term
   * @internal param \type $term
   */
  static function es_delete_term( $term_id, $tt_id, $taxonomy, $deleted_term ) {

    $taxonomies = \elasticsearch\Config::taxonomies();

    if ( !in_array( $taxonomy, $taxonomies ) ) return;

    $type = \elasticsearch\Indexer::_index(true)->getType( $taxonomy );

    try{
			$type->deleteById( $term_id );
		}catch(\Elastica\Exception\NotFoundException $ex){
			// ignore
		}

  }

  /**
   * index term
   * @param type $term_id
   * @param type $tt_id
   * @param type $taxonomy
   */
  static function es_index_term( $term_id, $tt_id, $taxonomy ) {

    $taxonomies = \elasticsearch\Config::taxonomies();

    if ( !in_array( $taxonomy, $taxonomies ) ) return;

    $_term_object = new \DiscoDonniePresents\Taxonomy( $term_id, $taxonomy );

    $type = \elasticsearch\Indexer::_index(true)->getType( $_term_object->getType() );

    $type->addDocument( new \Elastica\Document( $_term_object->getID(), $_term_object->toElasticFormat() ) );

  }

  /**
   * Reindex all terms
   */
  static function es_index_taxonomies() {

    $taxonomies = \elasticsearch\Config::taxonomies();

    if ( empty( $taxonomies ) ) die( \json_encode(array('success'=>0,'message'=>'No indexable taxonomies')) );

    $i = 0;

    foreach( (array)$taxonomies as $_tax ) {

      $_terms = get_terms($_tax);

      if ( !empty( $_terms ) && is_array( $_terms ) ) {

        foreach( $_terms as $_term ) {

          $_term_object = new \DiscoDonniePresents\Taxonomy( $_term->term_id, $_term->taxonomy );

          $type = \elasticsearch\Indexer::_index(true)->getType( $_term_object->getType() );

          $type->addDocument( new \Elastica\Document( $_term_object->getID(), $elastic_term = $_term_object->toElasticFormat() ) );

          $i++;

        }

      }

    }

    die( \json_encode(array('success'=>1,'message'=>'Done '.$i.' items')) );

  }

  /**
   *
   */
  static function es_remap_all() {

    $results = array();

    foreach( (array)json_decode( file_get_contents( get_stylesheet_directory().'/json/elasticsearch-mapping.json' ), true ) as $_type => $data ) {

      $type = \elasticsearch\Indexer::_index(false)->getType( $_type );

      try {

        $mapping = new \Elastica\Type\Mapping($type);
        $mapping->setProperties( $data['properties'] );
        if ( !empty( $data['_all'] ) ) {
          $mapping->setParam( '_all', $data['_all'] );
        }

        $results[$_type] = $mapping->send()->getData();

      } catch ( \Elastica\Exception\ClientException $ex ) {
        $results[$_type] = $ex->getMessage();
      } catch ( \Elastica\Exception\InvalidException $ex ) {
        $results[$_type] = $ex->getMessage();
      } catch ( \Exception $ex ) {
        $results[$_type] = $ex->getMessage();
      }

      sleep(1);

    }

    die( \json_encode(array('success'=>1, 'results'=>$results)) );

  }

  /**
   * Primary Loader
   *
   * @author potanin@UD
   */
  static function init_lower() {

    // enqueue theme style and dequeue unnecessary flawless styles
    // (these are included in the new theme stylesheet)
    add_action( 'wp_enqueue_scripts', function() {

      wp_dequeue_style( 'twitter-bootstrap' );
      wp_dequeue_style( 'flawless-style' );
      wp_dequeue_style( 'flawless-colors' );
      wp_dequeue_style( 'flawless-content' );
      wp_dequeue_style( 'flawless-custom-content' );
      wp_dequeue_style( 'flawless-child-style' );

      wp_enqueue_style( 'wp-disco-style', get_stylesheet_directory_uri() . '/css/app.css', array() );
      
    }, 120 );

    // enqueue additional scripts and styles
    add_action( 'wp_enqueue_scripts', function () {

      wp_enqueue_script( 'knockout' );
      wp_enqueue_script( 'hddp-frontend-js' );
      wp_enqueue_script( 'jquery-ui-tabs' );
      wp_enqueue_script( 'google-maps' );
      wp_enqueue_script( 'jquery-fitvids' );
      wp_enqueue_script( 'jquery-ui-datepicker' );
      wp_enqueue_style( 'jquery-jqtransform' );

      /** If we're on the front page, do simply scroll */
      if( is_front_page() ) {
        wp_enqueue_script( 'jquery-simplyscroll' );
        wp_enqueue_style( 'jquery-simplyscroll' );
      }

      wp_enqueue_script( 'jquery-ddp-elastic-suggest' );
      wp_enqueue_script( 'xmlhttpclient' );

    } );

    add_action( 'admin_enqueue_scripts', array( 'hddp', 'admin_enqueue_scripts' ) );

    add_action( 'template_redirect', array( 'hddp', 'template_redirect' ) );

    add_filter( 'the_category', function ( $c ) {
      return hddp::_backtrace_function( 'wp_popular_terms_checklist' ) ? '<span class="do_inline_hierarchial_taxonomy_stuff do_not_esc_html">' . $c . '</span>' : $c;
    } );

    add_filter( 'esc_html', function ( $s, $u = '' ) {
      return strpos( $s, 'do_not_esc_html' ) ? $u : $s;
    }, 10, 2 );

    add_action( 'flawless::header_bottom', function () {
      $header = flawless_breadcrumbs( array( 'hide_breadcrumbs' => false, 'wrapper_class' => 'breadcrumbs container', 'hide_on_home' => false, 'return' => true ) );
      $share  = hdp_share_button( false, true );
      echo $share . $header;
    } );

    add_filter( 'cfct-module-carousel-control-layout-order', function ( $order ) {
      return is_home() || is_front_page() ? array() : $order;
    } );

    add_filter( 'gform_shortcode_form', function ( $form ) {
      return preg_replace( '%(<select.*?>.+?<\/select>)%', '<div class="select-styled">$1</div>', $form );
    }, 10, 3 );

    /**
     * Custom Blog Pagination
     *
     * @author korotkov@ud
     * @ticket https://ud-dev.com/projects/projects/discodonniepresentscom-november-2012/tasks/19
     */
    add_filter( 'cfct-module-loop-query-args', function ( $args, $data ) {
      if( !empty( $_REQUEST[ 'paging' ] ) ) {
        $args[ 'paged' ] = $_REQUEST[ 'paging' ];
      }

      return $args;
    }, 10, 2 );
    add_filter( 'cfct-module-looploop-html', function ( $html, $data, $args, $query ) {
      $pagination = '';
      if( function_exists( 'wp_pagenavi' ) ) {
        $pagination = wp_pagenavi( array(
          'query' => $query,
          'echo'  => false
        ) );
      }

      return $html . $pagination;
    }, 10, 4 );
    add_filter( 'get_pagenum_link', function ( $link ) {
      $link = preg_replace( '%/blog\?paging.*%', '/blog', $link );
      if( strpos( $link, 'blog/page' ) ) {
        $link = preg_replace( '%/page/(\d{1,10}).*%', '?paging=$1', $link );
      }

      return $link;
    } );

    //** Remove URL from comments form */
    add_filter( 'comment_form_default_fields', function ( $fields ) {
      unset( $fields[ 'url' ] );

      return $fields;
    } );

    add_filter( 'img_caption_shortcode', array( 'hddp', 'img_caption_shortcode' ), 10, 3 );

    wp_enqueue_style( 'animate', get_stylesheet_directory_uri() . '/css/animate.css' );

    //** New Elastic Search Shortcodes */
    add_shortcode( 'elasticsearch_facets', array( 'hddp', 'elasticsearch_facets' ) );
    add_shortcode( 'elasticsearch_media', array( 'hddp', 'elasticsearch_media' ) );

    // Used on home, vieo and gallery pages.
    add_shortcode( 'hdp_custom_loop', array( 'hddp', 'shortcode_hdp_custom_loop' ) );

    // Used a lot.
    add_shortcode( 'hddp_gallery', array( 'hddp', 'shortcode_hddp_gallery' ) );

    // Used in Attachments' post_content
    add_shortcode( 'hddp_url', array( 'hddp', 'shortcode_hddp_url' ) );

  }

  /**
   * Force our custom template to load for Event post types
   *
   * @action template_redirect (10)
   * @author potanin@UD
   */
  static function template_redirect() {
    global $post, $flawless;

    //die( '<pre>' . print_r( wp_get_theme(), true ) . '</pre>' );
    /** Modify our HTML  for the mobile nav bar */
    $flawless[ 'mobile_navbar' ][ 'html' ][ 'left' ] = hdp_share_button( true, true ) . $flawless[ 'mobile_navbar' ][ 'html' ][ 'left' ];

    add_filter( 'single_template', function ( $template ) {

      if ( is_singular('event') || is_singular('imagegallery') || is_singular('videoobject') ) {
        add_filter( 'body_class', function ( $classes ) {
          return array_merge( $classes, array( 'single_event_page' ) );
        } );
      }

      return $template;

    } );

  }

  /**
   * Gets total events in the db
   */
  static function get_events_count() {
    global $wpdb;

    $wpdb->show_errors();

    return number_format( $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'event' AND post_status = 'publish'" ), 0 );
  }

  /**
   * Geo-locates and event based on venue address and updates the event meta.
   *
   * @todo Ideally address information should only be stored in the term's meta, not duplicated. - potanin@UD 5/17/12
   *
   * @param bool  $post_id
   * @param array $args
   *
   * @return bool true if posts location information was updated, false if it was not geolocated or already exists
   * @author potanin@UD
   */
  static function update_event_location( $post_id = false, $args = array() ) {

    if( !is_numeric( $post_id ) ) {
      return false;
    }

    $args = wp_parse_args( $args, array( 'add_log_entries' => true ) );

    /* Set coordinates for event */
    $post = get_post( $post_id );

    if( $post->post_type == '_tp_hdp_venue' ) {

      $extended_term_post_id = $post_id;
      $formatted_address     = get_post_meta( $extended_term_post_id, 'formatted_address', true );

    } else {

      $hdp_venue         = wp_get_object_terms( $post_id, 'hdp_venue' );
      $venue_term_id     = !is_wp_error( $hdp_venue ) ? $hdp_venue[ 0 ]->term_id : false;
      $formatted_address = get_term_meta( $venue_term_id, 'formatted_address', true );

    }

    if( $formatted_address && $geo_located = UD_Functions::geo_locate_address( $formatted_address ) ) {

      foreach( (array) $geo_located as $attribute => $value ) {
        update_post_meta( $post_id, $attribute, $value );

        if( $venue_term_id ) {
          update_term_meta( $venue_term_id, $attribute, $value );
        }

      }

      return true;

    } elseif( !empty( $formatted_address ) ) {

    } else {

    }

    return false;

  }

  /**
   * Admin Scripts
   *
   * @author potanin@UD
   */
  static function admin_enqueue_scripts() {

    /* General Scripts and CSS styles */
    wp_enqueue_script( 'hddp-backend-js' );
    wp_enqueue_style( 'hddp-backend-css' );

  }

  /**
   * Dummy shortcode.
   * Returns empty string.
   * It's used by self::shortcode_hddp_gallery()
   *
   * =USAGE=
   * Add [hddp_url href="http://example.com"] to Image Description textarea.
   * shortcode_hddp_gallery will parse it and will set custom url for current image in gallery.
   *
   * @author peshkov@UD
   */
  static public function shortcode_hddp_url( $args = false ) {
    return '';
  }

  /**
   * Renders Gallery
   *
   * =USAGE=
   * [hdp_gallery title="{Some title}" content="{Some content}" show_as="{list}"] - Show images of current post. Default view is 'list'
   * [hdp_gallery post_id=3] - Show images of custom post. I.e., it can be hdp_gallery
   *
   * @author peshkov@UD
   */
  static public function shortcode_hddp_gallery( $args = false ) {
    global $post;

    $content = '';

    $data = wp_parse_args( $args, array(
      'post_id'  => is_object( $post ) ? $post->ID : false,
      'fancybox' => false, // Optional. Enable/disable fancybox. Values: 'true', 'on'. By default, disabled.
      'title'    => false, // Optional. Custom title
      'content'  => false, // Optional. Custom content
      'ids'      => '', // Optional. List of media item IDs.
      'show_as'  => 'gallery', // Optional. Default is 'gallery'. Values: 'gallery', 'list'
      'orderby'  => false, // Optional. Allows custom (random) sorting. Values: 'rand', 'ID', 'title', 'name', 'date', 'modified'
    ) );

    $_post = ( is_object( $post ) && $post->ID == $data[ 'post_id' ] ) ? $post : get_post( $data[ 'post_id' ], ARRAY_A );

    // Break if there is no post
    if( empty( $_post ) ) {
      return $content;
    }

    $ids = explode( ',', trim( $data[ 'ids' ] ) );
    foreach( $ids as $k => $id ) {
      $ids[ $k ] = trim( $id );
      if( empty( $ids[ $k ] ) || !is_numeric( $ids[ $k ] ) ) {
        unset( $ids[ $k ] );
      }
    }

    $orderby = $data[ 'orderby' ] && in_array( $data[ 'orderby' ], array( 'rand', 'ID', 'title', 'name', 'date', 'modified' ) ) ? $data[ 'orderby' ] : false;

    if( !empty( $ids ) ) {
      $query = array(
        'post__in'       => $ids,
        'post_type'      => 'attachment',
        'post_mime_type' => 'image',
        'numberposts'    => -1,
        'orderby'        => ( $orderby ? $orderby : 'post__in' ),
      );
    } else {
      $query = array(
        'post_parent'    => $_post->ID,
        'exclude'        => get_post_thumbnail_id(),
        'post_status'    => 'inherit',
        'post_type'      => 'attachment',
        'post_mime_type' => 'image',
        'order'          => 'ASC',
        'numberposts'    => -1,
        'orderby'        => ( $orderby ? $orderby : 'menu_order ID' ),
      );
    }

    $data[ 'images' ] = get_posts( $query );

    // Loop images and parse postr_content ( description ) for custom url ( e.g. [hddp_url="http://example.com"] )
    $pattern = "\[\[?hddp_url(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)\]?";
    foreach( $data[ 'images' ] as $k => $image ) {
      $url = false;
      preg_match( "/$pattern/s", $image->post_content, $matches );
      if( !empty( $matches ) ) {
        $attrs = shortcode_parse_atts( $matches[ 1 ] );
        if( !empty( $attrs[ 'href' ] ) ) {
          $url = $attrs[ 'href' ];
        }
      }
      $data[ 'images' ][ $k ]->custom_url = $url;
    }

    // Break if there are no images
    if( empty( $data[ 'images' ] ) ) {
      return $content;
    }

    // Set title and content if they are undefined.
    $data[ 'title' ]   = ( ( !is_object( $post ) || $post->ID != $_post->ID ) && !$data[ 'title' ] ) ? $_post->post_title : (string) $data[ 'title' ];
    $data[ 'content' ] = ( ( !is_object( $post ) || $post->ID != $_post->ID ) && !$data[ 'content' ] ) ? $_post->post_content : (string) $data[ 'content' ];

    // Check and fix if needed 'show_as' argument
    $data[ 'show_as' ] = in_array( $data[ 'show_as' ], array( 'gallery', 'list' ) ) ? $data[ 'show_as' ] : 'gallery';

    $data[ 'anchors' ] = array(
      'gallery' => array(
        'icon' => 'icon-events',
        'text' => __( 'Show As List' )
      ),
      'list'    => array(
        'icon' => 'icon-hdp_photo_gallery',
        'text' => __( 'Show As Gallery' )
      )
    );

    $data[ 'anchor' ] = $data[ 'anchors' ][ $data[ 'show_as' ] ];

    wp_enqueue_style( 'hddp-jquery-fancybox', get_stylesheet_directory_uri() . '/css/jquery.fancybox.css', array(), HDDP_Version );
    wp_enqueue_script( 'hddp-jquery-fancybox', get_stylesheet_directory_uri() . '/js/jquery.fancybox.min.js', array( 'jquery', 'jquery-flexslider' ), HDDP_Version, true );
    wp_enqueue_script( 'hddp_shortcode', get_stylesheet_directory_uri() . '/js/hddp.gallery.js', array( 'jquery', 'jquery-flexslider', 'hddp-jquery-fancybox' ), HDDP_Version, true );

    /** Buffer output */
    ob_start();

    include 'templates/gallery.php';

    /** Get the content */
    $content = ob_get_clean();

    return $content;
  }

  /**
   * Custom loop
   * @param type $args
   * @return type
   */
  static public function shortcode_hdp_custom_loop( $args ) {
    $args = shortcode_atts( array(
      'post_type' => '',
      'per_page' => 25
    ), $args );

    ob_start();

    get_template_part( 'templates/elastic/loop', $args['post_type'] );

    return ob_get_clean();
  }

  /**
   * Filter to replace the [caption] shortcode text with HTML5 compliant code
   *
   * @param      $val
   * @param      $attr
   * @param null $content
   *
   * @return text HTML content describing embedded figure
   */
  static public function img_caption_shortcode( $val, $attr, $content = null ) {
    extract( shortcode_atts( array(
      'id'      => '',
      'align'   => '',
      'width'   => '',
      'caption' => ''
    ), $attr ) );

    if( 1 > (int) $width || empty( $caption ) ) {
      return $val;
    }

    $capid = '';
    if( $id ) {
      $id    = esc_attr( $id );
      $capid = 'id="figcaption_' . $id . '" ';
      $id    = 'id="' . $id . '" aria-labelledby="figcaption_' . $id . '" ';
    }

    return '<figure ' . $id . 'class="wp-caption ' . esc_attr( $align ) . '" >' . do_shortcode( $content ) . '<figcaption ' . $capid . 'class="wp-caption-text">' . $caption . '</figcaption></figure>';
  }

  static public function get_autogenerator_data() {
    return array(
      'post_types'  => array(
        'event',
        'imagegallery',
        'videoobject',
      ),
      'fields'      => array(
        'post_title'  => array(
          'allow_do_not'=> true,
          'label'       => 'Do not generate the title.',
          'callback'    => array( __CLASS__, 'autogenerate_post_title' ),
        ),
        'post_name'   => array(
          'allow_do_not'=> true,
          'label'       => 'Do not generate the slug.',
          'callback'    => array( __CLASS__, 'autogenerate_post_name' ),
        ),
        'post_excerpt'=> array(
          'allow_do_not'=> false,
          'label'       => 'Do not generate the excerpt.',
          'callback'    => array( __CLASS__, 'autogenerate_post_excerpt' ),
          'depends_on'  => 'post_title',
        ),
      ),
    );
  }

  static public function autogenerator_submitbox_actions() {
    global $post;

    $autogenerator = self::get_autogenerator_data();

    if ( in_array( $post->post_type, $autogenerator['post_types'] ) ) {
      foreach ( $autogenerator['fields'] as $field_name => $args ) {
        if ( $args['allow_do_not'] ) {
          echo '<p><input type="checkbox" name="do_not_generate_' . $field_name . '" value="true"' . checked( true, get_post_meta( $post->ID, 'do_not_generate_' . $field_name, true ), false ) . '> ' . $args['label'] . '</p>';
        }
      }
    }
  }

  static public function autogenerate_post_elements( $post_id, $post ) {
    global $wpdb;

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
      return $post_id;
    }

    if ( $post->post_status == 'auto-draft' ) {
      return;
    }

    if ( wp_is_post_revision( $post_id ) ) {
      return;
    }

    $autogenerator = self::get_autogenerator_data();

    if( in_array( $post->post_type, $autogenerator['post_types'] ) )
    {
      $post_updated = array();
      foreach ( $autogenerator['fields'] as $field_name => $args ) {
        $generate = true;
        if ( $args['allow_do_not'] ) {
          if ( isset( $_REQUEST['do_not_generate_' . $field_name ] ) ) {
            update_post_meta( $post_id, 'do_not_generate_' . $field_name, true );
            if ( $_REQUEST['do_not_generate_' . $field_name ] ) {
              $generate = false;
            }
          } else {
            delete_post_meta( $post_id, 'do_not_generate_' . $field_name );
          }
        }

        if ( $generate && is_callable( $args['callback'] ) ) {
          $callback_args = array( $post );
          if ( isset( $args['depends_on'] ) && !empty( $args['depends_on'] ) ) {
            if ( !is_array( $args['depends_on'] ) ) {
              $args['depends_on'] = array( $args['depends_on'] );
            }
            foreach ( $args['depends_on'] as $arg ) {
              if ( isset( $post_updated[ $arg ] ) ) {
                $callback_args[] = $post_updated[ $arg ];
              } elseif( isset( $post->$arg ) ) {
                $callback_args[] = $post->$arg;
              } else {
                $callback_args[] = '';
              }
            }
          }
          $generated = call_user_func_array( $args['callback'], $callback_args );

          if ( $generated !== false ) {
            $post_updated[ $field_name ] = $generated;
          }
        }
      }

      if ( count( $post_updated ) > 0 ) {
        $wpdb->update( $wpdb->posts, $post_updated, array( 'ID' => $post_id ) );
      }
    }
  }

  static public function autogenerate_post_title( $post ) {
    $event_id = self::get_event_id( $post );
    if ( !$event_id ) {
      return false;
    }

    $artists = get_post_meta( $event_id, 'artists' );
    if ( !is_array( $artists ) ) {
      $artists = array( $artists );
    }
    foreach ( $artists as &$artist ) {
      $artist = get_the_title( $artist );
    }

    $venue = get_post_meta( $event_id, 'venue', true );
    $venue = get_the_title( $venue );

    return sprintf( '%1$s at %2$s', implode( ', ', $artists ), $venue );
  }

  static public function autogenerate_post_name( $post ) {
    $event_id = self::get_event_id( $post );
    if ( !$event_id ) {
      return false;
    }

    $date = strtotime( get_post_meta( $event_id, 'dateStart', true ) );
    $date = date( 'Y-md', $date );

    $venue = get_post_meta( $event_id, 'venue', true );
    $city = wp_get_object_terms( $venue, 'city', array( 'fields' => 'names' ) );
    $venue = get_the_title( $venue );
    if ( is_array( $city ) ) {
      reset( $city );
      $city = $city[ key( $city ) ];
    } else {
      $city = '';
    }

    return wp_unique_post_slug( sanitize_title( sprintf( '%1$s %2$s %3$s', $date, $city, $venue ) ), $post->ID, $post->post_status, $post->post_type, $post->post_parent );
  }

  static public function autogenerate_post_excerpt( $post, $post_title ) {
    $event_id = self::get_event_id( $post );
    if ( !$event_id ) {
      return false;
    }

    if ( empty( $post_title ) ) {
      $post_title = self::autogenerate_post_title( $post );
    }

    $venue = get_post_meta( $event_id, 'venue', true );

    $city = wp_get_object_terms( $venue, 'city', array( 'fields' => 'names' ) );
    if ( is_array( $city ) ) {
      reset( $city );
      $city = $city[ key( $city ) ];
    } else {
      $city = '';
    }
    
    $state = wp_get_object_terms( $venue, 'state', array( 'fields' => 'names' ) );
    if ( is_array( $state ) ) {
      reset( $state );
      $state = $state[ key( $state ) ];
    } else {
      $state = '';
    }

    $raw_date = strtotime( get_post_meta( $event_id, 'dateStart', true ) );

    $date = date( 'l, F j, Y', $raw_date );

    $time = date( 'g:i A', $raw_date );

    return sprintf( '%1$s in %2$s, %3$s on %4$s at %5$s', $post_title, $city, $state, $date, $time );
  }

  static private function get_event_id( $post ) {
    $event_id = $post->ID;
    if ( $post->post_type != 'event' ) {
      $event_id = get_post_meta( $post->ID, 'event', true );
      if ( !$event_id ) {
        return false;
      }
    }
    return $event_id;
  }

  static public function maybe_redirect_post_type_archive() {
    if ( is_post_type_archive() ) {
      $root_page = self::get_root_page( get_post_type() );
      if ( $root_page ) {
        wp_redirect( get_permalink( $root_page ) );
        exit();
      }
    }
  }

  static public function get_root_page( $post_type ) {
    $root_pages = array(
      'event'         => 'events',
      'imagegallery'  => 'photos',
      'videoobject'   => 'videos',
    );

    if( isset( $root_pages[ $post_type ] ) )
    {
      $page = get_page_by_path( $root_pages[ $post_type ] );
      if( $page )
      {
        return $page->ID;
      }
    }
    return false;
  }

  /**
   * Check if Meta Box plugin is installed and if not, display activate/install link.
   *
   * @author Felix Arntz
   */
  static function check_for_metabox_plugin()
  {
    if( !defined( 'RWMB_VER' ) && current_user_can( 'manage_network_plugins' ) )
    {
      echo '<div class="error">';

      $plugin_slug = 'meta-box';
      $plugin_name = 'Meta Box';
      
      echo '<p>' . sprintf( __( 'The theme %1$s requires the plugin %2$s to work properly. Please make sure it is installed and activated.' ), 'WP-Disco v2.0', $plugin_name ) . '</p>';
      
      $installed_plugins = get_plugins();
      $plugin_file = '';
      foreach( $installed_plugins as $slug => $data ) {
        if ( $pos = strpos( $slug, '/' ) ) {
          if ( $plugin_slug == substr( $slug, 0, $pos ) ) {
            $plugin_file = $slug;
            break;
          }
        } elseif ( $plugin_slug == $slug ) {
          $plugin_file = $slug;
          break;
        }
      }
      
      if ( $plugin_file != '' ) {
        $class  = 'button';
        $text   = __( 'Network Activate' );
        $title  = __( 'Activate this plugin for all sites in this network' );
        $url    = wp_nonce_url( network_admin_url( 'plugins.php?action=activate&amp;plugin=' . $plugin_file . '&amp;plugin_status=all&amp;paged=1&amp;s=' ), 'activate-plugin_' . $plugin_file );
      } else {
        $class  = 'install-now button';
        $text   = __( 'Install Now' );
        $title  = sprintf( __( 'Install %s now' ), $plugin_name );
        $url    = wp_nonce_url( network_admin_url( 'update.php?action=install-plugin&plugin=' . $plugin_slug ), 'install-plugin_' . $plugin_slug );
      }
      echo '<p style="text-align: right;"><a class="' . $class . '" href="' . $url . '" aria-label="' . esc_attr( $title ) . '">' . $text . '</a></p>';
      
      echo '</div>';
    }
  }

  /**
   * Alter feed
   * @global type $post
   * @param string $content
   * @return string
   */
  static public function featured_image_in_feed( $content ) {
    global $post;
    if( is_feed() ) {
      if( has_post_thumbnail( $post->ID ) ) {
        $output  = get_the_post_thumbnail( $post->ID, 'hd_large', array( 'style' => 'margin:10px 0 10px 0;' ) );
        $content = $output . $content;
      }
    }

    return $content;
  }

  /**
   * Used to build document before sending to ES server
   * @param class $document
   * @param type $post
   * @return type
   */
  public function build_elastic_document( $document, $post ) {

    $_entities = array(
      'event' => '\DiscoDonniePresents\Event',
      'imagegallery' => '\DiscoDonniePresents\ImageGallery',
      'videoobject' => '\DiscoDonniePresents\VideoObject',
      'artist' => '\DiscoDonniePresents\Artist',
      'promoter' => '\DiscoDonniePresents\Promoter',
      'tour' => '\DiscoDonniePresents\Tour',
      'venue' => '\DiscoDonniePresents\Venue'
    );

    if ( !isset( $_entities[$post->post_type] ) ) return $document;
    
    $class = $_entities[$post->post_type];

    $document = new $class( $post->ID );

    return $document->toElasticFormat();
  }

  /**
   * Used for photos/videos media pages with elasticsearch implementation
   * @param type $args
   * @return type
   */
  static function elasticsearch_media( $args ) {

    $args = shortcode_atts( array(
        'post_type' => ''
    ), $args );

    ob_start();

    get_template_part( 'templates/elastic/list', $args['post_type'] );

    return ob_get_clean();

  }

  /**
   * New Elastic Search Facets
   *
   * @param type $atts
   *
   * @return type
   */
  public static function elasticsearch_facets( $atts ) {

    $args = shortcode_atts( array(
      'post_type' => ''
    ), $atts );

    ob_start();

    get_template_part( 'templates/elastic/facets', $args['post_type'] );

    return ob_get_clean();
  }


}