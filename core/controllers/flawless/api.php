<?php
/**
 * Flawless API
 *
 * @author potanin@UD
 * @version 0.0.1
 * @namespace Flawless
 */
namespace Flawless {

  /**
   * API
   *
   * Description: WPP API implementation
   *
   * @extends UD:API
   * @author potanin@UD
   * @version 0.1.0
   * @class API
   */
  class API extends \UsabilityDynamics\API {

    // Class Version.
    public $version = '0.1.1';

    /**
     * Constructor for the UD API class.
     *
     * @author potanin@UD
     * @version 0.0.1
     * @method __construct
     *
     * @constructor
     * @for API
     *
     * @param array $options
     */
    public function __construct( $options = array() ) {

      // add_action( 'wpp::generate_rewrites', array( __CLASS__, 'generate_rewrites' ) );
      // add_action( 'wpp_init', array( __CLASS__, 'api_routes' ), 125 );

    }

  }

}