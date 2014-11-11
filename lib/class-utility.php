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
      static public function get_host() {
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

	    /**
	     * Get Current Git Branch
	     *
	     * @return null
	     */
	    static public function get_git_branch() {

		    if( !is_dir( ABSPATH . '.git' ) || !is_file( ABSPATH . '.git/HEAD' ) ) {
			    return null;
		    }

		    $stringfromfile = file( ABSPATH . '.git/HEAD', FILE_USE_INCLUDE_PATH);

		    $firstLine = $stringfromfile[0]; //get the string from the array

		    $explodedstring = explode("/", $firstLine, 3); //seperate out by the "/" in the string

		    return $branchname = $explodedstring[2]; //get the one that is always the branch name

	    }

	    /**
	     * Get Current Git Tag
	     *
	     * @source https://gist.github.com/lukeoliff/5501074
	     * @return mixed
	     */
	    static public function get_git_version() {

		    exec('git describe --always',$version_mini_hash);
		    exec('git rev-list HEAD | wc -l',$version_number);
		    exec('git log -1',$line);
		    $version['short'] = "v1.".trim($version_number[0]).".".$version_mini_hash[0];
		    $version['full'] = "v1.".trim($version_number[0]).".$version_mini_hash[0] (".str_replace('commit ','',$line[0]).")";

		    return (object) $version;


	    }

	    /**
	     * Get Current Git Tag
	     *
	     *
	     * "git describe --always" - 2.1.1-2409-g2a20a3d
	     * "git rev-list HEAD | wc -l" - 2575
	     * "git log -1" - Author: andypotanin <andy.potanin@usabilitydynamics.com> \n Date:   Mon Nov 10 15:25:37 2014 -0500 \ n ...
	     *
	     * @source https://gist.github.com/lukeoliff/5501074
	     * @return mixed
	     */
	    static public function get_git_tag() {

		    exec('git describe --always',$version_mini_hash);
		    // exec('git rev-list HEAD | wc -l',$version_number);

		    $version_mini_hash = explode( '-', $version_mini_hash[0] );
		    //$version_number = trim( $version_number[0] );

		    return $version_mini_hash[0];

	    }

	    /**
	     * Get Latest Git Commit
	     *
	     * @source https://www.lullabot.com/blog/article/tip-show-last-git-commit-site-footer
	     * @param string $format
	     *
	     * @return mixed
	     */
	    static public function get_git_commit_message( $format = '%s' ) {
		    $commit_text = array();
		    exec( "git log -1 --pretty=format:'{$format}' --abbrev-commit", $commit_text );
		    return is_array( $commit_text ) ? $commit_text[0] : null;

	    }

    }

  }

}