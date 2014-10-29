<?php
/*********************************************************************************************

Registers Custom Portfolio Post Type

*********************************************************************************************/
$labels = array(
    'name'                          => __( 'Types', 'site5framework' ),
    'singular_name'                 => __( 'Type', 'site5framework' ),
    'search_items'                  => __( 'Search Types', 'site5framework' ),
    'popular_items'                 => __( 'Popular Types', 'site5framework' ),
    'all_items'                     => __( 'All Types', 'site5framework' ),
    'parent_item'                   => __( 'Parent Type', 'site5framework' ),
    'edit_item'                     => __( 'Edit Type', 'site5framework' ),
    'update_item'                   => __( 'Update Type', 'site5framework' ),
    'add_new_item'                  => __( 'Add New Type', 'site5framework' ),
    'new_item_name'                 => __( 'New Type', 'site5framework' ),
    'separate_items_with_commas'    => __( 'Separate Types with commas', 'site5framework' ),
    'add_or_remove_items'           => __( 'Add or remove Types', 'site5framework' ),'',
    'choose_from_most_used'         => __( 'Choose from most used Types', 'site5framework' )
    );

$args = array(
    'label'                         => __( 'Types', 'site5framework' ),
    'labels'                        => $labels,
    'public'                        => true,
    'hierarchical'                  => true,
    'show_ui'                       => true,
    'show_in_nav_menus'             => true,
    'args'                          => array( 'orderby' => 'term_order' ),
    'rewrite'                       => array( 'slug' => 'portfolio/types', 'with_front' => false ),
    'query_var'                     => true
);

register_taxonomy( 'types', 'portfolio', $args );


register_post_type( 'portfolio',
    array(
        'labels'                => array(
	    'name'                  => __( 'Portfolio', 'site5framework' ),
	    'singular_name'         => __( 'Portfolio Item', 'site5framework' ),
	    'add_new'               => __( 'Add New Item', 'site5framework' ),
	    'add_new_item'          => __( 'Add New Portfolio Item', 'site5framework' ),
	    'edit_item'             => __( 'Edit Portfolio Item', 'site5framework' ),
	    'new_item'              => __( 'Add New Portfolio Item', 'site5framework' ),
	    'view_item'             => __( 'View Item', 'site5framework' ),
	    'search_items'          => __( 'Search Portfolio', 'site5framework' ),
	    'not_found'             => __( 'No portfolio items found', 'site5framework' ),
	    'not_found_in_trash'    => __( 'No portfolio items found in trash', 'site5framework' )
            ),
        'public'                => true,
        'publicly_queryable'    => true,
        'show_ui'               => true,
        'query_var'             => true,
    	'permalink_epmask'      => true,
        'menu_position'         => 5,
        'show_in_menu'          => true,
        'supports' 				=> array( 'title','page-attributes' ),
        'rewrite'               => array( 'slug' => 'portfolio/details', 'with_front' => false ),
        'has_archive'           => true
    )
);


//  Add Columns to Portfolio Edit Screen


function portfolio_edit_columns($portfolio_columns){
	$portfolio_columns = array(
		"cb" 				=> "<input type=\"checkbox\" />",
		"title" 			=> __('Title', 'site5framework'),
		"portfolio-tags" 	=> __('Tags', 'site5framework'),
		"author" 			=> __('Author', 'site5framework'),
		"comments" 			=> __('Comments', 'site5framework'),
		"date" 				=> __('Date', 'site5framework'),
	);
	$portfolio_columns['comments'] = '<div class="vers"><img alt="Comments" src="' . esc_url( admin_url( 'images/comment-grey-bubble.png' ) ) . '" /></div>';
	return $portfolio_columns;
}

// GET PORTFOLIO IMAGE  
function wpt_get_featured_image($post_ID) {  
    $post_thumbnail_id = get_image_id_by_link ( get_post_meta($post_ID, 'snbp_pitemlink', true) );
    if ($post_thumbnail_id) {  
        $post_thumbnail_img = wp_get_attachment_image_src($post_thumbnail_id, 'small');  
        return $post_thumbnail_img[0];  
    }  
}  


