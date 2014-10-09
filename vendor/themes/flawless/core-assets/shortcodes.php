<?php
/**
 * Name: Theme Shortcodes
 * Description: Shortcodes for the Flawless theme.
 * Author: Usability Dynamics, Inc.
 * Version: 1.0
 * Copyright 2010 - 2012 Usability Dynamics, Inc.
 *
 */


add_action( 'flawless_content_type_added', array( 'flawless_shortcodes', 'flawless_content_type_added' ) );
add_action( 'flawless::init_lower', array( 'flawless_shortcodes', 'init' ) );

add_shortcode( 'code', array( 'flawless_shortcodes', 'code' ) );
add_shortcode( 'breadcrumbs', array( 'flawless_shortcodes', 'breadcrumbs' ) );
add_shortcode( 'current_year', array( 'flawless_shortcodes', 'current_year' ) );
add_shortcode( 'site_description', array( 'flawless_shortcodes', 'site_description' ) );
add_shortcode( 'post_link', array( 'flawless_shortcodes', 'post_link' ) );
add_shortcode( 'button', array( 'flawless_shortcodes', 'button' ) );
add_shortcode( 'get_permalink', array( 'flawless_shortcodes', 'get_permalink' ) );
add_shortcode( 'image_url', array( 'flawless_shortcodes', 'image_url' ) );
add_shortcode( 'pdf', array( 'flawless_shortcodes', 'google_docs_pdf' ) );
add_shortcode( 'placeholder', array( 'flawless_shortcodes', 'placeholder' ) );



class flawless_shortcodes {

  /**
   * Load shortcodes conditionally
   *
   * @since 0.2.5
   */
  static function init() {
    global $shortcode_tags;

    //** Load list-attachments shortcode if the List Attachments Shortcode plugin does not exist */
    if( !in_array( 'list-attachments', array_keys( $shortcode_tags ) ) ) {
      add_shortcode( 'list_attachments', array( 'flawless_shortcodes', 'list_attachments' ));
    }

  }


  /**
   * {}
   *
   * @version 1.25.0
   */
  function placeholder( $args = array() ) {

    $args = shortcode_atts( array(
      'width' => '100%',
      'height' => '100px',
      'class'  => 'flawless_placeholder'
    ), $args );

    if( is_admin() ) {
      return '[placeholder]';
    }

    return '<span class="' . $args[ 'class' ] . '" style="display:block;width:' . $args[ 'width' ] . ';line-height: ' . $args[ 'height' ] . ';height: ' . $args[ 'height' ] . ';">' . $args[ 'width' ] . ' X ' . $args[ 'height' ] . '</span>';

  }

