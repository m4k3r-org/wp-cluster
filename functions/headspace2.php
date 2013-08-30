<?php
/*
	Name: Headspace Plugin Extensions
	Description: Extra functionality for the Headspace plugin
	Author: Usability Dynamics, Inc.
	Version: 1.0
*/

add_action( 'add_meta_boxes', array('Flawless_Headspace', 'add_meta_boxes'));

class Flawless_Headspace {

  function add_meta_boxes() {
    global $flawless, $headspace2;
   
    if ( !function_exists( 'add_meta_box' ) || !is_object( $headspace2 ) ) {
      return;
    }
    
    foreach($flawless['post_types'] as $post_type => $post_type_data) {
      add_meta_box( 'headspacestuff', __('HeadSpace', 'headspace'), array( &$headspace2, 'metabox' ), $post_type, 'normal', 'high' );
    }    

  }


}