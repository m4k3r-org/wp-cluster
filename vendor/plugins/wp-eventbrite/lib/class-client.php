<?php
/**
 * Evenbrite Client for Evenbrite API
 *
 * @author Usability Dynamics
 * @namespace DiscoDonniePresents
 */
namespace DiscoDonniePresents\Eventbrite {

  if( !class_exists( 'DiscoDonniePresents\Eventbrite\Client' ) ) {

    /**
     * Eventbrite Core
     *
     * @author Usability Dynamics
     */
    class Client extends \UsabilityDynamics\Eventbrite\Client {
      
      /**
       *
       */
      private static $connected = NULL;
    
      /**
       *
       */
      public $errors = array();
    
      /**
       * Determine if provided credentials are valid by doing test request
       */
      public function ping() {
        if( NULL === self::$connected ) {
          self::$connected = true;
          try{
            $user = $this->user_get()->user;
          }catch( \Exception $e ){
            self::$connected = false;
            $this->errors[] = $e->getMessage();
          }
        }
        return self::$connected;
      }
      
      /**
       * Returns Errors
       */
      public function get_errors() {
        return trim( implode( ', ', $this->errors ) );
      }
      
      /**
       * Returns the list of finished ( ended ) yesterday events
       *
       * @author peshkov@UD
       */
      public function get_ended_yesterday_events() {
        $events = array();
        //** Be sure we're connected */
        try{
          $resp = $this->user_list_events( array(
            'event_statuses' => 'ended', // only return events that have ended in the past 7 days.
            'asc_or_desc' => 'desc'
          ) );
          if( isset( $resp->events ) && is_array( $resp->events ) ) {
            foreach( $resp->events as $e ) {
              $endDate = new \DateTime( $e->event->end_date, new \DateTimeZone( $e->event->timezone ) );
              $yesterdayDate = new \DateTime( NULL, new \DateTimeZone( $e->event->timezone ) );
              $yesterdayDate->sub( new \DateInterval( 'P1D' ) );
              //** Get only yesterday completed events */
              if( (int)$endDate->format('d') == (int)$yesterdayDate->format( 'd' ) ) {
                $events[] = $e;
              }
            }
          }
        } catch( \Exception $e ){
          $this->errors[] = $e->getMessage();
        }
        return $events;
      }
    
    }
  
  }

}