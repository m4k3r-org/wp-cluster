<?php
/**
 * -
 *
 * @author team@UD
 * @version 0.0.1
 * @namespace UD
 */

namespace UD {

  /**
   * XML-RPC API Class
   *
   * @beta
   *
   * @extends API
   * @class XML_RPC
   * @since 3.08.7
   * @author korotkov@ud
   */
  class XML_RPC extends API {

    /**
     * API Name
     *
     * @var string
     */
    public $name = 'XML_RPC';

    /**
     * API Description
     *
     * @var string
     */
    public $description = 'WP-Invoice XML-RPC API Handler. Can be used by authorized users by calling single method "wp.invoice" with arguments described in API Reference.';

    /**
     * Constructor for the UD API/XML-RPC class.
     *
     * Extend standard XML-RPC
     *
     * @author korotkov@UD
     * @version 3.08.7
     * @since 3.08.7
     * @method __construct
     *
     * @constructor
     * @for API
     *
     * @param {Object} [options] Configuration.
     * @return {Object} Instance.
     */
    function __construct() {
      add_filter( 'xmlrpc_methods', array( __CLASS__, '__register' ) );
      add_action( 'wpi_settings_before_help', 'wpi_help_api_reference' );
    }

    /**
     * Register method-hook
     *
     * @param array $methods
     *
     * @return array
     */
    function __register( $methods ) {
      $methods[ 'wp.invoice' ] = 'wpi_xmlrpc_request';
      return $methods;
    }

