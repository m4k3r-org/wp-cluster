<?php
/**
 * Name: Template Functions
 * Description: Functions meant to be used on frontend.
 * Author: Usability Dynamics, Inc.
 * Version: 1.0
 * Copyright 2010 - 2012 Usability Dynamics, Inc.
 *
 */

if( !function_exists( 'flawless_comment_form' ) ) {

  /**
   * Outputs a complete commenting form for use within a template.
   * Most strings and form fields may be controlled through the $args array passed
   * into the function, while you may also choose to use the comment_form_default_fields
   * filter to modify the array of default fields if you'd just like to add a new
   * one or remove a single field. All fields are also individually passed through
   * a filter of the form comment_form_field_$name where $name is the key used
   * in the array of fields.
   *
   * @since 3.0.0
   * @param array $args Options for strings, fields etc in the form
   * @param mixed $post_id Post ID to generate the form for, uses the current post if null
   * @return void
   */
  function flawless_comment_form( $args = array(), $post_id = null ) {
    global $id;

    if ( null === $post_id )
      $post_id = $id;
    else
      $id = $post_id;

    if( !comments_open() ) {
      do_action( 'comment_form_comments_closed' );
      return;
    }

    $commenter = wp_get_current_commenter();
    $user = wp_get_current_user();
    $user_identity = ! empty( $user->ID ) ? $user->display_name : '';

    $req = get_option( 'require_name_email' );

    $aria_req = ( $req ? " aria-required='true'" : '' );
    $fields =  array(
      'author' => '<p class="comment-form-author">' . '<label for="author">' . __( 'Name' ) . '</label> ' . ( $req ? '<span class="required">*</span>' : '' ) .
        '<input id="author" placeholder="Your Name" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' /></p>',
      'email'  => '<p class="comment-form-email"><label for="email">' . __( 'Email' ) . '</label> ' . ( $req ? '<span class="required">*</span>' : '' ) .
        '<input id="email" placeholder="Your Email Address" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' /></p>',
      'url'    => '<p class="comment-form-url"><label for="url">' . __( 'Website' ) . '</label>' .
        '<input id="url" placeholder="Website (optional)" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" /></p>',
    );

    //$required_text = sprintf( ' ' . __( 'Required fields are marked %s' ), '<span class="required">*</span>' );

    $defaults = array(
      'fields'               => apply_filters( 'comment_form_default_fields', $fields ),
      'comment_field'        => '<p class="comment-form-comment"><label for="comment">' . _x( 'Comment', 'noun' ) . '</label><textarea id="comment" placeholder="Your comment goes here." name="comment" cols="45" rows="8" aria-required="true"></textarea></p>',
      'must_log_in'          => '<p class="must-log-in">' . sprintf( __( 'You must be <a href="%s">logged in</a> to post a comment.' ), wp_login_url( apply_filters( 'the_permalink', get_permalink( $post_id ) ) ) ) . '</p>',
      'logged_in_as'         => '<p class="logged-in-as">' . sprintf( __( 'Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>' ), admin_url( 'profile.php' ), $user_identity, wp_logout_url( apply_filters( 'the_permalink', get_permalink( $post_id ) ) ) ) . '</p>',
      'comment_notes_before' => '<p class="comment-notes">' . __( 'Your email address will not be published.' ) . ( $req ? $required_text : '' ) . '</p>',
      'comment_notes_after'  => '<p class="form-allowed-tags">' . sprintf( __( 'You may use these <abbr title="HyperText Markup Language">HTML</abbr> tags and attributes: %s' ), ' <code>' . allowed_tags() . '</code>' ) . '</p>',
      'id_form'              => 'flawless_comment_form',
      'id_submit'            => 'submit',
      'title_reply'          => __( 'Leave a Reply' ),
      'title_reply_to'       => __( 'Leave a Reply to %s' ),
      'cancel_reply_link'    => __( 'Cancel reply' ),
      'label_submit'         => __( 'Post Comment' ),
    );

    $args = wp_parse_args( $args, apply_filters( 'comment_form_defaults', $defaults )); ?>

    <?php do_action( 'comment_form_before' ); ?>
    <div class="wp_comment_form_container">
      <h3 class="reply-title"><?php comment_form_title( $args['title_reply'], $args['title_reply_to'] ); ?> <small><?php cancel_comment_reply_link( $args['cancel_reply_link'] ); ?></small></h3>
      <?php if ( get_option( 'comment_registration' ) && !is_user_logged_in() ) : ?>
        <?php echo $args['must_log_in']; ?>
        <?php do_action( 'comment_form_must_log_in_after' ); ?>
      <?php else : ?>
        <form class="flawless_comment_form" action="<?php echo site_url( '/wp-comments-post.php' ); ?>" method="post" id="<?php echo esc_attr( $args['id_form'] ); ?>">
          <?php do_action( 'comment_form_top' ); ?>
          <?php if ( is_user_logged_in() ) : ?>
            <?php echo apply_filters( 'comment_form_logged_in', $args['logged_in_as'], $commenter, $user_identity ); ?>
            <?php do_action( 'comment_form_logged_in_after', $commenter, $user_identity ); ?>
          <?php else : ?>
            <?php echo $args['comment_notes_before']; ?>
            <?php
            do_action( 'comment_form_before_fields' );
            foreach ( ( array ) $args['fields'] as $name => $field ) {
              echo apply_filters( "comment_form_field_{$name}", $field ) . "\n";
            }
            do_action( 'comment_form_after_fields' );
            ?>
          <?php endif; ?>
          <?php echo apply_filters( 'comment_form_field_comment', $args['comment_field'] ); ?>
          <?php echo $args['comment_notes_after']; ?>
          <p class="form-submit">
            <input name="submit" class="btn btn-info" type="submit" id="<?php echo esc_attr( $args['id_submit'] ); ?>" value="<?php echo esc_attr( $args['label_submit'] ); ?>" />
            <?php comment_id_fields( $post_id ); ?>
          </p>
          <?php do_action( 'comment_form', $post_id ); ?>
        </form>
      <?php endif; ?>
    </div>
    <?php do_action( 'comment_form_after' ); ?>
  <?php
  }
}


