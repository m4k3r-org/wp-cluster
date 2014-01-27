<?php
/**
 * Get data from Social Networks and put it to local storage
 *
 * @version 0.1.0
 * @author Usability Dynamics
 * @namespace UsabilityDynamics
 */

namespace UsabilityDynamics\Theme {

  if( !class_exists( 'UsabilityDynamics\Theme\Sync_Twitter' ) ) {

    /**
     * Twitter Sync
     */
    class Sync_Twitter extends Sync_Stream {

      /**
       * Mapping
       * Should be used in sync() function to insert new post item
       * @var type
       */
      public $mapping = array(
        'post_title' => 'Tweet #[id_str]. Author [user.name]',
        'attachments' => array( '[entities.media:media_url]', 'media' ),
        '_ss_network' => 'twitter',
        '_ss_id' => '[id_str]',
        '_ss_content' => '[text]',
        '_ss_created_at' => array( '[created_at]', 'date' ),
        '_ss_author' => '[user.screen_name]',
        '_ss_url' => 'https://twitter.com/[user.screen_name]/status/[id_str]',
      );

      /**
       * Request
       *
       */
      public function do_request() {

        $settings = array_filter( shortcode_atts( array(
          'oauth_access_token' => false,
          'oauth_access_token_secret' => false,
          'consumer_key' => false,
          'consumer_secret' => false,
        ), $this->oauth ) );
        
        if( count( $settings ) < 4 ) {
          throw new \Exception( 'Credentials are invalid' );
        }
        
        $request = array_filter( wp_parse_args( $this->request, array(
          'screen_name' => false,
        ) ) );
        
        if( empty( $request[ 'screen_name' ] ) ) {
          throw new \Exception( 'Request is invalid. screen_name param is missed' );
        }
        
        if ( $last_twitter_id = get_option( "sync_stream::{$this->id}::from" ) ) {
          //$request[ 'since_id' ] = $last_twitter_id;
        }
        
        $request = '?' . http_build_query( $request );
        
        //echo "<pre>"; var_dump( $request ); echo "</pre>"; die();

        $twitter = new \TwitterAPIExchange( $settings );
        $response = $twitter->setGetfield( $request )
                            ->buildOauth( 'https://api.twitter.com/1.1/statuses/user_timeline.json', 'GET' )
                            ->performRequest();

        $this->results = json_decode( $response, true );
        
        if( empty( $this->results ) || !is_array( $this->results ) ) {
          throw new \Exception( 'Could not get results' );
        }
        
        if( isset( $this->results[0][ 'id_str' ] ) ) {
          update_option( "sync_stream::{$this->id}::from", $this->results[0][ 'id_str' ] );
        }

      }

    }

  }
  
}