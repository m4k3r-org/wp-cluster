<?php
/**
 * Inits Custom Post Type 'Artist'
 *
 * @author Usability Dynamics, Inc. <info@usabilitydynamics.com>
 * @package Festival
 */
namespace UsabilityDynamics\Festival {

  if( !class_exists( 'UsabilityDynamics\Festival\Post_Type_Artist' ) ) {

    class Post_Type_Artist extends \UsabilityDynamics\Post_Type {
    
      public function __construct() {
        global $festival;
        
        $this->args = array(
          'labels' => array(
            'name' => __( 'Artists', wp_festival( 'domain' ) ),
            'all_items' => __( 'All Artists', wp_festival( 'domain' ) ),
            'singular_name' => __( 'Artist', wp_festival( 'domain' ) ),
            'add_new' => __( 'Add Artist', wp_festival( 'domain' ) ),
            'add_new_item' => __( 'Add New Artist', wp_festival( 'domain' ) ),
            'edit_item' => __( 'Edit Artists', wp_festival( 'domain' ) ),
            'new_item' => __( 'New Artist', wp_festival( 'domain' ) ),
            'view_item' => __( 'View Artist', wp_festival( 'domain' ) ),
            'search_items' => __( 'Search Artist', wp_festival( 'domain' ) ),
            'not_found' => __( 'No Artists found', wp_festival( 'domain' ) ),
            'not_found_in_trash' => __( 'No Artists found in Trash', wp_festival( 'domain' ) ),
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