    /**
     * Create new invoice
     *
     * @param array $args
     *
     * @return UD_Invoice
     * @see UD_Invoice
     * @uses Internal API of plugin
     */
    function create_invoice( $args = array() ) {
      global $wpi_settings;

      //** Default arguments */
      $defaults = array(
        'custom_id' => false,
        'subject' => false,
        'description' => false,
        'type' => false,
        'user_data' => array(
          'user_email' => false,
          'first_name' => false,
          'last_name' => false,
          'phonenumber' => false,
          'streetaddress' => false,
          'city' => false,
          'state' => false,
          'zip' => false,
          'country' => false
        ),
        'deposit' => false,
        'due_date' => array(
          'year' => false,
          'month' => false,
          'day' => false
        ),
        'currency' => false,
        'tax' => false,
        'tax_method' => false,
        'recurring' => array(
          'unit' => false,
          'length' => false,
          'cycles' => false,
          'send_invoice_automatically' => false,
          'start_date' => array(
            'month' => false,
            'day' => false,
            'year' => false
          )
        ),
        'status' => false,
        'discount' => array(
          'name' => false,
          'type' => false,
          'amount' => false
        ),
        'items' => array(),
        'charges' => array()
      );

      //** Parse arguments */
      extract( $args = wp_parse_args( $args, $defaults ) );

      //** If empty subject - return error */
      if ( !$subject ) return new WP_Error( 'wp.invoice', __( 'Method requires "subject" argument to be passed.', WPI ), $args );

      //** If empty user_email - return error */
      if ( !$user_data[ 'user_email' ] ) return new WP_Error( 'wp.invoice', __( 'Method requires "user_email" in "user_data" argument to be passed.', WPI ), $args );
      if ( !filter_var( $user_data[ 'user_email' ], FILTER_VALIDATE_EMAIL ) ) return new WP_Error( 'wp.invoice', __( 'User Email is malformed.', WPI ), $args );

      //** Items/Charges check */
      if ( empty( $items ) && empty( $charges ) ) return new WP_Error( 'wp.invoice', __( 'Method requires "items" or "charges" argument to be passed.', WPI ), $args );

      //** If type is registered */
      if ( !array_key_exists( $type, $wpi_settings[ 'types' ] ) ) return new WP_Error( 'wp.invoice', __( 'Unknown invoice type.', WPI ), $args );

      //** If recurring */
      if ( $type == 'recurring' ) {
        $recurring = array_filter( $recurring );
        if ( empty( $recurring[ 'unit' ] ) || empty( $recurring[ 'cycles' ] ) ) return new WP_Error( 'wp.invoice', __( 'Method requires correct "recurring" argument if "type" is recurring.', WPI ), $args );
        if ( !empty( $deposit ) ) return new WP_Error( 'wp.invoice', __( 'Cannot use "deposit" with "recurring" type.', WPI ), $args );
      }

      //** If quote */
      if ( $type == 'quote' ) {
        if ( !empty( $deposit ) ) return new WP_Error( 'wp.invoice', __( 'Cannot use "deposit" with "quote" type.', WPI ), $args );
      }

      //** Check status */
      if ( !$status ) return new WP_Error( 'wp.invoice', __( 'Method requires "status" argument to be passed.', WPI ), $args );
      if ( !array_key_exists( $status, $wpi_settings[ 'invoice_statuses' ] ) ) return new WP_Error( 'wp.invoice', __( 'Unknown invoice status.', WPI ), $args );

      //** New Invoice object */
      $invoice = new UD_Invoice();

      //** Load invoice by ID */
      $invoice->create_new_invoice( $args );

      //** Set type */
      $invoice->set( array(
        'type' => $type
      ) );

      //** If quote */
      if ( $type == 'quote' ) {
        $invoice->set( array( 'status' => $type ) );
        $invoice->set( array( 'is_quote' => 'true' ) );
      }

      //** Recurring */
      if ( $type == 'recurring' ) {
        $invoice->create_schedule( $recurring );
      }

      //** Try loading user by email */
      $invoice->load_user( array(
        'email' => $user_data[ 'user_email' ]
      ) );

      //** If new user - add data to his object */
      if ( empty( $invoice->data[ 'user_data' ] ) ) {
        $invoice->data[ 'user_data' ] = $user_data;
      }

      //** Create/Update user if need */
      UD_Functions::update_user( $user_data );

      //** Try loading user by email again */
      $invoice->load_user( array(
        'email' => $user_data[ 'user_email' ]
      ) );

      //** Partial payments */
      if ( $deposit ) {
        $invoice->set( array( 'deposit_amount' => $deposit ) );
      } else {
        $invoice->set( array( 'deposit_amount' => 0 ) );
      }

      //** Due date */
      $invoice->set( array( 'due_date_year' => $due_date[ 'year' ] ) );
      $invoice->set( array( 'due_date_month' => $due_date[ 'month' ] ) );
      $invoice->set( array( 'due_date_day' => $due_date[ 'day' ] ) );

      //** Currency */
      $invoice->set( array( 'default_currency_code' => $currency ) );

      //** Tax */
      $invoice->set( array( 'tax' => $tax ) );

      //** Status */
      $invoice->set( array( 'post_status' => $status ) );

      //** Discount */
      $discount = array_filter( $discount );
      if ( !empty( $discount ) ) {
        if ( empty( $discount[ 'name' ] ) ) return new WP_Error( 'wp.invoice', __( 'Discount name is required.', WPI ), $args );
        if ( empty( $discount[ 'type' ] ) ) return new WP_Error( 'wp.invoice', __( 'Discount type is required. ("amount" or "percent").', WPI ), $args );
        if ( empty( $discount[ 'amount' ] ) ) return new WP_Error( 'wp.invoice', __( 'Discount amount is required.', WPI ), $args );
        $invoice->add_discount( $discount );
      }

      //** Items */
      foreach ( $items as $item ) {
        //** Do not allow to save melformed items */
        if ( empty( $item[ 'name' ] ) ||
          empty( $item[ 'quantity' ] ) ||
          empty( $item[ 'price' ] )
        ) {
          return new WP_Error( 'wp.invoice', __( 'One or more "items" have malformed structure. Cannot create Invoice.', WPI ), $args );
        }

        //** Global tax has higher priority */
        if ( !empty( $tax ) ) $item[ 'tax_rate' ] = $tax;

        //** Check types */
        if ( !is_numeric( $item[ 'quantity' ] ) ) return new WP_Error( 'wp.invoice', __( 'One or more "items" have wrong "quantity" value. Cannot create Invoice.', WPI ), $args );
        if ( !is_numeric( $item[ 'price' ] ) ) return new WP_Error( 'wp.invoice', __( 'One or more "items" have wrong "price" value. Cannot create Invoice.', WPI ), $args );
        if ( !empty( $item[ 'tax_rate' ] ) ) {
          if ( !is_numeric( $item[ 'tax_rate' ] ) ) return new WP_Error( 'wp.invoice', __( 'One or more "items" have wrong "tax_rate" value. Cannot create Invoice.', WPI ), $args );
        }

        //** If passed validation - save item */
        $invoice->line_item( $item );
      }

      //** Charges */
      foreach ( $charges as $charge ) {
        //** Do not allow to save melformed items */
        if ( empty( $charge[ 'name' ] ) ||
          empty( $charge[ 'amount' ] )
        ) {
          return new WP_Error( 'wp.invoice', __( 'One or more "charges" have malformed structure. Cannot create Invoice.', WPI ), $args );
        }

        //** Global tax has higher priority */
        if ( !empty( $tax ) ) $charge[ 'tax' ] = $tax;

        //** Check types */
        if ( !is_numeric( $charge[ 'amount' ] ) ) return new WP_Error( 'wp.invoice', __( 'One or more "charges" have wrong "amount" value. Cannot create Invoice.', WPI ), $args );
        if ( !empty( $charge[ 'tax' ] ) ) {
          if ( !is_numeric( $charge[ 'tax' ] ) ) return new WP_Error( 'wp.invoice', __( 'One or more "charges" have wrong "tax" value. Cannot create Invoice.', WPI ), $args );
        }

        //** If passed validation - save item */
        $invoice->line_charge( $charge );
      }

      //** Set tax method */
      if ( !empty( $tax_method ) ) {
        if ( $tax_method != 'before_discount' && $tax_method != 'after_discount' ) {
          return new WP_Error( 'wp.invoice', __( 'Unknown "tax_method".', WPI ), $args );
        }
      }
      $invoice->set( array( 'tax_method' => $tax_method ) );

      //** Save */
      $invoice->save_invoice();

      //** Return saved object */
      return $invoice;
    }

