<?php

/////////////////////////////////////
// Theme Setup
/////////////////////////////////////

if ( ! function_exists( 'mvp_setup' ) ) {
function mvp_setup(){
	load_theme_textdomain('mvp-text', get_template_directory() . '/languages');

	$locale = get_locale();
	$locale_file = get_template_directory() . "/languages/$locale.php";
	if ( is_readable( $locale_file ) )
		require_once( $locale_file );
}
}
add_action('after_setup_theme', 'mvp_setup');

/////////////////////////////////////
// Enqueue Javascript/CSS Files
/////////////////////////////////////

if ( ! function_exists( 'enqueue_iecss' ) ) {
function enqueue_iecss() {
    // Register stylesheet
    wp_register_style( 'iecss', get_template_directory_uri() . '/css/iecss.css' );
    // Apply IE conditionals
    $GLOBALS['wp_styles']->add_data( 'iecss', 'conditional', 'lt IE 9' );
    // Enqueue stylesheet
    wp_enqueue_style( 'iecss' );
} }
add_action( 'wp_enqueue_scripts', 'enqueue_iecss' );

if ( ! function_exists( 'mvp_scripts_method' ) ) {
function mvp_scripts_method() {
	wp_register_script('iosslider', get_template_directory_uri() . '/js/jquery.iosslider.js', array('jquery'), '', true);
	wp_register_script('braxton', get_template_directory_uri() . '/js/scripts.js', array('jquery'), '', true);
	wp_register_script('lazyload', get_template_directory_uri() . '/js/jquery.lazyload.js', array('jquery'), '', true);
	wp_register_script('infinitescroll', get_template_directory_uri() . '/js/jquery.infinitescroll.js', array('jquery'), '', true);
	wp_register_script('respond', get_template_directory_uri() . '/js/respond.min.js', array('jquery'), '', true);
	wp_register_script('retina', get_template_directory_uri() . '/js/retina.js', array('jquery'), '', true);
	wp_register_script('stickymojo', get_template_directory_uri() . '/js/stickyMojo.js', array('jquery'), '', true);

	wp_enqueue_script('jquery');
	wp_enqueue_script('iosslider');
	wp_enqueue_script('braxton');
	wp_enqueue_script('lazyload');
	wp_enqueue_script('infinitescroll');
	wp_enqueue_script('respond');
	wp_enqueue_script('retina');

	$mvp_sticky_sidebar = get_option('mvp_sticky_sidebar'); if ($mvp_sticky_sidebar == "true") { if (isset($mvp_sticky_sidebar)) {
	wp_enqueue_script('stickymojo');
	} }

	wp_enqueue_style( 'mvp-style', get_stylesheet_uri() );
	wp_enqueue_style( 'reset', get_template_directory_uri() . '/css/reset.css' );

	$mvp_respond = get_option('mvp_respond'); if ($mvp_respond == "true") { if (isset($mvp_respond)) {
	wp_enqueue_style( 'media-queries', get_template_directory_uri() . '/css/media-queries.css' );
	} }

	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	if (is_plugin_active('menufication/menufication.php')) {
	wp_enqueue_style( 'menufication', get_template_directory_uri() . '/css/menufication.css' );
	}

	wp_enqueue_style( 'googlefonts', 'http://fonts.googleapis.com/css?family=Quicksand:300,400|Vidaloka|Playfair+Display:400,700,900|Open+Sans+Condensed:300,700|Oswald:300,400,700|Raleway:200,300,400,500,600,700,800&subset=latin,latin-ext,cyrillic,cyrillic-ext,greek-ext,greek,vietnamese' );
}
}
add_action('wp_enqueue_scripts', 'mvp_scripts_method');

/////////////////////////////////////
// Theme Options
/////////////////////////////////////

require_once(TEMPLATEPATH . '/admin/admin-functions.php');
require_once(TEMPLATEPATH . '/admin/admin-interface.php');
require_once(TEMPLATEPATH . '/admin/theme-settings.php');

if ( ! function_exists( 'my_wp_head' ) ) {
function my_wp_head() {
	$bloginfo = get_template_directory_uri();
	$primarytheme = get_option('mvp_primary_theme');
	$mainmenu = get_option('mvp_menu_color');
	$link = get_option('mvp_link_color');
	$wallad = get_option('mvp_wall_ad');
	$slider_headline = get_option('mvp_slider_headline');
	$menu_font = get_option('mvp_menu_font');
	$headline_font = get_option('mvp_headline_font');
	$header_font = get_option('mvp_header_font');
	$google_slider = preg_replace("/ /","+",$slider_headline);
	$google_menu = preg_replace("/ /","+",$menu_font);
	$google_headlines = preg_replace("/ /","+",$headline_font);
	$google_header = preg_replace("/ /","+",$header_font);
	echo "
<style type='text/css'>

@import url(http://fonts.googleapis.com/css?family=$google_slider:100,200,300,400,500,600,700,800,900|$google_menu:100,200,300,400,500,600,700,800,900|$google_headlines:100,200,300,400,500,600,700,800,900|$google_header:100,200,300,400,500,600,700,800,900&subset=latin,latin-ext,cyrillic,cyrillic-ext,greek-ext,greek,vietnamese);

#nav-wrapper {
	background: $mainmenu;
	}

#main-nav .menu li a:hover,
#main-nav .menu li.current-menu-item a,
#search-button:hover,
.home-widget h3.widget-cat,
span.post-tags-header,
.post-tags a:hover,
.tag-cloud a:hover {
	background: $primarytheme;
	}

#main-nav .menu-item-home a:hover {
	background: $primarytheme !important;
	}

#top-story-left h3,
#top-story-right h3 {
	border-bottom: 3px solid $primarytheme;
	}

