<?php

namespace WP_Spectacle\Widgets;

/**
 * Class ArtistLineup
 * @package WP_Spectacle\Widgets
 * Artist Lineup Widget
 */
class Winner extends \WP_Widget
{

  private $_mustache_engine = null;

  public function __construct(){
    parent::__construct( 'wp_spectacle_winner_widget', __( 'Winner', 'wp_spectacle_widget_domain' ), [
      'description' => __( 'Part of WP Spectacle', 'wp_spectacle_widget_domain' )
    ] );

    // Set up the mustache engine
    $this->_mustache_engine = new \Mustache_Engine( [
      'loader' => new \Mustache_Loader_FilesystemLoader( dirname( __FILE__ ) . '/templates' ),
      'escape' => function ( $value ){
          return esc_attr( $value );
        },
      'strict_callables' => true
    ] );
  }

  /**
   * Display the widget.
   *
   * @param array $args
   * @param array $instance
   */
  public function widget( $args, $instance ){

    require_once 'socials.php';

    $data = [ ];

    for( $i = 0, $mi = count( $instance[ 'urls' ] ); $i < $mi; $i++ ){
      $current_data = [ ];

      if( strpos( $instance[ 'urls' ][ $i ], 'instagram.com/p/' ) !== false ){

        $api = file_get_contents( "http://api.instagram.com/oembed?url=" . $instance[ 'urls' ][ $i ] );
        $embed = json_decode( $api, true );

        if( $embed === null ){
          continue;
        }

        $media_id = isset( $embed[ 'media_id' ] ) ? $embed[ 'media_id' ] : null;
        $current_data[ 'title' ] = isset( $embed[ 'title' ] ) ? $embed[ 'title' ] : null;
        $current_data[ 'author_name' ] = isset( $embed[ 'author_name' ] ) ? $embed[ 'author_name' ] : null;
        $current_data[ 'image_url' ] = isset( $embed[ 'url' ] ) ? $embed[ 'url' ] : null;

        if( isset( $media_id ) ){
          $json_media = file_get_contents( "https://api.instagram.com/v1/media/" . $media_id . "23?access_token=" . WP_SOCIAL_STREAM_INSTAGRAM_ACCESS_TOKEN );
          $media_data = json_decode( $json_media, true );

          if( isset( $media_data[ 'meta' ][ 'code' ] ) && $media_data[ 'meta' ][ 'code' ] == '200' ){
            $current_data[ 'created_time' ] = isset( $media_data[ 'data' ][ 'created_time' ] ) ? $media_data[ 'data' ][ 'created_time' ] : null;
            $current_data[ 'author_profile_picture' ] = isset( $media_data[ 'data' ][ 'user' ][ 'profile_picture' ] ) ? $media_data[ 'data' ][ 'user' ][ 'profile_picture' ] : null;
          }

        }
        $data[ ] = $current_data;

      } elseif( strpos( $instance[ 'urls' ][ $i ], 'twitter.com/' ) !== false ){

        $exploded_url = explode( '/', $instance[ 'urls' ][ $i ] );

        $id = $exploded_url[ count( $exploded_url ) - 1 ];

        $tweet = $twitter->get( 'statuses/show/' . $id );

        if( isset( $tweet->errors ) ){
          continue;
        }

        $current_data[ 'title' ] = isset( $tweet->text ) ? $tweet->text : null;
        $current_data[ 'author_name' ] = isset( $tweet->user->name ) ? $tweet->user->name : null;
        $current_data[ 'author_profile_picture' ] = isset( $tweet->user->profile_image_url ) ? $tweet->user->profile_image_url : null;
        $current_data[ 'created_time' ] = isset( $tweet->created_at ) ? strtotime( $tweet->created_at ) : null;
        $current_data[ 'image_url' ] = null;

        $data[ ] = $current_data;

      } elseif( strpos( $instance[ 'urls' ][ $i ], 'youtube.com/' ) !== false ){

        $exploded_url = explode( '?v=', $instance[ 'urls' ][ $i ] );

        $id = $exploded_url[ count( $exploded_url ) - 1 ];

        $youtube_data = file_get_contents( "https://gdata.youtube.com/feeds/api/videos/" . $id . "?v=2&alt=jsonc&prettyprint=true" );

        if( $youtube_data === false ){
          continue;
        }

        $youtube_array = json_decode( $youtube_data, true );

        $current_data[ 'title' ] = isset( $youtube_array[ 'data' ][ 'title' ] ) ? $youtube_array[ 'data' ][ 'title' ] : null;
        $current_data[ 'created_time' ] = isset( $youtube_array[ 'data' ][ 'uploaded' ] ) ? strtotime( $youtube_array[ 'data' ][ 'uploaded' ] ) : null;
        $current_data[ 'image_url' ] = isset( $youtube_array[ 'data' ][ 'thumbnail' ][ 'hqDefault' ] ) ? $youtube_array[ 'data' ][ 'thumbnail' ][ 'hqDefault' ] : null;

        $author_data = file_get_contents( 'https://gdata.youtube.com/feeds/api/users/' . $youtube_array[ 'data' ][ 'uploader' ] . '?v=2&alt=json' );

        $author_array = json_decode( $author_data, true );

        $current_data[ 'author_name' ] = isset( $author_array[ 'entry' ][ 'title' ][ '$t' ] ) ? $author_array[ 'entry' ][ 'title' ][ '$t' ] : null;
        $current_data[ 'author_profile_picture' ] = isset( $author_array[ 'entry' ][ 'media$thumbnail' ][ 'url' ] ) ? $author_array[ 'entry' ][ 'media$thumbnail' ][ 'url' ] : null;

        $data[ ] = $current_data;

      }

    }

    echo $this->_mustache_engine->render( 'json', [
      'data' => json_encode( $data )
    ] );
  }

  /**
   * Admin form for the widget
   *
   * @param array $instance
   *
   * @return string|void
   */
  public function form( $instance ){

    $data[ 'urls' ] = [ ];

    if( isset( $instance[ 'urls' ] ) && is_array( $instance[ 'urls' ] ) && !empty( $instance[ 'urls' ] ) ){
      for( $i = 0, $mi = count( $instance[ 'urls' ] ); $i < $mi; $i++ ){
        $data[ 'urls' ][ ] = [ 'value' => $instance[ 'urls' ][ $i ] ];
      }
    } else{
      $data[ 'urls' ][ ] = [ [ 'value' => '' ] ];
    }

    // Populate the template data
    $data = [
      'urls_id' => $this->get_field_id( 'urls' ),
      'urls_name' => $this->get_field_name( 'urls' ),
      'urls' => $data[ 'urls' ]
    ];

    echo $this->_mustache_engine->render( 'admin-form', $data );
  }

  /**
   * Save the admin form.
   *
   * @param array $new_instance
   * @param array $old_instance
   *
   * @return array
   */
  public function update( $new_instance, $old_instance ){
    $instance = array();

    $instance[ 'urls' ] = ( !empty( $new_instance[ 'urls' ] ) ) ? $new_instance[ 'urls' ] : [ ];

    if( isset( $instance[ 'urls' ] ) && is_array( $instance[ 'urls' ] ) && !empty( $instance[ 'urls' ] ) ){
      for( $i = 0, $mi = count( $instance[ 'urls' ] ); $i < $mi; $i++ ){

        if( empty( $instance[ 'urls' ][ $i ] ) ){
          unset( $instance[ 'urls' ][ $i ] );
        }
      }
    }

    $instance[ 'urls' ] = array_values( $instance[ 'urls' ] );

    return $instance;
  }
}