if( !function_exists( 'get_term_attachment_image' ) ) {
  /**
   * Generate page title to be displayed in a template.
   *
   * @author potanin@UD
   */
  function get_term_attachment_image( $term_id = '', $size = 'thumbnail', $icon = false, $attr = '' ) {

    if ( !$term_id && ( is_tax() || is_tag() || is_category() ) ) {
      $term_id = get_queried_object()->term_id;
    }

    return wp_get_attachment_image( get_post_thumbnail_id( get_post_for_extended_term( $term_id )->ID ), $size, $icon , $attr );
  }
}


if( !function_exists( 'flawless_page_title' ) ) {
  /**
   * Generate page title to be displayed in a template.
   *
   * Based off wp_title(), but without separation.
   *
   * @version 0.1
   * @todo integrate get_the_title();
   * @author potanin@UD
   */
  function flawless_page_title( $args = '') {
    global $wpdb, $wp_locale, $post;

    $args = wp_parse_args( $args, array(
      'before' => '<h1 class="entry-title">',
      'after' => '</h1>',
      'link' => false
    ));

    if( hide_page_title() ) {
      return;
    }

    $m = get_query_var( 'm' );
    $year = get_query_var( 'year' );
    $monthnum = get_query_var( 'monthnum' );
    $day = get_query_var( 'day' );
    $search = get_query_var( 's' );

    $t_sep = '%WP_TITILE_SEP%'; // Temporary separator, for accurate flipping, if necessary

    // If there is a post
    if ( $post->post_title ) {
      $title =  $post->post_title;
    }

    // If there's a category or tag
    if ( !$title && ( is_category() || is_tag() ) ) {
      $title = single_term_title( '', false );
    }

    // If there's a taxonomy
    if ( !$title && is_tax() ) {
      $term = get_queried_object();
      $tax = get_taxonomy( $term->taxonomy );
      $title = single_term_title( $tax->labels->name . $t_sep, false );
    }

    // If there's an author
    if ( !$title  && is_author() ) {
      $author = get_queried_object();
      $title = $author->display_name;
    }

    // If there's a post type archive
    if ( !$title  && is_post_type_archive() )
      $title = post_type_archive_title( '', false );

    // If there's a month
    if ( !$title && ( is_archive() && !empty( $m ) )  ) {
      $my_year = substr( $m, 0, 4 );
      $my_month = $wp_locale->get_month( substr( $m, 4, 2 ));
      $my_day = intval( substr( $m, 6, 2 ));
      $title = $my_year . ( $my_month ? $t_sep . $my_month : '' ) . ( $my_day ? $t_sep . $my_day : '' );
    }

    // If there's a year
    if ( !$title  && ( is_archive() && !empty( $year ) ) ) {
      $title = $year;
      if ( !empty( $monthnum ) )
        $title .= $t_sep . $wp_locale->get_month( $monthnum );
      if ( !empty( $day ) )
        $title .= $t_sep . zeroise( $day, 2 );
    }

    // If it's a search
    if ( !$title && is_search() ) {

      if (trim(get_search_query()) == '' ) {
        $title =  __( 'Showing All Results', 'flawless' );
      } else {
        $title =  sprintf( __( 'Search: %s', 'flawless' ), '<span>' . get_search_query() . '</span>' );
      }

    }

    if ( !$title && is_404() ) {
      $title = __( 'Page not found' );
    }

    if( $args[ 'link' ] ) {
      $title = '<a href="' . apply_filters('the_permalink', get_permalink()) . '"  alt="' . $title . '" rel="bookmark">' . $title  . '</a>';
    }

    $title = $args[ 'before' ] . $title . $args[ 'after' ];

    $title = apply_filters( 'flawless::page_title', $title, array(
      'title' =>  $title,
      'before' =>  $before,
      'after' =>  $after,
      'position' => 'entry-title'
    ));

    if( $args[ 'return' ] ) {
      return $title;
    }

    echo $title;

  }

}