a, a:visited,
ul.top-stories li:hover h2,
ul.top-stories li:hover h2,
#top-story-middle:hover h2,
#top-story-wrapper:hover #feat2-main-text h2,
#feat1-left-wrapper:hover .feat1-left-text h2,
#feat1-right-wrapper:hover .feat1-right-text h2,
ul.split-columns li:hover h2,
.home-widget ul.wide-widget li:hover h2,
.home-widget ul.home-list li:hover h2,
h3.story-cat,
h3.story-cat a,
.sidebar-widget ul.home-list li:hover h2,
.sidebar-widget ul.wide-widget li:hover h2,
.sidebar-widget ul.split-columns li:hover h2,
#footer-nav .menu li a:hover,
.footer-widget ul.home-list li:hover h2,
.footer-widget ul.wide-widget li:hover h2,
.footer-widget ul.split-columns li:hover h2,
.prev,
.next {
	color: $link;
	}

#wallpaper {
	background: url($wallad) no-repeat 50% 0;
	}

.featured-text h2,
.featured-text h2 a,
.feat1-main-text h2,
.feat1-main-text h2 a {
	font-family: '$slider_headline', serif;
	}

#main-nav .menu li a,
#main-nav .menu li:hover ul li a {
	font-family: '$menu_font', sans-serif;
	}

#top-story-left h2,
#top-story-right h2,
.feat1-left-text h2,
.feat1-right-text h2,
#feat2-main-text h2,
#middle-text h2,
ul.split-columns li h2,
.home-widget .wide-text h2,
.home-widget .home-list-content h2,
h1.story-title,
.sidebar-widget .wide-text h2,
.sidebar-widget ul.split-columns li h2,
.footer-widget .wide-text h2,
.footer-widget ul.split-columns li h2,
#post-404 h1 {
	font-family: '$headline_font', serif;
	}

#top-story-left h3,
#top-story-right h3,
#middle-text h3,
h3.home-widget-header,
h1.archive-header,
h3.story-cat,
h3.story-cat a,
#content-area h1,
#content-area h2,
#content-area h3,
#content-area h4,
#content-area h5,
#content-area h6,
h4.post-header,
h3.sidebar-widget-header,
h3.footer-widget-header {
	font-family: '$header_font', sans-serif;
	}

@media screen and (max-width: 767px) and (min-width: 480px) {

	.sidebar-widget .home-list-content h2 {
		font-family: '$headline_font', serif;
		}

	}

</style>
	";
}
}
add_action( 'wp_head', 'my_wp_head' );

/////////////////////////////////////
// Register Widgets
/////////////////////////////////////

if ( !function_exists( 'mvp_sidebars_init' ) ) {
	function mvp_sidebars_init() {
		register_sidebar(array(
			'id' => 'homepage-widget',
			'name' => 'Homepage Widget Area',
			'before_widget' => '<div class="home-widget">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="home-widget-header">',
			'after_title' => '</h3>',
		));

		register_sidebar(array(
			'id' => 'sidebar-home-widget',
			'name' => 'Sidebar Home Widget Area',
			'before_widget' => '<div class="sidebar-widget">',
			'after_widget' => '</div>',
			'before_title' => '<span class="sidebar-widget-header"><h3 class="sidebar-widget-header">',
			'after_title' => '</h3></span>',
		));

		register_sidebar(array(
			'id' => 'sidebar-widget',
			'name' => 'Sidebar Widget Area',
			'before_widget' => '<div class="sidebar-widget">',
			'after_widget' => '</div>',
			'before_title' => '<span class="sidebar-widget-header"><h3 class="sidebar-widget-header">',
			'after_title' => '</h3></span>',
		));

		register_sidebar(array(
			'id' => 'footer-widget',
			'name' => 'Footer Widget Area',
			'before_widget' => '<div class="footer-widget">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="footer-widget-header">',
			'after_title' => '</h3>',
		));

	}
}
add_action( 'widgets_init', 'mvp_sidebars_init' );

include("widgets/widget-ad.php");
include("widgets/widget-ad125.php");
include("widgets/widget-columns.php");
include("widgets/widget-facebook.php");
include("widgets/widget-large.php");
include("widgets/widget-list.php");
include("widgets/widget-social.php");
include("widgets/widget-tags.php");

