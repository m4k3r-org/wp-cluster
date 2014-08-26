<?php

namespace WP_Spectacle;

class Shortcodes
{

  /**
   * Registers a highlighted shortcode.
   * Attribute -> type: notice, warning
   *
   * Example: [spectacle_highlighted_note type="warning"]content here[/spectacle_highlighted_note]
   */
  public function register_highlighted_notes()
  {
    add_shortcode('spectacle_highlighted_note', function( $atts, $content = null){

      $attributes = shortcode_atts( [
        'type' => 'notice'
      ], $atts );

      return '<aside class="' .esc_attr( $attributes[ 'type' ] ) .'">' .do_shortcode($content) .'</aside>';

    });

    return $this;
  }

  /**
   * Unordered list shortcode, list items must be written on a new line
   *
   * Example:
   * [spectacle_list]
   * list item 1
   * list item 2
   * [/spectacle_list]
   */
  public function register_list()
  {
    add_shortcode( 'spectacle_list', function( $atts, $content = null ){

      $content = do_shortcode( $content );
      $content = trim($content);
      $content = str_replace("\r\n", "\n", $content);
      $content = explode( "\n", $content );

      $html = '<ul>';
      for ( $i = 0, $mi = count( $content ); $i < $mi; $i++ )
      {
        $html .= '<li>' .$content[ $i ] .'</li>';
      }

      $html .= '</ul>';

      return $html;
    });

    return $this;
  }

  /**
   * List style content shortcode. It will look like the list item
   */
  public function register_list_content()
  {
    // Register the list content
    add_shortcode('spectacle_list_content', function( $atts, $content = null){

      return '<div class="spectacle_list_content"><div class="spectacle_list_content_inner">' .do_shortcode( $content ) .'</div></div>';

    });

    // Register the heading inside the list content
    add_shortcode('spectacle_heading', function( $atts){

      $attributes = shortcode_atts( [
        'title' => ''
      ], $atts );

      return '<h4 class="spectacle_heading">' .esc_attr( $attributes[ 'title' ] ) .'</h4>';

    });

    return $this;
  }

  /**
   * Implements Spectacle tabs navigation and tab content
   *
   * Example for showing the tab header:
   *
   * [spectacle_tabs_navigation]
   * [spectacle_tab id="1" title="General Questions"]
   * [spectacle_tab id="2" title="Ticket Questions"]
   * [spectacle_tab id="3" title="Other Questions"]
   * [/spectacle_tabs_navigation]
   *
   *
   * Example for showing the tab content
   *
   * [spectacle_tab_content id="1"]
   * Your content here
   * [/spectacle_tab_content]
   *
   */
  public function register_tabs()
  {
    // Register the navigation container
    add_shortcode('spectacle_tabs_navigation', function( $atts, $content = null){

      return '<nav class="spectacle_navigation_header">' .do_shortcode( $content ) .'</nav>';

    });

    // Register the navigation items
    add_shortcode('spectacle_tab', function( $atts ){

      $attributes = shortcode_atts( [
        'id' => '',
        'title' => ''
      ], $atts );

      return '<a href="#spectacle_tab_' .esc_attr( $attributes[ 'id' ] ) .'" class="spectacle_navigation_tab">' .esc_attr( $attributes[ 'title' ] ) .'</a>';

    });

    // Register the tab content
    add_shortcode('spectacle_tab_content', function( $atts, $content = null){

      $attributes = shortcode_atts( [
        'id' => ''
      ], $atts );

      return '<div id="spectacle_tab_' .esc_attr( $attributes[ 'id' ] ) .'" class="spectacle_tab_content">' .do_shortcode( $content ) .'</div>';

    });


    return $this;
  }