function wpt_portfolio_columns_head($defaults) {  
    $defaults['featured_image'] = 'Image';  
    return $defaults;  
}  
  
// SHOW THE FEATURED IMAGE  
function wpt_portfolio_columns_content ( $column, $post_id ) {

    if ( $column == 'featured_image') {  
        $post_portfolio_image = wpt_get_featured_image($post_id);  
        if ($post_portfolio_image) {  
            echo '<img src="' . $post_portfolio_image . '" />';
        }  
    }  
}
 
// ADDS EXTRA INFO TO ADMIN MENU FOR PORTFOLIO POST TYPE
add_filter("manage_edit-portfolio_columns", "wpt_portfolio_columns_head");
add_action("manage_portfolio_posts_custom_column", "wpt_portfolio_columns_content", 10, 2 );
?>
<?php

/*********************************************************************************************

Registers Custom Audio Post Type

 *********************************************************************************************/
$labels = array(
    'name'                          => __( 'Albums', 'site5framework' ),
    'singular_name'                 => __( 'Album', 'site5framework' ),
    'search_items'                  => __( 'Search Albums', 'site5framework' ),
    'popular_items'                 => __( 'Popular Albums', 'site5framework' ),
    'all_items'                     => __( 'All Albums', 'site5framework' ),
    'parent_item'                   => __( 'Parent Album', 'site5framework' ),
    'edit_item'                     => __( 'Edit Album', 'site5framework' ),
    'update_item'                   => __( 'Update Album', 'site5framework' ),
    'add_new_item'                  => __( 'Add New Album', 'site5framework' ),
    'new_item_name'                 => __( 'New Album', 'site5framework' ),
    'separate_items_with_commas'    => __( 'Separate Albums with commas', 'site5framework' ),
    'add_or_remove_items'           => __( 'Add or remove Albums', 'site5framework' ),'',
    'choose_from_most_used'         => __( 'Choose from most used Albums', 'site5framework' )
);

$args = array(
    'label'                         => __( 'Albums', 'site5framework' ),
    'labels'                        => $labels,
    'public'                        => true,
    'hierarchical'                  => true,
    'show_ui'                       => true,
    'show_in_nav_menus'             => true,
    'args'                          => array( 'orderby' => 'term_order' ),
    'rewrite'                       => array( 'slug' => 'audio/albums', 'with_front' => false ),
    'query_var'                     => true
);

register_taxonomy( 'albums', 'audio', $args );


register_post_type( 'audio',
    array(
        'labels'                => array(
            'name'                  => __( 'Audio', 'site5framework' ),
            'singular_name'         => __( 'Audio Song', 'site5framework' ),
            'add_new'               => __( 'Add New Song', 'site5framework' ),
            'add_new_item'          => __( 'Add New Audio Song', 'site5framework' ),
            'edit_item'             => __( 'Edit Audio Song', 'site5framework' ),
            'new_item'              => __( 'Add New Audio Song', 'site5framework' ),
            'view_item'             => __( 'View Song', 'site5framework' ),
            'search_items'          => __( 'Search Songs', 'site5framework' ),
            'not_found'             => __( 'No audio songs found', 'site5framework' ),
            'not_found_in_trash'    => __( 'No audio songs found in trash', 'site5framework' )
        ),
        'public'                => true,
        'publicly_queryable'    => true,
        'show_ui'               => true,
        'query_var'             => true,
        'permalink_epmask'      => true,
        'menu_position'         => 5,
        'show_in_menu'          => true,
        'supports' 				=> array( 'title', 'editor', 'comments', 'page-attributes' ),
        'rewrite'               => array( 'slug' => 'audio/details', 'with_front' => false ),
        'has_archive'           => true
    )
);


//  Add Columns to Audio Edit Screen

function audio_edit_columns($audio_columns){
    $audio_columns = array(
        "cb" 				=> "<input type=\"checkbox\" />",
        "title" 			=> __('Title', 'site5framework'),
        "portfolio-tags" 	=> __('Tags', 'site5framework'),
        "author" 			=> __('Author', 'site5framework'),
        "comments" 			=> __('Comments', 'site5framework'),
        "date" 				=> __('Date', 'site5framework'),
    );
    $audio_columns['comments'] = '<div class="vers"><img alt="Comments" src="' . esc_url( admin_url( 'images/comment-grey-bubble.png' ) ) . '" /></div>';
    return $audio_columns;
}