    /**
     * Refund invoice by ID
     *
     * @param type $args
     *
     * @return WP_Error|UD_Invoice
     */
    function refund_invoice( $args = array() ) {
      //** Defaults  */
      $defaults = array(
        'ID' => false
      );

      //** Parse arguments */
      extract( wp_parse_args( $args, $defaults ) );

      //** Check */
      if ( !$ID ) return new WP_Error( 'wp.invoice', __( 'Argument "ID" is required.', WPI ), $args );

      //** New Invoice object */
      $invoice = new UD_Invoice();

      //** Load invoice by ID */
      $invoice->load_invoice( array( 'id' => $ID ) );

      //** Check */
      if ( !empty( $invoice->error ) ) return new WP_Error( 'wp.invoice', __( 'Invoice not found', WPI ), $args );

      //** Do refund if it has payments */
      if ( empty( $invoice->data[ 'total_payments' ] ) ) return new WP_Error( 'wp.invoice', __( 'Cannot be refunded. No payments found.', WPI ), $args );

      $insert_id = $invoice->add_entry( array(
        'attribute' => 'balance',
        'note' => 'Refunded via XML-RPC',
        'amount' => (float) $invoice->data[ 'total_payments' ],
        'type' => 'refund'
      ) );
      if ( !$insert_id ) return new WP_Error( 'wp.invoice', __( 'Could not refund due to unknown error.', WPI ), $args );

      $invoice->save_invoice();

      //** Load again to get changes */
      $invoice = new UD_Invoice();
      $invoice->load_invoice( array( 'id' => $ID ) );

      return $invoice;
    }

    /**
     * Pay invoice by ID
     *
     * @param type $args
     *
     * @return WP_Error|UD_Invoice
     */
    function pay_invoice( $args = array() ) {
      //** Default arguments */
      $defaults = array(
        'ID' => false,
        'amount' => false
      );

      //** Parse arguments */
      extract( wp_parse_args( $args, $defaults ) );

      //** Check */
      if ( !$ID ) return new WP_Error( 'wp.invoice', __( 'Argument "ID" is required.', WPI ), $args );
      if ( !$amount ) return new WP_Error( 'wp.invoice', __( 'Argument "amount" is required.', WPI ), $args );
      if ( !is_numeric( $amount ) ) return new WP_Error( 'wp.invoice', __( 'Argument "amount" is malformed.', WPI ), $args );

      //** New Invoice object */
      $invoice = new UD_Invoice();

      //** Load invoice by ID */
      $invoice->load_invoice( array( 'id' => $ID ) );

      //** Check */
      if ( !empty( $invoice->error ) ) return new WP_Error( 'wp.invoice', __( 'Invoice not found', WPI ), $args );

      //** Pay only if status if not paid */
      if ( $invoice->data[ 'post_status' ] == 'paid' ) return new WP_Error( 'wp.invoice', __( 'Invoice is completely paid. Payments are not acceptable anymore.', WPI ), $args );

      //** Check amount */
      if ( (float) $invoice->data[ 'net' ] < (float) $amount ) return new WP_Error( 'wp.invoice', __( 'Cannot pay more that the balance is. Maximum is ' . $invoice->data[ 'net' ], WPI ), $args );

      //** Handle partial */
      if ( (float) $invoice->data[ 'net' ] > (float) $amount ) {
        if ( empty( $invoice->data[ 'deposit_amount' ] ) ) return new WP_Error( 'wp.invoice', __( 'Partial payments are not allowed. Pay minimum is ' . $invoice->data[ 'net' ], WPI ), $args );
        if ( (float) $amount < (float) $invoice->data[ 'deposit_amount' ] ) {
          return new WP_Error( 'wp.invoice', __( 'Minimum allowed payment is ' . $invoice->data[ 'deposit_amount' ], WPI ), $args );
        }
      }

      //** Add payment item */
      $invoice->add_entry( array(
        'attribute' => 'balance',
        'note' => 'Paid ' . ( (float) $amount ) . ' ' . $invoice->data[ 'default_currency_code' ] . ' via XML-RPC API',
        'amount' => (float) $amount,
        'type' => 'add_payment'
      ) );

      //** Save to be sure totals recalculated */
      $invoice->save_invoice();

      //** Load again to get changes */
      $invoice = new UD_Invoice();
      $invoice->load_invoice( array( 'id' => $ID ) );

      return $invoice;
    }

