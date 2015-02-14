<?php
/**
 *
 */
namespace UsabilityDynamics\Cluster {

  if( !class_exists( 'UsabilityDynamics\Cluster\Controller' ) ) {

    /**
     *
     *
     */
    class Controller {

      /**
       * Post ID.
       *
       * @var int
       */
      public $ID;

      public static $keyPath = '/var/www/wp-content/static/ssh/wpcloud.pem';

      public static $port = 22;

      /**
       * Constructor.
       *
       * @param WP_Post $post Post object.
       */
      public function __construct( $post = array() ) {
        global $wpdb;

        foreach ( (array)  $post as $key => $value ) {
          $this->$key = $value;
        }

        if( isset( $this->post_title ) && $this->post_title ) {
          $this->ID = $wpdb->get_var( "SELECT ID FROM {$wpdb->posts} WHERE post_title='{$this->post_title}';" );
        }

        $this->ID = wp_insert_post(array(
          'ID' => $this->ID,
          'post_status' => 'publish',
          'post_excerpt' => $this->post_excerpt,
          'post_type' => $this->post_type,
          'guid' => $this->guid,
          'import_id' => $this->import_id,
          'post_content' => $this->post_content,
          'post_title' => $this->post_title
        ));

        foreach( (array) $this->meta as $key => $value ) {
          update_post_meta( $this->ID, $key, $value );
        }

        return $this;

      }

      /**
       *
       * UsabilityDynamics\Cluster\Controller::sendCommand( 'docker --host=unix:///var/run/dockerServices.sock exec controller controller --version' )
       * UsabilityDynamics\Cluster\Controller::sendCommand( 'docker ps' )
       * UsabilityDynamics\Cluster\Controller::sendCommand( 'docker --host=unix:///var/run/dockerServices.sock ps' )
       *
       * @param $command
       * @return mixed
       */
      static public function sendCommand( $command ) {

        $_controllers = apply_filters( 'wpCloud:controller:controllers', array() );

        $_key = md5(serialize( $command ));

        if( $_cached = get_transient( $_key ) ) {
          return $_cached;
        }

        $output = array();

        foreach( $_controllers as $controller ) {

          $commands[ $controller->_id ] = $_full_command = 'ssh -t -o StrictHostKeyChecking=no core@' . $controller->networkAddress . '  -i ' . self::$keyPath . ' -p ' . self::$port. ' ' . $command;

          $output[] = array(
            "command" => $_full_command,
            "output" => explode( "\n", trim( shell_exec( $_full_command ), "\n" ) )
          );

        }

        set_transient( $_key, $output, 15 );

        return $output;

      }

    }

  }

}