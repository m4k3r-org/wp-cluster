<?php
/**
 * API Access Controller
 *
 * @version 0.1.5
 * @module Cluster
 * @author potanin@UD
 */
namespace UsabilityDynamics\Cluster {

  if( !class_exists( 'UsabilityDynamics\Cluster\API' ) ) {

    /**
     * Class API
     *
     * @module Cluster
     */
    class API {

      /**
       * Initialize API
       *
       * @version 0.1.5
       * @for API
       */
      public function __construct() {

        $this->actual_url = admin_url( 'admin-ajax.php' );
      }

      /**
       * Migrated from site-new.php
       */
      public function add_site() {
        if ( ! current_user_can( 'manage_sites' ) ) {}

        check_admin_referer( 'add-blog', '_wpnonce_add-blog' );

        if ( ! is_array( $_POST['blog'] ) )
          wp_die( __( 'Can&#8217;t create an empty site.' ) );

        $blog = $_POST['blog'];
        $domain = '';
        if ( preg_match( '|^([a-zA-Z0-9-])+$|', $blog['domain'] ) )
          $domain = strtolower( $blog['domain'] );

        // If not a subdomain install, make sure the domain isn't a reserved word
        if ( ! is_subdomain_install() ) {
          /** This filter is documented in wp-includes/ms-functions.php */
          $subdirectory_reserved_names = apply_filters( 'subdirectory_reserved_names', array( 'page', 'comments', 'blog', 'files', 'feed' ) );
          if ( in_array( $domain, $subdirectory_reserved_names ) )
            wp_die( sprintf( __('The following words are reserved for use by WordPress functions and cannot be used as blog names: <code>%s</code>' ), implode( '</code>, <code>', $subdirectory_reserved_names ) ) );
        }

        $email = sanitize_email( $blog['email'] );
        $title = $blog['title'];

        if ( empty( $domain ) )
          wp_die( __( 'Missing or invalid site address.' ) );
        if ( empty( $email ) )
          wp_die( __( 'Missing email address.' ) );
        if ( !is_email( $email ) )
          wp_die( __( 'Invalid email address.' ) );

        if ( is_subdomain_install() ) {
          $newdomain = $domain . '.' . preg_replace( '|^www\.|', '', $current_site->domain );
          $path      = $current_site->path;
        } else {
          $newdomain = $current_site->domain;
          $path      = $current_site->path . $domain . '/';
        }

        $password = 'N/A';
        $user_id = email_exists($email);
        if ( !$user_id ) { // Create a new user with a random password
          $password = wp_generate_password( 12, false );
          $user_id = wpmu_create_user( $domain, $password, $email );
          if ( false == $user_id )
            wp_die( __( 'There was an error creating the user.' ) );
          else
            wp_new_user_notification( $user_id, $password );
        }

        $wpdb->hide_errors();
        $id = wpmu_create_blog( $newdomain, $path, $title, $user_id , array( 'public' => 1 ), $current_site->id );
        $wpdb->show_errors();
        if ( !is_wp_error( $id ) ) {
          if ( !is_super_admin( $user_id ) && !get_user_option( 'primary_blog', $user_id ) )
            update_user_option( $user_id, 'primary_blog', $id, true );
          $content_mail = sprintf( __( 'New site created by %1$s

Address: %2$s
Name: %3$s' ), $current_user->user_login , get_site_url( $id ), wp_unslash( $title ) );
          wp_mail( get_site_option('admin_email'), sprintf( __( '[%s] New Site Created' ), $current_site->site_name ), $content_mail, 'From: "Site Admin" <' . get_site_option( 'admin_email' ) . '>' );
          wpmu_welcome_notification( $id, $user_id, $password, $title, array( 'public' => 1 ) );
          wp_redirect( add_query_arg( array( 'update' => 'added', 'id' => $id ), 'site-new.php' ) );
          exit;
        } else {
          wp_die( $id->get_error_message() );
        }

      }

    }
  }
}