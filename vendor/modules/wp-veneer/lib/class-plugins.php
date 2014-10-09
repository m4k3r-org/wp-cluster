<?php
/**
 * Holds our veneer specific functions for plugins, also handles active plugin
 * coordination
 *
 * @author Reid Williams
 * @class UsabilityDynamics\Veneer\Plugins
 */
namespace UsabilityDynamics\Veneer {
  use \UsabilityDynamics\Utility;

  if( !class_exists( 'UsabilityDynamics\Veneer\Plugins' ) ){
    class Plugins{

      /**
       * We're not going to do much here
       *
       * @param bool $do_stuff Whether we should actually do initialization( needed for 'init' )
       * @returns Plugins $this
       */
      function __construct( $do_stuff = true ){
        if( !( is_bool( $do_stuff ) && $do_stuff ) ){
          return;
        }
        return $this;
      }

      /**
       * This function lets us chain methods without having to instantiate first, YOU MUST COPY THIS TO ALL SUB CLASSES
       */
      static public function init(){
        return new self( __DIR__, false );
      }

      /**
       * Install a Specific Plugin.
       *
       * @param mixed $args The arguments being passed
       * @arg string $name The name of the plugin
       * @arg mixed $version The version to install (defaults to none)
       * @return mixed Based on utility install plugin
       */
      public function installPlugin( $args ){
        if( !current_user_can( 'install_plugins' ) || !current_user_can( 'activate_plugins' ) ) {
          return false;
        }
        /** Setup our args */
        $args = Utility::parse_args( $args, array(
          'name' => '',
          'version' => ''
        ) );
        /** Return the attempt to install it */
        return Utility::install_plugin( $args->name );
      }

    }
  }

}