if( !function_exists( 'flawless_set_color_scheme' ) ) {
  /**
   * Set current skin, very basic for now.
   *
   * @todo Add check to see if being called too early, or too late.
   * @author potanin@UD
   */
  function flawless_set_color_scheme( $skin = '' ) {
    global $flawless;
    $flawless[ 'color_scheme' ] = $skin;
  }
}


if( !function_exists( 'get_post_for_extended_term' ) ) {
  /**
   * {}
   *
   * @author potanin@UD
   */
  function get_post_for_extended_term( $term_id = false, $taxonomy = false ) {
    global $wpdb;

    if( !$term_id ) {
      return false;
    }

    if( is_object( $term_id ) ) {
      $term_id = $term_id->term_id;
      $taxonomy = $taxonomy ? $taxonomy : $term_id->taxonomy;
    }

    //** Try to get taxonomy -if this term only has one relationship, it's a good guess */
    if( !$taxonomy ) {
      $taxonomy = $wpdb->get_col( "SELECT taxonomy FROM {$wpdb->term_taxonomy} WHERE term_id = {$term_id}" );

      if( count( $taxonomy ) > 1 ) {
        return false;
      } else {
        $taxonomy = $taxonomy[0];
      }
    }

    if( !is_numeric( $term_id ) || empty( $taxonomy ) ) {
      return false;
    }

    $post_id = $wpdb->get_var( "SELECT post_id FROM {$wpdb->postmeta} pm LEFT JOIN {$wpdb->posts} p ON pm.post_id = p.ID WHERE meta_key = 'extended_term_id' AND meta_value = '{$term_id}' AND post_type = '_tp_{$taxonomy}' " );

    if( !$post_id ) {
      return false;
    }

    if( $post_id ) {
      $post = get_post( $post_id );
    }

    if( !$post ) {
      return false;
    }

    return $post;

  }
}


if( !function_exists( 'flawless_render_in_footer' ) ) {
  /**
   * {}
   *
   * @author potanin@UD
   */
  function flawless_render_in_footer( $content , $args = array() ) {
    global $flawless;
    $flawless[ 'runtime' ][ 'footer_scripts' ][] = $content;
  }
}


if( !function_exists( 'add_term_meta' ) ) {
  /**
   * Add meta data field to a term.
   *
   */
  function add_term_meta( $term_id, $meta_key, $meta_value, $unique = false ) {

    if( current_theme_supports( 'extended-taxonomies' ) ) {
      return add_post_meta( get_post_for_extended_term( $term_id )->ID, $meta_key, $meta_value, $unique );
    }

    return add_metadata( 'taxonomy', $term_id, $meta_key, $meta_value, $unique );
  }
}

if( !function_exists( 'delete_term_meta' ) ) {
  /**
   * Remove metadata matching criteria from a term.
   *
   *
   */
  function delete_term_meta( $term_id, $meta_key, $meta_value = '' ) {

    if( current_theme_supports( 'extended-taxonomies' ) ) {
      return delete_post_meta( get_post_for_extended_term( $term_id )->ID, $meta_key, $meta_value );
    }

    return delete_metadata( 'taxonomy', $term_id, $meta_key, $meta_value );
  }
}


if( !function_exists( 'get_term_meta' ) ) {
  /**
   * Retrieve term meta field for a term.
   *
   */
  function get_term_meta( $term_id, $key, $single = false ) {

    if( current_theme_supports( 'extended-taxonomies' ) ) {
      return get_post_meta( get_post_for_extended_term( $term_id )->ID, $key, $single );
    }

    return get_metadata( 'taxonomy', $term_id, $key, $single );
  }
}