    /**
     * Delete Invoice by ID
     *
     * @param array $args
     *
     * @return bool
     */
    function delete_invoice( $args = array() ) {
      //** Default arguments */
      $defaults = array( 'ID' => false );

      //** Parse arguments */
      extract( wp_parse_args( $args, $defaults ) );

      //** Check */
      if ( !$ID ) return new WP_Error( 'wp.invoice', __( 'Argument "ID" is required.', WPI ), $args );

      //** New Invoice object */
      $invoice = new UD_Invoice();

      //** Load invoice by ID */
      $invoice->load_invoice( array( 'id' => $ID ) );

      //** Return result of delete method */
      return $invoice->delete();
    }

    /**
     * Returns invoice object requested by ID
     *
     * @param array $args
     *
     * @return UD_Invoice
     */
    function get_invoice( $args = array() ) {
      //** Default arguments */
      $defaults = array( 'ID' => false );

      //** Parse arguments */
      extract( wp_parse_args( $args, $defaults ) );

      //** Check */
      if ( !$ID ) return new WP_Error( 'wp.invoice', __( 'Argument "ID" is required.', WPI ), $args );

      //** New Invoice object */
      $invoice = new UD_Invoice();

      //** Load invoice by ID */
      $invoice->load_invoice( array( 'id' => $ID ) );

      //** Return ready object */
      return empty( $invoice->error ) ? $invoice : new WP_Error( 'wp.invoice', __( 'Invoice not found', WPI ), $args );
    }