  /**
   * Display list of attached files to a s post.
   *
   * Function ported over from List Attachments Shortcode plugin.
   *
   * @version 1.25.0
   */
  function list_attachments( $atts = array() ) {
    global $post, $wp_query;

    $r = '';

    if( !is_array( $atts ) ) {
      $atts = array();
    }

    $atts = array_merge( array(
      'type' => NULL,
      'orderby' => NULL,
      'groupby' => NULL,
      'order' => NULL,
      'post_id' => false,
      'before_list' => '',
      'after_list' => '',
      'opening' => '<ul class="attachment-list flawless_attachment_list">',
      'closing' => '</ul>',
      'before_item' => '<li>',
      'after_item' => '</li>',
      'show_descriptions' => true,
      'include_icon_classes' => true,
      'showsize' => false
    ), $atts );

    if(isset($atts['post_id']) && is_numeric($atts['post_id'])) {
      $post = get_post($atts['post_id']);
    }

    if(!$post) {
      return;
    }

    if( !empty( $atts['type'] ) ) {
      $types = explode( ',', str_replace( ' ', '', $atts['type'] ) );
    } else {
      $types = array();
    }

    $showsize = ( $atts['showsize'] == true || $atts['showsize'] == 'true' || $atts['showsize'] == 1 ) ? true : false;
    $upload_dir = wp_upload_dir();

    $op = clone $post;
    $oq = clone $wp_query;

    foreach( array( 'before_list', 'after_list', 'opening', 'closing', 'before_item', 'after_item' ) as $htmlItem ) {
      $atts[$htmlItem] = str_replace( array( '&lt;', '&gt;' ), array( '<', '>' ), $atts[$htmlItem] );
    }

    $args = array(
      'post_type' => 'attachment',
      'numberposts' => -1,
      'post_status' => null,
      'post_parent' => $post->ID,
    );

    if( !empty( $atts['orderby'] ) ) {
      $args['orderby'] = $atts['orderby'];
    }
    if( !empty( $atts['order'] ) ) {
      $atts['order'] = ( in_array( $atts['order'], array('a','asc','ascending') ) ) ? 'asc' : 'desc';
      $args['order'] = $atts['order'];
    }
    if( !empty( $atts['groupby'] ) ) {
      $args['orderby'] = $atts['groupby'];
    }

    $attachments = get_posts($args);

    if( $attachments ) {
      $grouper = $atts['groupby'];
      $test = $attachments;
      $test = array_shift( $test );
      if( !property_exists( $test, $grouper ) ) {
        $grouper = 'post_' . $grouper;
      }

      $attlist = array();

      foreach( $attachments as $att ) {
        $key = ( !empty( $atts['groupby'] ) ) ? $att->$grouper : $att->ID;
        $key .= ( !empty( $atts['orderby'] ) ) ? $att->$atts['orderby'] : '';

        $attlink = wp_get_attachment_url( $att->ID );

        if( count( $types ) ) {
          foreach( $types as $t ) {
            if( substr( $attlink, (0- strlen( '.' . $t ) ) ) == '.' . $t ) {
              $attlist[ $key ] = clone $att;
              $attlist[ $key ]->attlink = $attlink;
            }
          }
        }
        else {
          $attlist[ $key ] = clone $att;
          $attlist[ $key ]->attlink = $attlink;
        }
      }
      if( $atts['groupby'] ) {
        if( $atts['order'] == 'asc' ) {
          ksort( $attlist );
        }
        else {
          krsort( $attlist );
        }
      }
    }


    if( count( $attlist ) ) {
      $open = false;
      $r = $atts['before_list'] . $atts['opening'];
      foreach( $attlist as $att ) {

        $container_classes = array('attachment_container');

        //** Determine class to display for this file type */
        if($atts['include_icon_classes']) {

          switch($att->post_mime_type) {

            case 'application/zip':
              $class = 'zip';
            break;

            case 'vnd.ms-excel':
              $class = 'excel';
            break;

            case 'image/jpeg':
            case 'image/png':
            case 'image/gif':
            case 'image/bmp':
              $class = 'image';
            break;

            default:
              $class = 'default';
            break;
          }
        }

        $icon_class = ($class ? 'flawless_attachment_icon file-' . $class : false);

        //** Determine if description shuold be displayed, and if it is not empty */
        $echo_description  = ($atts['show_descriptions'] && !empty($att->post_content ) ? ' <span class="attachment_description"> ' . $att->post_content . ' </span> ' : false);

        $echo_title = ($att->post_excerpt ?  $att->post_excerpt :  __('View ', 'flawless') . apply_filters('the_title_attribute',$att->post_title));

        if($icon_class) {
          $container_classes[] = 'has_icon';
        }

        if(!empty($echo_description)) {
          $container_classes[] = 'has_description';
        }

        //** Add conditional classes if class is not already passed into container */
        if(!strpos($atts['before_item'], 'class')) {
          $this_before_item = str_replace('>', ' class="' . implode(' ', $container_classes) . '">', $atts['before_item']);
        }

        $echo_size = ( ( $showsize ) ? ' <span class="attachment-size">' . WPP_F::get_filesize( str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], $attlink ) ) . '</span>' : '' ) ;

        if( !empty( $atts['groupby'] ) && $current_group != $att->$grouper ) {
          if( $open ) {
            $r .= $atts['closing'] . $atts['after_item'];
            $open = false;
          }
          $r .= $atts['before_item'] . '<h3>' . $att->$grouper . '</h3>' . $atts['opening'];
          $open = true;
          $current_group = $att->$grouper;
        }
        $attlink = $att->attlink;
        $r .= $this_before_item . '<a href="' . $attlink .'" title="'.$echo_title.'" class="flawless_attachment ' . $icon_class . '">' . apply_filters('the_title',$att->post_title) . '</a>'  . $echo_size  . $echo_description . $atts['after_item'];
      }
      if( $open ) {
        $r .= $atts['closing'] . $atts['after_item'];
      }
      $r .= $atts['closing'] . $atts['after_list'];
    }

    $wp_query = clone $oq;
    $post = clone $op;

    return $r;

  }


  /**
   * Add custom shortcodes for various post types
   *
   * @since 0.2.5
   */
  function flawless_content_type_added( $args = false ) {
    global $flawless;

    if( !$args ) {
      return;
    }

    $post_type = get_post_type_object( $args['type'] );

    if( !$post_type->public || !$post_type->has_archive ) {
      return;
    }

    //** Check if post type has custom Root page */
    if( $flawless['post_types'][$args['type']]['root_page'] ) {
      $root_url = get_permalink( $flawless['post_types'][$args['type']]['root_page'] );
    } else {
      $root_url = get_bloginfo( 'url' ) . '/' . $post_type->rewrite['slug'] . '/';
    }

    $shortcode = "{$post_type->labels->name} URL";

    add_shortcode( $shortcode, create_function( '$atts, $content, $code, $the_url="' .  $root_url  . '"', ' return "$the_url"; ' ) );

    $flawless['documentation']['shortcodes'][$shortcode] = sprintf( __( 'Returns a URL to the main %1s page.', 'flawless' ), $post_type->labels->name );

  }

  /**
   * Execude only [code] shortcode
   * @param sting $content
   * @return string
   * @author odokienko@UD
   */
  function do_code_shortcode($content){
    global $shortcode_tags;

    // Back up current registered shortcodes and clear them all out
    $orig_shortcode_tags = $shortcode_tags;
    remove_all_shortcodes();
    add_shortcode( 'code', array( 'flawless_shortcodes', 'code' ) );

    // Do the shortcode (only the [code] one is registered)
    $content = do_shortcode( $content );

    // Put the original shortcodes back
    $shortcode_tags = $orig_shortcode_tags;

    return $content;
	}


  /**
   * @param sting $code
   * @return string
   * @author odokienko@UD
   */
  function flawless_stripslashes($code){
    $code=str_replace(array("\\\"", "\\\'"), array ('"', "'"),$code);
    $code=htmlspecialchars($code);
    $code=str_replace(array('<', '>'), array('&lt;', '&gt;'),$code);
    return $code;
  }



  /**
   * Convert code within [code] [/code] shortcode into printable code.
   *
   * @since 0.3.5
   * @example [code] ... [/code]
   * @example [code linenums=74] ... [/code]
   * @example [code linenums=74 lang=lang-html class="prettyprint" container="pre"] ... [/code]
   * @author odokienko@UD
   */
  function code( $args = false , $content = null ) {
    /**
     * !important: if we want makes this function working then we need to run "do_shortcode" twice.
     * The first time it must calls before all the rest filters (especially kses)
     * the second time - after them
     */

    /** if it is the first time */
    if (empty($args['second_run'])){
      /** we remember initial arguments */
      $old_args = array();

      foreach ((array)$args as $key=>$val){
        $old_args[]= "$key=\"$val\"";
      }
      /** do encode the body and add flag "second_run" and left shortcode as-is */
      $old_args[] = 'second_run=true';
      $content = "[code".(($old_args)?" ".implode(' ',$old_args):'')."]".base64_encode(trim($content))."[/code]";

    }else{
      /** at the second time add css and js */
      wp_enqueue_style('google-pretify');
      wp_enqueue_script('google-pretify');

      $args = shortcode_atts( array(
        'class' => 'prettyprint',
        'linenums' => false,
        'lang'  => false,
        'container'  => 'code'
      ), $args );

      /** and prepare the final looking (will not forget make decode of content) */
      $content = "<{$args['container']} class='{$args['class']}".(($args['linenums'])?" linenums:{$args['linenums']}":'').(($args['lang'])? " ".$args['lang'] :'')."'>".flawless_shortcodes::flawless_stripslashes(base64_decode($content))."</{$args['container']}>";

    }
    return $content;
  }


  /**
   * Prints out breadcrumbs to current page.
   *
   * @since 0.2.5
   *
   */
  function breadcrumbs( $atts = false ) {

    if( !function_exists( 'flawless_breadcrumbs' ) ) {
      return;
    }

    return flawless_breadcrumbs( array(
      'return' => true,
      'hide_breadcrumbs' => false
    ) );

  }


  /**
   * Returns current year.
   *
   * @since 0.2.5
   *
   */
  function current_year( $atts = false ) {
    return date( 'Y' );
  }


  /**
   * URL to an image in the library.
   *
   * Example: [image_url id=333 size=thumbnail]
   *
   * Size arguments can be any custom size, or the default: thumbnail, medium, large or full
   * @since 0.2.5
   */
  function image_url( $attr, $content ) {

    $args = wp_parse_args( $attr, array(
      'id' => false,
      'size' => 'full',
      'icon' => false
    ) );

    if( empty( $args['id'] ) ) {
      return;
    }

    $image_data = wp_get_attachment_image_src( $args['id'], $args['size'], $args['icon'] );

    $url = $image_data['0'];

    return $url;

  }


  /**
   * Render a PDF in a Google Docs viewer
   *
   * @since 0.2.5
   *
   */
  function google_docs_pdf( $attr, $content ) {
    $url = urlencode( $attr['url'] );
    return '<iframe src="http://docs.google.com/viewer?url=' . $url . ' &embedded=true" width="99%" height="800"></iframe>';
  }


  /**
   * Returns current year.
   *
   * @since 0.2.5
   *
   */
  function site_description( $atts = false ) {
    return get_bloginfo( 'description' );
  }


  /**
   * Shortcode function for getting a permalink to a specific post
   *
   * @since 0.1
   * @param array $attr Attributes attributed to the shortcode.
   */
  function post_link( $attr ) {
    $url = get_permalink( $attr['id'] );

    if( empty( $url ) ) {
      return;
    }

    if( empty( $attr['title'] ) ) {
      $attr['title'] = get_the_title( $attr['id'] );
    }

    return '<a href="'. $url . '" class="'. $attr['class'] . '">' . $attr['title']. '</a>';
  }

  /**
   * Shortcode function for getting buttons
   *
   * @since 0.2.5
   * @param array $attr Attributes attributed to the shortcode.
   */
  function button( $attr ) {
    $url = get_permalink( $attr['id'] );

    if( empty( $attr['id'] ) ) {
      $url = $attr['url'];
    }

    if( empty( $url ) ) {
      return;
    }

    if( empty( $attr['title'] ) ) {
      $attr['title'] = get_the_title( $attr['id'] );
    }

    return '<a href="'. $url . '" class="btn '. $attr['class'] . '"><i class="'. $attr['icon'] .'"></i>' . $attr['title']. '</a>';
  }


  /**
   * Shortcode function for getting a permalink to a specific post
   *
   * @since 0.1
   * @param array $attr Attributes attributed to the shortcode.
   */
  function get_permalink( $attr ) {
    return get_permalink( $attr['id'] );
  }

}