if( !function_exists( 'update_term_meta' ) ) {
  /**
   * Update term meta field based on term ID.
   *
   */
  function update_term_meta( $term_id, $meta_key, $meta_value, $prev_value = '' ) {

    if( current_theme_supports( 'extended-taxonomies' ) ) {
      return update_post_meta( get_post_for_extended_term( $term_id )->ID, $meta_key, $meta_value, $prev_value );
    }

    return update_metadata( 'taxonomy', $term_id, $meta_key, $meta_value, $prev_value );
  }
}

if( !function_exists( 'wp_is_mobile' ) ) {
  /**
   * Test if the current browser runs on a mobile device ( smart phone, tablet, etc. )
   *
   * @return bool true|false
   */
  function wp_is_mobile() {
    static $is_mobile;

    if ( isset( $is_mobile ) )
      return $is_mobile;

    if ( empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
      $is_mobile = false;
    } elseif ( strpos( $_SERVER['HTTP_USER_AGENT'], 'Mobile' ) !== false // many mobile devices ( all iPhone, iPad, etc. )
      || strpos( $_SERVER['HTTP_USER_AGENT'], 'Android' ) !== false
      || strpos( $_SERVER['HTTP_USER_AGENT'], 'BlackBerry' ) !== false
      || strpos( $_SERVER['HTTP_USER_AGENT'], 'Opera Mini' ) !== false ) {
      $is_mobile = true;
    } else {
      $is_mobile = false;
    }

    return $is_mobile;
  }
}


if( !function_exists( 'flawless_image_link' ) ) {
  /**
   * Get imare src, resizing the image if needed.
   *
   * @author potanin@UD
   */
  function flawless_image_link( $attachment_id = false, $size = false , $args = array() ) {

    if( !$size  || !$attachment_id ) {
      return false;
    }

    $image_sizes = Flawless_F::image_sizes();

    $args = wp_parse_args( $args, array(
      'return' => 'string',
      'cache_id' => sanitize_title( $attachment_id . $size ),
      'default' => ''
    ));

    if( $return = wp_cache_get( $args[ 'cache_id' ] , 'flawless_image_link' ) ) {
      return $return;
    }

    $attachment_image_src = ( array ) wp_get_attachment_image_src( $attachment_id, $size );

    if( empty( $image_sizes ) || ( is_array( $attachment_id ) && $attachment_id[1] == $image_sizes[ $size ][ 'width' ] ) ) {
      $return = $attachment_image_src[0];
      wp_cache_set( $args[ 'cache_id' ], $return, 'flawless_image_link' );
      return $return;
    }

    /** Get the metadata */
    $metadata = wp_get_attachment_metadata( $attachment_id );
    /** If we have metadata, we need to check it before continuing */
    if( $metadata ){
      /** Check to see if the original file exists */
      if( isset( $metadata[ 'sizes' ] ) && isset( $metadata[ 'sizes' ][ $size ] ) && isset( $metadata[ 'sizes' ][ $size ][ 'file' ] ) ){
        /** Get the upload directory */
        $upload_dir = wp_upload_dir();
        /** Determine the file's directory */
        $file_dir = explode( '/', $metadata[ 'file' ] );
        array_pop( $file_dir );
        $file_dir = implode( '/', $file_dir );
        /** Build the file path */
        $resized_file = $upload_dir[ 'basedir' ] . '/' . $file_dir . '/' . $metadata[ 'sizes' ][ $size ][ 'file' ];
        if( file_exists( $resized_file ) ){
          $file_url = $upload_dir[ 'baseurl' ] . '/' . $file_dir . '/' . $metadata[ 'sizes' ][ $size ][ 'file' ];
          wp_cache_set( $args[ 'cache_id' ], $file_url, 'flawless_image_link' );
          return $file_url;
        }
      }
    }

    $image_resize = image_resize( get_attached_file( $attachment_id, true ), $image_sizes[ $size ][ 'width' ], $image_sizes[ $size ][ 'height' ], $image_sizes[ $size ][ 'crop' ], 'test' );

    if( is_wp_error( $image_resize ) || !file_exists( $image_resize ) ) {

      if( $attachment_image_src[0] ) {
        $return = $args[ 'default' ] ? $args[ 'default' ] : $attachment_image_src[0];
      } else {
        $return = $args[ 'default' ];
      }

      wp_cache_set( $args[ 'cache_id' ], $return, 'flawless_image_link' );
      return $return;
    }

    require_once( ABSPATH . 'wp-admin/includes/image.php' );

    wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id,  get_attached_file( $attachment_id, true ) ));

    $attachment_image_src = ( array ) wp_get_attachment_image_src( $attachment_id, $size );

    $return = $attachment_image_src[0] ? $attachment_image_src[0] : $args[ 'default' ];

    wp_cache_set( $args[ 'cache_id' ], $return, 'flawless_image_link' );

    return $return;

  }
}



