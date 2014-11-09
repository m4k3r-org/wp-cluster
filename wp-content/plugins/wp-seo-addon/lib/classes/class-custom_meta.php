<?php
/**
 * Adds Custom Meta functionality
 *
 */
namespace UsabilityDynamics\SEO {

  if( !class_exists( '\UsabilityDynamics\SEO\Custom_Meta' ) ) {

    class Custom_Meta {
    
      /**
       * Add specific hooks
       */
      public function __construct() {
        
        add_filter( 'wpseo_submenu_pages', array( $this, 'wpseo_submenu_pages' ) );
        add_filter( 'wpseo_admin_pages', array( $this, 'wpseo_admin_pages' ) );
        add_action( 'admin_init', array( $this, 'admin_init' ) );
        
        add_action( 'wpseo_head', array( $this, 'wpseo_head' ), 999 );
        
      }
      
      public function admin_init() {
        if( !empty( $_POST[ 'wpseo_custom_meta' ] ) && current_user_can( 'manage_options' ) ) {
          update_option( 'wpseo_custom_meta', $_POST[ 'wpseo_custom_meta' ] );
          wp_redirect( 'admin.php?page=wpseo_custom_metas&updated=true' );
          die();
        }
      }
      
      /**
       *
       */
      public function wpseo_admin_pages( $admin_pages ) {
        array_push( $admin_pages, 'wpseo_custom_metas' );
        return $admin_pages;
      }
      
      /**
       * 
       */
      public function wpseo_submenu_pages( $submenus ) {
        
        $_submenu = array(
          'wpseo_dashboard',
          __( 'Yoast WordPress SEO: Custom SiteWide Metas', ud_get_wp_seo_addon( 'text_domain' ) ),
          __( 'Custom Metas', ud_get_wp_seo_addon( 'text_domain' ) ),
          'manage_options',
          'wpseo_custom_metas',
          array( $this, 'load_page' )
        );
        
        $_submenus = array();
        foreach( $submenus as $submenu ) {
          array_push( $_submenus, $submenu );
          if( $submenu[4] == 'wpseo_titles' ) {
            array_push( $_submenus, $_submenu );
          }
        }
        
        return $_submenus;
      }
      
      /**
       *
       */
      public function load_page() {
        $options = get_option( 'wpseo_custom_meta', array() );
        wp_enqueue_style( 'wpseo-addon-custom', ud_get_wp_seo_addon()->path( 'static/styles/custom.css', 'url' ), array(), ud_get_wp_seo_addon( 'version' ) );
        require_once( ud_get_wp_seo_addon()->path( '/static/views/custom_metas.php', 'dir' ) );
      }
      
      /**
       *
       */
      public function wpseo_head() {
        $metas = get_option( 'wpseo_custom_meta', array() );
        if( !empty( $metas ) && is_array( $metas ) ) {
          foreach( $metas as $meta ) {
            if( !empty( $meta[ 'name' ] ) && !empty( $meta[ 'content' ] ) ) {
              $this->output_metatag( $meta[ 'name' ], $meta[ 'type' ], $meta[ 'content' ] );
            }
          }
        }
      }
      
      /**
       * Output the metatag
       *
       * @param $name
       * @param $metatag_key
       * @param $value
       * @param $escaped
       */
      private function output_metatag( $name, $metatag_key, $value, $escaped = false ) {
        //** Escape the value if not escaped */
        if ( false === $escaped ) {
          $value = esc_attr( $value );
        }
        //** Output meta */
        echo '<meta ' . $metatag_key . '="' . $name . '" content="' . $value . '"/>' . "\n";
      }

    }

  }

}
