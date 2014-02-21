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
       * @param type $options
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
       * @param type $atts
       * @return type
       */
      public function call( $atts = "" ) {

        //** Default values */
        $defaults = array(
          'size' => 'medium', //** small, medium, large */
          'color' => '#EF0D95', //** background */
          'anchor_color' => 'white', //** text color */
          'anchor' => __( 'Button', wp_festival( 'domain' ) ), //** Button text */
          'url' => '#', //** Button url */
          'style' => '', //** Custom styles */
          'class' => 'btn-default', //** Custom classes */
          'target' => '', //** Link target */
          'track' => false
        );

        extract( shortcode_atts( $defaults, $atts ) );

        $other = '';
        if ( $track ) {
          $other .= "onclick=\"if(typeof _gaq == 'object'){_gaq.push(['_link', '$url']); return false;}else{console.log('no _gaq')}\" ";
        }

        return '<a href="'.$url.'" target="'.$target.'" class="btn btn-custom '.$size.' '.$class.'" style="color:'.$anchor_color.';background:'.$color.';border-color:'.$color.'; '.$style.'" '.$other.'>'.$anchor.'</a>';

      }

    }

  }

}