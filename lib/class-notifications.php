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
        
        //** Set CRON Jobs */
        add_action( 'init', array( $this, 'set_crone_jobs' ) );
        
        //** WP-CRM Actions */
        /** Add CRM notification fire action */
        add_filter( "wp_crm_notification_actions", array( $this, 'crm_notification_actions' ) );
        
      }
      
      /**
       * 
       *
       *
       */
      public function set_crone_jobs() {
        //** Adds attendees notifications cron job */
        if ( !wp_next_scheduled( 'eb_attendees_notifications' ) ) {
          wp_schedule_event( time(), 'daily', 'eb_attendees_notifications' );
        }
        add_action( 'eb_attendees_notifications', array( $this, 'maybe_send_attendees_notifications' ) );
      }
      
      /**
       *
       */
      public function maybe_send_attendees_notifications() {
        
        //** STEP 1. Get information about events and attendees */
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
              $d = (object)array(
                'event' => $e->event,
                'organizer' => $post,
                'users' => $users,
                'attendees' => array(),
              );
              foreach( $resp->attendees as $a ) {
                array_push( $d->attendees, $a->attendee );
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
        
        //** STEP 2. Send Attendees Notifications */
        
        foreach( $data as $i ) {
          
          $attendees = '';
          foreach( $i->attendees as $attendee ) {
            $attendees .= $attendee->first_name . ' ' . $attendee->last_name . ' - ' . $attendee->email . ' ' . PHP_EOL;
          }
          
          //** Prepare notification data */
          $notification = wp_parse_args( array(
            'trigger_action' => 'eventbrite_attendees_notification',
            'subject' => sprintf( __( '%s Attendees', $this->get( 'domain' ) ), $i->event->title ),
            'message' => sprintf( __( 'Hello [display_name]%1$s%1$sThe following attendees visited event [event] yesterday:%1$s%1$s[attendees]', $this->get( 'domain' ) ), PHP_EOL ),
            'crm_log_message' => __( 'Sent Attendees Notification', $this->get( 'domain' ) ),
            'data' => array(
              'display_name' => '',
              'user_email' => '',
              'event' => "<a href=\"{$i->event->url}\">{$i->event->title}</a>",
              'event_label' => $i->event->title,
              'event_url' => $i->event->url,
              'event_date' => $i->event->end_date,
              'event_logo' => $i->event->logo,
              'event_description' => $i->event->description,
              'attendees' => $attendees,
            )
          ), $this->_get_notification_template );
          
          foreach( $i->users as $user ) {
            $notification[ 'user' ] = $user->ID;
            $notification[ 'data' ][ 'display_name' ] = $user->display_name;
            //** Get email(s) where to send notifications */
            $emails = apply_filters( 'eb::notification::attendee::emails', array( $user->user_email ), $user );
            foreach( $emails as $email ) {
              $notification[ 'data' ][ 'user_email' ] = $email;
              //** Finally, send notification */              
              $this->_send_notification( $notification );
            }
          }
          
        }
        
      }
      
      /**
       * Filter CRM actions list
       *
       * @uses WP-CRM
       * @param array $current
       * @return array
       */
      public function crm_notification_actions( $current ) {
        $current = array_merge( (array)$current, apply_filters( 'eventbrite::notification_actions', array(
          //'eventbrite_default_notification' => __( 'Eventbrite Default Notification' ),
          'eventbrite_attendees_notification' => __( 'Eventbrite Attendees Notification' ),
        ) ) );
        return $current;
      }
      
      /**
       *
       * @author peshkov@UD
       */
      private function _get_notification_template() {
        return apply_filters( 'eventbrite::notification_template', array(
          'trigger_action' => 'eventbrite_default_notification',
          'data' => array(),
          'user' => false,
          'subject' => __( 'Eventbrite Notification', $this->get( 'domain' ) ),
          'message' => '',
          'crm_log_message' => __( 'Sent Eventbrite Notification', $this->get( 'domain' ) ),
        ) );
      }
      
      /**
       * Sends Notification using WP-CRM if it's enabled or default way in other case.
       * 
       * @param type $notification
       * @author peshkov@UD
       */
      private function _send_notification( $notification ) {
        $notification = apply_filters( 'eventbrite::send_notification', $notification );
        Utility::send_notification( $notification );
      }
      

    }
  
  }

}
