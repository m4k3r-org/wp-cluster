<?php
/**
 * Inits Custom Post Type 'Artist'
 *
 * @author Usability Dynamics, Inc. <info@usabilitydynamics.com>
 * @package Theme
 * @since 2.0.0
 */
namespace UsabilityDynamics\Theme {

  if( !class_exists( 'UsabilityDynamics\Theme\Post_Type_Artist' ) ) {

    class Post_Type_Artist extends Post_Type {
    
      public function __construct() {
        
        $this->args = array(
          'labels' => array(
            'name' => __( 'Artists' ),
            'all_items' => __( 'All Artists' ),
            'singular_name' => __( 'Artist' ),
            'add_new' => __( 'Add Artist' ),
            'add_new_item' => __( 'Add New Artist' ),
            'edit_item' => __( 'Edit Artists' ),
            'new_item' => __( 'New Artist' ),
            'view_item' => __( 'View Artist' ),
            'search_items' => __( 'Search Artist' ),
            'not_found' => __( 'No Artists found' ),
            'not_found_in_trash' => __( 'No Artists found in Trash' ),
            'parent_item_colon' => ''
          ),
          'public' => true,
          'exclude_from_search' => false,
          'show_ui' => true,
          'supports' => array( 'title', 'editor', 'thumbnail', 'comments', 'excerpt', 'author' ),
        );
        
        parent::__construct(  );
      }
      
    }

  }

}



