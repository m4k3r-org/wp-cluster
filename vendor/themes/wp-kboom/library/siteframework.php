<?php
// Adding Translation Option
load_theme_textdomain( 'site5framework', get_template_directory() . '/languages' );
$locale = get_locale();
$locale_file = get_template_directory() . "/languages/$locale.php";
if( is_readable( $locale_file ) ) require_once( $locale_file );

// Cleaning up the Wordpress Head
function site5framework_head_cleanup() {
  // remove header links
  // remove_action( 'wp_head', 'feed_links_extra', 3 );                 // Category Feeds
  // remove_action( 'wp_head', 'feed_links', 2 );                       // Post and Comment Feeds
  remove_action( 'wp_head', 'rsd_link' ); // EditURI link
  remove_action( 'wp_head', 'wlwmanifest_link' ); // Windows Live Writer
  remove_action( 'wp_head', 'index_rel_link' ); // index link
  remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 ); // previous link
  remove_action( 'wp_head', 'start_post_rel_link', 10, 0 ); // start link
  remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 ); // Links for Adjacent Posts
  remove_action( 'wp_head', 'wp_generator' ); // WP version
}

// launching operation cleanup
add_action( 'init', 'site5framework_head_cleanup' );
// remove WP version from RSS
function site5framework_rss_version() {
  return '';
}

add_filter( 'the_generator', 'site5framework_rss_version' );

// load custom js libraries, diferent from the site5framework ones
function site5framework_queue_js() {
  // loading jquery reply elements on single pages automatically
  if( !is_admin() ) {
    if( is_singular() AND comments_open() AND ( get_option( 'thread_comments' ) == 1 ) ) wp_enqueue_script( 'comment-reply' );
  }

  // enqueue the backgrund stretch plugin
  wp_enqueue_script( 'jquery.backstretch', get_template_directory_uri() . '/library/js/fullwidth-bg/jquery.backstretch.min.js' );
  wp_enqueue_script( 'mediaelement-and-player', get_template_directory_uri() . '/library/js/player/mediaelement-and-player.min.js' );
  wp_enqueue_script( 'jquery.fitvids', get_template_directory_uri() . '/library/js/fit-videos/jquery.fitvids.js' );
  wp_enqueue_script( 'jquery.tipsy', get_template_directory_uri() . '/library/js/tipsy/jquery.tipsy.js' );
  wp_enqueue_script( 'jquery.flexslider', get_template_directory_uri() . '/library/js/flex-slider/jquery.flexslider-min.js' );
  wp_enqueue_script( 'jquery.jcarousel', get_template_directory_uri() . '/library/js/carousel/jquery.jcarousel.min.js' );
  wp_enqueue_script( 'jquery.easing', get_template_directory_uri() . '/library/js/jquery.easing-1.3.min.js' );

}

// load custom js libraries, diferent from the site5framework ones
add_action( 'wp_enqueue_scripts', 'site5framework_queue_js', 10 );

// load custom css libraries, diferent from the site5framework ones
function site5framework_queue_css() {

  wp_enqueue_style( 'flexslider', get_template_directory_uri() . '/library/css/flexslider.css' );

}

// load custom js libraries, diferent from the site5framework ones
add_action( 'wp_enqueue_scripts', 'site5framework_queue_css', 10 );

// Fixing the Read More in the Excerpts
// This removes the annoying […] to a Read More link
function site5framework_excerpt_more( $more ) {
  global $post;
  // edit here if you like
  /*return '<p class="readmore"> <a href="'. get_permalink($post->ID) . '" title="Read '.get_the_title($post->ID).'">'.__("Read more...", "site5framework").'</a> </p>';*/
}

add_filter( 'excerpt_more', 'site5framework_excerpt_more' );