/////////////////////////////////////
// Register Custom Menus
/////////////////////////////////////

if ( !function_exists( 'register_menus' ) ) {
function register_menus() {
	register_nav_menus(
		array(
			'main-menu' => __( 'Main Menu', 'mvp-text' ),
			'footer-menu' => __( 'Footer Menu', 'mvp-text' ),)
	  	);
	  }
}
add_action( 'init', 'register_menus' );


class select_menu_walker extends Walker_Nav_Menu{

	 function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "";
	}


	function end_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "";
	}

	 function start_el( &$output, $object, $depth = 0, $args = array(), $current_object_id = 0 ) {
		global $wp_query;
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$class_names = $value = '';

		$classes = empty( $object->classes ) ? array() : (array) $object->classes;
		$classes[] = 'menu-item-' . $object->ID;

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $object, $args ) );
		$class_names = ' class="' . esc_attr( $class_names ) . '"';

		$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $object->ID, $object, $args );
		$id = strlen( $id ) ? ' id="' . esc_attr( $id ) . '"' : '';

		$attributes  = ! empty( $object->attr_title ) ? ' title="'  . esc_attr( $object->attr_title ) .'"' : '';
		$attributes .= ! empty( $object->target )     ? ' target="' . esc_attr( $object->target     ) .'"' : '';
		$attributes .= ! empty( $object->xfn )        ? ' rel="'    . esc_attr( $object->xfn        ) .'"' : '';
		$attributes .= ! empty( $object->url )        ? ' href="'   . esc_attr( $object->url        ) .'"' : '';

		$sel_val =  ' value="'   . esc_attr( $object->url        ) .'"';

		//check if the menu is a submenu
		switch ($depth){
		  case 0:
			   $dp = "";
			   break;
		  case 1:
			   $dp = "-";
			   break;
		  case 2:
			   $dp = "--";
			   break;
		  case 3:
			   $dp = "---";
			   break;
		  case 4:
			   $dp = "----";
			   break;
		  default:
			   $dp = "";
		}

		$output .= $indent . '<option'. $sel_val . $id . $value . '>'.$dp;

		$item_output = $args->before;
		//$item_output .= '<a'. $attributes .'>';
		$item_output .= $args->link_before . apply_filters( 'the_title', $object->title, $object->ID ) . $args->link_after;
		//$item_output .= '</a>';
		$item_output .= $args->after;

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $object, $depth, $args );
	}

	function end_el( &$output, $object, $depth = 0, $args = array() ) {
		$output .= "</option>\n";
	}

}

/////////////////////////////////////
// Register Custom Background
/////////////////////////////////////

$custombg = array(
	'default-color' => 'f5f5f5',
);
add_theme_support( 'custom-background', $custombg );

/////////////////////////////////////
// Register Thumbnails
/////////////////////////////////////

if ( function_exists( 'add_theme_support' ) ) {
add_theme_support( 'post-thumbnails' );
set_post_thumbnail_size( 660, 400, true );
add_image_size( 'post-thumb', 660, 400, true );
add_image_size( 'medium-thumb', 400, 242, true );
add_image_size( 'small-thumb', 270, 164, true );
}

/////////////////////////////////////
// Title Meta Data
/////////////////////////////////////

if ( !function_exists( 'rw_title' ) ) {
function rw_title( $title, $sep, $seplocation )
{
    global $page, $paged;
    // Don't affect in feeds.
    if ( is_feed() )
        return $title;
    // Add the blog name
    if ( 'right' == $seplocation )
        $title .= get_bloginfo( 'name' );
    else
        $title = get_bloginfo( 'name' ) . $title;
    // Add the blog description for the home/front page.
    $site_description = get_bloginfo( 'description', 'display' );
    if ( $site_description && ( is_home() || is_front_page() ) )
        $title .= " {$sep} {$site_description}";
    // Add a page number if necessary:
    if ( $paged >= 2 || $page >= 2 )
        $title .= " {$sep} " . sprintf( __( 'Page %s', 'dbt' ), max( $paged, $page ) );
    return $title;
}
}
add_filter( 'wp_title', 'rw_title', 10, 3 );

/////////////////////////////////////
// Add Bread Crumbs
/////////////////////////////////////

