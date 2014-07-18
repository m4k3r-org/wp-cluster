<?php
/**
 * Notifications.
 *
 * @author Usability Dynamics
 * @namespace DiscoDonniePresents
 */
namespace DiscoDonniePresents\Eventbrite {

  if( !class_exists( 'DiscoDonniePresents\Eventbrite\Notifications' ) ) {

    /**
     * Notifications
     *
     * @author Usability Dynamics
     */
    class Notifications extends Scaffold {
      
      /**
       * Constructor
       *
       * @author peshkov@UD
       */
      public function __construct() {
        parent::__construct();
        
        //add_action( 'wp_loaded', array( $this, 'maybe_send_attendees_notifications' ) );
        
      }
      
      /**
       *
       */
      public function maybe_send_attendees_notifications() {
        
        //** STEP 1. Prepare notifications data */
       
        $data = array();
        //** Get ended (finished) yesterday events */
        $events = $this->client->get_ended_yesterday_events();
        //** Loop through returned events */
        foreach( $events as $e ) {
          try {
            //** Determine if organizer of event exists and has related users */
            $post = Organizers::get_organizer_by_eventbrite_id( $e->event->organizer->id  );
            $user_ids = array_filter( $post->related_users );
            if( !empty( $user_ids ) ) {
              $users = get_users( array( 'include' => $user_ids ) );
            }
            //** Ouch... */
            if( empty( $users ) ) {
              continue;
            }
            //** Still here? Get attendees for events then. */ 
            $resp = $this->client->event_list_attendees( array(
              'id' => $e->event->id,
              'status' => 'attending',
            ) );
            if( is_object( $resp ) && !empty( $resp->attendees ) ) {
              //** Prepare data now. */
              $d = array(
                'event' => $e->event,
                'organizer' => $post,
                'users' => $users,
                'attendees' => array(),
              );
              foreach( $resp->attendees as $a ) {
                array_push( $d[ 'attendees' ], $a->attendee );
              }
              array_push( $data, $d );
            }
          } catch ( \Exception $e ) {
            continue;
          }
        }
        //** Break, if there are no events and attendees */
        if( empty( $data ) ) {
          return NULL;
        }
        
        //** STEP 2. Determine if event */
        
        echo "<pre>"; print_r( $data ); echo "</pre>";
        
      }
      

    }
  
  }

}
