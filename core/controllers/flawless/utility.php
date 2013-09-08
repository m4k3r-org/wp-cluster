<?php
/**
 * Flawless Utility
 *
 * @author potanin@UD
 * @version 0.0.1
 * @namespace Flawless
 */
namespace Flawless {

  /**
   * Utility
   *
   * Description: WPP Utility implementation
   *
   * @extends \UsabilityDynamics\Utility
   * @author potanin@UD
   * @version 0.1.0
   * @class Utility
   */
  class Utility extends \UsabilityDynamics\Utility {

    // Class Version.
    public $version = '0.2.1';

    /**
     * Constructor for the UD Utility class.
     *
     * @author potanin@UD
     * @version 0.0.1
     * @method __construct
     *
     * @constructor
     * @for Utility
     *
     * @param array $options
     */
    public function __construct( $options = array() ) {
      $this->options = $options;
    }

  }

}