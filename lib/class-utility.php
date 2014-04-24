<?php
/**
 * Utility Access Controller
 *
 * @module Cluster
 * @author potanin@UD
 */
namespace UsabilityDynamics\Cluster {

  if( !class_exists( 'UsabilityDynamics\Cluster\Utility' ) ) {

    /**
     * Class Utility
     *
     * @module Cluster
     */
    class Utility {

      /**
       * Login Shortcode
       *
       * @param array $args
       */
      static public function wp_login_form_shortcode( $args = array() ) {

        $args = shortcode_atts( $args, array(
          'echo'           => true,
          'redirect'       => ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER[ 'HTTP_HOST' ] . $_SERVER[ 'REQUEST_URI' ], // Default redirect is back to the current page
          'form_id'        => 'loginform',
          'label_username' => __( 'Username' ),
          'label_password' => __( 'Password' ),
          'label_remember' => __( 'Remember Me' ),
          'label_log_in'   => __( 'Log In' ),
          'id_username'    => 'user_login',
          'id_password'    => 'user_pass',
          'id_remember'    => 'rememberme',
          'id_submit'      => 'wp-submit',
          'remember'       => true,
          'value_username' => '',
          'value_remember' => false
        ) );

        wp_login_form( $args );

      }

      /**
       * Get Request Headers.
       *
       * @method requestHeaders
       */
      static public function requestHeaders()  {
        $headers = '';
        foreach ($_SERVER as $name => $value)  {
         if (substr($name, 0, 5) == 'HTTP_')  {
           $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
         }
        }
        return (object) $headers;
      }

      /**
       * Replace Default Sender Email
       *
       * @param $from_email
       *
       * @return mixed
       */
      static public function wp_mail_from( $from_email ) {

        // Get the site domain and get rid of www.
        $sitename = strtolower( $_SERVER[ 'SERVER_NAME' ] );

        if ( substr( $sitename, 0, 4 ) == 'www.' ) {
          $sitename = substr( $sitename, 4 );
        }

        if( $from_email == 'wordpress@' . $sitename ) {
          return str_replace( 'wordpress', 'info', $from_email );
        }

        return $from_email;

      }

      /**
       * Replace Default Sender Name
       *
       * @param $from_name
       *
       * @return string
       */
      static public function wp_mail_from_name( $from_name ) {
        global $current_site;

        $from_name = str_replace( 'WordPress', $current_site->domain, $from_name );

        return $from_name;

      }

      /**
     * Apply a method to multiple filters
     *
     * @param $tags
     * @param $function
     */
      static public function add_filters( $tags, $function ) {

      foreach( $tags as $tag ) {
        add_filter( $tag, $function );
      }

    }

      /**
       * Root relative URLs
       *
       * WordPress likes to use absolute URLs on everything - let's clean that up.
       * Inspired by http://www.456bereastreet.com/archive/201010/how_to_make_wordpress_urls_root_relative/
       *
       * You can enable/disable this feature in config.php:
       * current_theme_supports('root-relative-urls');
       *
       * @souce roots
       * @author Scott Walkinshaw <scott.walkinshaw@gmail.com>
       */
      static public function relative_url( $input ) {
        return $input;

        preg_match( '|https?://([^/]+)(/.*)|i', $input, $matches );

        if( isset( $matches[ 1 ] ) && isset( $matches[ 2 ] ) && $matches[ 1 ] === $_SERVER[ 'SERVER_NAME' ] ) {
          return wp_make_link_relative( $input );
        } else {
          return $input;
        }
      }

      /**
       * Returns server hostname
       *
       * @return string
       */
      function get_host() {
        static $host = null;

        if ($host === null) {
          if (!empty($_SERVER['HTTP_X_FORWARDED_HOST'])) {
            $host = $_SERVER['HTTP_X_FORWARDED_HOST'];
          } elseif (!empty($_SERVER['HTTP_HOST'])) {
            // HTTP_HOST sometimes is not set causing warning
            $host = $_SERVER['HTTP_HOST'];
          } else {
            $host = '';
          }
        }

        return $host;
      }

    }

  }

}