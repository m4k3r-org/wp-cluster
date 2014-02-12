<?php
/**
 * Utility Customizer.
 *
 * @author team@UD
 * @version 0.2.4
 * @namespace UsabilityDynamics
 * @module Disco
 * @author potanin@UD
 */
namespace UsabilityDynamics\Disco {

  if( !class_exists( '\UsabilityDynamics\Disco\Search' ) ) {

    /**
     * Utility Class
     *
     * @class Utility
     * @author potanin@UD
     */
    class Search {

      /**
       *
       */
      public function __construct() {
        add_action( 'admin_menu', array( __CLASS__, 'add_pages' ) );
      }

      /**
       *
       */
      static public function add_pages() {
        add_menu_page( __( 'Manage Search', DOMAIN_CURRENT_SITE ), __( 'Manage Search', DOMAIN_CURRENT_SITE ), 'manage_options', 'wp-disco-manage-search', array( __CLASS__, 'manage_search' ), '', 91 );
        add_submenu_page( 'wp-disco-manage-search', __( 'Manage Search / Server', DOMAIN_CURRENT_SITE ), __( 'Server', DOMAIN_CURRENT_SITE ), 'manage_options', 'wp-disco-manage-search-server', array( __CLASS__, 'manage_search_server' ) );
        add_submenu_page( 'wp-disco-manage-search', __( 'Manage Search / Mapping', DOMAIN_CURRENT_SITE ), __( 'Mapping', DOMAIN_CURRENT_SITE ), 'manage_options', 'wp-disco-manage-search-mapping', array( __CLASS__, 'manage_search_mapping' ) );
        add_submenu_page( 'wp-disco-manage-search', __( 'Manage Search / Index', DOMAIN_CURRENT_SITE ), __( 'Index', DOMAIN_CURRENT_SITE ), 'manage_options', 'wp-disco-manage-search-index', array( __CLASS__, 'manage_search_index' ) );
      }

      /**
       *
       */
      static public function manage_search() {
        require_once TEMPLATEPATH.'/templates/admin/manage_search.php';
      }

      /**
       *
       */
      static public function manage_search_server() {

      }

      /**
       *
       */
      static public function manage_search_mapping() {

      }

      /**
       * 
       */
      static public function manage_search_index() {

      }

    }

  }

}