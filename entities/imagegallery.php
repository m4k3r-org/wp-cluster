<?php

namespace DiscoDonniePresents {

  if ( !class_exists( 'DiscoDonniePresents\ImageGallery' ) ) {

    class ImageGallery extends Entity {

      /**
       *
       * @var type
       */
      public $_type = 'imagegallery';

      /**
       *
       * @var type
       */
      public $_meta_key = 'imagegallery';

      /**
       *
       * @var type
       */
      public $_event;

      /**
       *
       * @var type
       */
      public $_images;

      /**
       *
       * @var type
       */
      public $_credit;

      /**
       *
       * @param type $id
       */
      public function __construct($id = null, $preload = true) {
        parent::__construct($id);

        if ( $preload ) {
          $this->_event = $this->load_event();

          $this->_images = $this->load_images();

          $this->_credit = $this->load_credit();
        }

      }

      /**
       *
       * @return type
       */
      public function load_event() {
        return new Event( $this->meta( 'event' ), false );
      }

      /**
       *
       */
      public function load_credit() {
        return new Credit( $this->meta( 'creator' ), false );
      }

      /**
       *
       * @param type $args
       * @return type
       */
      public function event() {

        if ( empty( $this->_event ) ) {
          $this->_event = $this->load_event();
        }

        return $this->_event;
      }

      /**
       *
       * @param type $args
       * @return type
       */
      public function credit() {

        if ( empty( $this->_credit ) ) {
          $this->_credit = $this->load_credit();
        }

        return $this->_credit;
      }

      /**
       *
       * @return type
       */
      public function images() {

        if ( empty( $this->_images ) ) {
          $this->_images = $this->load_images();
        }

        return $this->_images;

      }

    }

  }

}