if ( ! function_exists( 'flawless_comment' ) ) {
  /**
   * Handles comments
   *
   * Based on denali 1.1 comment handler
   *
   * @todo Needs major revision, ported from Denali.
   * @since Flawless 0.2.3
   */
  function flawless_comment( $comment, $args, $depth ) {
    $GLOBALS[ 'comment' ] = $comment;
    switch ( $comment->comment_type ) :
      case '' :
        ?>
        <li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>" data-comment_id="<?php comment_ID(); ?>">
        <?php echo get_avatar( $comment, 80 ); ?>
        <div id="comment-<?php comment_ID(); ?>" class="comment-content">
          <div class="comment-author vcard">

            <?php printf( __( '%s', 'flawless' ), sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() )); ?><span class="comment-meta commentmetadata"><a href="<?php echo esc_url( get_comment_link( $comment->comment_ID )); ?>">
                <?php
                /* translators: 1: date, 2: time */
                printf( __( '%1$s at %2$s', 'flawless' ), get_comment_date(),  get_comment_time()); ?></a><?php edit_comment_link( __( '( Edit )', 'flawless' ), ' ' );
              ?>

              <span class="reply">
              <?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args[ 'max_depth' ] ) )); ?>
          </span><!-- .reply -->
          </span><!-- .comment-meta .commentmetadata -->
          </div><!-- .comment-author .vcard -->
          <?php if ( $comment->comment_approved == '0' ) : ?>
            <em><?php _e( 'Your comment is awaiting moderation.', 'flawless' ); ?></em>
            <br />
          <?php endif; ?>

          <div class="comment-body"><?php comment_text(); ?></div>
        </div><!-- #comment-##  -->

        <?php
        break;
      case 'pingback'  :
      case 'trackback' :
        ?>
        <li class="post pingback">
        <p><?php _e( 'Pingback:', 'flawless' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( '( Edit )', 'flawless' ), ' ' ); ?></p>
        <?php
        break;
    endswitch;
  }
}


/**
 * Conditional tag to determine if current page is selected to be the primary posts page
 *
 * @since Flawless 0.2.3
 */
if ( ! function_exists( 'is_posts_page' ) ) {
  function is_posts_page() {
    global $wp_query;
    return $wp_query->is_posts_page ? true : false;
  }
}


/**
 * Builds classes for the wrapper element based on conditional elements.
 *
 * This is the equivalent of a CB .row
 *
 * @since Flawless 0.2.3
 */
if ( ! function_exists( 'flawless_wrapper_class' ) ) {
  function flawless_wrapper_class( $custom_class = '' ) {
    global $wp_query, $flawless;

    $classes = array( $custom_class ) ;
    $classes[] = 'container';
    $classes[] = 'content_wrapper';
    $classes[] = 'row-fluid';

    //** Prevent classes from being blanked out */
    $maybe_classes = apply_filters( 'flawless::wrapper_class', $classes );

    if( !empty( $maybe_classes ) ) {
      $classes = $maybe_classes;
    }

    //** Remove blanks */
    $classes = array_filter( ( array ) $classes );

    //** Remove duplicates */
    $classes = array_unique( ( array ) $classes );

    $flawless[ 'wrapper_class' ] = !empty( $classes ) ? $classes : array();

    echo implode( ' ', $classes );

  }
}


if ( ! function_exists( 'flawless_block_class' ) ) {
  /**
   * Builds classes for the .main.cfct-block based on conditional elements.
   *
   * @since Flawless 0.5.0
   * @author willis@UD
   */
  function flawless_block_class( $custom_class = '' ) {
    global $flawless;

    $classes[] = $custom_class;

    //** Added classes to body */
    foreach( ( array ) $flawless[ 'current_view' ][ 'block_classes' ] as $class ) {
      $classes[] = $class;
    }

    echo implode( ' ', ( array ) $classes );
  }
}


if ( ! function_exists( 'flawless_module_class' ) ) {
  /**
   * Builds classes for the .hentry.cfct-module based on conditional elements.
   *
   * Called in templates intead of post_class(). On CB pages, the cfct-move is removed by flawless_carrington::module_class()
   *
   * @since Flawless 0.5.0
   * @author potanin@UD
   */
  function flawless_module_class( $custom_class = '' ) {
    global $flawless, $wp_query, $post;

    //** Load Post Classes if this is a post */
    $classes = get_post_class( '', $post->ID );

    $classes[] = $custom_class;
    $classes[] = 'cfct-module';

    $classes = apply_filters( 'flawless::module_class', $classes );

    echo implode( ' ', ( array ) $classes );

  }
}


