<?php
/**
 * Flawless Schema
 *
 * @author potanin@UD
 * @version 0.0.1
 * @namespace Flawless
 */
namespace UsabilityDynamics\Flawless {

  /**
   * JSON Schema validation.
   *
   * @author potanin@UD
   * @version 0.1.0
   * @class Schema
   */
  class Schema {

    /**
     * Schema Class version.
     *
     * @public
     * @static
     * @property $version
     * @type {Object}
     */
    public static $version = '0.1.0';

    // @property $data Data object to validate.
    public $data = stdClass;

    // @property $schema JSON Schema to validate against.
    public $schema = stdClass;

    // @property $errors Error array.
    public $errors = null;

    // @property $validator Reference to JsonSchema\Validator library.
    private $validator;

    /**
     * Constructor for the Schema Class.
     *
     * Attempt to fetch the data if string provided.
     * Attempt to fetch the schema if string provided.
     *
     * @author potanin@UD
     * @version 0.0.1
     * @method __construct
     *
     * @constructor
     * @for Schema
     *
     * @param object $data Data object to validate.
     * @param object $schema JSON Schema to validate against.
     */
    public function __construct( $data = stdClass, $schema = stdClass ) {

      try {

        if( is_string( $data ) ) {
          $this->data = json_decode( file_get_contents( $data ) );
        } else if( is_object( $data ) ) {
          $this->data = $data;
        }

      } catch ( Exception $e ) { $errors[] = $e->getMessage(); }

      try {

        if( is_string( $schema ) ) {
          $this->schema = json_decode( file_get_contents( $schema ) );
        } else if( is_object( $schema ) ) {
          $this->schema = $schema;
        }

      } catch ( Exception $e ) { $errors[] = $e->getMessage(); }

      try {

        $this->validator = new \JsonSchema\Validator();
        $this->validator->check( $this->data, $this->schema );

        if( $this->validator->getErrors() ) {
          $this->errors = array_merge( (array) $this->errors, (array) $this->validator->getErrors() );
        }

      } catch ( Exception $e ) { $errors[] = $e->getMessage(); }

      return this;

    }

    /**
     * Test if validation was successful.
     *
     * @method is_valid
     * @for Schema
     *
     * @return bool
     */
    public function is_valid() {
      return !$this->errors();
    }

  }

}