<?php
/**
 * Inits Custom Post Type
 *
 * @author Usability Dynamics, Inc. <info@usabilitydynamics.com>
 * @package Theme
 * @author peshkov@UD
 */
namespace UsabilityDynamics\Theme {

  if( !class_exists( 'UsabilityDynamics\Theme\Post_Type' ) ) {

    class Post_Type {
    
      /**
       * Errors
       *
       */
      public $errors = null;
      
      /**
       * Use specific prefix in post_type slug
       *
       */
      public $prefix = '';
      
      /**
       * post_type slug
       *
       */
      public $object_type = null;
      
      /**
       * Standard post_type arguments
       * used by register_post_type();
       *
       */
      public $args = array();
      
      /**
       * Specific Meta data
       * used by \UsabilityDynamics\Meta_Box
       *
       * Example:
       * $this->meta = array(
       *   array( 
       *     'name' => __( 'Network' ),
       *     'id' => 'network',
       *     'type' => 'select_advanced',
       *     'options' => array(
       *       'facebook' => __( 'Facebook' ),
       *       'instagram' => __( 'Instagram' ),
       *       'twitter' => __( 'Twitter' ),
       *     ),
       *     'metabox' => 'general'
       *   )
       * );
       */
      public $meta = array();
      
      /**
       * Must be overwitten or extended in extended class
       * Just add taxonomy slugs to array 
       * and it will generate and register taxonomies automatically.
       *
       * Example:
       * $this->taxonomies( 'post_tag', 'category', 'genre' );
       */
      public $taxonomies = array(
        'post_tag',
        'category',
      );
    
      /**
       * Constructor
       *
       */
      public function __construct() {
        
        // Generate post_type based on class name
        preg_match( '#Post_Type_(.*)$#', ( get_class( $this ) ), $matches );
        if( empty( $matches[1] ) ) {
          $this->set_errors( __( 'Post Type object_type can not be matched from class name.' ) );
          return null;
        }
        $this->object_type = $this->prefix . ( strtolower( $matches[1] ) );
        
        // Register current post_type
        $this->register_post_type();

        // Loop taxonomies and register them
        if( is_array( $this->taxonomies ) ) {
          foreach( $this->taxonomies as $taxonomy ) {
            $this->register_taxonomy( $taxonomy );
          }
        }
        
        // Adds metaboxes
        $this->add_meta();
        
      }
      
      /**
       *
       *
       */
      private function add_meta() {
        
        if( !is_array( $this->meta ) ) {
          return false;
        }
        
        $metaboxes = array();
        foreach( $this->meta as $meta ) {
          if( !is_array( $meta ) ) {
            continue;
          }
          $meta = wp_parse_args( $meta, array(
            'name' => __( 'No Name' ),
            'desc'  => '',
            'type' => 'text',
            'id' => ( md5( serialize( $meta ) ) ),
            'metabox' => 'general',
            'class' => '',
          ) );
          if( !isset( $metaboxes[ $meta[ 'metabox' ] ] ) ) {
            $metaboxes[ $meta[ 'metabox' ] ] = array(
              'id' => $meta[ 'metabox' ],
              'title' => \UsabilityDynamics\Utility::de_slug( $meta[ 'metabox' ] ),
              'pages' => array( $this->object_type ),
              'context'  => 'normal',
              'priority' => 'high',
              'autosave' => false,
              'fields' => array(),
            );
          }
          $metaboxes[ $meta[ 'metabox' ] ][ 'fields' ][] = $meta;
        }
        
        foreach( $metaboxes as $metabox  ) {
          new \RW_Meta_Box( $metabox );
        }
        
      }
      
      /**
       * Registers post_type
       *
       */
      private function register_post_type() {
        register_post_type( $this->object_type, $this->args );
      }
      
      /**
       * Registers taxonomy if it doesn't exist
       * And adds taxonomy to current post_type
       *
       */
      private function register_taxonomy( $taxonomy ) {
        // Register taxonomy if it doesn't exist
        if( !taxonomy_exists( $taxonomy ) ) {
          $label = \UsabilityDynamics\Utility::de_slug( $taxonomy );
          $settings = array(
            'label' => $label,
          );
          register_taxonomy( $taxonomy, null, $settings );
        }
        // Adds taxonomy to current post_type
        return register_taxonomy_for_object_type( $taxonomy, $this->object_type );
      }
      
      /**
       * Set errors ( add message to $this->errors )
       *
       */
      public function set_errors( $mes ) {
        if( !is_array( $this->errors ) ) {
          $this->errors = array();
        }
        $this->errors[] = $mes;
      }
      
    }

  }

}



