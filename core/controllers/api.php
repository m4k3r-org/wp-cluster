<?php
  /**
   * Flawless API
   *
   * @author potanin@UD
   * @version 0.0.1
   *
   * @submodule Flawless
   * @namespace Flawless
   */
  namespace Flawless {

    /**
     * API
     *
     * Description: WPP API implementation
     *
     * @submodule API
     * @extends UsabilityDynamics\API
     * @author potanin@UD
     * @version 0.1.0
     * @class API
     */
    class API extends \UsabilityDynamics\API {

      // Class Version.
      public static $version = '0.1.1';

      /**
       * Constructor for the UD API class.
       *
       * @author potanin@UD
       * @version 0.0.1
       * @method __construct
       *
       * @constructor
       * @for API
       *
       * @param array|bool $options
       */
      public function __construct( $options = false ) {

        //** Admin AJAX Handler */
        add_action( 'wp_ajax_flawless_action', create_function( '', ' die( json_encode( API::ajax_actions() )); ' ) );
        add_action( 'wp_ajax_nopriv_flawless_action', create_function( '', ' die( json_encode( API::ajax_actions() )); ' ) );

        add_action( 'wp_ajax_frontend_ajax_handler', create_function( '', ' die( json_encode( API::frontend_ajax_handler() )); ' ) );
        add_action( 'wp_ajax_nopriv_frontend_ajax_handler', create_function( '', ' die( json_encode( API::frontend_ajax_handler() )); ' ) );

        add_action( 'wp_ajax_flawless_signup_field_check', array( __CLASS__, 'flawless_signup_field_check' ), 10, 3 );

      }

      /**setup_content_types
       * Frontend AJAX Handler.
       *
       * @since 0.6.0
       */
      static function frontend_ajax_handler() {
        global $flawless, $wpdb;

        nocache_headers();

        $return = array( 'success' => false );

        ob_start();

        switch ( $_REQUEST[ 'the_action' ] ) {

          /**
           *
           *
           * @todo Add  get_option('require_name_email') check / support.
           */
          case 'comment_submit':

            parse_str( $_POST[ 'form_data' ], $form_data );

            $args = wp_parse_args( $form_data, array(
              'comment_post_ID' => 0,
              'author'          => null,
              'email'           => null,
              'url'             => null,
              'comment'         => null,
              'comment_parent'  => 0
            ) );

            foreach ( (array) $args as $key => $value ) {
              $args[ $key ] = trim( $value );
            }

            $args[ 'author' ] = strip_tags( $args[ 'author' ] );

            $post       = get_post( $args[ 'comment_post_ID' ] );
            $status     = get_post_status( $post );
            $status_obj = get_post_status_object( $status );

            if ( empty( $args[ 'comment' ] ) ) {
              $return[ 'message' ] = __( 'Please enter a comment.', 'flawless' );
              break;
            }

            if ( empty( $post->comment_status ) || !comments_open( $args[ 'comment_post_ID' ] ) ) {
              do_action( 'comment_id_not_found', $args[ 'comment_post_ID' ] );
              $return[ 'message' ] = __( 'Sorry, comments are closed for this item.' );
              break;
            } elseif ( 'trash' == $status ) {
              do_action( 'comment_on_trash', $args[ 'comment_post_ID' ] );
              break;
            } elseif ( !$status_obj->public && !$status_obj->private ) {
              do_action( 'comment_on_draft', $args[ 'comment_post_ID' ] );
              break;
            } elseif ( post_password_required( $args[ 'comment_post_ID' ] ) ) {
              do_action( 'comment_on_password_protected', $args[ 'comment_post_ID' ] );
              break;
            } else {
              do_action( 'pre_comment_on_post', $args[ 'comment_post_ID' ] );
            }

            $user = wp_get_current_user();

            if ( $user->ID ) {

              $args[ 'user_ID' ] = $user->ID;
              $args[ 'email' ]   = $user->data->user_email;
              $args[ 'author' ]  = $user->data->display_name;

            } else {

              if ( get_option( 'comment_registration' ) || 'private' == $status ) {
                $return[ 'message' ] = __( 'Sorry, you must be logged in to post a comment.' );
                break;
              }

            }

            if ( !is_email( $args[ 'email' ] ) ) {
              $return[ 'message' ] = __( 'Please enter a valid email address.' );
              break;
            }

            $comment_id = wp_new_comment( array(
              'comment_post_ID'      => $args[ 'comment_post_ID' ],
              'comment_author'       => $args[ 'author' ],
              'comment_author_email' => $args[ 'email' ],
              'comment_author_url'   => $args[ 'url' ],
              'comment_content'      => $args[ 'comment' ],
              'comment_parent'       => $args[ 'comment_parent' ],
              'user_id'              => $args[ 'user_id' ],
              'comment_type'         => '',
            ) );

            $comment = get_comment( $comment_id );

            if ( $comment ) {

              if ( !$user->ID ) {
                $comment_cookie_lifetime = apply_filters( 'comment_cookie_lifetime', 30000000 );
                setcookie( 'comment_author_' . COOKIEHASH, $comment->comment_author, time() + $comment_cookie_lifetime, COOKIEPATH, COOKIE_DOMAIN );
                setcookie( 'comment_author_email_' . COOKIEHASH, $comment->comment_author_email, time() + $comment_cookie_lifetime, COOKIEPATH, COOKIE_DOMAIN );
                setcookie( 'comment_author_url_' . COOKIEHASH, esc_url( $comment->comment_author_url ), time() + $comment_cookie_lifetime, COOKIEPATH, COOKIE_DOMAIN );
              }

              $return[ 'success' ] = true;

              $comments = get_comments( array(
                'post_id' => $args[ 'comment_post_ID' ],
                'status'  => 'approve',
                'order'   => 'ASC'
              ) );

              if ( $user->ID ) {
                $comments = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d AND (comment_approved = '1' OR ( user_id = %d AND comment_approved = '0' ) )  ORDER BY comment_date_gmt", $args[ 'comment_post_ID' ], $args[ 'user_ID' ] ) );
              } else if ( empty( $args[ 'comment_author' ] ) ) {
                $comments = get_comments( array( 'post_id' => $args[ 'comment_post_ID' ], 'status' => 'approve', 'order' => 'ASC' ) );
              } else {
                $comments = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d AND ( comment_approved = '1' OR ( comment_author = %s AND comment_author_email = %s AND comment_approved = '0' ) ) ORDER BY comment_date_gmt", $args[ 'comment_post_ID' ], wp_specialchars_decode( $args[ 'comment_author' ], ENT_QUOTES ), $args[ 'comment_author_email' ] ) );
              }

              $return[ 'comment_list' ]  = $comments;
              $return[ 'comment_count' ] = count( $comments );

              ob_start();
              wp_list_comments( array( 'callback' => 'flawless_comment' ), $comments );
              $comment_html = ob_get_contents();
              ob_end_clean();

              $return[ 'comment_html' ] = $comment_html;

            }

            break;

        }

        $output = ob_get_contents();
        ob_end_clean();

        $return[ 'output' ] = $output;

        return $return;

      }

      /**
       * Admin Flawless-specific ajax actions.
       *
       * Called when AJAX call with action:flawless_action is used.
       * Must return array, which is automatically converted into JSON.
       *
       * @todo May want to update nonce verification to something more impressive since used on back and front-end calls.
       * @since 0.0.2
       */
      static function ajax_actions() {
        global $flawless, $wpdb;

        nocache_headers();

        if ( !current_user_can( 'edit_theme_options' ) ) {
          die( '0' );
        }

        $flawless = stripslashes_deep( get_option( 'flawless_settings' ) );

        switch ( $_REQUEST[ 'the_action' ] ) {

          case 'delete_logo':

            //** Delete old logo */
            if ( is_numeric( $flawless[ 'flawless_logo' ][ 'post_id' ] ) ) {
              wp_delete_attachment( $flawless[ 'flawless_logo' ][ 'post_id' ], true );
              unset( $flawless[ 'flawless_logo' ] );
            } elseif ( !empty( $flawless[ 'flawless_logo' ][ 'url' ] ) ) {
              unset( $flawless[ 'flawless_logo' ] );
            }

            update_option( 'flawless_settings', $flawless );
            $return = array( 'success' => 'true' );

            break;

          case 'clean_up_revisions':

            if ( current_user_can( 'delete_posts' ) ) {

              $args[ 'max_revisions' ] = intval( defined( 'WP_POST_REVISIONS' ) ? WP_POST_REVISIONS : 3 );

              $revisions_over_limit = $wpdb->get_results( "SELECT post_parent, ID as revision_id, ( SELECT count(ID) FROM {$wpdb->posts} pp WHERE pp.post_parent = p.post_parent ) as revisions, ( SELECT post_date FROM {$wpdb->posts} last WHERE last.post_parent = p.post_parent ORDER BY last.post_date DESC LIMIT {$args[max_revisions]},1) as date_cutoff FROM {$wpdb->posts} p WHERE post_type = 'revision' AND post_date <= ( SELECT post_date FROM {$wpdb->posts} last WHERE last.post_parent = p.post_parent ORDER BY last.post_date DESC LIMIT " . ( $args[ max_revisions ] + 1 ) . ",1)" );

              $args[ 'revisions_over_limit' ] = count( $revisions_over_limit );

              foreach ( (array) $revisions_over_limit as $post_row ) {
                $args[ 'deleted' ][ ] = !is_wp_error( wp_delete_post_revision( $post_row->revision_id ) ) ? $post_row->revision_id : '';
              }

              $args[ 'deleted' ] = count( (array) array_filter( (array) $args[ 'deleted' ] ) );

              if ( $args[ 'deleted' ] ) {
                $wpdb->query( "OPTIMIZE TABLE {$wpdb->posts}" );
                $wpdb->query( "OPTIMIZE TABLE {$wpdb->postmeta}" );

                $return = array( 'success' => 'true', 'message' => sprintf( __( 'Success! We removed %1s post revisions and optimized your MySQL tables. ', 'flawless' ), $args[ 'deleted' ], $args[ 'max_revisions' ] ) );
              } else {
                $return = array( 'success' => 'false', 'message' => __( 'Does not look like there were any revisions to remove.', 'flawless' ) );
              }

            }

            break;

          case 'delete_all_settings':

            delete_option( 'flawless_settings' );

            $return = array(
              'success' => 'true',
              'message' => __( 'All Flawless settings deleted.', 'flawless' )
            );
            break;

          case 'show_permalink_structure':

            $return = array(
              'success' => 'true',
              'message' => '<pre class="flawless_class_pre">' . print_r( get_option( 'rewrite_rules' ), true ) . '</pre>'
            );

            break;

          case 'show_flawless_configuration':

            $return = array(
              'success' => 'true',
              'message' => '<pre class="flawless_class_pre">' . print_r( $flawless, true ) . '</pre>'
            );

            break;

          default:
            $return = apply_filters( 'flawless_ajax_action', array( 'success' => $false ), $flawless );
            break;

        }

        if ( empty( $return ) ) {

          $return = array(
            'success' => false,
            'message' => __( 'No action found.', 'flawless' )
          );

        }

        return $return;

      }

      /**
       * Flawless Signup Field Check
       *
       * @author odokienko@UD
       */
      static function flawless_signup_field_check() {
        global $wpdb;

        $field_name  = $_REQUEST[ 'field_name' ];
        $field_value = $_REQUEST[ 'field_value' ];
        $field_type  = $_REQUEST[ 'field_type' ];
        $response    = array(
          'success' => 'true'
        );

        switch ( $field_name ) {
          case "signup_username":
            $user_exists = $wpdb->get_row( "SELECT * FROM {$wpdb->users} WHERE `user_login` = '{$field_value}' limit 1" );

            if ( !empty( $user_exists ) ) {
              $response = array(
                'success' => 'false',
                'message' => __( 'Sorry, that username already exists!', 'flawless' )
              );
            }
            break;
          case "signup_email":
            $user_exists = $wpdb->get_row( "SELECT * FROM {$wpdb->users} WHERE `user_email` = '{$field_value}' limit 1" );

            if ( !empty( $user_exists ) ) {
              $response = array(
                'success'  => 'false',
                'message'  => __( 'Sorry, that email address is already used!', 'flawless' ),
                'setfocus' => '.flawless_login_form input[name=log]'
              );
            }
            break;

          default:

        }

        die( json_encode( $response ) );
      }

    }

  }