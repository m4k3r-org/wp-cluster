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
       * @return array List of UsabilityDynamics\Model\Post objects
       */
      public static function get_organizers( $args = false ) {
        $args = wp_parse_args( $args, array(
          'post_type' => get_wp_eventbrite( 'post_type.organizer' ),
          'posts_per_page' => -1,
          'orderby' => 'title',
          'order' => 'ASC',
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
       * Returns organizer object by Eventbrite ID
       *
       * @return UsabilityDynamics\Model\Post
       */
      public static function get_organizer_by_eventbrite_id( $eventbrite_id ) {
        global $wpdb;
        $result = $wpdb->get_col( "
          SELECT `p`.`ID`
            FROM `{$wpdb->posts}` as `p` INNER JOIN `{$wpdb->postmeta}` as `m`
              ON `p`.`ID` = `m`.`post_id`
            WHERE `p`.`post_type` = '" . get_wp_eventbrite( 'post_type.organizer' ) . "'
              AND `m`.`meta_key` = 'eventbrite_id'
              AND `m`.`meta_value` = '{$eventbrite_id}'
        " );
        if( !empty( $result ) ) {
          $post = Post::get( $result[0] );
          if( !is_wp_error( $post ) ) {
            return $post;
          }
        }
        return false;
      }
      
      /**
       * Bulk Updater
       *
       * @param array $organizers data
       * @return mixed
       */
      public static function bulk_update( $organizers ) {
        try {        
          if( !is_array( $organizers ) ) {
            throw new \Exception( __( 'Incorrect organizers data presented.', get_wp_eventbrite( 'domain' ) ) );
          }
          $invalid_post = false;
          foreach( $organizers as $organizer_id => $data ) {
            $post = Post::get( $organizer_id );
            if( is_wp_error( $post ) ) {
              $invalid_post = true;
              continue;
            }
            foreach( $data as $key => $value ) {
              $post->{$key} = $value;
            }
            $post->save();
          }
          if( $invalid_post ) {
            throw new \Exception( __( 'Some of the organizers were(was) not updated due to incorrect ID presented.', get_wp_eventbrite( 'domain' ) ) );
          }
        } catch ( \Exception $e ) {
          return new WP_Error( 'failed', $e->getMessage() );
        }
        return true;
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
              if( $wp_organizer->eventbrite_id == $i->organizer->id ) {
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
            $post->post_content = ( !empty( $i->organizer->long_description ) ? $i->organizer->long_description : __( 'No Description', get_wp_eventbrite( 'domain' ) ) );
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
          
        } catch( \Exception $e ){
          return new WP_Error( 'failed', $e->getMessage() );
        }
        return true;
      }

    }
  
  }

}