// Adding WP 3+ Functions & Theme Support
function site5framework_theme_support() {
  add_theme_support( 'post-thumbnails' ); // wp thumbnails (sizes handled in functions.php)
  set_post_thumbnail_size( 125, 125, true ); // default thumb size
  add_theme_support( 'custom-background' );
  add_theme_support( 'automatic-feed-links' ); // rss thingy
  // to add header image support go here: http://themble.com/support/adding-header-background-image-support/
  // adding post format support
  add_theme_support( 'post-formats', array( /*'aside',*/
    'gallery', 'image', /*'link','quote','video','audio'*/ ) );
}

// launching this stuff after theme setup
add_action( 'after_setup_theme', 'site5framework_theme_support' );
// adding the site5framework search form (created in functions.php)
add_filter( 'get_search_form', 'site5framework_wpsearch' );
// launching this stuff after theme setup

/****************** PLUGINS & EXTRA FEATURES **************************/

// Related Posts Function (call using site5framework_related_posts(); )
function site5framework_related_posts() {
  echo '<ul id="site5framework-related-posts">';
  global $post;
  $tags = wp_get_post_tags( $post->ID );
  if( $tags ) {
    foreach( $tags as $tag ) {
      $tag_arr .= $tag->slug . ',';
    }
    $args          = array(
      'tag'          => $tag_arr,
      'numberposts'  => 5, /* you can change this to show more */
      'post__not_in' => array( $post->ID )
    );
    $related_posts = get_posts( $args );
    if( $related_posts ) {
      foreach( $related_posts as $post ) : setup_postdata( $post ); ?>
        <li class="related_post"><a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></li>
      <?php endforeach;
    } else {
      ?>
      <li class="no_related_post">No Related Posts Yet!</li>
    <?php
    }
  }
  wp_reset_query();
  echo '</ul>';
}

// Numeric Page Navi (built into the theme by default)
function page_navi( $before = '', $after = '' ) {
  global $wpdb, $wp_query;
  $request        = $wp_query->request;
  $posts_per_page = intval( get_query_var( 'posts_per_page' ) );
  $paged          = intval( get_query_var( 'paged' ) );
  $numposts       = $wp_query->found_posts;
  $max_page       = $wp_query->max_num_pages;
  if( $numposts <= $posts_per_page ) {
    return;
  }
  if( empty( $paged ) || $paged == 0 ) {
    $paged = 1;
  }
  $pages_to_show         = 7;
  $pages_to_show_minus_1 = $pages_to_show - 1;
  $half_page_start       = floor( $pages_to_show_minus_1 / 2 );
  $half_page_end         = ceil( $pages_to_show_minus_1 / 2 );
  $start_page            = $paged - $half_page_start;
  if( $start_page <= 0 ) {
    $start_page = 1;
  }
  $end_page = $paged + $half_page_end;
  if( ( $end_page - $start_page ) != $pages_to_show_minus_1 ) {
    $end_page = $start_page + $pages_to_show_minus_1;
  }
  if( $end_page > $max_page ) {
    $start_page = $max_page - $pages_to_show_minus_1;
    $end_page   = $max_page;
  }
  if( $start_page <= 0 ) {
    $start_page = 1;
  }
  echo $before . '<nav class="page-navigation"><ol class="site5framework_page_navi clearfix">' . "";
  if( $start_page >= 2 && $pages_to_show < $max_page ) {
    $first_page_text = "First";
    echo '<li class="bpn-first-page-link"><a href="' . get_pagenum_link() . '" title="' . $first_page_text . '">' . $first_page_text . '</a></li>';
  }
  echo '<li class="bpn-prev-link">';
  previous_posts_link( '<<' );
  echo '</li>';
  for( $i = $start_page; $i <= $end_page; $i++ ) {
    if( $i == $paged ) {
      echo '<li class="bpn-current">' . $i . '</li>';
    } else {
      echo '<li><a href="' . get_pagenum_link( $i ) . '">' . $i . '</a></li>';
    }
  }
  echo '<li class="bpn-next-link">';
  next_posts_link( '>>' );
  echo '</li>';
  if( $end_page < $max_page ) {
    $last_page_text = "Last";
    echo '<li class="bpn-last-page-link"><a href="' . get_pagenum_link( $max_page ) . '" title="' . $last_page_text . '">' . $last_page_text . '</a></li>';
  }
  echo '</ol></nav>' . $after . "";
}

