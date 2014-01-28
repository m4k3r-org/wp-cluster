<?php
/**
 * Inits Custom Post Type 'Social'
 *
 * @author Usability Dynamics, Inc. <info@usabilitydynamics.com>
 * @package Festival
 */
namespace UsabilityDynamics\Festival {

  if( !class_exists( 'UsabilityDynamics\Festival\Post_Type_Social' ) ) {

    class Post_Type_Social extends UsabilityDynamics\Theme\Post_Type {
    
      public $taxonomies = array();
    
      public function __construct() {
        global $festival;
        
        $this->args = array(
          'labels' => array(
            'name' => __( 'Social Streams', $festival->text_domain ),
            'all_items' => __( 'All Socials', $festival->text_domain ),
            'singular_name' => __( 'Social', $festival->text_domain ),
            'add_new' => __( 'Add Social', $festival->text_domain ),
            'add_new_item' => __( 'Add New Social', $festival->text_domain ),
            'edit_item' => __( 'Edit Social', $festival->text_domain ),
            'new_item' => __( 'New Social', $festival->text_domain ),
            'view_item' => __( 'View Social', $festival->text_domain ),
            'search_items' => __( 'Search Socials', $festival->text_domain ),
            'not_found' => __( 'No Records found', $festival->text_domain ),
            'not_found_in_trash' => __( 'No Records found in Trash', $festival->text_domain ),
            'parent_item_colon' => ''
          ),
          'public' => true,
          'exclude_from_search' => true,
          'show_ui' => true,
          'supports' => array( 'title', 'thumbnail' ),
        );
        
        $this->meta = array(
          array( 
            'name' => __( 'Network', $festival->text_domain ),
            'id' => 'network',
            'type' => 'select_advanced',
            'options' => array(
              'facebook' => __( 'Facebook', $festival->text_domain ),
              'instagram' => __( 'Instagram', $festival->text_domain ),
              'twitter' => __( 'Twitter', $festival->text_domain ),
            ),
            'multiple'    => false,
            'placeholder' => __( 'Select an Item', $festival->text_domain ),
            'metabox' => 'network_specific'
          )
        );
        
        parent::__construct(  );
      }
      
    }

  }

}