if ( !function_exists( 'dimox_breadcrumbs' ) ) {
function dimox_breadcrumbs() {

  $delimiter = '/';
  $home = 'Home'; // text for the 'Home' link
  $before = '<span class="current">'; // tag before the current crumb
  $after = '</span>'; // tag after the current crumb

  if ( !is_home() && !is_front_page() || is_paged() ) {

    echo '<div id="crumbs">';

    global $post;
    $homeLink = home_url();
    echo '<a href="' . $homeLink . '">' . $home . '</a> ' . $delimiter . ' ';

    if ( is_category() ) {
      global $wp_query;
      $cat_obj = $wp_query->get_queried_object();
      $thisCat = $cat_obj->term_id;
      $thisCat = get_category($thisCat);
      $parentCat = get_category($thisCat->parent);
      if ($thisCat->parent != 0) echo(get_category_parents($parentCat, TRUE, ' ' . $delimiter . ' '));
      echo $before . single_cat_title('', false) . $after;

    } elseif ( is_day() ) {
      echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
      echo '<a href="' . get_month_link(get_the_time('Y'),get_the_time('m')) . '">' . get_the_time('F') . '</a> ' . $delimiter . ' ';
      echo $before . get_the_time('d') . $after;

    } elseif ( is_month() ) {
      echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
      echo $before . get_the_time('F') . $after;

    } elseif ( is_year() ) {
      echo $before . get_the_time('Y') . $after;

    } elseif ( is_single() && !is_attachment() ) {
      if ( get_post_type() != 'post' ) {
        $post_type = get_post_type_object(get_post_type());
        $slug = $post_type->rewrite;
        echo '<a href="' . $homeLink . '/' . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a> ' . $delimiter . ' ';
        echo $before . get_the_title() . $after;
      } else {
        $cat = get_the_category(); $cat = $cat[0];
        echo get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
        echo $before . get_the_title() . $after;
      }

    } elseif ( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() && !is_search()) {
      $post_type = get_post_type_object(get_post_type());
      echo $before . $post_type->labels->singular_name . $after;

    } elseif ( is_attachment() ) {
      $parent = get_post($post->post_parent);
      $cat = get_the_category($parent->ID); $cat = $cat[0];
      echo get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
      echo '<a href="' . get_permalink($parent) . '">' . $parent->post_title . '</a> ' . $delimiter . ' ';
      echo $before . get_the_title() . $after;

    } elseif ( is_page() && !$post->post_parent ) {
      echo $before . get_the_title() . $after;

    } elseif ( is_page() && $post->post_parent ) {
      $parent_id  = $post->post_parent;
      $breadcrumbs = array();
      while ($parent_id) {
        $page = get_page($parent_id);
        $breadcrumbs[] = '<a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>';
        $parent_id  = $page->post_parent;
      }
      $breadcrumbs = array_reverse($breadcrumbs);
      foreach ($breadcrumbs as $crumb) echo $crumb . ' ' . $delimiter . ' ';
      echo $before . get_the_title() . $after;

    } elseif ( is_search() ) {
      echo $before . 'Search results for "' . get_search_query() . '"' . $after;

    } elseif ( is_tag() ) {
      echo $before . 'Posts tagged "' . single_tag_title('', false) . '"' . $after;

    } elseif ( is_author() ) {
       global $author;
      $userdata = get_userdata($author);
      echo $before . 'Articles posted by ' . $userdata->display_name . $after;

    } elseif ( is_404() ) {
      echo $before . 'Error 404' . $after;
    }

    if ( get_query_var('paged') ) {
      if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ' (';
      echo __('Page', 'mvp-text') . ' ' . get_query_var('paged');
      if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ')';
    }

    echo '</div>';

  }
}
}

/////////////////////////////////////
// Add Custom Meta Box
/////////////////////////////////////

/* Fire our meta box setup function on the post editor screen. */
add_action( 'load-post.php', 'mvp_post_meta_boxes_setup' );
add_action( 'load-post-new.php', 'mvp_post_meta_boxes_setup' );

/* Meta box setup function. */
if ( !function_exists( 'mvp_post_meta_boxes_setup' ) ) {
function mvp_post_meta_boxes_setup() {

	/* Add meta boxes on the 'add_meta_boxes' hook. */
	add_action( 'add_meta_boxes', 'mvp_add_post_meta_boxes' );

	/* Save post meta on the 'save_post' hook. */
	add_action( 'save_post', 'mvp_save_video_embed_meta', 10, 2 );
	add_action( 'save_post', 'mvp_save_featured_headline_meta', 10, 2 );
	add_action( 'save_post', 'mvp_save_photo_credit_meta', 10, 2 );
	add_action( 'save_post', 'mvp_save_featured_image_meta', 10, 2 );
}
}

/* Create one or more meta boxes to be displayed on the post editor screen. */
if ( !function_exists( 'mvp_add_post_meta_boxes' ) ) {
function mvp_add_post_meta_boxes() {

	add_meta_box(
		'mvp-video-embed',			// Unique ID
		esc_html__( 'Video/Audio Embed', 'mvp-text' ),		// Title
		'mvp_video_embed_meta_box',		// Callback function
		'post',					// Admin page (or post type)
		'normal',				// Context
		'high'					// Priority
	);

	add_meta_box(
		'mvp-featured-headline',			// Unique ID
		esc_html__( 'Featured Headline', 'mvp-text' ),		// Title
		'mvp_featured_headline_meta_box',		// Callback function
		'post',					// Admin page (or post type)
		'normal',				// Context
		'high'					// Priority
	);

	add_meta_box(
		'mvp-photo-credit',			// Unique ID
		esc_html__( 'Photo Credit', 'mvp-text' ),		// Title
		'mvp_photo_credit_meta_box',		// Callback function
		'post',					// Admin page (or post type)
		'normal',				// Context
		'high'					// Priority
	);

	add_meta_box(
		'mvp-featured-image',			// Unique ID
		esc_html__( 'Featured Image Show/Hide', 'mvp-text' ),		// Title
		'mvp_featured_image_meta_box',		// Callback function
		'post',					// Admin page (or post type)
		'side',				// Context
		'core'					// Priority
	);
}
}