    /**
     * Update invoice by ID
     *
     * @global Array $wpi_settings
     *
     * @param Array $args
     *
     * @return WP_Error|UD_Invoice
     */
    function update_invoice( $args = array() ) {
      global $wpi_settings;

      //** Default arguments */
      $defaults = array(
        'ID' => false,
        'subject' => false,
        'description' => false,
        'type' => false,
        'deposit' => false,
        'due_date' => false,
        'tax' => false,
        'tax_method' => false,
        'recurring' => false,
        'discount' => false,
        'items' => array(),
        'charges' => array()
      );

      //** Parse arguments */
      extract( $args = wp_parse_args( $args, $defaults ) );

      //** Check */
      if ( !$ID ) return new WP_Error( 'wp.invoice', __( 'Argument "ID" is required.', WPI ), $args );

      //** New Invoice object */
      $invoice = new UD_Invoice();

      //** Load invoice by ID */
      $invoice->load_invoice( array( 'id' => $ID ) );

      $set = array();

      //** Subject */
      if ( $subject ) {
        $subject = trim( $subject );
        if ( !empty( $subject ) ) {
          $set[ 'subject' ] = $subject;
          $set[ 'post_title' ] = $subject;
        }
      }

      //** Description */
      if ( $description ) {
        $description = trim( $description );
        if ( !empty( $description ) ) {
          $set[ 'description' ] = $description;
        }
      }

      if ( $type ) {
        //** If type is registered */
        if ( !array_key_exists( $type, $wpi_settings[ 'types' ] ) ) return new WP_Error( 'wp.invoice', __( 'Unknown invoice type.', WPI ), $args );

        //** If recurring */
        if ( $type == 'recurring' ) {
          $recurring = array_filter( $recurring );
          if ( empty( $recurring[ 'unit' ] ) || empty( $recurring[ 'cycles' ] ) ) return new WP_Error( 'wp.invoice', __( 'Method requires correct "recurring" argument if "type" is recurring.', WPI ), $args );
          if ( !empty( $deposit ) ) return new WP_Error( 'wp.invoice', __( 'Cannot use "deposit" with "recurring" type.', WPI ), $args );
        }

        //** If quote */
        if ( $type == 'quote' ) {
          if ( !empty( $deposit ) ) return new WP_Error( 'wp.invoice', __( 'Cannot use "deposit" with "quote" type.', WPI ), $args );
        }

        $set[ 'type' ] = $type;

        //** If quote */
        if ( $type == 'quote' ) {
          $set[ 'status' ] = $type;
          $set[ 'is_quote' ] = 'true';
        }

        //** Recurring */
        if ( $type == 'recurring' ) {
          $invoice->create_schedule( $recurring );
        }
      }

      //** Partial payments */
      if ( $deposit ) {
        $set[ 'deposit_amount' ] = (float) $deposit;
      }

      if ( $due_date ) {
        $set[ 'due_date_year' ] = $due_date[ 'year' ];
        $set[ 'due_date_month' ] = $due_date[ 'month' ];
        $set[ 'due_date_day' ] = $due_date[ 'day' ];
      }

      if ( $tax ) {
        $set[ 'tax' ] = $tax;
      }

      if ( $tax_method ) {
        if ( $tax_method != 'before_discount' && $tax_method != 'after_discount' ) {
          return new WP_Error( 'wp.invoice', __( 'Unknown "tax_method".', WPI ), $args );
        }
        $set[ 'tax_method' ] = $tax_method;
      }

      if ( $discount ) {
        if ( empty( $discount[ 'name' ] ) ) return new WP_Error( 'wp.invoice', __( 'Discount name is required.', WPI ), $args );
        if ( empty( $discount[ 'type' ] ) ) return new WP_Error( 'wp.invoice', __( 'Discount type is required. ("amount" or "percent").', WPI ), $args );
        if ( empty( $discount[ 'amount' ] ) ) return new WP_Error( 'wp.invoice', __( 'Discount amount is required.', WPI ), $args );
        $invoice->data[ 'discount' ] = array();
        $invoice->add_discount( $discount );
      }

      if ( $items ) {
        //** Items */
        foreach ( $items as $item ) {
          //** Do not allow to save melformed items */
          if ( empty( $item[ 'name' ] ) ||
            empty( $item[ 'quantity' ] ) ||
            empty( $item[ 'price' ] )
          ) {
            return new WP_Error( 'wp.invoice', __( 'One or more "items" have malformed structure. Cannot create Invoice.', WPI ), $args );
          }

          //** Global tax has higher priority */
          if ( !empty( $tax ) ) $item[ 'tax_rate' ] = $tax;

          //** Check types */
          if ( !is_numeric( $item[ 'quantity' ] ) ) return new WP_Error( 'wp.invoice', __( 'One or more "items" have wrong "quantity" value. Cannot create Invoice.', WPI ), $args );
          if ( !is_numeric( $item[ 'price' ] ) ) return new WP_Error( 'wp.invoice', __( 'One or more "items" have wrong "price" value. Cannot create Invoice.', WPI ), $args );
          if ( !empty( $item[ 'tax_rate' ] ) ) {
            if ( !is_numeric( $item[ 'tax_rate' ] ) ) return new WP_Error( 'wp.invoice', __( 'One or more "items" have wrong "tax_rate" value. Cannot create Invoice.', WPI ), $args );
          }
        }
      }

      if ( $charges ) {
        //** Charges */
        foreach ( $charges as $charge ) {
          //** Do not allow to save melformed items */
          if ( empty( $charge[ 'name' ] ) ||
            empty( $charge[ 'amount' ] )
          ) {
            return new WP_Error( 'wp.invoice', __( 'One or more "charges" have malformed structure. Cannot create Invoice.', WPI ), $args );
          }

          //** Global tax has higher priority */
          if ( !empty( $tax ) ) $charge[ 'tax' ] = $tax;

          //** Check types */
          if ( !is_numeric( $charge[ 'amount' ] ) ) return new WP_Error( 'wp.invoice', __( 'One or more "charges" have wrong "amount" value. Cannot create Invoice.', WPI ), $args );
          if ( !empty( $charge[ 'tax' ] ) ) {
            if ( !is_numeric( $charge[ 'tax' ] ) ) return new WP_Error( 'wp.invoice', __( 'One or more "charges" have wrong "tax" value. Cannot create Invoice.', WPI ), $args );
          }
        }
      }

      //** If passed validation - save item */
      if ( $charges ) {
        $invoice->data[ 'itemized_charges' ] = array();
        foreach ( $charges as $charge ) {
          $invoice->line_charge( $charge );
        }
      }
      if ( $items ) {
        $invoice->data[ 'itemized_list' ] = array();
        foreach ( $items as $item ) {
          $invoice->line_item( $item );
        }
      }

      $invoice->set( $set );

      $invoice->save_invoice();

      $invoice = new UD_Invoice();
      //** Load invoice by ID */
      $invoice->load_invoice( array( 'id' => $ID ) );

      return $invoice;
    }

  }

}