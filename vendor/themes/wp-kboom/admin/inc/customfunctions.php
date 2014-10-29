<?php
/*********************************************************************************************

Set Max Content Width

*********************************************************************************************/
if ( ! isset( $content_width ) ) $content_width = 700;

/*********************************************************************************************

If 3.1 isn't installed display a notice that post type archives will not work

*********************************************************************************************/
function site5framework_archive_nag(){
    global $pagenow;
    if ( $pagenow == 'themes.php' ) {
         echo '<div class="updated"><p>';
		 _e('Portfolio archive pages will only display in WordPress 3.1 or above.  Please upgrade.', 'site5framework');
		 echo '</p></div>';
    }
}

if ( get_bloginfo('version') < 3.1 ) {
	add_action('admin_notices', 'site5framework_archive_nag');
}


/*********************************************************************************************

Adds a body class to indicate sidebar position

*********************************************************************************************/
function site5framework_body_class($classes) {
	$layout = of_get_option('layout','layout-2cr');
	$classes[] = $layout;
	return $classes;
}

add_filter('body_class','site5framework_body_class');


/*********************************************************************************************

Add Theme Support

*********************************************************************************************/
add_theme_support( 'menus' );
add_theme_support( 'automatic-feed-links' );
add_editor_style('editor_style.css');

/*********************************************************************************************

Post & Page Thumbnails Support

*********************************************************************************************/
if ( function_exists( 'add_theme_support' ) ) { // Added in 2.9
	add_theme_support( 'post-thumbnails' );
  set_post_thumbnail_size( 220, 220, true );
  add_image_size( 'small', 125, '', true ); // Small thumbnails
  add_image_size( 'medium', 250, '', true ); // Medium thumbnails
  add_image_size( 'large', 700, '', true ); // Large thumbnails
  add_image_size( 'post-thumb', 220, '', true ); // Post thumbnails
  add_image_size( 'portfolio-thumbnail', 460, 460, true ); // Portfolio thumbnails
  add_image_size( 'archive-thumbnail', 700, 250, true ); // Archive thumbnails
  add_image_size( 'slide', 940, 415, true ); // Sliders
  add_image_size( 'page-header', 1020, 200, true ); // Page Header
}


/*********************************************************************************************

Custom Admin Login Logo

*********************************************************************************************/
function custom_login_logo() {
    if ( !of_get_option('sc_clogo')== '') {
    echo '<style type="text/css">
    #login h1 a {background-image: url('.of_get_option('sc_clogo').') !important; background-size: auto !important;  }
    </style>';
    }
}
add_action('login_head', 'custom_login_logo');


/*********************************************************************************************

Default Wordpress Gallery With PrettyPhoto

*********************************************************************************************/
add_filter( 'wp_get_attachment_link', 'site5framework_prettyphoto');

function site5framework_prettyphoto ($content) {
    $content = preg_replace("/<a/","<a class=\"prettyPhoto[mixed]\"",$content,1);
    return $content;
}

/*********************************************************************************************

Theme Contents Format

*********************************************************************************************/
function theme_content() {
        $content = get_the_content();
        $content = strip_tags($content, '<a><strong><em><b><i><embed><object>');
        $content = preg_replace('/\[.+\]/','', $content);
        $content = apply_filters('the_content', $content);
        $content = str_replace(']]>', ']]&gt;', $content);
        echo $content;
}

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



/*********************************************************************************************

Catch First Image

*********************************************************************************************/
function wp_catch_first_image($image_size = '',$return_empty = false) {
    global $post, $posts;
    $first_img = '';
    ob_start();
    ob_end_clean();
    $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
    $first_img = $matches [1] [0];
      if(empty($first_img) && $return_empty == false){
            if($image_size == 's') {
                    $first_img = get_template_directory_uri()."/images/thumb_small.jpg";
            }
            else if($image_size == 'm') {
                    $first_img = get_template_directory_uri()."/images/thumb_medium.jpg";
            }
            else if($image_size == 'l') {
                    $first_img = get_template_directory_uri()."/images/thumb_large.jpg";
            }
            else {
                    $first_img = get_template_directory_uri()."/images/default.jpg";
            }
    }

    return $first_img;
}


/*********************************************************************************************

Author Related Posts

*********************************************************************************************/
function get_related_author_posts() {
    global $authordata, $post;
    $authors_posts = get_posts( array( 'author' => $authordata->ID, 'post_not_in' => array( $post->ID ), 'posts_per_page' => 10 ) );
    $output = '<ul>';
    foreach ( $authors_posts as $authors_post ) {
        $output .= '<li> <a href="' . get_permalink( $authors_post->ID ) . '">' . apply_filters( 'the_title', $authors_post->post_title, $authors_post->ID ) . '</a></li>';
    }
    $output .= '</ul>';
    return $output;
}

/*********************************************************************************************

Enable Threaded Comments

*********************************************************************************************/
function enable_threaded_comments(){
if (!is_admin()) {
     if (is_singular() AND comments_open() AND (get_option('thread_comments') == 1))
          wp_enqueue_script('comment-reply');
     }
}

add_action('get_header', 'enable_threaded_comments');



function wpthemess_content_nav() {
	global $wp_query;
	if (  $wp_query->max_num_pages > 1 ) :
		if (function_exists('wp_pagenavi') ) {
			wp_pagenavi();
		} else { ?>
        	<nav id="nav-below">
			<h1 class="screen-reader-text"><?php _e( 'Post navigation', 'site5framework' ); ?></h1>
			<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'site5framework' ) ); ?></div>
			<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'site5framework' ) ); ?></div>
			</nav><!-- #nav-below -->
    	<?php }
	endif;
}