/* Display the post meta box. */
if ( !function_exists( 'mvp_featured_headline_meta_box' ) ) {
function mvp_featured_headline_meta_box( $object, $box ) { ?>

	<?php wp_nonce_field( basename( __FILE__ ), 'mvp_featured_headline_nonce' ); ?>

	<p>
		<label for="mvp-featured-headline"><?php _e( "Add a custom featured headline that will be displayed in the featured slider.", 'example' ); ?></label>
		<br />
		<input class="widefat" type="text" name="mvp-featured-headline" id="mvp-featured-headline" value="<?php echo esc_html__( get_post_meta( $object->ID, 'mvp_featured_headline', true ) ); ?>" size="30" />
	</p>

<?php }
}

/* Display the post meta box. */
if ( !function_exists( 'mvp_video_embed_meta_box' ) ) {
function mvp_video_embed_meta_box( $object, $box ) { ?>

	<?php wp_nonce_field( basename( __FILE__ ), 'mvp_video_embed_nonce' ); ?>

	<p>
		<label for="mvp-video-embed"><?php _e( "Enter your video or audio embed code.", 'mvp-text' ); ?></label>
		<br />
		<input class="widefat" type="text" name="mvp-video-embed" id="mvp-video-embed" value="<?php echo esc_html__( get_post_meta( $object->ID, 'mvp_video_embed', true ) ); ?>" />
	</p>

<?php }
}

/* Display the post meta box. */
if ( !function_exists( 'mvp_photo_credit_meta_box' ) ) {
function mvp_photo_credit_meta_box( $object, $box ) { ?>

	<?php wp_nonce_field( basename( __FILE__ ), 'mvp_photo_credit_nonce' ); ?>

	<p>
		<label for="mvp-photo-credit"><?php _e( "Add a photo credit for the featured image.", 'mvp-text' ); ?></label>
		<br />
		<input class="widefat" type="text" name="mvp-photo-credit" id="mvp-photo-credit" value="<?php echo esc_html__( get_post_meta( $object->ID, 'mvp_photo_credit', true ) ); ?>" size="30" />
	</p>

<?php }
}

/* Display the post meta box. */
if ( !function_exists( 'mvp_featured_image_meta_box' ) ) {
function mvp_featured_image_meta_box( $object, $box ) { ?>

	<?php wp_nonce_field( basename( __FILE__ ), 'mvp_featured_image_nonce' ); $selected = esc_html__( get_post_meta( $object->ID, 'mvp_featured_image', true ) ); ?>

	<p>
		<label for="mvp-featured-image"><?php _e( "Select to show or hide the featured image from automatically displaying in this post.", 'mvp-text' ); ?></label>
		<br /><br />
		<select class="widefat" name="mvp-featured-image" id="mvp-featured-image">
            		<option value="show" <?php selected( $selected, 'show' ); ?>>Show</option>
            		<option value="hide" <?php selected( $selected, 'hide' ); ?>>Hide</option>
        	</select>
	</p>
<?php }
}

/* Save the meta box's post metadata. */
if ( !function_exists( 'mvp_save_video_embed_meta' ) ) {
function mvp_save_video_embed_meta( $post_id, $post ) {

	/* Verify the nonce before proceeding. */
	if ( !isset( $_POST['mvp_video_embed_nonce'] ) || !wp_verify_nonce( $_POST['mvp_video_embed_nonce'], basename( __FILE__ ) ) )
		return $post_id;

	/* Get the post type object. */
	$post_type = get_post_type_object( $post->post_type );

	/* Check if the current user has permission to edit the post. */
	if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
		return $post_id;

	/* Get the posted data and sanitize it for use as an HTML class. */
	$new_meta_value = ( isset( $_POST['mvp-video-embed'] ) ? balanceTags( $_POST['mvp-video-embed'] ) : '' );

	/* Get the meta key. */
	$meta_key = 'mvp_video_embed';

	/* Get the meta value of the custom field key. */
	$meta_value = get_post_meta( $post_id, $meta_key, true );

	/* If a new meta value was added and there was no previous value, add it. */
	if ( $new_meta_value && '' == $meta_value )
		add_post_meta( $post_id, $meta_key, $new_meta_value, true );

	/* If the new meta value does not match the old value, update it. */
	elseif ( $new_meta_value && $new_meta_value != $meta_value )
		update_post_meta( $post_id, $meta_key, $new_meta_value );

	/* If there is no new meta value but an old value exists, delete it. */
	elseif ( '' == $new_meta_value && $meta_value )
		delete_post_meta( $post_id, $meta_key, $meta_value );
} }

