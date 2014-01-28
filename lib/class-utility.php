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

  if( !class_exists( '\UsabilityDynamics\Disco\Utility' ) ) {

    /**
     * Utility Class
     *
     * @class Utility
     * @author potanin@UD
     */
    class Utility extends \UsabilityDynamics\Utility {

      /**
       * Clean up and escape classes. Remove empties, run through esc_attr,
       * get rid of junk whitespace.
       *
       * @param array $classes
       *
       * @return array
       */
      public static function clean_classes( $classes = array() ) {
        $classes = array_map( 'trim', $classes );
        $classes = array_map( 'esc_attr', $classes );
        // Remove empties
        $classes = array_diff( $classes, array( '' ) );

        // Remove dupes
        return array_unique( $classes );
      }

      /**
       * Take up to 2 arrays, merge them and combine them into an
       * HTML classname string
       *
       * @param array $classes1 (optional) classses
       * @param array $classes2 (optional) more classes
       *
       * @return string
       */
      public static function to_classname( $classes1 = array(), $classes2 = array() ) {
        $classes = array_merge( $classes1, $classes2 );
        $classes = self::clean_classes( $classes );

        return implode( ' ', $classes );
      }

      /**
       * Take a string of HTML classes and turn them into an array of
       * strings (1 for each class).
       *
       * @param string $classname (optional) string of classes
       *
       * @return array
       */
      public static function extract_classes( $classname = '' ) {
        $classes = explode( ' ', trim( $classname ) );
        $classes = self::clean_classes( $classes );

        return $classes;
      }

      /**
       * Take 2 strings of classes and merge them, preventing dupes.
       * Convenient!
       *
       * @param string $classname1 (optional) classes
       * @param string $classname2 (optional) more classes
       *
       * @return string
       */
      public static function merge_classnames( $classname1 = '', $classname2 = '' ) {
        return self::to_classname(
          self::extract_classes( $classname1 ),
          self::extract_classes( $classname2 )
        );
      }

      /**
       * Turn an array or two into HTML attribute string
       */
      public function to_attr( $arr1 = array(), $arr2 = array() ) {
        $attrs = array();
        $arr   = array_merge( $arr1, $arr2 );
        foreach( $arr as $key => $value ) {
          if( !$value ) {
            continue;
          }

          $attrs[ ] = esc_attr( $key ) . '="' . esc_attr( $value ) . '"';
        }

        return implode( ' ', $attrs );
      }

    }

  }

}