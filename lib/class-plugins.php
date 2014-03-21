<?php
/**
 * Our custom handler for plugins
 *
 * @author Reid Williams
 * @class UsabilityDynamics\Cluster\Plugins
 */
namespace UsabilityDynamics\Cluster {
  if( !class_exists( 'UsabilityDynamics\Cluster\Plugins' ) ){
    class Plugins{

      /**
       * Holds our possible module directories
       */
      public $plugin_directories = array();

      /**
       * Holds a list of all the plugins we have manually loaded
       */
      public $plugins_loaded = array();

      /**
       * This filter handles the array of network wide active plugins
       */
      function _filter_site_option_active_sitewide_plugins( $value ){
        return $value;
      }

      /**
       * This filter handles the array of of ALL plugins that are network wide and site-specific.
       * (not sure how works w/ multiple sites
       */
      function _filter_option_active_plugins( $value ){
        return $value;
      }

      function _filter_all_plugins( $plugins ) {
        // hide some plugins from plugin list
        //unset( $plugins[ 'wp-admin-column-view-master/admin-column-view.php' ] );
        //unset( $plugins[ 'akismet/akismet.php' ] );
        return $plugins;
      }

      function _filter_plugin_action_links( $actions, $plugin_file, $plugin_data, $context ) {
        $actions[ 'test' ] = 'Certified';
        return $actions;
      }

      function _filter_network_admin_plugin_action_links( $actions, $plugin_file, $plugin_data, $context ) {
        $actions[ 'test' ] = 'Certified';
        return $actions;
      }

      /**
       * This function activates our must use plugins
       */
      function _activate_must_use_plugins(){
        global $wp_cluster;
        if( isset( $wp_cluster->vertical_config[ 'plugins' ][ 'mustuse' ] ) && is_array( $wp_cluster->vertical_config[ 'plugins' ][ 'mustuse' ] ) ){
          /** Ok, so we're going to first go through the vertical, and bring in the MU plugins */
          foreach( $wp_cluster->vertical_config[ 'plugins' ][ 'mustuse' ] as $plugin ){
            list( $name, $version ) = explode( '/', $plugin );
            /** Make sure we haven't already loaded the plugin */
            if( isset( $this->plugins_loaded[ $name ] ) ){
              continue;
            }
            /** Ok, now go through each of the directories and see if we need to load them */
            foreach( $this->plugin_directories as $directory ){
              $file = WP_BASE_DIR . '/' . $directory . '/' . $name . '/' . $version . '/' . $name . '.php';
              if( is_file( $file ) ){
                /** Bring it in */
                include_once( $file );
                /** Add it to our loaded plugins */
                $this->plugins_loaded[ $name ] = $file;
              }
            }
          }
        }
      }

      /**
       * Adds a module directory to the local array
       *
       * @param string $directory The directory we want to add
       */
      function register_plugin_directory( $directory ){
        if( !in_array( $directory, $this->plugin_directories ) ){
          $this->plugin_directories[] = $directory;
        }
      }

      /**
       * On init, we're just going to setup and include all our config files
       * @param bool $do_stuff Whether we should actually do initialization( needed for 'init' )
       */
      function __construct( $do_stuff = true ){
        if( !( is_bool( $do_stuff ) && $do_stuff ) ){
          return;
        }
        /** Our custom functionality to add modules to the cluster */
        $this->register_plugin_directory( 'modules' );
        /** Ok, now we're going to try to activate any of our must use plugins */
        $this->_activate_must_use_plugins();
        /** Add our filters */
        /** add_filter( 'site_option_active_sitewide_plugins', array( $this, '_filter_site_option_active_sitewide_plugins' ) );
        add_filter( 'option_active_plugins', array( $this, '_filter_option_active_plugins' ) );
        add_filter( 'all_plugins', array( $this, '_filter_all_plugins' ) );
        add_filter( 'plugin_action_links', array( $this, '_filter_plugin_action_links' ), 10, 4 );
        add_filter( 'network_admin_plugin_action_links', array( $this, '_filter_network_admin_plugin_action_links' ), 10, 4 ); */
      }

      /**
       * This function lets us chain methods without having to instantiate first, YOU MUST COPY THIS TO ALL SUB CLASSES
       */
      static public function init(){
        return new self( false );
      }

    }
  }
}