// remove the p from around imgs (http://css-tricks.com/snippets/wordpress/remove-paragraph-tags-from-around-images/)
function filter_ptags_on_images( $content ) {
  return preg_replace( '/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content );
}

add_filter( 'the_content', 'filter_ptags_on_images' );

/**
 * Add "first" and "last" CSS classes to dynamic sidebar widgets. Also adds numeric index class for each widget (widget-1, widget-2, etc.)
 */
function site5framework_widget_first_last_classes( $params ) {

  global $my_widget_num; // Global a counter array
  $this_id                = $params[ 0 ][ 'id' ]; // Get the id for the current sidebar we're processing
  $arr_registered_widgets = wp_get_sidebars_widgets(); // Get an array of ALL registered widgets

  if( !$my_widget_num ) { // If the counter array doesn't exist, create it
    $my_widget_num = array();
  }

  if( !isset( $arr_registered_widgets[ $this_id ] ) || !is_array( $arr_registered_widgets[ $this_id ] ) ) { // Check if the current sidebar has no widgets
    return $params; // No widgets in this sidebar... bail early.
  }

  if( isset( $my_widget_num[ $this_id ] ) ) { // See if the counter array has an entry for this sidebar
    $my_widget_num[ $this_id ]++;
  } else { // If not, create it starting with 1
    $my_widget_num[ $this_id ] = 1;
  }

  $class = 'class="widget-' . $my_widget_num[ $this_id ] . ' '; // Add a widget number class for additional styling options

  if( $my_widget_num[ $this_id ] == 1 ) { // If this is the first widget
    $class .= 'first ';
  } elseif( $my_widget_num[ $this_id ] == count( $arr_registered_widgets[ $this_id ] ) ) { // If this is the last widget
    $class .= 'last ';
  }

  $params[ 0 ][ 'before_widget' ] = str_replace( 'class="', $class, $params[ 0 ][ 'before_widget' ] ); // Insert our new classes into "before widget"

  return $params;

}

add_filter( 'dynamic_sidebar_params', 'site5framework_widget_first_last_classes' );

/**
 * Get the image attachment ID based on the url
 */
function get_image_id_by_link( $link ) {

  global $wpdb;

  // $link = apply_filters( 'get_the_guid', $link );

  $id = $wpdb->get_var( "SELECT ID FROM {$wpdb->posts} WHERE guid='$link'" );

  // if attachment exists, it's a url to a full image size
  if( $id ) {
    return $id; //apply_filters( 'get_the_guid', $id );
  }

  // check if it's a thumbnail url
  $link = preg_replace( '/-\d+x\d*+(?=\.(jpg|jpeg|png|gif)$)/i', '', $link );
  return $wpdb->get_var( "SELECT ID FROM {$wpdb->posts} WHERE guid='$link'" );

}

add_filter( 'post_thumbnail_html', 'remove_width_attribute', 10 );
add_filter( 'image_send_to_editor', 'remove_width_attribute', 10 );

function remove_width_attribute( $html ) {
  $html = preg_replace( '/(width|height)=\"\d*\"\s/', "", $html );

  return $html;
}

function pr( $obj ) {
  echo "<pre>";
  print_r( $obj );
  echo "</pre>";
}

$option_posts_per_page = get_option( 'posts_per_page' );
add_action( 'init', 'my_modify_posts_per_page', 0 );
function my_modify_posts_per_page() {
  add_filter( 'option_posts_per_page', 'my_option_posts_per_page' );
}

function my_option_posts_per_page( $value ) {
  global $option_posts_per_page;
  if( is_tax( 'types' ) ) {
    return of_get_option( 'sc_portfolioitemsperpage' );
  } else {
    return $option_posts_per_page;
  }
}

?>