if ( ! function_exists( 'hide_page_title' ) ) {
  /**
   * Conditional tag to determine if current page is selected to be the primary posts page
   *
   * @since Flawless 0.2.3
   * @author potanin@UD
   */
  function hide_page_title() {
    global $post;

    if( is_home() ) {
      return true;
    }

    return get_post_meta( $post->ID, 'hide_page_title', true ) == 'true' ? true : false;

  }
}


if( !function_exists( 'flawless_footer_copyright' ) ) {
  /**
   * Displays the Copyright info the footer.
   *
   * Avoid applying the_content filter since Carrington will take it over.
   *
   * @since Flawless 0.2.3
   * @author potanin@UD
   */
  function flawless_footer_copyright() {
    global $flawless;
    echo do_shortcode( nl2br( $flawless[ 'footer' ][ 'copyright' ] ));
  }

}


if( !function_exists( 'flawless_element' ) ) {
  /**
   * Generates unique <div> attributes for a draggable element
   *
   * @since 1.0
   * @author potanin@UD
   */
  function flawless_element( $classes = false, $args = false ) {
    global $flawless;

    $template_part = false;

    //** Figure out where this got called from */
    foreach( ( array ) debug_backtrace() as $item ) {

      if( $item[ 'function' ] == 'get_header' ) {
        $template_part = 'header';
        break;
      }

      if( $item[ 'function' ] == 'get_footer' ) {
        $template_part = 'footer';
        break;
      }

    }

    $classes = explode( ' ', $classes );

    $classes[] = 'cfct-module';
    $classes[] = 'flawless_module';

    $classes = implode( ' ' , $classes );

    //** Generate unique ID for this element, as long as classes don't change and it stays in same template part, it'll be good */
    $element_hash = md5( $classes . $template_part );

    echo ' class="' . $classes . '" template_part="' . $template_part . '" element_hash="'. $element_hash . '" ';

  }

}


