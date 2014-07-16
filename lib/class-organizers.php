<?php
/**
 * Orginizers Manager
 *
 * @author Usability Dynamics
 * @namespace DiscoDonniePresents
 */
namespace DiscoDonniePresents\Eventbrite {

  use UsabilityDynamics\Model\Post;

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
        $posts = get_posts( $args );
        if( !empty( $posts ) && is_array( $posts ) ) {
          foreach( $posts as &$post ) {
            $post = Post::get( $post );
          }
        }
        return $posts;
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
          
          //** STEP 1. Get organizers data for synchronization */
          
          //** Get list of organizers from Eventbrite */
          $eb_organizers = get_wp_eventbrite()->client->user_list_organizers()->organizers;
          //** Get List of already existing organizers */
          $wp_organizers = self::get_organizers();
          
          //** STEP 2. Add / Update Wordpress organizers */
          
          foreach( $eb_organizers as $i ) {
            $post = false;
            foreach( $wp_organizers as $k => $wp_organizer ) {
              if( $post->unique_id == $i->organizer->id ) {
                $post = $wp_organizer;
                unset( $wp_organizers[ $k ] );
                break;
              }
            }
            if( !$post ) {
              $post = Post::get( false, get_wp_eventbrite( 'post_type.organizer' ) );
            }
            //** Data */
            $post->post_title = ( !empty( $i->organizer->name ) ? $i->organizer->name : 'No Name. ID#' . $i->organizer->id );
            $post->post_excerpt = $i->organizer->description;
            $post->post_content = $i->organizer->long_description;
            //** Meta */
            $post->eventbrite_url = $i->organizer->url;
            $post->eventbrite_id = $i->organizer->id;
            
            $post->save();
          }
          
          //** STEP 3. Removed organizers which no more exist on Eventbrite */
          
          if( !empty( $wp_organizers ) ) {
            foreach( $wp_organizers as $wp_organizer ) {
              wp_delete_post( $wp_organizer->ID, true );
            }
          }
          
          //echo "<pre>"; print_r( $organizers ); echo "</pre>"; die();
          
        } catch( \Exception $e ){
          return new WP_Error( 'failed', $e->getMessage() );
        }
        return true;
      }

    }
  
  }

}
