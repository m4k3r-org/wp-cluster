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
        //add_action( 'wp_loaded', array( $this, 'maybe_send_attendees_notifications' ), 99 );
        
        //** Return CSV File is requested */
        add_filter( "parse_request", array( $this, 'maybe_get_csv_file' ) );
        
        //** WP-CRM Actions */
        /** Add CRM notification fire action */
        add_filter( "wp_crm_notification_actions", array( $this, 'crm_notification_actions' ) );
        
      }
      
      /**
       * Set CRONE Jobs
       */
      public function set_crone_jobs() {
        //** Adds attendees notifications cron job */
        if ( !wp_next_scheduled( 'eb_attendees_notifications' ) ) {
          wp_schedule_event( time(), 'daily', 'eb_attendees_notifications' );
        }
        add_action( 'eb_attendees_notifications', array( $this, 'maybe_send_attendees_notifications' ) );
      }
      
      /**
       * Returns CSV file if request is valid
       *
       */
      public function maybe_get_csv_file() {
        if( !empty( $_REQUEST[ 'eventbrite-attendees' ] ) ) {
          $uploads = wp_upload_dir();
          $file = $uploads['basedir'] . '/eb/attendees/' . $_REQUEST[ 'eventbrite-attendees' ] . '.csv';
          //die( $file );
          if( file_exists( $file ) ) {
            //** force download */
            header( "Content-Type: application/force-download" );
            header( "Content-Type: application/octet-stream" );
            header( "Content-Type: application/download" );
            //** disposition / encoding on response body */
            header( "Content-Disposition: attachment; filename={$_REQUEST[ 'eventbrite-attendees' ]}.csv" );
            header( "Content-Transfer-Encoding: binary" );
            readfile( $file );
            die();
          }
        }
      }
      
      /**
       * Send Attendees Notifications of ended events to related users.
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
          $list = array();
          foreach( $i->attendees as $attendee ) {
            array_push( $list, array(
             'first_name' => $attendee->first_name,
             'last_name' => $attendee->last_name,
             'email' => $attendee->email,
            ) );
            $attendees .= $attendee->first_name . ' ' . $attendee->last_name . ' - ' . $attendee->email . ' ' . PHP_EOL;
          }
          
          //** Save CSV file of attendees */
          $uploads = wp_upload_dir();
          $filename = '';
          if ( wp_mkdir_p( $uploads['basedir'] . '/eb/attendees' ) ) {
            $filename = md5( $i->event->id );
            $fp = fopen( $uploads['basedir'] . '/eb/attendees/' . $filename . '.csv', 'w' );
            foreach ( $list as $fields ) {
              fputcsv( $fp, $fields );
            }
            fclose( $fp );
          }
          
          //** Prepare notification data */
          $notification = wp_parse_args( array(
            'trigger_action' => 'eventbrite_attendees_notification',
            'subject' => sprintf( __( '%s Attendees', $this->get( 'domain' ) ), $i->event->title ),
            'message' => sprintf( __( 'Hello [display_name]%1$s%1$sThe following attendees visited event [event] yesterday:%1$s%1$s[attendees]%1$s%1$sAlso you can download csv file [csv_file]', $this->get( 'domain' ) ), PHP_EOL ),
            'crm_log_message' => sprintf( __( 'Sent %s Attendees Notification', $this->get( 'domain' ) ), $i->event->title ),
            'data' => array(
              'display_name' => '',
              'user_email' => '',
              'event' => "<a href=\"{$i->event->url}\">{$i->event->title}</a>",
              'event_name' => $i->event->title,
              'event_url' => $i->event->url,
              'event_date' => $i->event->end_date,
              'event_logo' => $i->event->logo,
              'event_description' => $i->event->description,
              'csv_file' => site_url( "?eventbrite-attendees={$filename}" ),
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