// GET AUDIO IMAGE
function wpa_get_featured_image($post_ID) {
    $post_thumbnail_id = get_image_id_by_link ( get_post_meta($post_ID, 'snbp_pitemlink', true) );
    if ($post_thumbnail_id) {
        $post_thumbnail_img = wp_get_attachment_image_src($post_thumbnail_id, 'small');
        return $post_thumbnail_img[0];
    }
}


function wpa_audio_columns_head($defaults) {
    $defaults['featured_image'] = 'Image';
    return $defaults;
}

// SHOW THE FEATURED IMAGE
function wpa_audio_columns_content ( $column, $post_id ) {

    if ( $column == 'featured_image') {
        $post_audio_image = wpa_get_featured_image($post_id);
        if ($post_audio_image) {
            echo '<img src="' . $post_audio_image . '" />';
        }
    }
}

// ADDS EXTRA INFO TO ADMIN MENU FOR AUDIO POST ALBUMS
add_filter("manage_edit-audio_columns", "wpa_audio_columns_head");
add_action("manage_audio_posts_custom_column", "wpa_audio_columns_content", 10, 2 );
?>
<?php

/*********************************************************************************************

Registers Custom Video Post Type

 *********************************************************************************************/
$labels = array(
    'name'                          => __( 'Collections', 'site5framework' ),
    'singular_name'                 => __( 'Collection', 'site5framework' ),
    'search_items'                  => __( 'Search Collections', 'site5framework' ),
    'popular_items'                 => __( 'Popular Collections', 'site5framework' ),
    'all_items'                     => __( 'All Collections', 'site5framework' ),
    'parent_item'                   => __( 'Parent Collection', 'site5framework' ),
    'edit_item'                     => __( 'Edit Collection', 'site5framework' ),
    'update_item'                   => __( 'Update Collection', 'site5framework' ),
    'add_new_item'                  => __( 'Add New Collection', 'site5framework' ),
    'new_item_name'                 => __( 'New Collection', 'site5framework' ),
    'separate_items_with_commas'    => __( 'Separate Collections with commas', 'site5framework' ),
    'add_or_remove_items'           => __( 'Add or remove Collections', 'site5framework' ),'',
    'choose_from_most_used'         => __( 'Choose from most used Collections', 'site5framework' )
);

$args = array(
    'label'                         => __( 'Collections', 'site5framework' ),
    'labels'                        => $labels,
    'public'                        => true,
    'hierarchical'                  => true,
    'show_ui'                       => true,
    'show_in_nav_menus'             => true,
    'args'                          => array( 'orderby' => 'term_order' ),
    'rewrite'                       => array( 'slug' => 'video/collections', 'with_front' => false ),
    'query_var'                     => true
);

register_taxonomy( 'collections', 'video', $args );


register_post_type( 'video',
    array(
        'labels'                => array(
            'name'                  => __( 'Video', 'site5framework' ),
            'singular_name'         => __( 'Video', 'site5framework' ),
            'add_new'               => __( 'Add New Video Item', 'site5framework' ),
            'add_new_item'          => __( 'Add New Video Item', 'site5framework' ),
            'edit_item'             => __( 'Edit Video Item', 'site5framework' ),
            'new_item'              => __( 'Add New Video Item', 'site5framework' ),
            'view_item'             => __( 'View Video', 'site5framework' ),
            'search_items'          => __( 'Search Video', 'site5framework' ),
            'not_found'             => __( 'No video items found', 'site5framework' ),
            'not_found_in_trash'    => __( 'No video items found in trash', 'site5framework' )
        ),
        'public'                => true,
        'publicly_queryable'    => true,
        'show_ui'               => true,
        'query_var'             => true,
        'permalink_epmask'      => true,
        'menu_position'         => 5,
        'show_in_menu'          => true,
        'supports' 				=> array( 'title', 'page-attributes' ),
        'rewrite'               => array( 'slug' => 'video/details', 'with_front' => false ),
        'has_archive'           => true
    )
);


