<?php
/**
 * Name: UsabilityDynamics Classes
 * Version 2.1
 * Copyright 2010 - 2012 Andy Potanin <andy.potanin@usabilitydynamics.com>
 *
 *
 * @version 2.1
 * @author Andy Potanin <andy.potanin@usabilitydynamics.com>
 * @package UsabilityDynamics
 */


if( class_exists( 'Flawless_F' ) ) {
  return;
}

define( 'UD_Lang', 'UD_Lang' );

/**
 * General Shared Functions used in UsabilityDynamics and TwinCitiesTech.com plugins and themes.
 *
 * Used for performing various useful functions applicable to different plugins.
 *
 * @package UsabilityDynamics
 */
class Flawless_F {


  /**
   * Returns image sizes for a passed image size slug
   *
   * @source WP-Property
   * @since 0.54
   * @returns array keys: 'width' and 'height' if image type sizes found.
   */
  static function image_sizes( $type = false, $args = '' ) {
    global $_wp_additional_image_sizes;

    $image_sizes = (array) $_wp_additional_image_sizes;

    $image_sizes[ 'thumbnail' ] = array(
      'width' => intval( get_option( 'thumbnail_size_w' ) ),
      'height' => intval( get_option( 'thumbnail_size_h' ) )
   );

    $image_sizes[ 'medium' ] = array(
      'width' => intval( get_option( 'medium_size_w' ) ),
      'height' => intval( get_option( 'medium_size_h' ) )
   );

    $image_sizes[ 'large' ] = array(
      'width' => intval( get_option( 'large_size_w' ) ),
      'height' => intval( get_option( 'large_size_h' ) )
   );

    foreach( (array) $image_sizes as $size => $data ) {
      $image_sizes[ $size ] = array_filter( (array) $data );
    }

    return array_filter( (array) $image_sizes );

  }


  /**
   * {}
   *
   * @source http://stackoverflow.com/questions/6501845/php-need-help-inserting-arrays-into-associative-arrays-at-given-keys
   * @author potanin@UD
   */
  function array_insert_before($array, $key, $new) {
    $keys = array_keys($array);
    $pos = (int) array_search($key, $keys);
    return array_merge(
        array_slice($array, 0, $pos),
        $new,
        array_slice($array, $pos)
   );
  }


  /**
   * {}
   *
   * @source http://stackoverflow.com/questions/6501845/php-need-help-inserting-arrays-into-associative-arrays-at-given-keys
   * @author potanin@UD
   */
  function array_insert_after($array, $key, $new) {
    $keys = array_keys($array);
    $pos = (int) array_search($key, $keys) + 1;
    return array_merge(
        array_slice($array, 0, $pos),
        $new,
        array_slice($array, $pos)
   );
  }


  /**
   * Gracefully Die on Fatal Errors
   *
   * To Enable:  add_filter( 'wp_die_handler', function() { return 'ud_graceful_death'; } , 10, 3 );
   *
   * @author potanin@UD
   */
  function ud_graceful_death( $message, $title = '', $args = array() ) {
    $defaults = array( 'response' => 500 );
    $r = wp_parse_args($args, $defaults);
    $backtrace = debug_backtrace();

    if( $backtrace[2]['function'] == 'wp_die' ) {

      switch( $message ) {

        case 'You do not have sufficient permissions to access this page.':
          $original_message = $message;
          $message = array();
          $message[] = '<li class="title">Access Denied</li>';
          $message[] = '<li class="message">' . $original_message  . '</li>';
          $message = '<ul>' . implode( (array) $message ) . '</li>';
        break;

      }

    }

    if ( !headers_sent() ) {
      status_header( $r['response'] );
      nocache_headers();
      header( 'Content-Type: text/html; charset=utf-8' );
    } else {
      echo '<div class="ud_inline_fatal_error">' . $message . '</div>';
      die();
    }


    if ( empty($title) ) {
      $title = function_exists('__') ? __('UD Error', UD_Lang ) : 'UD Error';
    }

  ?>
  <!DOCTYPE html>
  <!-- Ticket #11289, IE bug fix: always pad the error page with enough characters such that it is greater than 512 bytes, even after gzip compression abcdefghijklmnopqrstuvwxyz1234567890aabbccddeeffgghhiijjkkllmmnnooppqqrrssttuuvvwwxxyyzz11223344556677889900abacbcbdcdcededfefegfgfhghgihihjijikjkjlklkmlmlnmnmononpopoqpqprqrqsrsrtstsubcbcdcdedefefgfabcadefbghicjkldmnoepqrfstugvwxhyz1i234j567k890laabmbccnddeoeffpgghqhiirjjksklltmmnunoovppqwqrrxsstytuuzvvw0wxx1yyz2z113223434455666777889890091abc2def3ghi4jkl5mno6pqr7stu8vwx9yz11aab2bcc3dd4ee5ff6gg7hh8ii9j0jk1kl2lmm3nnoo4p5pq6qrr7ss8tt9uuvv0wwx1x2yyzz13aba4cbcb5dcdc6dedfef8egf9gfh0ghg1ihi2hji3jik4jkj5lkl6kml7mln8mnm9ono
  -->
  <html xmlns="http://www.w3.org/1999/xhtml" class="graceful_death">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo $title ?></title>
    <link rel='stylesheet' id='wp-admin-css'  href="<?php echo admin_url( '/css/wp-admin.css' ); ?>" type='text/css' media='all' />
    </head><?php echo $message; ?></body></html>
    <?php
    die();
  }


