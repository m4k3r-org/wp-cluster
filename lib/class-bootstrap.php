<?php

namespace UsabilityDynamics\Disco {

  if( !class_exists( 'UsabilityDynamics\Disco\Bootstrap' ) ) {

    /**
     * Disco Theme Bootstrap.
     *
     * @author Usability Dynamics
     */
    final class Bootstrap {

      public $theme;

      /**
       * WP-Disco Theme Constructor.
       *
       */
      public function __construct() {

        include_once( untrailingslashit( __DIR__ ) . '/legacy/ud_saas.php' );
        include_once( untrailingslashit( __DIR__ ) . '/legacy/ud_functions.php' );
        include_once( untrailingslashit( __DIR__ ) . '/legacy/ud_tests.php' );
        include_once( untrailingslashit( __DIR__ ) . '/legacy/backend-functions.php' );
        include_once( untrailingslashit( __DIR__ ) . '/legacy/business-card.php' );
        include_once( untrailingslashit( __DIR__ ) . '/legacy/class-flawless-utility.php' );
        include_once( untrailingslashit( __DIR__ ) . '/legacy/front-end-editor.php' );
        include_once( untrailingslashit( __DIR__ ) . '/legacy/login_module.php' );
        include_once( untrailingslashit( __DIR__ ) . '/legacy/theme_ui.php' );
        include_once( untrailingslashit( __DIR__ ) . '/legacy/shortcodes.php' );

        // Disco Libraries.
        include_once( untrailingslashit( __DIR__ ) . '/widgets.php' );
        include_once( untrailingslashit( __DIR__ ) . '/template.php' );

        if( !class_exists( 'UsabilityDynamics\Disco' ) ) {
          wp_die( '<h1>Fatal Error</h1><p>Disco Theme not found.</p>' );
        }

        $this->theme = new \UsabilityDynamics\Theme\Disco();

        add_action( 'flawless::init', array( $this, 'init' ) );
        add_action( 'flawless::theme_setup::after', array( $this->theme, 'setup' ) );
        add_action( 'template_redirect', array( $this, 'redirect' ) );
        add_action( 'admin_init', array( $this, 'admin' ) );

      }

      /**
       * Theme Setup.
       *
       * @author potanin@UD
       */
      public function setup() {
        $this->theme->setup();
      }

      /**
       * Primary Loader.
       * Scripts and styles are registered here so they overwriten Flawless scripts if needed.
       *
       * @author potanin@UD
       */
      public function init() {
        $this->theme->init();
      }

      /**
       * Force our custom template to load for Event post types
       *
       * @method redirect
       * @action template_redirect (10)
       * @author potanin@UD
       */
      public function redirect() {
        global $post, $flawless;

        // Modify our HTML for the mobile nav bar
        if( isset( $flawless[ 'mobile_navbar' ] ) ) {
          $flawless[ 'mobile_navbar' ][ 'html' ][ 'left' ] = hdp_share_button( true, true ) . $flawless[ 'mobile_navbar' ][ 'html' ][ 'left' ];
        }

        $this->theme->redirect();

      }

      /**
       * Return JSON post results Dynamic Filter requests
       *
       * @action admin_init (10)
       * @author potanin@UD
       */
      public function admin() {
        $this->theme->admin();
      }

    }

  }

}