/* Save the meta box's post metadata. */
if ( !function_exists( 'mvp_save_featured_headline_meta' ) ) {
function mvp_save_featured_headline_meta( $post_id, $post ) {

	/* Verify the nonce before proceeding. */
	if ( !isset( $_POST['mvp_featured_headline_nonce'] ) || !wp_verify_nonce( $_POST['mvp_featured_headline_nonce'], basename( __FILE__ ) ) )
		return $post_id;

	/* Get the post type object. */
	$post_type = get_post_type_object( $post->post_type );

	/* Check if the current user has permission to edit the post. */
	if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
		return $post_id;

	/* Get the posted data and sanitize it for use as an HTML class. */
	$new_meta_value = ( isset( $_POST['mvp-featured-headline'] ) ? balanceTags( $_POST['mvp-featured-headline'] ) : '' );

	/* Get the meta key. */
	$meta_key = 'mvp_featured_headline';

	/* Get the meta value of the custom field key. */
	$meta_value = get_post_meta( $post_id, $meta_key, true );

	/* If a new meta value was added and there was no previous value, add it. */
	if ( $new_meta_value && '' == $meta_value )
		add_post_meta( $post_id, $meta_key, $new_meta_value, true );

	/* If the new meta value does not match the old value, update it. */
	elseif ( $new_meta_value && $new_meta_value != $meta_value )
		update_post_meta( $post_id, $meta_key, $new_meta_value );

	/* If there is no new meta value but an old value exists, delete it. */
	elseif ( '' == $new_meta_value && $meta_value )
		delete_post_meta( $post_id, $meta_key, $meta_value );
} }

/* Save the meta box's post metadata. */
if ( !function_exists( 'mvp_save_photo_credit_meta' ) ) {
function mvp_save_photo_credit_meta( $post_id, $post ) {

	/* Verify the nonce before proceeding. */
	if ( !isset( $_POST['mvp_photo_credit_nonce'] ) || !wp_verify_nonce( $_POST['mvp_photo_credit_nonce'], basename( __FILE__ ) ) )
		return $post_id;

	/* Get the post type object. */
	$post_type = get_post_type_object( $post->post_type );

	/* Check if the current user has permission to edit the post. */
	if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
		return $post_id;

	/* Get the posted data and sanitize it for use as an HTML class. */
	$new_meta_value = ( isset( $_POST['mvp-photo-credit'] ) ? balanceTags( $_POST['mvp-photo-credit'] ) : '' );

	/* Get the meta key. */
	$meta_key = 'mvp_photo_credit';

	/* Get the meta value of the custom field key. */
	$meta_value = get_post_meta( $post_id, $meta_key, true );

	/* If a new meta value was added and there was no previous value, add it. */
	if ( $new_meta_value && '' == $meta_value )
		add_post_meta( $post_id, $meta_key, $new_meta_value, true );

	/* If the new meta value does not match the old value, update it. */
	elseif ( $new_meta_value && $new_meta_value != $meta_value )
		update_post_meta( $post_id, $meta_key, $new_meta_value );

	/* If there is no new meta value but an old value exists, delete it. */
	elseif ( '' == $new_meta_value && $meta_value )
		delete_post_meta( $post_id, $meta_key, $meta_value );
} }

/* Save the meta box's post metadata. */
if ( !function_exists( 'mvp_save_featured_image_meta' ) ) {
function mvp_save_featured_image_meta( $post_id, $post ) {

	/* Verify the nonce before proceeding. */
	if ( !isset( $_POST['mvp_featured_image_nonce'] ) || !wp_verify_nonce( $_POST['mvp_featured_image_nonce'], basename( __FILE__ ) ) )
		return $post_id;

	/* Get the post type object. */
	$post_type = get_post_type_object( $post->post_type );

	/* Check if the current user has permission to edit the post. */
	if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
		return $post_id;

	/* Get the posted data and sanitize it for use as an HTML class. */
	$new_meta_value = ( isset( $_POST['mvp-featured-image'] ) ? balanceTags( $_POST['mvp-featured-image'] ) : '' );

	/* Get the meta key. */
	$meta_key = 'mvp_featured_image';

	/* Get the meta value of the custom field key. */
	$meta_value = get_post_meta( $post_id, $meta_key, true );

	/* If a new meta value was added and there was no previous value, add it. */
	if ( $new_meta_value && '' == $meta_value )
		add_post_meta( $post_id, $meta_key, $new_meta_value, true );

	/* If the new meta value does not match the old value, update it. */
	elseif ( $new_meta_value && $new_meta_value != $meta_value )
		update_post_meta( $post_id, $meta_key, $new_meta_value );

	/* If there is no new meta value but an old value exists, delete it. */
	elseif ( '' == $new_meta_value && $meta_value )
		delete_post_meta( $post_id, $meta_key, $meta_value );
} }

/////////////////////////////////////
// Add Content Limit
/////////////////////////////////////

