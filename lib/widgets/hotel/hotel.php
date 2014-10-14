<?php

namespace WP_Spectacle\Widgets;

/**
 * Class Hotel
 * @package WP_Spectacle\Widgets
 * Hotel Widget
 */
class Hotel extends \WP_Widget
{
  private $_mustache_engine = null;

  public function __construct(){
    parent::__construct( 'wp_spectacle_hotel_widget', __( 'Spectacle Hotel', 'wp_spectacle_widget_domain' ), [
      'description' => __( 'Part of WP Spectacle', 'wp_spectacle_widget_domain' )
    ] );

    // Set up the mustache engine
    $this->_mustache_engine = new \Mustache_Engine( [
      'loader' => new \Mustache_Loader_FilesystemLoader( dirname( __FILE__ ) . '/templates' ),
      'escape' => function ( $value ){
          return esc_attr( $value );
        },
      'strict_callables' => true
    ] );
  }


  /**
   * Display the widget.
   *
   * @param array $args
   * @param array $instance
   */
  public function widget( $args, $instance ){

    if( array_key_exists( 'image_image_id', $instance ) )
    {
      $image_src = wp_get_attachment_image_src( $instance[ 'image_image_id' ], 'full' );
      $instance['image_source'] = $image_src[0];
    }

    echo $this->_mustache_engine->render( 'widget', $instance);
  }

  /**
   * Admin form for the widget
   *
   * @param array $instance
   *
   * @return string|void
   */
  public function form( $instance ){

    // Get the selected image if any
    $data[ 'selected_image' ] = null;

    // Get saved data
    if( array_key_exists( 'image', $instance ) ){
      $data[ 'selected_image' ] = $instance[ 'image' ];
    }

    if( array_key_exists( 'image_id', $instance ) ){
      $data[ 'image_id_value' ] = $instance[ 'image_id' ];
    }

    $data = [
      'image_id' => $this->get_field_id( 'image' ),
      'image_name' => $this->get_field_name( 'image' ),
      'image_image_id' => $this->get_field_id( 'image_image_id' ),
      'image_image_name' => $this->get_field_name( 'image_image_id' ),

      'images' => $this->_get_images( $data[ 'selected_image' ] ),

      'name_id' => $this->get_field_id('name'),
      'name_name' => $this->get_field_name('name'),
      'name_value' => (! empty($instance['name'])) ? $instance['name'] : '',

      'location_id' => $this->get_field_id('location'),
      'location_name' => $this->get_field_name('location'),
      'location_value' => (! empty($instance['location'])) ? $instance['location'] : '',

      'distance_id' => $this->get_field_id('distance'),
      'distance_name' => $this->get_field_name('distance'),
      'distance_value' => (! empty($instance['distance'])) ? $instance['distance'] : '',

      'phone_id' => $this->get_field_id('phone'),
      'phone_name' => $this->get_field_name('phone'),
      'phone_value' => (! empty($instance['phone'])) ? $instance['phone'] : '',

      'book_now_link_id' => $this->get_field_id('book_now_link'),
      'book_now_link_name' => $this->get_field_name('book_now_link'),
      'book_now_link_value' => (! empty($instance['book_now_link'])) ? $instance['book_now_link'] : '',

      'discount_id' => $this->get_field_id('discount'),
      'discount_name' => $this->get_field_name('discount'),
      'discount_value' => (! empty($instance['discount'])) ? $instance['discount'] : '',

      'price_id' => $this->get_field_id('price'),
      'price_name' => $this->get_field_name('price'),
      'price_value' => (! empty($instance['price'])) ? $instance['price'] : '$0',

      'services' => [
        [
          'label' => 'Internet/Wi-fi',
          'id' => $this->get_field_id('services_internet_wifi'),
          'name' => $this->get_field_name('services_internet_wifi'),
          'value' => ( (isset($instance['services_internet_wifi'])) && ($instance['services_internet_wifi'] == 'on') ) ? true : false
        ],
        [
          'label' => 'Swimming Pool',
          'id' => $this->get_field_id('services_swimming_pool'),
          'name' => $this->get_field_name('services_swimming_pool'),
          'value' => ( (isset($instance['services_swimming_pool'])) && ($instance['services_swimming_pool'] == 'on') ) ? true : false
        ],
        [
          'label' => 'Spa/Massage/Wellness',
          'id' => $this->get_field_id('services_wellness'),
          'name' => $this->get_field_name('services_wellness'),
          'value' => ( (isset($instance['services_wellness'])) && ($instance['services_wellness'] == 'on') ) ? true : false
        ],
        [
          'label' => 'Restaurant/Bar',
          'id' => $this->get_field_id('services_restaurant_bar'),
          'name' => $this->get_field_name('services_restaurant_bar'),
          'value' => ( (isset($instance['services_restaurant_bar'])) && ($instance['services_restaurant_bar'] == 'on') ) ? true : false
        ],
        [
          'label' => 'Travel &amp; Transfers',
          'id' => $this->get_field_id('services_transfers'),
          'name' => $this->get_field_name('services_transfers'),
          'value' => ( (isset($instance['services_transfers'])) && ($instance['services_transfers'] == 'on') ) ? true : false
        ],
        [
          'label' => 'Gym',
          'id' => $this->get_field_id('services_gym'),
          'name' => $this->get_field_name('services_gym'),
          'value' => ( (isset($instance['services_gym'])) && ($instance['services_gym'] == 'on') ) ? true : false
        ],
        [
          'label' => 'Parking',
          'id' => $this->get_field_id('services_parking'),
          'name' => $this->get_field_name('services_parking'),
          'value' => ( (isset($instance['services_parking'])) && ($instance['services_parking'] == 'on') ) ? true : false
        ],
        [
          'label' => 'Business Facilities',
          'id' => $this->get_field_id('services_business_facilities'),
          'name' => $this->get_field_name('services_business_facilities'),
          'value' => ( (isset($instance['services_business_facilities'])) && ($instance['services_business_facilities'] == 'on') ) ? true : false
        ]
      ] // services
    ];

    // No images found in the media library
    if( $data[ 'images' ] === false ){
      $data[ 'error' ] = true;
    }

    echo $this->_mustache_engine->render( 'admin-form', $data );
  }