//  Add Columns to Audio Edit Screen

function video_edit_columns($audio_columns){
    $video_columns = array(
        "cb" 				=> "<input type=\"checkbox\" />",
        "title" 			=> __('Title', 'site5framework'),
        "portfolio-tags" 	=> __('Tags', 'site5framework'),
        "author" 			=> __('Author', 'site5framework'),
        "comments" 			=> __('Comments', 'site5framework'),
        "date" 				=> __('Date', 'site5framework'),
    );
    $video_columns['comments'] = '<div class="vers"><img alt="Comments" src="' . esc_url( admin_url( 'images/comment-grey-bubble.png' ) ) . '" /></div>';
    return $video_columns;
}

// GET AUDIO IMAGE
function wpv_get_featured_image($post_ID) {
    $post_thumbnail_id = get_image_id_by_link ( get_post_meta($post_ID, 'snbp_pitemlink', true) );
    if ($post_thumbnail_id) {
        $post_thumbnail_img = wp_get_attachment_image_src($post_thumbnail_id, 'small');
        return $post_thumbnail_img[0];
    }
}


function wpv_video_columns_head($defaults) {
    $defaults['featured_image'] = 'Image';
    return $defaults;
}

// SHOW THE FEATURED IMAGE
function wpv_video_columns_content ( $column, $post_id ) {

    if ( $column == 'featured_image') {
        $post_video_image = wpv_get_featured_image($post_id);
        if ($post_video_image) {
            echo '<img src="' . $post_video_image . '" />';
        }
    }
}

// ADDS EXTRA INFO TO ADMIN MENU FOR AUDIO POST ALBUMS
add_filter("manage_edit-video_columns", "wpv_video_columns_head");
add_action("manage_video_posts_custom_column", "wpv_video_columns_content", 10, 2 );
?>
<?php

/*********************************************************************************************

Registers Custom Event Post Type

 *********************************************************************************************/
$labels = array(
    'name'                          => __( 'Event Types', 'site5framework' ),
    'singular_name'                 => __( 'Event Type', 'site5framework' ),
    'search_items'                  => __( 'Search Event Types', 'site5framework' ),
    'popular_items'                 => __( 'Popular Event Types', 'site5framework' ),
    'all_items'                     => __( 'All Event Types', 'site5framework' ),
    'parent_item'                   => __( 'Parent Event Type', 'site5framework' ),
    'edit_item'                     => __( 'Edit Event Type', 'site5framework' ),
    'update_item'                   => __( 'Update Event Type', 'site5framework' ),
    'add_new_item'                  => __( 'Add New Event Type', 'site5framework' ),
    'new_item_name'                 => __( 'New Event Type', 'site5framework' ),
    'separate_items_with_commas'    => __( 'Separate Event Types with commas', 'site5framework' ),
    'add_or_remove_items'           => __( 'Add or remove Event Types', 'site5framework' ),'',
    'choose_from_most_used'         => __( 'Choose from most used Event Types', 'site5framework' )
);

$args = array(
    'label'                         => __( 'Event Types', 'site5framework' ),
    'labels'                        => $labels,
    'public'                        => true,
    'hierarchical'                  => true,
    'show_ui'                       => true,
    'show_in_nav_menus'             => true,
    'args'                          => array( 'orderby' => 'term_order' ),
    'rewrite'                       => array( 'slug' => 'event/event_types', 'with_front' => false ),
    'query_var'                     => true
);

register_taxonomy( 'event_types', 'event', $args );