if ( !function_exists( 'excerpt' ) ) {
function excerpt($limit) {
  $excerpt = explode(' ', get_the_excerpt(), $limit);
  if (count($excerpt)>=$limit) {
    array_pop($excerpt);
    $excerpt = implode(" ",$excerpt).'...';
  } else {
    $excerpt = implode(" ",$excerpt);
  }
  $excerpt = preg_replace('`\[[^\]]*\]`','',$excerpt);
  return $excerpt;
}
}

if ( !function_exists( 'content' ) ) {
function content($limit) {
  $content = explode(' ', get_the_content(), $limit);
  if (count($content)>=$limit) {
    array_pop($content);
    $content = implode(" ",$content).'...';
  } else {
    $content = implode(" ",$content);
  }
  $content = preg_replace('/\[.+\]/','', $content);
  $content = apply_filters('the_content', $content);
  $content = str_replace(']]>', ']]&gt;', $content);
  return $content;
}
}

/////////////////////////////////////
// Comments
/////////////////////////////////////

if ( !function_exists( 'mvp_comment' ) ) {
function mvp_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case '' :
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">


		<div class="comment-wrapper" id="comment-<?php comment_ID(); ?>">
			<div class="comment-inner">

				<div class="comment-avatar">
					<?php echo get_avatar( $comment, 46 ); ?>
				</div>

				<div class="commentmeta">
					<p class="comment-meta-1">
						<?php printf( __( '%s ', 'mvp-text'), sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() ) ); ?>
					</p>
					<p class="comment-meta-2">
						<?php echo get_comment_date(); ?> <?php _e( 'at', 'mvp-text'); ?> <?php echo get_comment_time(); ?>
						<?php edit_comment_link( __( 'Edit', 'mvp-text'), '(' , ')'); ?>
					</p>

				</div>

				<div class="text">

					<?php if ( $comment->comment_approved == '0' ) : ?>
						<p class="waiting_approval"><?php _e( 'Your comment is awaiting moderation.', 'mvp-text' ); ?></p>
					<?php endif; ?>

					<div class="c">
						<?php comment_text(); ?>
					</div>

				</div><!-- .text  -->
				<div class="clear"></div>
				<div class="comment-reply"><span class="reply"><?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?></span></div>
			</div><!-- comment-inner  -->
		</div><!-- comment-wrapper  -->
	<?php
			break;
		case 'pingback'  :
		case 'trackback' :
	?>
	<li class="post pingback">
		<p><?php _e( 'Pingback:', 'mvp-text' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( 'Edit', 'mvp-text' ), ' ' ); ?></p>
	<?php
			break;
	endswitch;
}
}

/////////////////////////////////////
// Popular Posts
/////////////////////////////////////

if ( !function_exists( 'popularPosts' ) ) {
function popularPosts($num) {
    global $wpdb;

    $posts = $wpdb->get_results("SELECT comment_count, ID, post_title FROM $wpdb->posts ORDER BY comment_count DESC LIMIT 0 , $num");

    foreach ($posts as $post) {
        setup_postdata($post);
        $id = $post->ID;
        $title = $post->post_title;
        $count = $post->comment_count;

        if ($count != 0) {
            $popular .= '<li>';
            $popular .= '<a href="' . get_permalink($id) . '" title="' . $title . '">' . $title . '</a> ';
            $popular .= '</li>';
        }
    }
    return $popular;
}
}

/////////////////////////////////////
// Related Posts
/////////////////////////////////////

if ( !function_exists( 'getRelatedPosts' ) ) {
function getRelatedPosts( $count=3) {
    global $post;
    $orig_post = $post;

    $tags = wp_get_post_tags($post->ID);
    if ($tags) {
        $tag_ids = array();
        foreach($tags as $individual_tag) $tag_ids[] = $individual_tag->term_id;
        $args=array(
            'tag__in' => $tag_ids,
            'post__not_in' => array($post->ID),
            'posts_per_page'=> $count, // Number of related posts that will be shown.
            'ignore_sticky_posts'=>1
        );
        $my_query = new WP_Query( $args );
        if( $my_query->have_posts() ) { ?>
            <div id="related-posts">
            	<h4 class="post-header"><span class="post-header"><?php _e( 'You may also like...', 'mvp-text' ); ?></span></h4>
			<ul>
            		<?php while( $my_query->have_posts() ) { $my_query->the_post(); ?>
            			<li>
                		<div class="related-image">
					<?php if (  (function_exists('has_post_thumbnail')) && (has_post_thumbnail())  ) { ?>
					<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_post_thumbnail('medium-thumb'); ?></a>
					<?php } ?>
				</div><!--related-image-->
				<div class="related-text">
					<a href="<?php the_permalink() ?>" class="main-headline"><?php the_title(); ?></a>
				</div><!--related-text-->
            			</li>
            		<?php }
            echo '</ul></div>';
        }
    }
    $post = $orig_post;
    wp_reset_query();
}
}

/////////////////////////////////////
// Theia Posts Slider
/////////////////////////////////////