  /**
   * Save the admin form.
   *
   * @param array $new_instance
   * @param array $old_instance
   *
   * @return array
   */
  public function update( $new_instance, $old_instance ){
    $instance = array();

    foreach ( $new_instance as $key => $value )
    {
      if (! empty($new_instance[ $key ]) )
      {
        $instance[ $key ] = $value;
      }
      else
      {
        $instance[ $key ] = '';
      }
    }

    if( array_key_exists( 'image', $new_instance ) ){
      $instance[ 'image' ] = $new_instance[ 'image' ];

      $images = $this->_get_images( $new_instance[ 'image' ] );
      $images = $images[ 'meta' ];

      $instance[ 'image_image_id' ] = $images[ 'sel_image_id' ];
    }

    if( array_key_exists( 'image_image_id', $new_instance ) ){
      $instance[ 'image_image_id' ] = $new_instance[ 'image_image_id' ];
    }

    return $instance;
  }


  /**
   * Get the images from the media library.
   *
   * @param null $sel_image Selected image src
   *
   * @return array|bool
   */
  private function _get_images( $sel_image = null ){
    $images = new \WP_Query( [
      'post_type' => 'attachment',
      'post_status' => 'inherit',
      'post_mime_type' => 'image',
      'posts_per_page' => -1
    ] );

    $ret_val = [
      'meta' => [ ],
      'data' => [ ]
    ];

    if( $images->have_posts() ){
      while( $images->have_posts() ){
        $images->the_post();

        $src = wp_get_attachment_image_src( get_the_ID() );

        $selected = false;
        if( $src[ 0 ] === $sel_image ){
          $selected = true;
          $ret_val[ 'meta' ][ 'sel_image' ] = $sel_image;
          $ret_val[ 'meta' ][ 'sel_image_id' ] = get_the_ID();
        }

        array_push( $ret_val[ 'data' ], [
          'id' => get_the_ID(),
          'src' => $src[ 0 ],
          'name' => get_the_title(),
          'selected' => $selected
        ] );
      }

      // Populate default value, first image from the media library
      if( empty( $ret_val[ 'meta' ] ) ){
        $ret_val[ 'meta' ][ 'sel_image' ] = $ret_val[ 'data' ][ 0 ][ 'src' ];
        $ret_val[ 'meta' ][ 'sel_image_id' ] = $ret_val[ 'data' ][ 0 ][ 'id' ];
      }

      return $ret_val;
    } else{
      return false;
    }
  }
}