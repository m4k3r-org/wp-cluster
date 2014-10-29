<?php
/**
 * Bootstrap
 *
 * @since 1.0.0
 */
namespace UsabilityDynamics\SEO {

  if( !class_exists( 'UsabilityDynamics\SEO\Bootstrap' ) ) {

    final class Bootstrap extends \UsabilityDynamics\WP\Bootstrap_Plugin {
      
      /**
       * Singleton Instance Reference.
       *
       * @protected
       * @static
       * @property $instance
       * @type UsabilityDynamics\SEO\Bootstrap object
       */
      protected static $instance = null;
      
      /**
       * Instantaite class.
       */
      public function init() {
        //** Init Settings */
        $this->settings = new \UsabilityDynamics\Settings( array(
          'key'  => $this->domain,
          'data' => array(
            'version' => $this->args[ 'version' ],
            'text_domain' => $this->domain,
          )
        ) );
        //** Run plugin on after_setup_theme hook */
        add_action( "after_setup_theme", array( $this, 'run' ) );
      }
      
      /**
       *
       */
      public function run() {
        //** Adds Social Twitter customizations */
        new Twitter();
        //** Adds Sitewide Custom Meta Functionality */
        new Custom_Meta();
      }
      
      /**
       * Plugin Activation
       *
       */
      public function activate() {}
      
      /**
       * Plugin Deactivation
       *
       */
      public function deactivate() {}

    }

  }

}