define('TPS_PLUGINS_URL', get_template_directory_uri() . '/admin/theia-post-slider/');
define('TPS_USE_AS_STANDALONE', false);
require_once 'admin/theia-post-slider/main.php';
add_action('admin_menu', 'mvp_tps_admin_menu');
if ( !function_exists( 'mvp_tps_admin_menu' ) ) {
function mvp_tps_admin_menu() {
	add_theme_page('Theia Post Slider Settings', 'Theia Post Slider', 'manage_options', 'tps', 'TpsMenu::do_page');
}
}

/////////////////////////////////////
// Pagination
/////////////////////////////////////

if ( !function_exists( 'pagination' ) ) {
function pagination($pages = '', $range = 4)
{
     $showitems = ($range * 2)+1;

     global $paged;
     if(empty($paged)) $paged = 1;

     if($pages == '')
     {
         global $wp_query;
         $pages = $wp_query->max_num_pages;
         if(!$pages)
         {
             $pages = 1;
         }
     }

     if(1 != $pages)
     {
         echo "<div class=\"pagination\"><span>Page ".$paged." of ".$pages."</span>";
         if($paged > 2 && $paged > $range+1 && $showitems < $pages) echo "<a href='".get_pagenum_link(1)."'>&laquo; First</a>";
         if($paged > 1 && $showitems < $pages) echo "<a href='".get_pagenum_link($paged - 1)."'>&lsaquo; Previous</a>";

         for ($i=1; $i <= $pages; $i++)
         {
             if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
             {
                 echo ($paged == $i)? "<span class=\"current\">".$i."</span>":"<a href='".get_pagenum_link($i)."' class=\"inactive\">".$i."</a>";
             }
         }

         if ($paged < $pages && $showitems < $pages) echo "<a href=\"".get_pagenum_link($paged + 1)."\">Next &rsaquo;</a>";
         if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) echo "<a href='".get_pagenum_link($pages)."'>Last &raquo;</a>";
         echo "</div>\n";
     }
}
}

/////////////////////////////////////
// Add/Remove User Contact Info
/////////////////////////////////////

if ( !function_exists( 'new_contactmethods' ) ) {
function new_contactmethods( $contactmethods ) {
    $contactmethods['facebook'] = 'Facebook'; // Add Facebook
    $contactmethods['twitter'] = 'Twitter'; // Add Twitter
    $contactmethods['pinterest'] = 'Pinterest'; // Add Pinterest
    $contactmethods['googleplus'] = 'Google Plus'; // Add Google Plus
    $contactmethods['instagram'] = 'Instagram'; // Add Instagram
    $contactmethods['linkedin'] = 'LinkedIn'; // Add LinkedIn
    unset($contactmethods['yim']); // Remove YIM
    unset($contactmethods['aim']); // Remove AIM
    unset($contactmethods['jabber']); // Remove Jabber

    return $contactmethods;
}
}
add_filter('user_contactmethods','new_contactmethods',10,1);

/////////////////////////////////////
// Social Media Javascript
/////////////////////////////////////

if ( !function_exists( 'mvp_wp_footer' ) ) {
function mvp_wp_footer() {

?>

<?php $mvp_infinite_scroll = get_option('mvp_infinite_scroll'); if ($mvp_infinite_scroll == "true") { if (isset($mvp_infinite_scroll)) { ?>
<script type="text/javascript">
//<![CDATA[
jQuery(document).ready(function($) {
"use strict";
$('.infinite-content').infinitescroll({
	navSelector: ".nav-links",
	nextSelector: ".nav-links a:first",
	itemSelector: ".infinite-post",
	donetext: "No more posts" ,
});
});
//]]>
</script>
<?php } } ?>

<?php if ( is_single() || is_page() ) { ?>

<div id="fb-root"></div>
<script>
//<![CDATA[
(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.async = true;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));
//]]>
</script>

<script type="text/javascript">
//<![CDATA[
!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.async=true;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");
//]]>
</script>

<script type="text/javascript">
//<![CDATA[
(function() {
    window.PinIt = window.PinIt || { loaded:false };
    if (window.PinIt.loaded) return;
    window.PinIt.loaded = true;
    function async_load(){
        var s = document.createElement("script");
        s.type = "text/javascript";
        s.async = true;
        s.src = "http://assets.pinterest.com/js/pinit.js";
        var x = document.getElementsByTagName("script")[0];
        x.parentNode.insertBefore(s, x);
    }
    if (window.attachEvent)
        window.attachEvent("onload", async_load);
    else
        window.addEventListener("load", async_load, false);
})();
//]]>
</script>

<script type="text/javascript">
//<![CDATA[
  (function() {
    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
    po.src = 'https://apis.google.com/js/plusone.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
  })();
//]]>
</script>

<?php } ?>

<?php }

}
add_action( 'wp_footer', 'mvp_wp_footer' );

/////////////////////////////////////
// Miscellaneous
/////////////////////////////////////

// Set Content Width
if ( ! isset( $content_width ) ) $content_width = 620;

// Add RSS links to <head> section
add_theme_support( 'automatic-feed-links' );

add_action('init', 'do_output_buffer');
function do_output_buffer() {
        ob_start();
}


?>