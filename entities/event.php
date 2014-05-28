<?php

namespace DiscoDonniePresents {

  if ( !class_exists( 'DiscoDonniePresents\Event' ) ) {

    class Event extends Entity {

      /**
       *
       * @var type
       */
      public $_type = 'event';

      /**
       *
       * @var type
       */
      public $_venue;

      /**
       *
       * @param type $id
       */
      public function __construct( $id = null ) {
        parent::__construct( $id );

        $this->_venue = $this->load_venue();

        $this->apply_formatting();
      }

      /**
       *
       * @return \DiscoDonniePresents\Venue
       */
      private function load_venue() {
        return new Venue( $this->meta('venue') );
      }

      /**
       *
       */
      private function apply_formatting() {

        //** Date and time */
        $this->meta( 'eventDateHuman', date( 'l, F j, Y', strtotime( $this->meta( 'dateStart' ) ) ) );
        $this->meta( 'eventTimeHuman', date( 'g:i A', strtotime( $this->meta( 'dateStart' ) ) ) );

      }

      /**
       *
       * @return type
       */
      public function venue() {
        return $this->_venue;
      }

    }

  }

}