  /**
   * {}
   *
   * @author potanin@UD
   */
  static function depluralize($word) {
    $rules = array( 'ss' => false, 'os' => 'o', 'ies' => 'y', 'xes' => 'x', 'oes' => 'o', 'ies' => 'y', 'ves' => 'f', 's' => '' );

    foreach( array_keys($rules) as $key) {

      if(substr($word, (strlen($key) * -1)) != $key)
        continue;

      if($key === false)
        return $word;

      return substr($word, 0, strlen($word) - strlen($key)) . $rules[$key];

    }

    return $word;

  }


  /**
   * {}
   *
   * @author potanin@UD
   */
  static function is_url($url) {
    return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
  }


  /**
   * {}
   *
   * @author potanin@UD
   */
  static function format_bytes($bytes, $precision = 2) {
    $kilobyte = 1024;
    $megabyte = $kilobyte * 1024;
    $gigabyte = $megabyte * 1024;
    $terabyte = $gigabyte * 1024;

    if (($bytes >= 0) && ($bytes < $kilobyte)) {
      return $bytes . ' B';

    } elseif (($bytes >= $kilobyte) && ($bytes < $megabyte)) {
      return round($bytes / $kilobyte, $precision) . ' KB';

    } elseif (($bytes >= $megabyte) && ($bytes < $gigabyte)) {
      return round($bytes / $megabyte, $precision) . ' MB';

    } elseif (($bytes >= $gigabyte) && ($bytes < $terabyte)) {
      return round($bytes / $gigabyte, $precision) . ' GB';

    } elseif ($bytes >= $terabyte) {
      return round($bytes / $terabyte, $precision) . ' TB';
    } else {
      return $bytes . ' B';
    }
  }


  /**
   * Used to enable/disable/print SQL log
   *
   * Usage:
   * self::sql_log( 'enable' );
   * self::sql_log( 'disable' );
   * $queries= self::sql_log( 'print_log' );
   *
   * @since 0.1
   */
  function sql_log( $action = 'attach_filter' ) {
    global $wpdb;

    if( !in_array( $action, array( 'enable', 'disable', 'print_log' ) ) ) {
      $wpdb->ud_queries[] = array( $action, $wpdb->timer_stop(), $wpdb->get_caller() );
      return $action;
    }

    if( $action == 'enable' ) {
      add_filter( 'query', array( Flawless_F, 'sql_log'), 75 );
    }

    if( $action == 'disable' ) {
      remove_filter( 'query', array( Flawless_F, 'sql_log'), 75 );
    }

    if( $action == 'print_log' ) {
      $result = array();
      foreach( (array) $wpdb->ud_queries as $query ) {
        $result[] = $query[0] ? $query[0] . ' (' .  $query[1] . ')' : $query[2];
      }
      return $result;
    }

  }