  /**
   * Wrapper shortcode for the artist lineup widget which is shown on the information page
   */
  public function register_info_page_artist_lineup_wrapper()
  {
    add_shortcode('spectacle_info_page_artist_lineup_wrapper', function( $atts, $content = null){

      if ( $content === null )
      {
        return '';
      }

      $content = do_shortcode( $content );
      $content = trim($content);

      $content = str_replace("\r\n", "\n", $content);
      $content = explode( "\n", $content );
      foreach( $content as &$line ){
        $line = trim( $line );
      }
      $content = '[' . implode( ',', $content ) . ']';
      $content = json_decode( $content, true );
      $attributes = shortcode_atts( [
        'buy_tickets_link' => ''
      ], $atts );

      if ( isset( $content[0] ) ) { $content[0]['class'] = 'diamond-box-left'; }
      if ( isset( $content[1] ) ) { $content[1]['class'] = 'diamond-box-right'; }

      $html = '<div class="diamond-box-container-widget">';

      $buy_tickets_html = '<a href="' .esc_attr( $attributes[ 'buy_tickets_link' ]) .'" onclick="javascript:_gaq.push( [ \'_link\', \'' .esc_attr( $attributes[ 'buy_tickets_link' ] ) .'\' ] ); return false;" class="buy-tickets" target="_blank"><div class="inner">Buy<strong>Tickets</strong></div></a>';
      $diamond_box_html = '<div class="diamond-box ::diamond_box_class::"><div class="inner"><strong>::date_day::<br>::date::</strong>::location::<hr><div class="time">::time::</div></div></div>';


      // Construct the html...

      $html .= $buy_tickets_html;

      for ( $i = 0, $mi = count( $content ); $i < $mi; $i++ )
      {
        $tpl = $diamond_box_html;

        $tpl = str_replace('::diamond_box_class::', $content[ $i ][ 'class' ], $tpl);
        $tpl = str_replace('::date_day::', date('l', strtotime($content[ $i ][ 'data' ][ 'date' ])), $tpl);
        $tpl = str_replace('::date::', date('F jS', strtotime($content[ $i ][ 'data' ][ 'date' ])), $tpl);
        $tpl = str_replace('::location::', $content[ $i ][ 'data' ][ 'location' ], $tpl);
        $tpl = str_replace('::time::', $content[ $i ][ 'data' ][ 'time' ], $tpl);

        $html .= $tpl;
      }

      $html .= '</div>';


      return $html;
    });


    return $this;
  }

  public function register_highlighted_background()
  {
    // Register the list content
    add_shortcode('spectacle_highlighted_background', function( $atts, $content = null){

      $attributes = shortcode_atts( [
        'class' => ''
      ], $atts );

      return '<div class="spectacle_highlighted_background ' .esc_attr( $attributes[ 'class' ] ) .'">' .do_shortcode( $content ) .'</div>';

    });

    return $this;
  }

  public function register_horizontal_line()
  {
    // Register the list content
    add_shortcode('hr', function(){

      return '<div class="horizontal_line"><hr></div>';

    });

    return $this;
  }

  public function register_icon()
  {
    // Register the list content
    add_shortcode('spectacle_icon', function( $atts ){

      $attributes = shortcode_atts( [
        'name' => ''
      ], $atts );

      return '<span class="spectacle_icon spectacle_icon_' .esc_attr( $attributes[ 'name' ] ) .'"></span>';

    });

    return $this;
  }


  public function register_spacer()
  {
    // Register the list content
    add_shortcode('spectacle_spacer', function( $atts ){

      $attributes = shortcode_atts( [
        'height' => ''
      ], $atts );

      return '<div class="spectacle_spacer clearfix" style="height:' .esc_attr( $attributes[ 'height' ]) .'px"></div>';

    });

    return $this;
  }

  public function register_box()
  {
    // Register the list content
    add_shortcode('spectacle_box', function( $atts, $content = null ){

      $attributes = shortcode_atts( [
        'space' => 0
      ], $atts );

      return '<div class="spectacle_box clearfix" style="padding-left:' .esc_attr( $attributes[ 'space' ]) .'px; padding-right: ' .esc_attr( $attributes[ 'space' ]) .'px">' .do_shortcode( $content ) .'</div>';

    });

    return $this;
  }

}