if( !function_exists( 'flawless_breadcrumbs' ) ) {
  /**
   * Prints out breadcrumbs
   *
   * @todo Improve the way term and taxonomy is handled here.
   * @version 0.1
   * @author potanin@UD
   */
  function flawless_breadcrumbs( $args = false ) {
    global $wp_query, $post, $flawless;

    $args = wp_parse_args( $args, array(
      'hide_breadcrumbs' => get_post_meta( $post->ID, 'hide_breadcrumbs', true ) == 'true' || $flawless[ 'hide_breadcrumbs' ]? true : false,
      'return' => false,
      'home_label' => __( 'Home' ),
      'home_link' => home_url(),
      'wrapper_class' => 'breadcrumbs',
      'divider' => ' <span class="divider">&raquo;</span> ',
      'hide_on_home' => true
    ));

    if( $args[ 'hide_breadcrumbs' ] ) {
      return;
    }

    $before = '<span class="current">';
    $after = '</span>';

    if ( $args[ 'hide_on_home' ] && ( is_home() || is_front_page() ) ) {
      return;
    }

    $html[] = '<a class="home_link" href="' . $args[ 'home_link' ]. '">' . $args[ 'home_label' ] . '</a> ' . $delimiter . ' ';

    if ( is_home() || is_front_page() ) {


    } elseif ( is_category() ) {

      $cat_obj = $wp_query->get_queried_object();
      $thisCat = $cat_obj->term_id;
      $thisCat = get_category( $thisCat );
      $parentCat = get_category( $thisCat->parent );
      if ( $thisCat->parent != 0 ) $html[] =( get_category_parents( $parentCat, TRUE, ' ' . $delimiter . ' ' ));
      $html[] = $before . single_cat_title( '', false ) . $after;

    } elseif ( is_day() ) {
      $html[] = '<a href="' . get_year_link( get_the_time( 'Y' ) ) . '">' . get_the_time( 'Y' ) . '</a> ' . $delimiter . ' ';
      $html[] = '<a href="' . get_month_link( get_the_time( 'Y' ),get_the_time( 'm' ) ) . '">' . get_the_time( 'F' ) . '</a> ' . $delimiter . ' ';
      $html[] = $before . get_the_time( 'd' ) . $after;

    } elseif ( is_month() ) {
      $html[] = '<a href="' . get_year_link( get_the_time( 'Y' ) ) . '">' . get_the_time( 'Y' ) . '</a> ' . $delimiter . ' ';
      $html[] = $before . get_the_time( 'F' ) . $after;

    } elseif ( is_year() ) {
      $html[] = $before . get_the_time( 'Y' ) . $after;

    } elseif ( is_single() && !is_attachment() ) {

      if ( get_post_type() != 'post' ) {
        $post_type = get_post_type_object( get_post_type());
        $slug = $post_type->rewrite;

        //** Check if this content type has a custom Root page */
        if( $flawless[ 'post_types' ][get_post_type()][ 'root_page' ] ) {
          $content_type_home = get_permalink( $flawless[ 'post_types' ][get_post_type()][ 'root_page' ] );
        } else {
          $content_type_home = flawless_theme::filter_post_link( $args[ 'home_link' ]. '/' . $slug[ 'slug' ] . '/', $post );
        }

        /** Fix 'Pages' */
        if ( $post->post_type == 'page' ) {
          if ( $anc = get_post_ancestors( $post ) ) {
            $anc = wp_get_single_post( $anc[0] );
            $content_type_home = get_permalink( $anc->ID );
          }
        }

        if ( $anc ) {
          $title = $anc->post_title;
        } else {
          $title = $post_type->labels->name;
        }

        $html[ 'content_type_home' ] = '<a href="' . $content_type_home . '">' . $title . '</a>';
        $html[ 'this_page' ] = $before . get_the_title() . $after;

      } else {
        $cat = get_the_category(); $cat = $cat[0];

        if( $cat ) {
          $html[] = get_category_parents( $cat, TRUE, ' ' . $delimiter . ' ' );
        }

        $html[] = $before . get_the_title() . $after;
      }

    } elseif ( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() && !is_search() ) {

      $taxonomy = get_taxonomy( $wp_query->query_vars[ 'taxonomy' ] );
      $post_type = get_post_type_object( get_post_type() );

      //** Check if this content type has a custom Root page */
      if( $flawless[ 'post_types' ][get_post_type()][ 'root_page' ] ) {
        $content_type_home = get_permalink( $flawless[ 'post_types' ][get_post_type()][ 'root_page' ] );
      } else {
        $content_type_home = flawless_theme::filter_post_link( $args[ 'home_link' ]. '/' . $slug[ 'slug' ] . '/', $post );
      }

      /** Fix 'Pages' */
      if ( $post->post_type == 'page' ) {
        if ( $anc = get_post_ancestors( $post ) ) {
          $anc = wp_get_single_post( $anc[0] );
          $content_type_home = get_permalink( $anc->ID );
        }
      }

      if ( $anc ) {
        $title = $anc->post_title;
      } else {
        $title = $post_type->labels->name;
      }

      switch ( true ) {

        case is_tag():
          $html[ 'content_type_home' ] = '<a href="' . $content_type_home . '">' . $title . '</a>';
          $html[] = $before . get_queried_object()>name . $after;
          break;

        case is_tax():
          $html[ 'content_type_home' ] = '<a href="' . $content_type_home . '">' . $title . '</a>';
          $html[] = $before . get_queried_object()->name . $after;
          break;

        default:
          $html[] = $before . $post_type->labels->name . $after;
          break;

      }

    } elseif ( is_attachment() ) {
      $parent = get_post( $post->post_parent );
      $cat = get_the_category( $parent->ID ); $cat = $cat[0];

      //** Must check a category was found */
      if( $cat && !is_wp_error( $cat ) ) {
        $html[] = get_category_parents( $cat, TRUE, ' ' . $delimiter . ' ' );
      }

      $html[] = '<a href="' . get_permalink( $parent ) . '">' . $parent->post_title . '</a> ' . $delimiter . ' ';
      $html[] = $before . get_the_title() . $after;

    } elseif ( is_page() && !$post->post_parent ) {
      $html[] = $before . get_the_title() . $after;

    } elseif ( is_page() && $post->post_parent ) {
      $parent_id  = $post->post_parent;
      $breadcrumbs = array();
      while ( $parent_id ) {
        $page = get_page( $parent_id );
        $breadcrumbs[] = '<a href="' . get_permalink( $page->ID ) . '">' . get_the_title( $page->ID ) . '</a>';
        $parent_id  = $page->post_parent;
      }
      $breadcrumbs = array_reverse( $breadcrumbs );
      foreach ( $breadcrumbs as $crumb ) $html[] = $crumb . ' ' . $delimiter . ' ';
      $html[] = $before . get_the_title() . $after;

    } elseif ( is_search() ) {

      $html[] = $before . 'Search results for "' . get_search_query() . '"' . $after;

    } elseif ( is_tag() ) {

      $html[] = $before . 'Posts tagged "' . single_tag_title( '', false ) . '"' . $after;

    } elseif ( is_author() ) {
      global $author;
      $userdata = get_userdata( $author );
      $html[] = $before . 'Content by ' . $userdata->display_name . $after;

    } elseif ( is_404() ) {
      $html[] = $before . '404 Error' . $after;
    } elseif( is_tax() ) {

      $taxonomy = get_taxonomy( $wp_query->query_vars[ 'taxonomy' ] );

      $html[] = '<a href="' . $args[ 'home_link' ]. '/' . $taxonomy->rewrite[ 'slug' ]  . '">' . $taxonomy->labels->name . '</a> ';
      $html[] = $before . $wp_query->get_queried_object()->name . $after;

    } else {

      //$html[] = "<pre>";print_r( $wp_query );$html[] = "</pre>";
    }

    if ( get_query_var( 'paged' ) ) {
      if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) $page[] = ' ( ';
      $page[] = __( 'Page' ) . ' ' . get_query_var( 'paged' );
      if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) $page[] = ' )';

      $html[] = implode( '', ( array ) $page );
    }

    $html = apply_filters( 'flawless::breadcrumb_trail', $html );

    $final_html = '<div class="' . $args[ 'wrapper_class' ] . '">' . implode(  apply_filters( 'flawless_bcreadcrumbs::delimiter', $args[ 'divider' ] ) , $html )  . '</div>';

    if( $args[ 'return' ] ) {
      return $final_html;
    }

    echo $final_html;

  }
}


