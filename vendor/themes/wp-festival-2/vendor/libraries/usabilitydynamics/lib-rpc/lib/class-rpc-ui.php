<?php
namespace UsabilityDynamics\RPC {

  if( !class_exists( 'UsabilityDynamics\RPC\UI' ) ) {
    /**
     * Class that is responsible for API UI
     */
    class UI {

      /**
       * End point
       *
       * @var type
       */
      private $server;

      /**
       * Construct
       *
       * @param type $namespace
       */
      function __construct( $parent_object ) {
        $this->server = $parent_object;
        add_action( 'wp_ajax_' . $this->server->namespace . '_ud_api_save_keys', array( $this, 'ud_api_save_keys' ) );
      }

      /**
       * Use this for rendering API Keys fields anywhere.
       *
       * @param type $args
       *
       * @return type
       */
      function render_api_fields( $args = array() ) {

        wp_enqueue_script( 'jquery' );

        $defaults = array(
          'return'              => false,
          'input_class'         => 'ud_api_input',
          'container'           => 'div',
          'container_class'     => 'ud_api_credentials',
          'input_wrapper'       => 'div',
          'input_wrapper_class' => 'ud_api_field',
          'public_key_label'    => 'Public Key',
          'before'              => '',
          'after'               => ''
        );

        extract( wp_parse_args( $args, $defaults ) );

        ob_start();
        echo $before;
        ?>

        <script type="text/javascript">
            jQuery( document ).ready( function() {
              jQuery( '.up_api_keys .ud_api_keys_save' ).on( 'click', function( e ) {
                jQuery( '.up_api_keys .ud_api_message' ).empty();

                var data = {
                  action: '<?php echo $this->server->namespace ?>_ud_api_save_keys',
                  <?php echo $this->server->namespace ?>_api_public_key: jQuery( '[name="<?php echo $this->server->namespace ?>_api_public_key"]' ).val()
                };

                jQuery.ajax( ajaxurl, {
                  dataType: 'json',
                  type: 'post',
                  data: data,
                  success: function( data ) {
                    jQuery.each( data.message, function( key, value ) {
                      jQuery( '.up_api_keys .ud_api_message' ).append( '<p>' + value + '</p>' );
                    } );
                  }
                } );
              } );
            } );
          </script>

        <<?php echo $container; ?> class="<?php echo $container_class; ?> up_api_keys">

        <<?php echo $input_wrapper; ?> class="<?php echo $input_wrapper_class; ?>">
        <label for="<?php echo $this->server->namespace ?>_api_public_key"><?php echo $public_key_label; ?></label>
        <input id="<?php echo $this->server->namespace ?>_api_public_key" value="<?php echo get_option( $this->server->namespace . '_api_public_key', '' ); ?>" name="<?php echo $this->server->namespace ?>_api_public_key"/>
        </<?php echo $input_wrapper; ?>>

        <input class="ud_api_keys_save" type="button" value="Save"/>

        <span class="ud_api_message"></span>

        </<?php echo $container; ?>>

        <?php
        echo $after;
        $html = apply_filters( $this->server->namespace . '_ud_api_ui', ob_get_clean() );

        if( $return ) return $html;
        echo $html;
      }

      /**
       * Save API keys
       */
      function ud_api_save_keys() {
        $result  = array();
        $success = false;

        //** Save option for current namespace */
        if( update_option( $this->server->namespace . '_api_public_key', $_POST[ $this->server->namespace . '_api_public_key' ] ) ) {
          $result[ ] = 'Public Key has been updated.';
          $success   = true;
        }

        //** Meant if is not registered yet */
        if( 1 ) {
          $c = new XMLRPC_CLIENT(
            $this->server->endpoint,
            get_option( $this->server->namespace . '_api_public_key' ),
            $this->server->useragent, array(), false, 80, 15, true
          );

          /**
           * @todo: Need to get secret key from the server using 'register' method.
           * Then save it in {namespace}._api_public_key option and remember that site was already registered.
           */
          $registered = $c->register();
          /**
           * @todo
           */
        }

        if( empty( $result ) ) {
          $result[ ] = 'Nothing has been updated.';
        }

        die( json_encode( array(
          'success' => $success,
          'message' => $result
        ) ) );
      }
    }
  }

}