<?php
/**
 * Orginizers Manager
 *
 * @author Usability Dynamics
 * @namespace DiscoDonniePresents
 */
namespace DiscoDonniePresents\Eventbrite {

  if( !class_exists( 'DiscoDonniePresents\Eventbrite\Organizers' ) ) {

    /**
     * 
     *
     * @author Usability Dynamics
     */
    class Organizers {
    
      /**
       * Returns the list of all organizers
       *
       */
      public static function get_organizers( $args = false ) {
        $args = wp_parse_args( $args, array(
          'post_type' => get_wp_eventbrite( 'post_type.organizer' ),
          'posts_per_page' => -1
        ) );
        return get_posts( $args );
      }
    
      /**
       * Returns specific schema from file.
       *
       * @param string $name Filename without EXT
       * @param array $l10n Locale data
       * @author peshkov@UD
       */
      public static function sync(  ) {
        try {
          $organizers = get_wp_eventbrite()->client->user_list_organizers()->organizers;
          
          //echo "<pre>"; print_r( $organizers ); echo "</pre>"; die();
          
        } catch( \Exception $e ){
          return new WP_Error( 'failed', $e->getMessage() );
        }
        return true;
      }

    }
  
  }

}