register_post_type( 'event',
    array(
        'labels'                => array(
            'name'                  => __( 'Events', 'site5framework' ),
            'singular_name'         => __( 'Event', 'site5framework' ),
            'add_new'               => __( 'Add New Event Item', 'site5framework' ),
            'add_new_item'          => __( 'Add New Event Item', 'site5framework' ),
            'edit_item'             => __( 'Edit Event Item', 'site5framework' ),
            'new_item'              => __( 'Add New Event Item', 'site5framework' ),
            'view_item'             => __( 'View Event', 'site5framework' ),
            'search_items'          => __( 'Search Event', 'site5framework' ),
            'not_found'             => __( 'No video items found', 'site5framework' ),
            'not_found_in_trash'    => __( 'No video items found in trash', 'site5framework' )
        ),
        'public'                => true,
        'publicly_queryable'    => true,
        'show_ui'               => true,
        'query_var'             => true,
        'permalink_epmask'      => true,
        'menu_position'         => 5,
        'show_in_menu'          => true,
        'supports' 				=> array( 'title','editor', 'comments', 'page-attributes' ),
        'rewrite'               => array( 'slug' => 'event/details', 'with_front' => false ),
        'has_archive'           => true
    )
);


//  Add Columns to Audio Edit Screen

function event_edit_columns($audio_columns){
    $video_columns = array(
        "cb" 				=> "<input type=\"checkbox\" />",
        "title" 			=> __('Title', 'site5framework'),
        "portfolio-tags" 	=> __('Tags', 'site5framework'),
        "author" 			=> __('Author', 'site5framework'),
        "comments" 			=> __('Comments', 'site5framework'),
        "date" 				=> __('Date', 'site5framework'),
    );
    $event_columns['comments'] = '<div class="vers"><img alt="Comments" src="' . esc_url( admin_url( 'images/comment-grey-bubble.png' ) ) . '" /></div>';
    return $event_columns;
}

// GET AUDIO IMAGE
function wpe_get_featured_image($post_ID) {
    $post_thumbnail_id = get_image_id_by_link ( get_post_meta($post_ID, 'snbp_pitemlink', true) );
    if ($post_thumbnail_id) {
        $post_thumbnail_img = wp_get_attachment_image_src($post_thumbnail_id, 'small');
        return $post_thumbnail_img[0];
    }
}


function wpe_event_columns_head($defaults) {
    $defaults['featured_image'] = 'Image';
    return $defaults;
}

// SHOW THE FEATURED IMAGE
function wpe_event_columns_content ( $column, $post_id ) {

    if ( $column == 'featured_image') {
        $post_event_image = wpe_get_featured_image($post_id);
        if ($post_event_image) {
            echo '<img src="' . $post_event_image . '" />';
        }
    }
}

// ADDS EXTRA INFO TO ADMIN MENU FOR AUDIO POST ALBUMS
add_filter("manage_edit-event_columns", "wpe_event_columns_head");
add_action("manage_event_posts_custom_column", "wpe_event_columns_content", 10, 2 );
?>
<?php

/*********************************************************************************************

Registers Custom Slider Post Type

*********************************************************************************************/
function wpt_slider_posttype() {

$labels = array(
    'name' 					=> __( 'Slides', 'site5framework' ),
    'singular_name' 		=> __( 'Slide Item', 'site5framework' ),
    'add_new' 				=> __( 'Add New Item', 'site5framework' ),
    'add_new_item' 			=> __( 'Add New Slide Item', 'site5framework' ),
    'edit_item' 			=> __( 'Edit Slide Item', 'site5framework' ),
    'new_item' 				=> __( 'Add New Slide Item', 'site5framework' ),
    'view_item'				=> __( 'View Item', 'site5framework' ),
    'search_items' 			=> __( 'Search Slide', 'site5framework' ),
    'not_found' 			=> __( 'No slide items found', 'site5framework' ),
    'not_found_in_trash' 	=> __( 'No slide items found in trash', 'site5framework' )
);

$args = array(
    'labels' 				=> $labels,
    'public' 				=> true,
	'publicly_queryable'    => true,
	'show_ui'               => true,
	'query_var'             => true,
	'permalink_epmask'      => true,
    'supports' 				=> array( 'title','page-attributes' ),
	'rewrite'               => array( 'slug' => 'featured', 'with_front' => false ),
    'menu_position' 		=> 5,
	'show_in_menu'          => true,
    'has_archive' 			=> true
	
);

register_post_type( 'featured', $args);
}

add_action( 'init', 'wpt_slider_posttype' );



