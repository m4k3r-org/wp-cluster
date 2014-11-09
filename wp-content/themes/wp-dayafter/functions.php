<?php
/**
 * Class DayAfter
 *
 */

class DayAfter {

  /**
   * Version of child theme
   *
   * @public
   * @property version
   * @var string
   */
  public static $version = '1.0.0';

  /**
   * Textdomain String
   *
   * @public
   * @property text_domain
   * @var string
   */
  public static $text_domain = 'DayAfter';

  /**
   * Constructor
   *
   */
  public function __construct() {

    // Set Timezone
    //date_default_timezone_set( get_option( 'timezone_string' ) );

    // Initializers
    add_action( 'init', array( __CLASS__, 'admin_favicon' ) );
    //add_action( 'template_redirect', array( $this, 'template_redirect' ) );
    add_action( 'init', array( __CLASS__, 'init' ) );
    add_action( 'wp_enqueue_scripts', array( __CLASS__, 'wp_enqueue_scripts' ) );
    add_filter( 'sanitize_file_name', array( __CLASS__, 'sanitize_file_name' ), 10 );

    add_theme_support( 'menus' );
    register_nav_menu( 'primary', 'Primary Menu' );

    // Detect visitor language
    // $this->language = substr( $_SERVER[ 'HTTP_ACCEPT_LANGUAGE' ], 0, 2 );

  }

  /**
   * Rename uploaded files as the hash of their original.
   *
   * @author sopp@UD
   */
  function sanitize_file_name( $filename ) {
    $info = pathinfo( $filename );
    $ext = empty( $info[ 'extension' ] ) ? '' : '.' . $info[ 'extension' ];
    $rnd = rand( 0, 99 );
    $name = basename( $filename, $ext );
    return md5( $name ) . $rnd . $ext;
  }

  /**
   * WordPress "init" action
   *
   * @public
   * @for DayAfter
   * @method init
   *
   * @author potanin@UD
   */
  public function init() {

    // Styles
    wp_register_style( 'yui-reset', get_stylesheet_directory_uri() . '/reset.css', array(), DayAfter::$version, 'all' );
    wp_register_style( 'app-style', get_stylesheet_directory_uri() . '/style.css', array( 'yui-reset' ), DayAfter::$version, 'all' );

    // Scripts
    wp_register_script( 'html5', 'http://html5shiv.googlecode.com/svn/trunk/html5.js', array( 'jquery' ), DayAfter::$version, true );
    wp_register_script( 'app-lib', get_stylesheet_directory_uri() . '/js/lib.js', array( 'jquery' ), DayAfter::$version, true );
    wp_register_script( 'app-script', get_stylesheet_directory_uri() . '/js/main.js', array( 'html5', 'app-lib' ), DayAfter::$version, true );

  }

  /**
   * Enqueue Styles
   *
   * @public
   * @method wp_enqueue_scripts
   * @for DayAfter
   *
   * @author potanin@UD
   */
  public function wp_enqueue_scripts() {
    wp_enqueue_script( 'app-lib' );
    wp_enqueue_script( 'app-script' );
    wp_enqueue_style( 'app-style' );
  }

  /**
   * Add Favicon HTML
   *
   * @author potanin@UD
   */
  public function admin_favicon() {

    function favicon_html() {
      echo '<link rel="shortcut icon" type="image/x-icon" href="' . get_stylesheet_directory_uri() . '/favicon.ico" />';
    }

    if( file_exists( get_stylesheet_directory() . '/favicon.ico' ) ) {
      add_action( 'admin_head', 'favicon_html' );
      add_action( 'login_head', 'favicon_html' );
    }

  }
}

new DayAfter;

/*================================================================================*/
/* Set Timezone */
/*================================================================================*/

/*================================================================================*/
/* Page and Subpage Parent Checks */
/*================================================================================*/
// Get the id of a page by its name
function get_page_id( $page_name ) {
  global $wpdb;
  $page_name = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_name = '" . $page_name . "'" );

  return $page_name;
}

function is_tree( $pid ) {
  global $post;
  $anc = get_post_ancestors( $post->ID );
  foreach( $anc as $ancestor ) {
    if( is_page() && $ancestor == $pid ) {
      return true;
    }
  }
  if( is_page() && ( is_page( $pid ) ) )
    return true;
  else
    return false;
}

