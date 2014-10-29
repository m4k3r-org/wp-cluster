<?php ob_start(); ?>

<?php
require_once( 'library/siteframework.php' );		// core functions
require( 'theme-options.php' );          			// theme options

add_action( 'add_meta_boxes', 'action_add_meta_boxes', 10, 2 );
function action_add_meta_boxes() {
	global $_wp_post_type_features;
	if (isset($_wp_post_type_features['post']['editor']) && $_wp_post_type_features['post']['editor']) {
		unset($_wp_post_type_features['post']['editor']);
		add_meta_box(
			'description_section',
			__('Editor'),
			'inner_custom_box',
			'post', 'normal', 'default'
		);
	}
	if (isset($_wp_post_type_features['page']['editor']) && $_wp_post_type_features['page']['editor']) {
		unset($_wp_post_type_features['page']['editor']);
		add_meta_box(
			'description_sectionid',
			__('Editor'),
			'inner_custom_box',
			'page', 'normal', 'default'
		);
	}
}
function inner_custom_box( $post ) {
	the_editor($post->post_content);
}


//include another function files
include( "admin/inc/widget-flickr.php" );



//set multiple custom excerpts
function excerpt($limit) {
    $excerpt = explode(' ', get_the_excerpt(), $limit);
    if (count($excerpt)>=$limit) {
        array_pop($excerpt);
        $excerpt = implode(" ",$excerpt).'...';
    } else {
        $excerpt = implode(" ",$excerpt);
    }
    $excerpt = preg_replace('`[[^]]*]`','',$excerpt);
    return $excerpt;
}

function excerpt_content($limit) {
    $content = explode(' ', get_the_content(), $limit);
    if (count($content)>=$limit) {
        array_pop($content);
        $content = implode(" ",$content).'...';
    } else {
        $content = implode(" ",$content);
    }
    $content = preg_replace('/[.+]/','', $content);
    $content = apply_filters('the_content', $content);
    $content = str_replace(']]>', ']]&gt;', $content);
    return $content;
}

global $pagenow;
if ( is_admin() && isset( $_GET['activated'] ) && $pagenow == 'themes.php' )
{
    wp_redirect( admin_url( 'admin.php?page=options-framework' ) );
    exit;
}
?>