<?php

namespace WP_Spectacle;

class Shortcodes
{

  /**
   * Registers a highlighted shortcode.
   * Attribute -> type: notice, warning
   * Example: [spectacle_highlighted_note type="warning"]content here[/spectacle_highlighted_note]
   */
  public function register_highlighted_notes(){
    add_shortcode( 'spectacle_highlighted_note', function ( $atts, $content = null ){

      $attributes = shortcode_atts( array(
        'type' => 'notice'
      ), $atts );

      return '<aside class="' . esc_attr( $attributes[ 'type' ] ) . '">' . do_shortcode( $content ) . '</aside>';

    } );

    return $this;
  }

  /**
   * Unordered list shortcode, list items must be written on a new line
   * Example:
   * [spectacle_list]
   * list item 1
   * list item 2
   * [/spectacle_list]
   */
  public function register_list(){
    add_shortcode( 'spectacle_list', function ( $atts, $content = null ){

      $attributes = shortcode_atts( array(
        'title' => '',
        'background' => '',
        'equalize' => ''
      ), $atts );

      $content = do_shortcode( $content );
      $content = trim( $content );
      $content = str_replace( "\r\n", "\n", $content );
      $content = explode( "\n", $content );

      if ( isset( $atts['background'] ) && $atts['background'] == true ){

        $equalize_class = '';

        if ( isset( $atts['equalize'] ) && $atts['equalize'] == true ){
          $equalize_class = 'equaual-shorcode-list';
        }

        $html = '<ul class="is-background '. $equalize_class .'">';
      }
      else{
        $html = '<ul>';
      }

      for( $i = 0, $mi = count( $content ); $i < $mi; $i++ ){
        $html .= '<li>' . $content[ $i ] . '</li>';
      }

      $html .= '</ul>';

      return $html;
    } );

    return $this;
  }

  /**
   * List style content shortcode. It will look like the list item
   */
  public function register_list_content(){
    // Register the list content
    add_shortcode( 'spectacle_list_content', function ( $atts, $content = null ){

      return '<div class="spectacle_list_content"><div class="spectacle_list_content_inner">' . do_shortcode( $content ) . '</div></div>';

    } );

    // Register the heading inside the list content
    add_shortcode( 'spectacle_heading', function ( $atts ){

      $attributes = shortcode_atts( array(
        'title' => ''
      ), $atts );

      return '<h4 class="spectacle_heading">' . esc_attr( $attributes[ 'title' ] ) . '</h4>';

    } );

    return $this;
  }

  /**
   * Implements Spectacle tabs navigation and tab content
   * Example for showing the tab header:
   * [spectacle_tabs_navigation]
   * [spectacle_tab id="1" title="General Questions"]
   * [spectacle_tab id="2" title="Ticket Questions"]
   * [spectacle_tab id="3" title="Other Questions"]
   * [/spectacle_tabs_navigation]
   * Example for showing the tab content
   * [spectacle_tab_content id="1"]
   * Your content here
   * [/spectacle_tab_content]

   */
  public function register_tabs(){
    // Register the navigation container
    add_shortcode( 'spectacle_tabs_navigation', function ( $atts, $content = null ){

      return '<nav class="spectacle_navigation_header">' . do_shortcode( $content ) . '</nav>';

    } );

    // Register the navigation items
    add_shortcode( 'spectacle_tab', function ( $atts ){

      $attributes = shortcode_atts( array(
        'id' => '',
        'title' => ''
      ), $atts );

      return '<a href="#spectacle_tab_' . esc_attr( $attributes[ 'id' ] ) . '" class="spectacle_navigation_tab">' . esc_attr( $attributes[ 'title' ] ) . '</a>';

    } );

    // Register the tab content
    add_shortcode( 'spectacle_tab_content', function ( $atts, $content = null ){

      $attributes = shortcode_atts( array(
        'id' => '',
        'background' => ''
      ), $atts );

      $extra_class = '';

      if ( isset( $attributes['background'] ) && $atts['background'] == true ){
        $extra_class .= 'is-background';
      }

      return '<div id="spectacle_tab_' . esc_attr( $attributes[ 'id' ] ) . '" class="spectacle_tab_content ' . $extra_class  . ' ">' . do_shortcode( $content ) . '</div>';

    } );

    return $this;
  }

