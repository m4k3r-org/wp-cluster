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
       * @var type
       */
      static $errors = array();

      /**
       *
       * @var type
       */
      static $success = array();

      /**
       *
       */
      public function __construct() {
        add_action( 'admin_menu', array( __CLASS__, 'add_pages' ) );
        add_action( 'wp_ajax_index_documents', array( __CLASS__, 'index_documents' ) );
      }

      public static function index_documents() {
        echo '<pre>';
        print_r( $_REQUEST );
        echo '</pre>';
        die();
      }

      /**
       *
       * @return \UsabilityDynamics\Veneer\Search
       */
      static public function get_client() {
        return new \UsabilityDynamics\Veneer\Search(
          array(
            'url' => wp_disco()->get('search.server')
          )
        );
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
        $search_server = wp_disco()->get('search.server');
        $search_index = wp_disco()->get('search.index');

        $client = self::get_client();

        try {

          if ( !$search_server || !$search_index ) {
            throw new \Exception( sprintf( __('Currently your ElasticSearch configuration is empty. Visit <a href="%s">Server</a> section to configure.', DOMAIN_CURRENT_SITE), admin_url('admin.php?page=wp-disco-manage-search-server')) );
          }

          $cluster_health = $client->getCluster()->getHealth()->getData();
          $cluster_info = $client->getStatus()->getServerStatus();
          $current_index = $client->getIndex( $search_index )->getStats()->getData();

        } catch ( \Elastica\Exception\ClientException $ex ) {
          self::$errors[] = $ex->getMessage();
        } catch ( \Exception $ex ) {
          self::$errors[] = $ex->getMessage();
        }

        require_once TEMPLATEPATH.'/templates/admin/manage_search.php';
      }

      /**
       *
       */
      static public function manage_search_server() {

        if ( !empty( $_POST ) && !empty( $_POST['configuration'] ) ) {

          $filtered = array_filter( $_POST['configuration'] );
          if ( empty( $filtered ) ) {
            wp_disco()->set('search.server', false);
            wp_disco()->set('search.index', false);
            wp_disco()->settings->commit();
          }

          foreach ($_POST['configuration'] as $option_key => $option_value) {
            wp_disco()->set($option_key, $option_value);
          }

          $client = self::get_client();

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

        if ( !empty( $_POST ) && !empty( $_POST['index_types'] ) ) {
          wp_disco()->set('search.index_types', $_POST['index_types']);
          if ( wp_disco()->settings->commit() ) {
            self::$success[] = __('Saved.', DOMAIN_CURRENT_SITE);
          }
        }

        if ( !empty( $_POST ) && !empty( $_POST['mapping'] ) ) {

          try {

            $mapping_decoded = array();

            if ( is_array( $_POST['mapping'] ) ) {
              foreach( $_POST['mapping'] as $type => $_mapping ) {
                if ( !$mapping_decoded[$type] = json_decode( preg_replace( "/\s+/", "", stripslashes( $_mapping ) ), true ) ) {
                  throw new \Exception( __( 'Could not validate mapping object.', DOMAIN_CURRENT_SITE ) );
                }
              }
            }

            $client = self::get_client();
            $mapping = new \Elastica\Type\Mapping();
            if ( is_array( $mapping_decoded ) && !empty( $mapping_decoded ) ) {
              foreach( $mapping_decoded as $_type => $_data ) {
                $mapping->setType( $client->getIndex( wp_disco()->get('search.index') )->getType($_type) );
                $mapping->setProperties( $_data['properties'] );
                if ( $mapping->send()->isOk() ) {
                  self::$success[] = $_type.': '.__( 'Mapping has been updated', DOMAIN_CURRENT_SITE );
                }
              }
            }

          } catch ( \Elastica\Exception\ClientException $ex ) {
            self::$errors[] = $_type.': '.$ex->getMessage();
          } catch ( \Elastica\Exception\InvalidException $ex ) {
            self::$errors[] = $_type.': '.$ex->getMessage();
          } catch ( \Exception $ex ) {
            self::$errors[] = $_type.': '.$ex->getMessage();
          }

        }

        $post_types = get_post_types(array(), 'objects');
        $active_types = wp_disco()->get('search.index_types');

        try {

          if ( !wp_disco()->get('search.index') ) {
            throw new \Exception(__( 'Configure search server first to manage mappings.', DOMAIN_CURRENT_SITE ));
          }

          $client = self::get_client();

          $_index_mapping = $client->getIndex( wp_disco()->get('search.index') )->getMapping();

          $mapping = array();
          foreach( $_index_mapping[ wp_disco()->get('search.index') ] as $type_key => $type_mapping ) {
            $mapping[$type_key] = json_encode( $type_mapping, JSON_PRETTY_PRINT );
          }

          wp_enqueue_script('jquery-ui-tabs');
          wp_enqueue_style('ui-tabs-theme', '//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css');

        } catch ( \Elastica\Exception\ClientException $ex ) {
          self::$errors[] = $ex->getMessage();
        } catch ( \Elastica\Exception\InvalidException $ex ) {
          self::$errors[] = $ex->getMessage();
        } catch ( \Exception $ex ) {
          self::$errors[] = $ex->getMessage();
        }

        require_once TEMPLATEPATH.'/templates/admin/manage_search_mapping.php';
      }

      /**
       *
       */
      static public function manage_search_index() {

        try {

          if ( !wp_disco()->get('search.index') ) {
            throw new \Exception(__( 'Configure search server first to manage indexing.', DOMAIN_CURRENT_SITE ));
          }

          //** TEST */
//          $client = self::get_client();
//          $_index = $client->getIndex( wp_disco()->get('search.index') );
//          $_type  = $_index->getType('photo');
//
//          // The Id of the document
//          $id = rand(1, 9999999);
//
//          // Create a document
//          $event = array(
//              'id'      => $id,
//              'user'    => array(
//                  'name'      => 'mewantcookie',
//                  'fullName'  => 'Cookie Monster'
//              ),
//              'msg'     => 'Me wish there were expression for cookies like there is for apples. "A cookie a day make the doctor diagnose you with diabetes" not catchy.',
//              'tstamp'  => time(),
//              'location'=> '41.12,-71.34',
//              'terms' => array(
//                  'f', 'g', 'e'
//              )
//          );
//          // First parameter is the id of document.
//          $eventDocument = new \Elastica\Document($id, $event);
//
//          echo '<pre>';
//          print_r( $_type->addDocument($eventDocument) );
//          echo '</pre>';
//
//          // Refresh Index
//          $_type->getIndex()->refresh();

          //** #TEST */

        } catch ( \Elastica\Exception\ClientException $ex ) {
          self::$errors[] = $ex->getMessage();
        } catch ( \Elastica\Exception\InvalidException $ex ) {
          self::$errors[] = $ex->getMessage();
        } catch ( \Exception $ex ) {
          self::$errors[] = $ex->getMessage();
        }

        require_once TEMPLATEPATH.'/templates/admin/manage_search_index.php';
      }

    }

  }

}