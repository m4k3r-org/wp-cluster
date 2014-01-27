<?php
/**
 * Inits Custom Post Type 'Social'
 *
 * @author Usability Dynamics, Inc. <info@usabilitydynamics.com>
 * @package Theme
 * @since 2.0.0
 */
namespace UsabilityDynamics\Theme {

  if( !class_exists( 'UsabilityDynamics\Theme\Post_Type_Social' ) ) {

    class Post_Type_Social extends Post_Type {
    
      public $taxonomies = array();
    
      public function __construct() {
        
        $this->args = array(
          'labels' => array(
            'name' => __( 'Social Streams' ),
            'all_items' => __( 'All Socials' ),
            'singular_name' => __( 'Social' ),
            'add_new' => __( 'Add Social' ),
            'add_new_item' => __( 'Add New Social' ),
            'edit_item' => __( 'Edit Social' ),
            'new_item' => __( 'New Social' ),
            'view_item' => __( 'View Social' ),
            'search_items' => __( 'Search Socials' ),
            'not_found' => __( 'No Records found' ),
            'not_found_in_trash' => __( 'No Records found in Trash' ),
            'parent_item_colon' => ''
          ),
          'public' => true,
          'exclude_from_search' => true,
          'show_ui' => true,
          'supports' => array( 'title', 'thumbnail' ),
        );
        
        $this->meta = array(
          array( 
            'name' => __( 'Network' ),
            'id' => 'network',
            'type' => 'select_advanced',
            'options' => array(
              'facebook' => __( 'Facebook' ),
              'instagram' => __( 'Instagram' ),
              'twitter' => __( 'Twitter' ),
            ),
            'multiple'    => false,
            'placeholder' => __( 'Select an Item', 'wpp' ),
            'metabox' => 'network_specific'
          )
        );
        
        parent::__construct(  );
      }
      
    }

  }

}



