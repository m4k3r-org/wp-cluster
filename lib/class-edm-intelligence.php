<?php
/**
 *
 * @author Andy Potanin <andy.potanin@usabilitydynamics.com>
 */
namespace wpCloud\Vertical\EDM {

  class Intelligence {

    /**
     * Admin Page Scripts.
     *
     */
    function admin_scripts() {
      wp_enqueue_script( 'udx-requires', 'http://cdn.udx.io/udx.requires.js' );
      wp_enqueue_script( 'intelligence-app',  plugins_url( 'static/scripts/intelligence.js', __FILE__ ), array( 'udx-requires' ) );
      wp_enqueue_style( 'intelligence-app',   plugins_url( 'static/styles/intelligence.css', __FILE__ ) );
    }

    /**
     * Add Contextual Help
     *
     * Include "Common Searches" in help.
     *
     */
    function screen_options() {
      add_meta_box( 'showFilter.audience', 'Audience Filter', function() {} );
      add_meta_box( 'showFilter.location', 'Location Filter', function() {} );
      add_meta_box( 'showFilter.metrics', 'Metrics Filter', function() {} );
      add_meta_box( 'showFilter.interests', 'Interests Filter', function() {} );
      add_meta_box( 'showFilter', 'Filter Sidebar ', function() {} );
      add_meta_box( 'showDebug', 'Show Debugger', function() {} );
      //add_meta_box( 'showAudience', 'Audience Filters', function() {} );
    }

    /**
     * Render Page.
     *
     */
    function admin_page() {
      include_once( 'static/templates/profiles.php' );
    }

    /**
     * Adds Menu
     *
     * Replaces Jetpack, because fuck'em.
     *
     */
    function admin_menu() {
      $_page = add_menu_page( 'Intelligence', 'Intelligence', 'manage_options', 'intelligence', 'wpCloud\Modules\Intelligence::admin_page', '', 3 );
      add_action( "load-$_page", 'wpCloud\Modules\Intelligence::screen_options' );
      add_action( "admin_print_scripts-$_page", 'wpCloud\Modules\Intelligence::admin_scripts' );
    }

  }

}