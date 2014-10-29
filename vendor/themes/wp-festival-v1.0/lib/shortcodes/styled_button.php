<?php
/**
* Name: Styled Button
* ID: styled_button
* Type: shortcode
* Group: Festival
* Class: UsabilityDynamics\Festival\Shortcode_Styled_Button
* Version: 1.0
* Description: Draws a button styled by setting different parameters.
*/
namespace UsabilityDynamics\Festival {

  /**
   * Prevent class redeclaration
   */
  if( !class_exists( 'UsabilityDynamics\Festival\Shortcode_Styled_Button' ) ) {

    /**
     * main shortcode class
     * @extends \UsabilityDynamics\Shortcode\Shortcode
     */
    class Shortcode_Styled_Button extends \UsabilityDynamics\Shortcode\Shortcode {

      /**
       * ID
       * @var type
       */
      public $id = 'styled_button';

      /**
       * Group
       * @var type
       */
      public $group = 'Festival';

      /**
       * Construct
       *
       * @param array|\UsabilityDynamics\Festival\type $options
       */
      public function __construct( $options = array() ) {

        $this->name = __( 'Styled Button', wp_festival( 'domain' ) );

        $this->description = __( 'Draws a button styled by setting different parameters.', wp_festival( 'domain' ) );

        $this->params = array(
          'size' => '',
          'color' => '',
          'anchor_color' => '',
          'anchor' => '',
          'url' => '',
          'style' => '',
          'class' => '',
          'target' => '',
          'track' => ''
        );

        parent::__construct( $options );
      }

      /**
       * Caller
       *
       * @param string|\UsabilityDynamics\Festival\type $atts
       *
       * @return type
       */
      public function call( $atts = "" ) {

        $atts = shortcode_atts( array(
          'size' => 'medium', //** small, medium, large */
          'color' => '', //** background */
          'anchor_color' => '', //** text color */
          'anchor' => __( 'Button', wp_festival( 'domain' ) ), //** Button text */
          'url' => '#', //** Button url */
          'style' => '', //** Custom styles */
          'class' => 'btn-default', //** Custom classes */
          'target' => '', //** Link target */
          'track' => false
        ), $atts );

        return '<a href="'.$atts['url'].'" target="'.$atts['target'].'" class="btn btn-custom '.$atts['size'].' '.$atts['class'].'" style="color:'.$atts['anchor_color'].';background:'.$atts['color'].';border-color:'.$atts['color'].'; '.$atts['style'].'" '.($atts['track']?'data-track':'').'>'.$atts['anchor'].'</a>';

      }

    }

  }

}