  /**
   * Wrapper shortcode for the artist lineup widget which is shown on the information page
   */
  public function register_info_page_artist_lineup_wrapper(){
    add_shortcode( 'spectacle_info_page_artist_lineup_wrapper', function ( $atts, $content = null ){

      if( $content === null ){
        return '';
      }

      $content = do_shortcode( $content );
      $content = trim( $content );

      $content = str_replace( "\r\n", "\n", $content );
      $content = explode( "\n", $content );
      foreach( $content as &$line ){
        $line = trim( $line );
      }
      $content = '[' . implode( ',', $content ) . ']';
      $content = json_decode( $content, true );
      $attributes = shortcode_atts( array(
        'buy_tickets_link' => ''
      ), $atts );

      if( isset( $content[ 0 ] ) ){
        $content[ 0 ][ 'class' ] = 'diamond-box-left';
        $content[ 0 ][ 'icon' ] = 'calendar';
      }
      if( isset( $content[ 1 ] ) ){
        $content[ 1 ][ 'class' ] = 'diamond-box-right';
        $content[ 1 ][ 'icon' ] = 'location';
      }

      $html = '<div class="diamond-box-container-widget">';

      $buy_tickets_html = '<a href="' . esc_attr( $attributes[ 'buy_tickets_link' ] ) . '" onclick="javascript:_gaq.push( [ \'_link\', \'' . esc_attr( $attributes[ 'buy_tickets_link' ] ) . '\' ] ); return false;" class="buy-tickets" target="_blank"><div class="inner">Buy<strong>Tickets</strong></div></a>';
      $diamond_box_html = '<div class="diamond-box ::diamond_box_class::"><div class="inner"><div class="icon-::icon::"></div><strong>::text1::</strong><div class="text2">::text2::</div><hr><div class="time">::text3::</div></div></div>';

      // Construct the html...

      $html .= $buy_tickets_html;

      for( $i = 0, $mi = count( $content ); $i < $mi; $i++ ){
        $tpl = $diamond_box_html;

        $tpl = str_replace( '::diamond_box_class::', $content[ $i ][ 'class' ], $tpl );
        $tpl = str_replace( '::icon::', $content[ $i ][ 'icon' ], $tpl );
        $tpl = str_replace( '::text1::', $content[ $i ][ 'data' ][ 'text1' ], $tpl );
        $tpl = str_replace( '::text2::', $content[ $i ][ 'data' ][ 'text2' ], $tpl );
        $tpl = str_replace( '::text3::', $content[ $i ][ 'data' ][ 'text3' ], $tpl );

        $html .= $tpl;
      }

      $html .= '</div>';

      return $html;
    } );

    return $this;
  }

  public function register_highlighted_background(){
    // Register the list content
    add_shortcode( 'spectacle_highlighted_background', function ( $atts, $content = null ){

      $attributes = shortcode_atts( array(
        'class' => ''
      ), $atts );

      return '<div class="spectacle_highlighted_background ' . esc_attr( $attributes[ 'class' ] ) . '">' . do_shortcode( $content ) . '</div>';

    } );

    return $this;
  }

  public function register_horizontal_line(){
    // Register the list content
    add_shortcode( 'hr', function (){

      return '<div class="horizontal_line"><hr></div>';

    } );

    return $this;
  }

  public function register_icon(){
    // Register the list content
    add_shortcode( 'spectacle_icon', function ( $atts ){

      $attributes = shortcode_atts( array(
        'name' => ''
      ), $atts );

      return '<span class="spectacle_icon spectacle_icon_' . esc_attr( $attributes[ 'name' ] ) . '"></span>';

    } );

    return $this;
  }

  public function register_spacer(){
    // Register the list content
    add_shortcode( 'spectacle_spacer', function ( $atts ){

      $attributes = shortcode_atts( array(
        'height' => ''
      ), $atts );

      return '<div class="spectacle_spacer clearfix" style="height:' . esc_attr( $attributes[ 'height' ] ) . 'px"></div>';

    } );

    return $this;
  }

  public function register_box(){
    // Register the list content
    add_shortcode( 'spectacle_box', function ( $atts, $content = null ){

      $attributes = shortcode_atts( array(
        'space' => 0
      ), $atts );

      return '<div class="spectacle_box clearfix" style="padding-left:' . esc_attr( $attributes[ 'space' ] ) . 'px; padding-right: ' . esc_attr( $attributes[ 'space' ] ) . 'px">' . do_shortcode( $content ) . '</div>';

    } );

    return $this;
  }

  public function register_share_counts(){
    add_shortcode( 'social_share_count', array( $this, 'shortcode_social_share_count' ) );
  }

