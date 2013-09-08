<?php
/**
 * -
 *
 * @namespace Flawless
 * @user potanin@UD
 * @date 8/31/13
 * @time 10:33 AM
 */
namespace Flawless {

  /**
   * Class Widget
   *
   * @class Widget
   */
  class Widget extends Element {

    /**
     * Widget Class version.
     *
     * @public
     * @static
     * @property $version
     * @type {Object}
     */
    public static $version = '0.1.1';

    /**
     * Constructor for the Views class.
     *
     * @author potanin@UD
     * @version 0.0.1
     * @method __construct
     *
     * @constructor
     * @for Widget
     *
     * @param bool $options
     *
     * @internal param $array
     */
    public function __construct( $options = false ) {
      // add_action( 'flawless::init_lower',           array( $this, 'init_lower' ), 10 );
      // add_action( 'flawless::template_redirect',    array( $this, 'template_redirect' ), 10 );
      // add_action( 'flawless::theme_setup::after',   array( $this, 'theme_setup' ), 10 );
    }

  }

}