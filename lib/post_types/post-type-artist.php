<?php
/**
 * Inits Custom Post Type 'Artist'
 *
 * @author Usability Dynamics, Inc. <info@usabilitydynamics.com>
 * @package Festival
 */
namespace UsabilityDynamics\Festival {

  if( !class_exists( 'UsabilityDynamics\Festival\Post_Type_Artist' ) ) {

    class Post_Type_Artist extends UsabilityDynamics\Theme\Post_Type {
    
      public function __construct() {
        global $festival;
        
        $this->args = array(
          'labels' => array(
            'name' => __( 'Artists', $festival->text_domain ),
            'all_items' => __( 'All Artists', $festival->text_domain ),
            'singular_name' => __( 'Artist', $festival->text_domain ),
            'add_new' => __( 'Add Artist', $festival->text_domain ),
            'add_new_item' => __( 'Add New Artist', $festival->text_domain ),
            'edit_item' => __( 'Edit Artists', $festival->text_domain ),
            'new_item' => __( 'New Artist', $festival->text_domain ),
            'view_item' => __( 'View Artist', $festival->text_domain ),
            'search_items' => __( 'Search Artist', $festival->text_domain ),
            'not_found' => __( 'No Artists found', $festival->text_domain ),
            'not_found_in_trash' => __( 'No Artists found in Trash', $festival->text_domain ),
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