/*********************************************************************************************

WP MU IMAGE SUPPORT

*********************************************************************************************/
function get_image_url() {
    $theImageSrc = wp_get_attachment_url(get_post_thumbnail_id($post_id));
    global $blog_id;
    if (isset($blog_id) && $blog_id > 0) {
        $imageParts = explode('/files/', $theImageSrc);
        if (isset($imageParts[1])) {
            $theImageSrc = '/blogs.dir/' . $blog_id . '/files/' . $imageParts[1];
        }
    }
    echo $theImageSrc;
}

/*********************************************************************************************

WP MU CUSTOM META IMAGE SUPPORT

*********************************************************************************************/
function get_image_path($cutommeta_image) {
$theImageSrc1 = $cutommeta_image;
global $blog_id;
if (isset($blog_id) && $blog_id > 0) {
    $imageParts = explode('/files/', $theImageSrc1);
    if (isset($imageParts[1])) {
        $theImageSrc1 = '/blogs.dir/' . $blog_id . '/files/' . $imageParts[1];
    }
}
return $theImageSrc1;
}
/*********************************************************************************************

THUMBNAIL SIZE OPTIONS

*********************************************************************************************/
add_image_size( 'siteframework-thumb-600', 600, 150, true );
add_image_size( 'siteframework-thumb-300', 300, 100, true );


/*********************************************************************************************

COMMENT LAYOUT

*********************************************************************************************/
function site5framework_comments($comment, $args, $depth) {
   $GLOBALS['comment'] = $comment; ?>
	<li <?php comment_class(); ?>>
		<article id="comment-<?php comment_ID(); ?>" class="clearfix">
			<header class="comment-author vcard">
				<?php echo get_avatar($comment,$size='40',$default='<path_to_url>' ); ?>

        <div class="authormeta">
          <h3 class="comment-author"><?php printf(__('<cite class="fn">%s</cite>'), get_comment_author_link()) ?></h3>
          <div class='reply-link'>
              <strong class='reply-line'>&#8722;</strong><?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
          </div>
          <span class="datetime">
           <time datetime="<?php echo comment_time('Y-m-j'); ?>"><a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>"><?php comment_time('F jS, Y'); ?> </a></time>
          </span>
          <?php edit_comment_link(__('(Edit)'),'  ','') ?>
        </div>
			</header>

			<div class="comment-text">
				<?php comment_text() ?>
			</div>
      <?php if ($comment->comment_approved == '0') : ?>
      <em><?php _e('Your comment is awaiting moderation.','site5framework') ?></em>
      <?php endif; ?>
		</article>
    <!-- </li> is added by wordpress automatically -->
<?php
} // don't remove this bracket!

/*********************************************************************************************

SEARCH FORM LAYOUT

*********************************************************************************************/
// Search Form
function site5framework_wpsearch($form) {
    $form = '<form role="search" method="get" id="searchform" action="' . home_url( '/' ) . '" >
    <label class="screen-reader-text" for="s">' . __('Search for:', 'site5framework') . '</label>
    <input type="text" value="' . get_search_query() . '" name="s" id="s" placeholder="Search..." />
    <input type="submit" id="searchsubmit" value="'. esc_attr__('Search') .'" />
    </form>';
    return $form;
} 

/*******************************
Audio player
********************************/
function player_audio($postid){

$mp3 = get_post_meta($postid, 'sn_audio_post_mp3', $single = true);
$ogg = get_post_meta($postid, 'sn_$audio_post_ogg', $single = true);
$poster = get_post_meta($postid, 'sn_audio_post_poster',true);
?>
<script type="text/javascript">
    jQuery(document).ready(function($){

        if($().jPlayer) {
            $("#jquery_jplayer_<?php echo $postid; ?>").jPlayer({
                ready: function () {
                    $(this).jPlayer("setMedia", {
                    <?php if($poster != '') : ?>
                        poster: "<?php echo $poster['src']; ?>",
                        <?php endif; ?>
                    <?php if($mp3 != '') : ?>
                        mp3: "<?php echo $mp3; ?>",
                        <?php endif; ?>
                    <?php if($ogg != '') : ?>
                        oga: "<?php echo $ogg; ?>",
                        <?php endif; ?>
                        end: ""
                    });
                },
                size: {
                    width: "100%",
                    height:"auto"
                },
                swfPath: "<?php echo get_template_directory_uri(); ?>/library/js",
                cssSelectorAncestor: "#jp_interface_<?php echo $postid; ?>",
                supplied: "<?php if($ogg != '') : ?>oga,<?php endif; ?><?php if($mp3 != '') : ?>mp3, <?php endif; ?> all"
            });
        }
    });
</script>

<div id="jquery_jplayer_<?php echo $postid; ?>" class="jp-jplayer jp-jplayer-audio"></div>
<div class="jp-audio-container">
    <div class="jp-audio">
        <div class="jp-type-single">
            <div id="jp_interface_<?php echo $postid; ?>" class="jp-interface">
                <ul class="jp-controls">
                    <li><div class="seperator-first"></div></li>
                    <li><div class="seperator-second"></div></li>
                    <li><a href="#" class="jp-play" tabindex="1">play</a></li>
                    <li><a href="#" class="jp-pause" tabindex="1">pause</a></li>
                    <li><a href="#" class="jp-mute" tabindex="1">mute</a></li>
                    <li><a href="#" class="jp-unmute" tabindex="1">unmute</a></li>
                </ul>
                <div class="jp-progress-container">
                    <div class="jp-progress">
                        <div class="jp-seek-bar">
                            <div class="jp-play-bar"></div>
                        </div>
                    </div>
                </div>
                <div class="jp-volume-bar-container">
                    <div class="jp-volume-bar">
                        <div class="jp-volume-bar-value"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php


}?>
