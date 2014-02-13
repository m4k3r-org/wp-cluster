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

      static $errors = array();
      static $success = array();

      /**
       *
       */
      public function __construct() {
        add_action( 'admin_menu', array( __CLASS__, 'add_pages' ) );
      }

      /**
       *
       */
      public function action_messages() {
        if ( !empty( self::$errors ) ) {
          foreach( self::$errors as $error ) {
            ?>
            <div class="error settings-error" id="setting-error-settings_updated">
              <p><strong><?php echo $error; ?></strong></p>
            </div>
            <?php
          }
        }

        if ( !empty( self::$success ) ) {
          foreach( self::$success as $success ) {
            ?>
            <div class="updated settings-error" id="setting-error-settings_updated">
              <p><strong><?php echo $success; ?></strong></p>
            </div>
            <?php 
          }
        }
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

        if (!empty($_POST) && !empty($_POST['configuration'])) {
          foreach ($_POST['configuration'] as $option_key => $option_value) {
            wp_disco()->set($option_key, $option_value);
          }

          $client = new \UsabilityDynamics\Veneer\Search(
            array(
              'url' => wp_disco()->get('search.server')
            )
          );

          try {
            $server_status = $client->getStatus()->getResponse()->getData();

            if ( !empty( $server_status['ok'] ) && $server_status['ok'] === true ) {
              $_index = trim( wp_disco()->get('search.index') );

              if ( !empty( $_index ) ) {
                if ( $client->getIndex( $_index )->exists() ) {

                  if ( wp_disco()->settings->commit() ) {
                    self::$success[] = __('Server settings has been validated and saved.', DOMAIN_CURRENT_SITE);
                  }

                } else {
                  self::$errors['server_index'] = sprintf(__('Index "%s" does not exist on ElasticSearch server with address %s', DOMAIN_CURRENT_SITE), wp_disco()->get('search.index'), wp_disco()->get('search.server'));
                }
              } else {
                self::$errors['server_index'] = __('Please specify Search Index', DOMAIN_CURRENT_SITE);
              }

            } else {
              self::$errors['server_address'] = __('Search server returned bad Status. Check ElasticSearch installation on your server or change address.', DOMAIN_CURRENT_SITE);
            }

          } catch ( \Elastica\Exception\ClientException $e ) {
            self::$errors['server_address'] = $e->getMessage();
          }

        }

        require_once TEMPLATEPATH.'/templates/admin/manage_search_server.php';
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