/*================================================================================*/
/* Pagination */
/*================================================================================*/
function tfg_pagination( $pages = '', $range = 3 ) {
  $showitems = ( $range * 2 ) + 1;

  global $paged;
  if( empty( $paged ) ) $paged = 1;

  if( $pages == '' ) {
    global $wp_query;
    $pages = $wp_query->max_num_pages;
    if( !$pages ) {
      $pages = 1;
    }
  }

  if( 1 != $pages ) {
    echo "<ul class='tfg-pagination clearfix'>";
    if( $paged > 2 && $paged > $range + 1 && $showitems < $pages ) echo "<li><a href='" . get_pagenum_link( 1 ) . "'>&laquo;</a><li>";
    if( $paged > 1 && $showitems < $pages ) echo "<li><a href='" . get_pagenum_link( $paged - 1 ) . "'>&lsaquo;</a></li>";

    for( $i = 1; $i <= $pages; $i++ ) {
      if( 1 != $pages && ( !( $i >= $paged + $range + 1 || $i <= $paged - $range - 1 ) || $pages <= $showitems ) ) {
        echo ( $paged == $i ) ? "<li><a href='" . get_pagenum_link( $i ) . "' class='current'>" . $i . "</a></li>" : "<li><a href='" . get_pagenum_link( $i ) . "'>" . $i . "</a></li>";
      }
    }

    if( $paged < $pages && $showitems < $pages ) echo "<li><a href='" . get_pagenum_link( $paged + 1 ) . "'>&rsaquo;</a></li>";
    if( $paged < $pages - 1 && $paged + $range - 1 < $pages && $showitems < $pages ) echo "<li><a href='" . get_pagenum_link( $pages ) . "'>&raquo;</a></li>";
    echo "</ul>";
  }
}

/*================================================================================*/
/* Head */
/*================================================================================*/

function tfg_title() {
  global $tfg_page;
  if( !empty( $tfg_page[ 'title' ] ) ) {
    echo $tfg_page[ 'title' ];
  } else {
    global $page, $paged;
    if( $paged >= 2 || $page >= 2 ) {
      echo sprintf( __( 'Page %s', 'tfg' ), max( $paged, $page ) ) . ' | ';
    }
    wp_title( '|', true, 'right' );
    bloginfo();
  }
}

function tfg_description() {
  global $tfg_page;
  $excerpt = wp_trim_excerpt();
  if( !empty( $tfg_page[ 'description' ] ) ) {
    echo $tfg_page[ 'description' ];
  } else if( !empty( $excerpt ) ) {
    echo $excerpt;
  } else {
    echo get_bloginfo( 'description' );
  }
}

function tfg_image() {
  global $tfg_page;
  if( !empty( $tfg_page[ 'image' ] ) ) {
    echo $tfg_page[ 'image' ];
  } else {
    echo get_bloginfo( 'template_url' ) . '/images/fb.jpg';
  }
}

/*================================================================================*/
/* Miscellaenous */
/*================================================================================*/
function current_url() {
  return ( !empty( $_SERVER[ 'HTTPS' ] ) ) ? "https://" . $_SERVER[ 'SERVER_NAME' ] . $_SERVER[ 'REQUEST_URI' ] : "http://" . $_SERVER[ 'SERVER_NAME' ] . $_SERVER[ 'REQUEST_URI' ];
}

function button_shortcode( $atts, $content = null ) {
  extract( shortcode_atts( array(
    'href'   => 'caption',
    'target' => '_self'
  ), $atts ) );

  return '<a href="' . esc_attr( $href ) . '" target="' . esc_attr( $target ) . '" class="button">' . $content . '</a>';
}

add_shortcode( 'button', 'button_shortcode' );

function divider_shortcode( $atts, $content = null ) {
  return '<div class="divider"></div>';
}

add_shortcode( 'divider', 'divider_shortcode' );

/*
function email_shortcode($atts, $content = null){
	$content = mb_convert_encoding($content , 'UTF-32', 'UTF-8');
    $t = unpack("N*", $content);
	function tester($n) { return "&#$n;"; }
    $t = array_map($tester, $t);
    $encoded = implode("", $t);
	return '<a href="mailto:'.$encoded.'">'.$encoded.'</a>';
}
add_shortcode('email', 'email_shortcode');
*/

function tfg_excerpt( $id = false ) {
  global $post;

  $old_post = $post;
  if( $id != $post->ID ) {
    $post = get_page( $id );
  }

  if( !$excerpt = trim( $post->post_excerpt ) ) {
    $excerpt        = $post->post_content;
    $excerpt        = strip_shortcodes( $excerpt );
    $excerpt        = apply_filters( 'the_content', $excerpt );
    $excerpt        = str_replace( ']]>', ']]&gt;', $excerpt );
    $excerpt        = strip_tags( $excerpt );
    $excerpt_length = apply_filters( 'excerpt_length', 55 );
    /*$excerpt_more = apply_filters('excerpt_more', ' ' . '[...]');*/

    $words = preg_split( "/[\n\r\t ]+/", $excerpt, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY );
    if( count( $words ) > $excerpt_length ) {
      array_pop( $words );
      $excerpt = implode( ' ', $words );
      //$excerpt = $excerpt . $excerpt_more;
      $excerpt = $excerpt . $excerpt_more;
    } else {
      $excerpt = implode( ' ', $words );
    }
  }

  $post = $old_post;

  return $excerpt;
}

add_filter( 'post_thumbnail_html', 'remove_thumbnail_dimensions', 10, 3 );
function remove_thumbnail_dimensions( $html, $post_id, $post_image_id ) {
  $html = preg_replace( '/(width|height)=\"\d*\"\s/', "", $html );
  return $html;
}


add_theme_support( 'post-thumbnails' );