  /**
   * Logs errors. Called by set_error_handler() and register_shutdown_function()
   *
   *  set_error_handler( array( 'Flawless_F', 'log_error' ) );
   *  register_shutdown_function( array( 'Flawless_F', 'log_error' ) );
   *
   * Types:
   * 1) Fatal run-time errors.
   * 2) Run-time warnings (non-fatal errors). Execution of the script is not halted.
   * 4) Compile-time parse errors. Parse errors should only be generated by the parser.
   * 256) User-generated error message. This is like an E_ERROR, except it is generated in PHP code by using the PHP function trigger_error().
   * 512) User-generated warning message. This is like an E_WARNING, except it is generated in PHP code by using the PHP function trigger_error().
   *
   * self::log_error( array( 'type' => 512, 'message' => 'Something bad, but not fatal.', 'file' => __FILE__ , 'line' =>  __LINE__ ));
   *
   * @author potanin@UD
   */
  static function log_error( $error = false ) {
    global $wpdb, $current_user;

    if(func_num_args() == 5) {
      $error = error_get_last();
      $args = func_get_args();
    } else {
      $passed_error = true;
    }

    //** Always ignore 2048 */
    if( $error['type'] == 2048 ) {
      return;
    }

    $is_admin = isset( $current_user ) && function_exists( 'current_user_can' ) && current_user_can( 'manage_options' ) ? true : false;

    if( !$is_admin && $wpdb->error_log && !in_array( $error['type'], array( 0, 8 ) ) ) {
      $wpdb->insert( $wpdb->error_log, array( 'code' => $error['type'], 'string' => $error['message'], 'file' => $error['file'], 'line' => $error['line'] ));
    }

    //** If we had a fatal error */
    if( $error['type'] === 1 ) {

      if( !$is_admin ) {

        $admin_notified = self::critical_notice( array(
          'error' => print_r( $error, true ),
          'server' => print_r( $_SERVER, true ),
          'backtrace' => print_r( debug_backtrace(), true )
       ), $error['message'] );

      }

      if( $current_user && $current_user->data->first_name ) {
        $display_name = get_user_meta( $current_user->ID, 'first_name', true );
        if( trim( $display_name ) == '' ) {
          $display_name = $current_user->display_name;
        }
      }

      if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
        die('-1');
      }

      if( $error['title'] ) {
        $message[] = '<li class="title">' . $error['title'] .'</li>';
      } elseif( $passed_eror) {
        $message[] = '<li class="title">' . $error['message'] .'</li>';
      } else {
        $message[] = '<li class="title">' . ( $display_name ? ', ' . $display_name : '' ) . ', You Have a Fatal Error!</li>';
      }

      if( $passed_error ) {
        $message[] = '<li class="message">' . $error['message']  . '</li>';
        $message[] = '<li>' . $error['file'] .' (' .  $error['line']  . ')</li>';
      } else {
        $message[] = '<li>Here\'s what happened... <b>' . $error['message'] .'</b> on line <b>' . $error['line'] . '</b>  from a file called <b>' . basename($error['file']) . '</b> which lives in a directory called <b>' . dirname( $error['file']) . '</b> - and that wasn\'t supposed to happen like that.</li>';
      }

      $message[] = 'Memory usage: ' . round(( memory_get_peak_usage() / 1048576 )) . ' mb. ';

      if( isset($admin_notified) && $admin_notified && !$is_admin ) {
        $message[] = '<li class="admin_notified">As with all fatal errors, the administrator has already been notified.</li>';
      } elseif( !$is_admin ) {
        $message[] = '<li class="admin_notified">The error has been logged, and will be investigated shortly.</li>';
      } elseif( !isset($passed_error) || !$passed_error ) {
        $message[] = '<li class="admin_notified">You are an administrator, this is your fault..</li>';
      }

      call_user_func( array( 'Flawless_F', 'ud_graceful_death' ), '<ul>' . implode( (array) $message ) . '</li>', 'Whoops...' );

    }

  }


  /**
   * Helpder function for figuring out if another specific function is a predecesor.
   *
   */
  function _backtrace_function( $function = false ) {

    foreach( debug_backtrace() as $step ) {
      if( $function && $step[ 'function' ] == $function ) {
        return true;
      }
    }

  }


  /**
   * Helpder function for figuring out if another specific function is a predecesor.
   *
   */
  function _backtrace_file( $file = false ) {

    foreach( debug_backtrace() as $step ) {
      if( $file && basename( $step[ 'file' ] ) == $file ) {
        return true;
      }
    }

  }


  /**
   * Parse standard WordPress readme file
   *
   * @source Readme Parser ( http://www.tomsdimension.de/wp-plugins/readme-parser )
   * @author potanin@UD
   */
  function parse_readme( $readme_file = false ) {

    if( !$readme_file ) {
      $readme_file = untrailingslashit( TEMPLATEPATH ) . '/readme.txt';
    }

    $file = @file_get_contents( $readme_file );

    if( !$file ) {
      return false;
    }

    $file = preg_replace( "/(\n\r|\r\n|\r|\n)/", "\n", $file );

    // headlines
    $s = array( '===','==','=' );
    $r = array( 'h2' ,'h3', 'h4' );
    for ( $x = 0; $x < sizeof( $s ); $x++ )
      $file = preg_replace( '/(.*?)'.$s[$x].'(?!\")(.*?)'.$s[$x].'(.*?)/', '$1<'.$r[$x].'>$2</'.$r[$x].'>$3', $file );

    // inline
    $s = array( '\*\*','\'' );
    $r = array( 'b'   ,'code' );
    for ( $x = 0; $x < sizeof( $s ); $x++ ) {
      $file = preg_replace( '/(.*?)'.$s[$x].'(?!\s)(.*?)(?!\s )'.$s[$x].'(.*?)/', '$1<'.$r[$x].'>$2</'.$r[$x].'>$3', $file );
    }

    // ' _italic_ '
    $file = preg_replace( '/(\s)_(\S.*?\S)_(\s|$)/', '<em>$2</em> ', $file );

    // ul lists
    $s = array( '\*','\+','\-' );
    for ( $x = 0; $x < sizeof( $s ); $x++ ) {
      $file = preg_replace( '/^[ '.$s[$x].' ](\s)(.*?)(\n|$)/m', '<li>$2</li>', $file );
    }

    $file = preg_replace( '/\n<li>(.*?)/', '<ul><li>$1', $file );
    $file = preg_replace( '/(<\/li>)(?!<li>)/', '$1</ul>', $file );

    // ol lists
    $file = preg_replace( '/(\d{1,2}\. )\s(.*?)(\n|$)/', '<li>$2</li>', $file );
    $file = preg_replace( '/\n<li>(.*?)/', '<ol><li>$1', $file );
    $file = preg_replace( '/(<\/li>)(?!(\<li\>|\<\/ul\> ))/', '$1</ol>', $file );

    // ol screenshots style
    $file = preg_replace( '/(?=Screenshots)(.*?)<ol>/', '$1<ol class="readme-parser-screenshots">', $file );

    // line breaks
    $file = preg_replace( '/(.*?)(\n)/', "$1<br/>\n", $file );
    $file = preg_replace( '/(1|2|3|4)(><br\/>)/', '$1>', $file );
    $file = str_replace( '</ul><br/>', '</ul>', $file );
    $file = str_replace( '<br/><br/>', '<br/>', $file );

    // urls
    $file = str_replace( 'http://www.', 'www.', $file );
    $file = str_replace( 'www.', 'http://www.', $file );
    $file = preg_replace( '#(^|[^\"=]{1})(http://|ftp://|mailto:|https://)([^\s<>]+)([\s\n<>]|$)#', '$1<a href="$2$3">$3</a>$4', $file );

    // divs
    $file = preg_replace( '/(<h3> Description <\/h3>)/', "$1\n<div class=\"readme-description readme-div\">\n", $file );
    $file = preg_replace( '/(<h3> Installation <\/h3>)/', "</div>\n$1\n<div id=\"readme-installation\" class=\"readme-div\">\n", $file );
    $file = preg_replace( '/(<h3> Frequently Asked Questions <\/h3>)/', "</div>\n$1\n<div id=\"readme-faq\" class=\"readme-div\">\n", $file );
    $file = preg_replace( '/(<h3> Screenshots <\/h3>)/', "</div>\n$1\n<div id=\"readme-screenshots\" class=\"readme-div\">\n", $file );
    $file = preg_replace( '/(<h3> Arbitrary section <\/h3>)/', "</div>\n$1\n<div id=\"readme-arbitrary\" class=\"readme-div\">\n", $file );
    $file = preg_replace( '/(<h3> Changelog <\/h3>)/', "</div>\n$1\n<div id=\"readme-changelog\" class=\"readme-changelog readme-div\">\n", $file );
    $file = $file.'</div>';

    return $file;

  }


  /**
   * {}
   *
   * @source http://shauninman.com/archive/2008/01/08/recovering_truncated_php_serialized_arrays
   */
  function repair_serialized_array($serialized) {
    $tmp = preg_replace('/^a:\d+:\{/', '', $serialized);
    return Flawless_F::repair_serialized_array_callback($tmp); // operates on and whittles down the actual argument
  }


  /**
   * The recursive function that does all of the heavy lifing. Do not call directly.
   *
   *
   */
  function repair_serialized_array_callback(&$broken){

      $data		= array();
      $index		= null;
      $len		= strlen($broken);
      $i			= 0;

      while(strlen($broken)) {
        $i++;
        if ($i > $len)
        {
          break;
        }

        if (substr($broken, 0, 1) == '}') // end of array
        {
          $broken = substr($broken, 1);
          return $data;
        }
        else
        {
          $bite = substr($broken, 0, 2);
          switch($bite)
          {
            case 's:': // key or value
              $re = '/^s:\d+:"([^\"]*)";/';
              if (preg_match($re, $broken, $m))
              {
                if ($index === null)
                {
                  $index = $m[1];
                }
                else
                {
                  $data[$index] = $m[1];
                  $index = null;
                }
                $broken = preg_replace($re, '', $broken);
              }
            break;

            case 'i:': // key or value
              $re = '/^i:(\d+);/';
              if (preg_match($re, $broken, $m))
              {
                if ($index === null)
                {
                  $index = (int) $m[1];
                }
                else
                {
                  $data[$index] = (int) $m[1];
                  $index = null;
                }
                $broken = preg_replace($re, '', $broken);
              }
            break;

            case 'b:': // value only
              $re = '/^b:[01];/';
              if (preg_match($re, $broken, $m))
              {
                $data[$index] = (bool) $m[1];
                $index = null;
                $broken = preg_replace($re, '', $broken);
              }
            break;

            case 'a:': // value only
              $re = '/^a:\d+:\{/';
              if (preg_match($re, $broken, $m))
              {
                $broken			= preg_replace('/^a:\d+:\{/', '', $broken);
                $data[$index]	= Flawless_F::repair_serialized_array_callback($broken);
                $index = null;
              }
            break;

            case 'N;': // value only
              $broken = substr($broken, 2);
              $data[$index]	= null;
              $index = null;
            break;
          }
        }
      }

      return $data;
    }



 /**
  * Determine if an item is in array and return checked
  *
  *
  * @since 1.5.17
  */
 function checked_in_array($item, $array) {

    if(is_array($array) && in_array($item, $array)) {
      echo ' checked="checked" ';
    }

 }


  /**
   * Removes all metaboxes from given page
   *
   * Should be called by function in add_meta_boxes_$post_type
   * Cycles through all metaboxes
   *
   * @since 1.1
   */
   function remove_object_ui_elements($post_type, $remove_elements) {
    global $wp_meta_boxes, $_wp_post_type_features;

		if(!is_array($remove_elements)) {
			$remove_elements = array($remove_elements);
    }

    if(is_array($wp_meta_boxes[$post_type])) {
      foreach($wp_meta_boxes[$post_type] as $context_slug => $priority_array) {

        foreach($priority_array as $priority_slug => $meta_box_array) {

          foreach($meta_box_array as $meta_box_slug => $meta_bog_data) {

            if(in_array($meta_box_slug, $remove_elements))
              unset($wp_meta_boxes[$post_type][$context_slug][$priority_slug][$meta_box_slug]);
          }
        }
      }
    }


    if(is_array($_wp_post_type_features[$post_type])) {
      // Remove features
      foreach($_wp_post_type_features[$post_type] as $feature => $enabled) {

        if(in_array($feature, $remove_elements))
          unset($_wp_post_type_features[$post_type][$feature]);

      }
    }
   }



  /**
   * Load a template part into a template
   *
   * Same as default get_template_part() but returned as a variable.
   *
   * @version 1.6
   */
  function get_template_part( $slug, $name = null ) {

    do_action( "get_template_part_{$slug}", $slug, $name );

    $templates = array();
    if ( isset($name) )
      $templates[] = "{$slug}-{$name}.php";

    $templates[] = "{$slug}.php";

    ob_start();
    locate_template($templates, true, false);
    $return = ob_get_clean();

    if( empty( $return ) ) {
      return false;
    }

    return $return;

  }

  /**
   * Generates .htaccess file for rewrites if it doesn't exist.
   *
   * Same as default save_mod_rewrite_rules() but can be called from front-end.
   *
   * @version 1.6
   */
  static function save_mod_rewrite_rules( $args = array() ) {
    global $wp_rewrite;

   	$home = get_option( 'home' );
  	$siteurl = get_option( 'siteurl' );

  	if ( $home != '' && $home != $siteurl ) {
  		$wp_path_rel_to_home = str_replace($home, '', $siteurl); /* $siteurl - $home */
	   	$pos = strpos($_SERVER["SCRIPT_FILENAME"], $wp_path_rel_to_home);
  		$home_path = substr($_SERVER["SCRIPT_FILENAME"], 0, $pos);
	   	$home_path = trailingslashit( $home_path );
	 } else {
		  $home_path = ABSPATH;
  	}

    $htaccess_file = $home_path. '.htaccess';

  	if( !file_exists( $htaccess_file ) && is_writable( $home_path ) && $wp_rewrite->using_mod_rewrite_permalinks() ) {

      /** WordPress Administration File API */
      require_once( ABSPATH . 'wp-admin/includes/file.php' );

      /** WordPress Misc Administration API */
      require_once( ABSPATH . 'wp-admin/includes/misc.php' );

      save_mod_rewrite_rules();

      return true;

    }

    return false;

  }


  /**
   * Return simple array of column tables in a table
   *
   * @version 1.6
   */
  function get_column_names( $table ) {

    global $wpdb;

    $table_info = $wpdb->get_results( "SHOW COLUMNS FROM $table" );

    if( empty( $table_info ) ) {
      return array();
    }

    foreach( (array) $table_info as $row ) {
      $columns[] = $row->Field;
    }

    return $columns;

  }


  /**
   * Creates a Quick-Access table for post
   *
   * @param $table_name Can be anything but for consistency should use Post Type slug.
   * @param $args
   *    - update - Either existing Post Type or ID of a post.  Post Type will trigger update for all posts.
   *
   * @author potanin@UD
   * @version 1.6
   */
  function update_qa_table( $table_name = false , $args = false ) {
    global $wpdb;

    $args = array_filter( wp_parse_args( $args, array(
      'table_name' => $wpdb->base_prefix . 'ud_qa_' . $table_name,
      'drop_current' => false,
      'attributes' => array(),
      'update' => array(),
      'debug' => false
   )));

    $return = array();

    if( $args[ 'debug' ] ) {
      self::sql_log( 'enable' );
    }

    /* Remove current table */
    if( $args[ 'drop_current' ] ) {
      $wpdb->query( "DROP TABLE {$args['table_name']}" );
    }

    /* Check if this table exists */
    if( $wpdb->get_var( "SHOW TABLES LIKE '{$args['table_name']}' " ) != $args[ 'table_name' ] ) {
      $wpdb->query( "CREATE TABLE {$args['table_name']} (
        post_id mediumint(9) NOT NULL,
        ud_last_update timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY post_id ( post_id ) ) ENGINE = MyISAM" );
    }

    $args[ 'current_columns' ] = Flawless_F::get_column_names( $args[ 'table_name' ] );

    /* Add attributes, if they don't exist, to table */
    foreach( (array) $args[ 'attributes' ] as $attribute => $type ) {

      $type = is_array( $type ) ? $type[ 'type' ] : $type;

      if( $type  == 'taxonomy' ){
        $wpdb->query( "ALTER TABLE {$args['table_name'] } ADD {$attribute}_ids VARCHAR( 512 ) NULL DEFAULT NULL, COMMENT '{$type}', ADD FULLTEXT INDEX ( {$attribute}_ids ) ;" );
        $wpdb->query( "ALTER TABLE {$args['table_name'] } ADD {$attribute} VARCHAR( 512 ) NULL DEFAULT NULL, COMMENT '{$type}', ADD FULLTEXT INDEX ( {$attribute} )" );
      }else{
        $wpdb->query( "ALTER TABLE {$args['table_name'] } ADD {$attribute} VARCHAR( 512 ) NULL DEFAULT NULL, COMMENT '{$type}', ADD FULLTEXT INDEX ( {$attribute} )" );
      }

    }

    /* If no update requested, leave */
    if( !$args[ 'update' ] ) {
      return true;
    }

    /* Determine update type and initiate updater */
    foreach( (array) $args[ 'update' ] as $update_type ) {

      if( is_numeric( $update_type ) ) {

        $insert_id = Flawless_F::update_qa_table_item( $update_type, $args );

        if( !is_wp_error( $insert_id ) ) {
          $return[ 'updated' ][] = $insert_id;
        } else {
          $return[ 'error' ][] = $insert_id->get_error_message();
        }

      }

      if( post_type_exists ( $update_type ) ) {
        foreach( (object) $wpdb->get_col( "SELECT ID FROM {$wpdb->posts} WHERE post_status = 'publish' AND post_type = '{$update_type}' " ) as $post_id ) {

          $insert_id = Flawless_F::update_qa_table_item( $post_id, $args );

          if( !is_wp_error( $insert_id ) ) {
            $return[ 'updated' ][] = $insert_id;
          } else {
            $return[ 'error' ][] = $insert_id->get_error_message();
          }

        }
      }

    }

    if( $args[ 'debug' ] ) {
      self::sql_log( 'disable' );
      $return[ 'debug' ] = self::sql_log( 'print_log' );
    }

    return $return;

  }


  /**
   * Update post data in QA table
   *
   * @author potanin@UD
   * @version 1.6
   */
  function update_qa_table_item( $post_id = false, $args ) {
    global $wpdb;

    ini_set('memory_limit', -1);

    $types = array();

    /* Organize requested  meta by type */
    foreach( (array) $args[ 'attributes' ] as $attribute_key => $type ) {

      $type = is_array( $type ) ? $type[ 'type' ] : $type;

      $types[ $type ][] = $attribute_key;
      $types[ $type ] = array_filter( (array) $types[ $type ] );
    }

    /* Get Primary Data */
    if( !empty( $types[ 'primary' ] ) ) {
      $insert = $wpdb->get_row( "SELECT ID as post_id, " . implode( ', ', $types[ 'primary' ] ) . " FROM {$wpdb->posts} WHERE ID = {$post_id} ", ARRAY_A );
    }

    /* Get Meta Data */
    if( !empty( $types[ 'post_meta' ] ) ) {
      foreach( (object) $wpdb->get_results( "SELECT meta_key, meta_value FROM {$wpdb->postmeta} WHERE post_id = {$post_id} AND meta_key IN ( '" . implode( "', '", $types[ 'post_meta' ] ) . "' ); ") as $row ) {
        $insert[ $row->meta_key ] .= $row->meta_value.',';
      }
      /* Remove leading/trailing commas */
      foreach( (array) $types[ 'post_meta' ] as $type ){
        $insert[ $type ] = trim( $insert[ $type ], ',' );
      }
    }

    if( !empty( $types[ 'taxonomy' ] ) ) {
      foreach( (object) $wpdb->get_results( "
      SELECT {$wpdb->term_taxonomy}.term_id, taxonomy, name FROM {$wpdb->terms}
      LEFT JOIN {$wpdb->term_taxonomy} on {$wpdb->terms}.term_id = {$wpdb->term_taxonomy}.term_id
      LEFT JOIN {$wpdb->term_relationships} on {$wpdb->term_relationships}.term_taxonomy_id = {$wpdb->term_taxonomy}.term_taxonomy_id
      WHERE object_id = $post_id AND taxonomy IN ( '" . implode( "', '", $types[ 'taxonomy' ] ) . "' ); " ) as $row ) {
        $insert[ $row->taxonomy.'_ids' ] .= $row->term_id.',';
        $insert[ $row->taxonomy ] .= $row->name.',';
      }

      /* Loop again, removing trailing/leading commas */
      foreach( (array) $types[ 'taxonomy' ] as $taxonomy ){
        $insert[ $taxonomy ] = trim( $insert[ $taxonomy ], ',' );
        $insert[ $taxonomy.'_ids' ] = trim( $insert[ $taxonomy.'_ids' ], ',' );
      }
    }

    /** Get the serialized item */
    $insert[ 'object' ] = json_encode( get_event( $post_id ) );

    $insert = array_filter( (array) $insert );

    if( $wpdb->get_var( "SELECT post_id FROM {$args[ 'table_name' ]} WHERE post_id = {$post_id} " ) == $post_id ) {
      $wpdb->update( $args[ 'table_name' ], $insert, array( 'post_id' => $post_id ) );
      $response = $post_id;
    } else {
      if( $wpdb->insert( $args[ 'table_name' ], $insert ) ) {
        $response = $wpdb->insert_id;
      }
    }

    return $response ? $response : new WP_Error( 'error' , $wpdb->print_error() ? $wpdb->print_error() : __( 'Unknown error.' . $wpdb->last_query ) );

  }


  /**
   * Merges any number of arrays / parameters recursively,
   *
   * Replacing entries with string keys with values from latter arrays.
   * If the entry or the next value to be assigned is an array, then it
   * automagically treats both arguments as an array.
   * Numeric entries are appended, not replaced, but only if they are
   * unique
   *
   * @source http://us3.php.net/array_merge_recursive
   * @version 1.4
  */
   static function array_merge_recursive_distinct () {
    $arrays = func_get_args();
    $base = array_shift( $arrays );
    if( !is_array( $base ) ) $base = empty( $base ) ? array() : array( $base );
    foreach( (array) $arrays as $append ) {
    if( !is_array( $append ) ) $append = array( $append );
    foreach( (array) $append as $key => $value ) {
      if( !array_key_exists( $key, $base ) and !is_numeric( $key ) ) {
      $base[ $key ] = $append[ $key ];
      continue;
      }
      if( @is_array( $value ) or @is_array( $base[ $key ] ) ) {
      $base[ $key ] = self::array_merge_recursive_distinct( $base[ $key ], $append[ $key ] );
      } else if( is_numeric( $key ) ) {
      if( !in_array( $value, $base ) ) $base[] = $value;
      } else {
      $base[ $key ] = $value;
      }
    }
    }
    return $base;
  }


  /**
   * Returns a URL to a post object based on passed variable.
   *
   * If its a number, then assumes its the id, If it resembles a slug, then get the first slug match.
   *
   * @since 1.0
   * @param string $title A page title, although ID integer can be passed as well
   * @return string The page's URL if found, otherwise the general blog URL
   */
  function post_link( $title = false ) {
    global $wpdb;

    if( !$title )
      return get_bloginfo( 'url' );

    if( is_numeric( $title ) )
      return get_permalink( $title );

        if( $id = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_name = '$title'  AND post_status='publish'" ) )
      return get_permalink( $id );

    if( $id = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE LOWER( post_title ) = '".strtolower( $title )."'   AND post_status='publish'" ) )
      return get_permalink( $id );

  }


  /**
   * Add an entry to the plugin-specifig log.
   *
   * Creates log if one does not exist.
   *
   * = USAGE =
   * self::log( "Settings updated" );
   *
   */
  function log( $message = false , $type = 'default' ) {

    self::check_prefix();

    $current_user = wp_get_current_user();
    $log_name = UD_PREFIX . 'log';
    $this_log = get_option( $log_name );

    if( empty( $this_log ) ) {

      $this_log = array();

      $entry = array(
        'time' => time(),
        'message' => __( 'Log Started.' , UD_Lang ),
        'user' => $current_user->ID,
        'type' => $type
     );

    }

    if( $message ) {

      $entry = array(
        'time' => time(),
        'message' => $message,
        'user' => $type == 'system' ? 'system' : $current_user->ID,
        'type' => $type
     );

    }

    if( !is_array( $entry ) ) {
      return false;
    }

    array_push( $this_log, $entry );

    $this_log = array_filter( $this_log );

    update_option( $log_name, $this_log );

    return true;
  }


  /**
   * Used to get the current plugin's log created via UD class
   *
   * If no log exists, it creates one, and then returns it in chronological order.
   *
   * Example to view log:
   * <code>
   * print_r( self::get_log() );
   * </code>
   *
   * $param string Event description
   * @uses get_option()
   * @uses update_option()
   * @uses check_prefix()
   * @return array Using the get_option function returns the contents of the log.
   *
   */
  function get_log( $args = false ) {
    self::check_prefix();

    $args = wp_parse_args( $args, array(
      'limit' => 20
   ));

    $log_name = UD_PREFIX . 'log';

    $this_log = get_option( $log_name );

    if( empty( $this_log ) ) {
      $this_log = self::log();
    }

    $entries = (array) get_option( $log_name );

    $entries = array_reverse( $entries );

    $entries = array_slice($entries, 0, $args[ 'args' ] ? $args[ 'args' ] : 10 );

    return $entries;

  }


  /**
   * Delete UD log for this plugin.
   *
   *
   * @uses update_option()
   * @uses check_prefix()
    *
   */
  function delete_log() {
    self::check_prefix();

    $log_name = UD_PREFIX . 'log';

    delete_option( $log_name );

  }


  /**
   * Check if a prefix is defined, if not defines one automatically.
   *
   *
   */
  function check_prefix( $prefix = false ) {

    if( !defined( 'UD_PREFIX' ) ) {
      define( UD_PREFIX, $prefix ? $prefix : 'ud_' );
    }

  }


  /**
   * Displays the numbers of days elapsed between a provided date and today.
   *
   */
  function days_since( $from, $to = false ) {
    human_time_diff( $from, $to );
  }


  /**
   * Creates Admin Menu page for UD Log
    *
   * @todo Need to make sure this will work if multiple plugins utilize the UD classes
   * @see function show_log_page
   * @since 1.0
   * @uses add_action() Calls 'admin_menu' hook with an anonymous ( lambda-style ) function which uses add_menu_page to create a UI Log page
   */
  function add_log_page() {
    add_action( 'admin_menu', create_function( '', "add_menu_page( __( 'Log' ,UD_Lang ), __( 'Log',UD_Lang ), 10, 'ud_log', array( 'Flawless_F','show_log_page' ) );" ) );
  }



  /**
   * Displays the UD UI log page
    *
   * @todo Add button or link to delete log
   * @todo Add nonce to clear_log functions
   * @since 1.0
   * @uses Flawless_F::delete_log()
   * @uses Flawless_F::get_log()
   * @uses Flawless_F::nice_time()
   * @uses add_action() Calls 'admin_menu' hook with an anonymous (lambda-style) function which uses add_menu_page to create a UI Log page
   */

  function show_log_page() {

    if($_REQUEST['ud_action'] == 'clear_log') {
      Flawless_F::delete_log();
    }

    ?>
    <style type="text/css">.ud_event_row b { background:none repeat scroll 0 0 #F6F7DC; padding:2px 6px;}</style>

    <div class="wrap">
    <h2><?php _e('UD Log Page for','wpp') ?> get_option('<?php echo UD_PREFIX . 'log'; ?>');
    <a href="<?php echo admin_url("admin.php?page=ud_log&ud_action=clear_log"); ?>" class="button"><?php _e('Clear Log','wpp') ?></a>
    </h2>


    <table class="widefat">
      <thead>
      <tr>
        <th style="width: 150px"><?php _e('Timestamp','wpp') ?></th>
        <th><?php _e('Type','wpp') ?></th>
        <th><?php _e('Event','wpp') ?></th>
        <th><?php _e('User','wpp') ?></th>
        <th><?php _e('Related Object','wpp') ?></th>
      </tr>
      </thead>

      <tbody>
      <?php foreach(Flawless_F::get_log() as $event): ?>
      <tr class="ud_event_row">
        <td><?php echo Flawless_F::nice_time($event[0]); ?></td>
        <td><?php echo $event[1]; ?></td>
        <td><?php echo $event[2]; ?></td>
        <td><?php $user_data = get_userdata($event[2]); echo $user_data->display_name; ?></td>
        <td><?php echo $event[4]; ?></td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    </div>
    <?php

   }

  /**
   * Turns a passed string into a URL slug
   *
   * Argument 'check_existance' will make the function check if the slug is used by a WordPress post
   *
   * @param string $content
   * @param string $args Optional list of arguments to overwrite the defaults.
   * @since 1.0
   * @uses add_action() Calls 'admin_menu' hook with an anonymous (lambda-style) function which uses add_menu_page to create a UI Log page
   * @return string
   */
  static function create_slug($content, $args = false) {

    $defaults = array(
      'separator' => '-',
      'check_existance' => false
   );

    extract( wp_parse_args( $args, $defaults ), EXTR_SKIP );

    $content = preg_replace('~[^\\pL0-9_]+~u', $separator, $content); // substitutes anything but letters, numbers and '_' with separator
    $content = trim($content, $separator);
    $content = iconv("utf-8", "us-ascii//TRANSLIT", $content); // TRANSLIT does the whole job
    $content = strtolower($content);
    $slug = preg_replace('~[^-a-z0-9_]+~', '', $content); // keep only letters, numbers, '_' and separator

    return $slug;
  }

  /**
   * Convert a slug to a more readable string
   *
   * @since 1.3
   * @return string
   */
  function de_slug( $string ) {
    return  ucwords( str_replace( "_", " ", $string ) );

  }


  /**
   * Returns location information from Google Maps API call
   *
   *
   * @since 1.2
   * @return object
   */
  function geo_locate_address( $address = false, $localization = "en", $return_obj_on_fail = false ) {

    if( !$address ) {
      return false;
    }

    if( is_array( $address ) ) {
      return false;
    }

    $address = urlencode( $address );

    $url = str_replace( " ", "+" ,"http://maps.google.com/maps/api/geocode/json?address={$address}&sensor=true&language=$localization" );

    $obj = ( json_decode( wp_remote_fopen( $url ) ) );

    if( $obj->status != "OK" ) {

      if( $return_obj_on_fail ) {
        return $obj;
      }

      return false;

    }

    $results = $obj->results;
    $results_object = $results[ 0 ];
    $geometry = $results_object->geometry;

    $return->formatted_address = $results_object->formatted_address;
    $return->latitude = $geometry->location->lat;
    $return->longitude = $geometry->location->lng;

    foreach( (array) $results_object->address_components as $ac ) {

      foreach( (array) $ac->types as $type ) {
        switch( $type ){

          case 'street_number':
            $return->street_number = $ac->long_name;
          break;

          case 'route':
            $return->route = $ac->long_name;
          break;

          case 'locality':
              $return->city = $ac->long_name;
          break;

          case 'administrative_area_level_3':
            if( empty( $return->city ) )
            $return->city = $ac->long_name;
          break;

          case 'administrative_area_level_2':
            $return->county = $ac->long_name;
          break;

          case 'administrative_area_level_1':
            $return->state = $ac->long_name;
            $return->state_code = $ac->short_name;
          break;

          case 'country':
            $return->country = $ac->long_name;
            $return->country_code = $ac->short_name;
          break;

          case 'postal_code':
            $return->postal_code = $ac->long_name;
          break;

          case 'sublocality':
            $return->district = $ac->long_name;
          break;

        }
      }
    }

    $return = apply_filters( 'ud::geo_locate_address', $return, $results_object, $address, $localization );

    return $return;

  }



  /**
   * This function splits our shortcode attributes
   *
   * ex. "key3=value4,key5=value5"
   * to. array( 'key3' => 'value4', 'key5' => 'value5' )
   */
  function split_shortcode_att( $str = '' ){
    if( empty( $str ) ) return $str;
    $ret = array();
    $csvs = explode( ',', $str );
    foreach( (array) $csvs as $csv ){
      $values = explode( '=', $csv );
      if( count($values) < 2 ) continue;
      $ret[ $values[ 0 ] ] = $values[ 1 ];
    }
    return $ret;
  }

}



