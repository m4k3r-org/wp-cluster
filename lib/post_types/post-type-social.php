<?php
/**
 * Inits Custom Post Type 'Social'
 *
 * @author Usability Dynamics, Inc. <info@usabilitydynamics.com>
 * @package Festival
 */
namespace UsabilityDynamics\Festival {

  if( !class_exists( 'UsabilityDynamics\Festival\Post_Type_Social' ) ) {

    class Post_Type_Social extends \UsabilityDynamics\Post_Type {
    
      public $taxonomies = array();
    
      public function __construct() {
        global $festival;
        
        $this->args = array(
          'labels' => array(
            'name' => __( 'Social Streams', wp_festival( 'domain' ) ),
            'all_items' => __( 'All Socials', wp_festival( 'domain' ) ),
            'singular_name' => __( 'Social', wp_festival( 'domain' ) ),
            'add_new' => __( 'Add Social', wp_festival( 'domain' ) ),
            'add_new_item' => __( 'Add New Social', wp_festival( 'domain' ) ),
            'edit_item' => __( 'Edit Social', wp_festival( 'domain' ) ),
            'new_item' => __( 'New Social', wp_festival( 'domain' ) ),
            'view_item' => __( 'View Social', wp_festival( 'domain' ) ),
            'search_items' => __( 'Search Socials', wp_festival( 'domain' ) ),
            'not_found' => __( 'No Records found', wp_festival( 'domain' ) ),
            'not_found_in_trash' => __( 'No Records found in Trash', wp_festival( 'domain' ) ),
            'parent_item_colon' => ''
          ),
          'public' => true,
          'exclude_from_search' => true,
          'show_ui' => true,
          'supports' => array( 'title', 'thumbnail' ),
        );
        
        $this->meta = array(
          array( 
            'name' => __( 'Network', wp_festival( 'domain' ) ),
            'id' => 'network',
            'type' => 'select_advanced',
            'options' => array(
              'facebook' => __( 'Facebook', wp_festival( 'domain' ) ),
              'instagram' => __( 'Instagram', wp_festival( 'domain' ) ),
              'twitter' => __( 'Twitter', wp_festival( 'domain' ) ),
            ),
            'multiple'    => false,
            'placeholder' => __( 'Select an Item', wp_festival( 'domain' ) ),
            'metabox' => 'network_specific'
          )
        );
        
        parent::__construct(  );
      }
      
    }

  }

}



