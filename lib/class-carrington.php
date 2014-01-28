<?php
/**
 * Carrington Builder
 *
 * @version 0.1.0
 * @author peshkov@UD
 * @namespace UsabilityDynamics
 */

namespace UsabilityDynamics\Festival {

  // Localize UsabilityDynamics classes
  use UsabilityDynamics\Utility;
  use UsabilityDynamics\Settings;
  use UsabilityDynamics\LayoutEngine;
  use UsabilityDynamics\UI;

  /**
   * Carrington Builder functionality
   *
   */
  final class Carrington {

    /**
     * Carrington Build Handler
     *
     */
    public function __construct() {

      define( 'CFCT_BUILD_TAXONOMY_LANDING', false );
      define( 'CFCT_BUILD_DEBUG_ERROR_LOG', false );
      
      add_filter( 'cfct-build-url', function ( $url ) {
        return str_replace( '\\', '/', $url );
      } );

      add_filter( 'init', function () {

      } );

      add_filter( 'cfct-modules-included', function ( $dirs ) {

      });

      add_filter( 'cfct-module-dirs', function ( $dirs ) {
        $dirs[ ] = __DIR__ . '/lib';
        $dirs[ ] = __DIR__ . '/lib/carrington-build/modules';

        return $dirs;
      } );

      add_action( 'cfct-rows-loaded', function ( $dirs ) {

      }, 100 );

      add_filter( 'cfct-build-display-class', function ( $current ) {
        global $post;

        return $current . ( get_post_meta( $post->ID, '_cfct_build_data', true ) ? ' build-enabled' : ' build-disabled' );
      } );

      add_filter( 'cfct-build-module-url-unknown', function ( $url, $module, $file_key ) {
        return get_stylesheet_directory_uri() . "/vendor/usabilitydynamics/lib-carrington/lib/modules/" . $file_key . '/';
      }, 10, 3 );

      add_filter( 'cfct-block-c6-12-classes', function ( $classes ) {
        return array_merge( array( 'col-md-4', 'col-sm-4', 'col-lg-4', 'col-first' ), $classes );
      } );

      add_filter( 'cfct-block-c6-34-classes', function ( $classes ) {
        return array_merge( array( 'col-md-4', 'col-sm-4', 'col-lg-4', 'col-middle' ), $classes );
      } );

      add_filter( 'cfct-block-c6-56-classes', function ( $classes ) {
        return array_merge( array( 'col-md-4', 'col-sm-6', 'col-lg-4', 'col-last' ), $classes );
      } );

      add_filter( 'cfct-block-c6-123-classes', function ( $classes ) {
        return array_merge( array( 'col-md-6', 'col-sm-6', 'col-lg-6', 'col-first' ), $classes );
      } );

      add_filter( 'cfct-block-c6-456-classes', function ( $classes ) {
        return array_merge( array( 'col-md-6', 'col-sm-6', 'col-lg-6', 'col-last' ), $classes );
      } );

      add_filter( 'cfct-block-c4-12-classes', function ( $classes ) {
        return array_merge( array( 'col-md-6', 'col-sm-6', 'col-lg-6', 'col-first' ), $classes );
      } );

      add_filter( 'cfct-block-c4-34-classes', function ( $classes ) {
        return array_merge( array( 'col-md-6', 'col-sm-6', 'col-lg-6', 'col-last' ), $classes );
      } );

      add_filter( 'cfct-block-c6-1234-classes', function ( $classes ) {
        return array_merge( array( 'col-md-8', 'col-sm-12', 'col-lg-8', 'col-first' ), $classes );
      } );

      add_filter( 'cfct-block-c6-3456-classes', function ( $classes ) {
        return array_merge( array( 'col-md-8', 'col-sm-12', 'col-lg-8', 'col-last' ), $classes );
      } );

      add_filter( 'cfct-block-c6-123456-classes', function ( $classes ) {
        return array_merge( array( 'col-md-12', 'col-sm-12', 'col-lg-12', 'col-first', 'col-last', 'col-full-width' ), $classes );
      } );

      add_filter( 'cfct-block-c4-1234-classes', function ( $classes ) {
        return array_merge( array( 'col-md-12', 'col-sm-12', 'col-lg-12', 'col-first', 'col-last', 'col-full-width' ), $classes );
      } );

      add_filter( 'cfct-build-page-options', function () {
        global $post;

        $cfct_data = get_post_meta( $post->ID, '_cfct_build_data', true );

        $current_setting = !empty( $cfct_data[ 'template' ][ 'custom_class' ] ) ? $cfct_data[ 'template' ][ 'custom_class' ] : '';

        $options[] = '<li><a id="cfct-set-build-class" href="#cfct-set-build-class" current_setting="' . $current_setting . '" >Set Build Class</a></li>';

        // $options[] = '<li><a id="cfct-copy-build-data" href="#cfct-copy-build">Copy Layout</a></li>';
        // $options[] = '<li><a id="cfct-paste-build-data" href="#cfct-paste-build">Paste Layout</a></li>';

        return implode( '', $options );

      });

      add_filter( 'cfct-build-module-class', function ( $class ) {
        return trim( $class . ' clearfix ' );
      } );

      add_filter( 'cfct-module-cf-post-callout-module-view', function ( $view ) {
        return __DIR__ . '/lib/carrington-build/modules/post-callout/view.php';
      } );

      include_once( __DIR__ . '/../vendor/usabilitydynamics/lib-carrington/lib/carrington-build.php' );

    }

  }

}