if( !function_exists( 'flawless_widget_area' ) ) {
  /**
   * Checks and renders sidebar template.
   * It's just modified get_sidebar() function.
   *
   * Note: use this function instead of default get_sidebar() or dynamic_sidebar()
   * in Flawless theme.
   *
   * @todo Needs to check if the requested widget are has active widgets, and if not, should not return.
   * @see get_sidebar()
   * @return HTML
   * @author Maxim Peshkov
   */
  function flawless_widget_area( $name = null ) {
    do_action( 'get_sidebar', $name );

    $templates = array();
    if ( isset( $name ) ) {
      $templates[] = "sidebar-{$name}.php";
    }

    $templates[] = 'sidebar.php';

    /** Backward compat code will be removed in a future WP release */
    if ( '' == locate_template( $templates, true, false ) ) {
      load_template( ABSPATH . WPINC . '/theme-compat/sidebar.php', false );
    }
  }
}


if( !function_exists( 'flawless_thumbnail' ) ) {
  /**
   * Displays a thumbail with wrapper, if applicable
   *
   * Default wrapper is 'entry-thumbnail'
   *
   * @author potanin@UD
   */
  function flawless_thumbnail( $args = array() ) {
    $args = wp_parse_args( $args, $defaults = array(
      'wrapper_class' => 'entry-thumbnail',
      'size' => array( 100,100 ),
      'return' => false,
      'link' => true
    ));

    $thumbnail = get_the_post_thumbnail( NULL, $args[ 'size' ] );

    if( !$thumbnail ) {
      return;
    }

    $html[] =  '<div class="' . $args[ 'wrapper_class' ] . '">';

    if( $args[ 'link' ] ) {
      $html[] = '<a href="'. get_permalink() . '" alt="' . get_the_title() . '">';
    }

    $html[] = $thumbnail;

    if( $args[ 'link' ] ) {
      $html[] = '</a>';
    }

    $html[] = '</div>';

    $html = implode( '',  ( array ) $html );

    if( $args[ 'return' ] ) {
      return $html;
    }

    echo $html;

  }

}


if( !function_exists( 'flawless_word_trim' ) ) {
  /**
   * Truncate a string, stop at preceding word.
   *
   * @author potanin@UD
   */
  function flawless_word_trim( $string, $count, $ellipsis = false ) {

    if( strlen( $string ) < $count ) {
      return $string;
    }

    $truncated = preg_replace( '/\s+?( \S+ )?$/', '', substr( $string, 0, $ellipsis ? $count + 3 : $count ));

    if( strlen( $string ) > strlen( $truncated ) && $ellipsis ) {
      $truncated .= '...';
    }

    return $truncated;

  }
}


if( !function_exists( 'flawless_primary_notice_container' ) ) {
  /**
   * Render any notices.
   *
   * @author potanin@UD
   */
  function flawless_primary_notice_container( $notice = '', $args = array() ) {

    $notices = array_filter( ( array ) apply_filters( 'flawless::primary_notice_container', array( $notice ) ));

    if( !is_array( $notices ) || empty( $notices ) ) {
      return;
    }

    echo '<div class="primary_notice_container">' . implode( '', ( array ) $notices ) . '</div>';

  }
}