$labels = array(
    'name'                          => __( 'Sliders', 'site5framework' ),
    'singular_name'                 => __( 'Slider', 'site5framework' ),
    'search_items'                  => __( 'Search Sliders', 'site5framework' ),
    'popular_items'                 => __( 'Popular Sliders', 'site5framework' ),
    'all_items'                     => __( 'All Sliders', 'site5framework' ),
    'parent_item'                   => __( 'Parent Slider', 'site5framework' ),
    'edit_item'                     => __( 'Edit Slider', 'site5framework' ),
    'update_item'                   => __( 'Update Slider', 'site5framework' ),
    'add_new_item'                  => __( 'Add New Slider', 'site5framework' ),
    'new_item_name'                 => __( 'New Slider', 'site5framework' ),
    'separate_items_with_commas'    => __( 'Separate Sliders with commas', 'site5framework' ),
    'add_or_remove_items'           => __( 'Add or remove Sliders', 'site5framework' ),'',
    'choose_from_most_used'         => __( 'Choose from most used Sliders', 'site5framework' )
    );

$args = array(
    'label'                         => __( 'Sliders', 'site5framework' ),
    'labels'                        => $labels,
    'public'                        => true,
    'hierarchical'                  => true,
    'show_ui'                       => true,
    'show_in_nav_menus'             => true,
    'args'                          => array( 'orderby' => 'term_order' ),
    'rewrite'                       => array( 'slug' => 'sliders', 'with_front' => false ),
    'query_var'                     => true
);

register_taxonomy( 'sliders', 'featured', $args );




// Styling for the custom post type icon

add_action( 'admin_head', 'wpt_slider_icons' );

function wpt_slider_icons() {
    ?>
    <style type="text/css" media="screen">
        #menu-posts-slider .wp-menu-image {
            background: url(<?php echo get_template_directory_uri(); ?>/admin/images/slider-icon.png) no-repeat 6px 6px !important;
        }
		#menu-posts-slider:hover .wp-menu-image, #menu-posts-slider.wp-has-current-submenu .wp-menu-image {
            background-position:6px -16px !important;
        }
		#icon-edit.icon32-posts-slider {background: url(<?php echo get_template_directory_uri(); ?>/admin/images/slider-32x32.png) no-repeat;}
    </style>
<?php }

?>
<?php
// GET PORTFOLIO IMAGE  
function wpt_get_slide_image($post_ID) {  
    $post_thumbnail_id = get_image_id_by_link ( get_post_meta($post_ID, 'snbf_slideimage_src', true) );
    if ($post_thumbnail_id) {  
        $post_thumbnail_img = wp_get_attachment_image_src($post_thumbnail_id, 'small');  
        return $post_thumbnail_img[0];  
    }  
}  


function wpt_slide_columns_head($defaults) {  
    $defaults['slide_caption'] = 'Slide Caption'; 
//    $defaults['slider'] = 'Slider';
    $defaults['slide_image'] = 'Slide Image'; 
    
    return $defaults;  
}  
  
// SHOW THE FEATURED IMAGE  
function wpt_slide_columns_content ( $column, $post_id ) {
    switch ( $column ) {
        case 'slide_image':
            $post_slide_image = wpt_get_slide_image($post_id);  
            if ($post_slide_image) {  
                echo '<img src="' . $post_slide_image . '" />';  
            }
            break;
//        case 'slider':
//            $terms = get_the_terms( $post_id , 'sliders' , '' , ',' , '' );
//            if ( count( $terms ) > 0 ) {
//                foreach ( $terms as $term ) {
//                    echo  $term->name . ' ';
//                }
//            } else {
//                echo 'Unable to get slider(s)';
//            }
//            break;
        case 'slide_caption':
            echo get_post_meta($post_id, 'snbf_fitemcaption', true);
            break;
    }  
} 
 
// ADDS EXTRA INFO TO ADMIN MENU FOR PORTFOLIO POST TYPE
add_filter("manage_edit-featured_columns", "wpt_slide_columns_head");
add_action("manage_featured_posts_custom_column", "wpt_slide_columns_content", 10, 2 );
?>