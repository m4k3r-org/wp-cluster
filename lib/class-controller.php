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

      public static $port = 1134;

      private $networkAddress;

      /**
       * Constructor.
       * @param array $post
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
       * Send command to all clusters.
       *
       * UsabilityDynamics\Cluster\Controller::sendCommand( 'docker ps' )
       * UsabilityDynamics\Cluster\Controller::sendCommand( 'docker --host=unix:///var/run/dockerServices.sock exec controller controller --version' )
       * UsabilityDynamics\Cluster\Controller::sendCommand( 'docker --host=unix:///var/run/dockerServices.sock ps' )
       *
       * @param $command
       * @param array $args
       * @return mixed
       */
      static public function sendCommand( $command, $args = array() ) {

        $args = (object) wp_parse_args( (array) $args, array(
          "cache" => true,
          "ttl" => 15,
          "prefix" => apply_filters( 'wpCloud:controller:command:prefix', 'PATH=$PATH:/home/core/.bin' ),
          "controllers" => apply_filters( 'wpCloud:controller:controllers', array() )
        ));

        $_key = md5(serialize( $command ));

        if( $args->cache && $_cached = get_transient( $_key ) ) {
          foreach( $_cached as $_index => $response ) {
            $_cached[$_index]['_cached'] = true;
          }
          return $_cached;
        }

        $output = array();

        foreach( $args->controllers as $controller ) {

          $_id = is_object( $controller ) ? $controller->_id : $controller;

          // @todo Resolve controller address if just a name is provided.
          $_address = is_object( $controller ) ? $controller->networkAddress : 'localhost';

          $commands[ $_id ] = $_full_command = join( " ", array(
            'ssh',
            '-o StrictHostKeyChecking=no',
            'core@' . $_address,
            '-i ' . self::$keyPath,
            '-p ' . self::$port,
            $args->prefix,
            $command
          ));

          $output[] = array(
            "controller" => $_id,
            "command" => $_full_command,
            "output" => explode( "\n", trim( shell_exec( $_full_command ), "\n" ) )
          );

        }

        if( $args->cache ) {
          set_transient( $_key, $output, $args->ttl );
        }

        return $output;

      }

    }

  }

}