  public function shortcode_social_share_count( $atts, $content = null ){

    $atts = shortcode_atts( array(

      'facebook' => false,
      'twitter' => false,
      'google_plus' => false,
      'pinterest' => false,
      'total' => false,
      'url' => ''

    ), $atts );

    // If no URL was specified
    if( empty( $atts[ 'url' ] ) ){
      return json_encode( false );
    }

    $shares = array(
      'twitter' => 0,
      'facebook' => 0,
      'google_plus' => 0,
      'pinterest' => 0,
      'total' => 0,
      'url' => null
    );

    if( (bool) $atts[ 'total' ] === true ){
      $shares = $this->_get_total_shares( $atts[ 'url' ] );
    } else{
      if( (bool) $atts[ 'facebook' ] === true ){
        $shares[ 'facebook' ] = $this->_get_facebook_shares( $atts[ 'url' ] );
      }
      if( (bool) $atts[ 'twitter' ] === true ){
        $shares[ 'twitter' ] = $this->_get_twitter_shares( $atts[ 'url' ] );
      }
      if( (bool) $atts[ 'google_plus' ] === true ){
        $shares[ 'google_plus' ] = $this->_get_google_plus_shares( $atts[ 'url' ] );
      }
      if( (bool) $atts[ 'pinterest' ] === true ){
        $shares[ 'pinterest' ] = $this->_get_pinterest_shares( $atts[ 'url' ] );
      }
    }

    $shares[ 'url' ] = $atts[ 'url' ];

    return json_encode( $shares );

  }

  private function _get_facebook_shares( $url ){
    $fb_url = 'http://api.facebook.com/restserver.php?method=links.getStats&format=json&urls=' . $url;

    $data = $this->_curl_get_data( $fb_url );
    $data = json_decode( $data, true );

    if( isset( $data[ 0 ][ 'total_count' ] ) ){
      $data = $data[ 0 ][ 'total_count' ];
    } else{
      $data = 0;
    }

    return $data;
  }

  private function _get_twitter_shares( $url ){
    $twitter_url = 'http://urls.api.twitter.com/1/urls/count.json?url=' . $url;

    $data = $this->_curl_get_data( $twitter_url );
    $data = json_decode( $data, true );

    if( isset( $data[ 'count' ] ) ){
      $data = $data[ 'count' ];
    } else{
      $data = 0;
    }

    return $data;
  }

  private function _get_google_plus_shares( $url ){
    $curl = curl_init();
    curl_setopt( $curl, CURLOPT_URL, "https://clients6.google.com/rpc" );
    curl_setopt( $curl, CURLOPT_POST, true );
    curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $curl, CURLOPT_POSTFIELDS, '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"' . rawurldecode( $url ) . '","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]' );
    curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $curl, CURLOPT_HTTPHEADER, array( 'Content-type: application/json' ) );

    $data = curl_exec( $curl );
    curl_close( $curl );

    $data = json_decode( $data, true );

    if( isset( $data[ 0 ][ 'result' ][ 'metadata' ][ 'globalCounts' ][ 'count' ] ) ){
      $data = $data[ 0 ][ 'result' ][ 'metadata' ][ 'globalCounts' ][ 'count' ];
    } else{
      $data = 0;
    }

    return $data;
  }

  private function _get_pinterest_shares( $url ){
    $pinterest_url = 'http://api.pinterest.com/v1/urls/count.json?url=' . $url;

    $data = $this->_curl_get_data( $pinterest_url );
    $data = preg_replace( '/^receiveCount((.*))$/', "\1", $data );
    $data = json_decode( $data, true );

    if( isset( $data[ 'count' ] ) ){
      $data = $data[ 'count' ];
    } else{
      $data = 0;
    }

    return $data;
  }

  private function _get_total_shares( $url ){
    $twitter = $this->_get_twitter_shares( $url );
    $facebook = $this->_get_facebook_shares( $url );
    $google_plus = $this->_get_google_plus_shares( $url );
    $pinterest = $this->_get_pinterest_shares( $url );

    $total = (int) $twitter + (int) $facebook + (int) $google_plus + (int) $pinterest;

    return array(
      'twitter' => $twitter,
      'facebook' => $facebook,
      'google_plus' => $google_plus,
      'pinterest' => $pinterest,
      'total' => $total
    );
  }

  private function _curl_get_data( $data_url ){
    $curl = curl_init();

    curl_setopt( $curl, CURLOPT_URL, $data_url );
    curl_setopt( $curl, CURLOPT_USERAGENT, $_SERVER[ 'HTTP_USER_AGENT' ] );
    curl_setopt( $curl, CURLOPT_FAILONERROR, 1 );
    curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, 1 );
    curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt( $curl, CURLOPT_TIMEOUT, 10 );

    $data = curl_exec( $curl );

    if( curl_error( $curl ) ){
      $data = false;
    }

    curl_close( $curl );

    return $data;
  }

}
