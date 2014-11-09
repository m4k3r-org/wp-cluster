<?php
/**
 * Cache Controller
 *
 * @module Cluster
 * @author potanin@UD
 */
namespace UsabilityDynamics\Veneer {

  if( !class_exists( 'UsabilityDynamics\Veneer\Cache' ) ) {

    /**
     * Class Cache
     *
     * Based on http://wordpress.org/plugins/memcached/.
     *
     * @module Cluster
     */
    class Cache {

      /**
       * Basic Minification.
       *
       * @param $buffer
       *
       * @source https://coderwall.com/p/fatjmw
       * @return mixed|string
       */
      static function minify( $buffer ) {

        return self::minify_html( $buffer );

        // Strip Linebreaks.
        $buffer = str_replace( array( "\n","\r","\t" ), '', $buffer );

        // Strip Comments.
        $buffer = preg_replace( '/<!--(.*)-->/Uis', '', $buffer );

        //preg_replace('/<!--(.*)-->/Uis', '', $html)

        // Strip whitespace.
        $buffer = preg_replace( '/\s+/', ' ', $buffer );

        return trim( $buffer );

      }

      static function minify_css( $text ) {
        $from = array(
          //                  '%(#|;|(//)).*%',               // comments:  # or //
          '%/\*(?:(?!\*/).)*\*/%s', // comments:  /*...*/
          '/\s{2,}/', // extra spaces
          "/\s*([;{}])[\r\n\t\s]/", // new lines
          '/\\s*;\\s*/', // white space (ws) between ;
          '/\\s*{\\s*/', // remove ws around {
          '/;?\\s*}\\s*/', // remove ws around } and last semicolon in declaration block
          //                  '/:first-l(etter|ine)\\{/',     // prevent triggering IE6 bug: http://www.crankygeek.com/ie6pebug/
          //                  '/((?:padding|margin|border|outline):\\d+(?:px|em)?) # 1 = prop : 1st numeric value\\s+/x',     // Use newline after 1st numeric value (to limit line lengths).
          //                  '/([^=])#([a-f\\d])\\2([a-f\\d])\\3([a-f\\d])\\4([\\s;\\}])/i',
        );
        $to   = array(
          //                  '',
          '',
          ' ',
          '$1',
          ';',
          '{',
          '}',
          //                  ':first-l$1 {',
          //                  "$1\n",
          //                  '$1#$2$3$4$5',
        );
        $text = preg_replace( $from, $to, $text );

        return $text;
      }

      static function minify_js( $text ) {

        $file_cache = strtolower( md5( $text ) );
        $folder     = WP_CONTENT_DIR . '/cache' . DIRECTORY_SEPARATOR . substr( $file_cache, 0, 2 ) . DIRECTORY_SEPARATOR;

        if( !is_dir( $folder ) ) @mkdir( $folder, 0766, true );

        if( !is_dir( $folder ) ) {
          echo 'Impossible to create the cache folder:' . $folder;

          return 1;
        }

        $file_cache = $folder . $file_cache . '_content.js';

        if( !file_exists( $file_cache ) ) {
          if( strlen( $text ) <= 100 ) {
            $contents = $text;
          } else {
            $contents      = '';
            $post_text     = http_build_query( array(
              'js_code'           => $text,
              'output_info'       => 'compiled_code', //($returnErrors ? 'errors' : 'compiled_code'),
              'output_format'     => 'text',
              'compilation_level' => 'SIMPLE_OPTIMIZATIONS', //'ADVANCED_OPTIMIZATIONS',//'SIMPLE_OPTIMIZATIONS'
            ), null, '&' );
            $URL           = 'http://closure-compiler.appspot.com/compile';
            $allowUrlFopen = preg_match( '/1|yes|on|true/i', ini_get( 'allow_url_fopen' ) );
            if( $allowUrlFopen ) {
              $contents = file_get_contents( $URL, false, stream_context_create( array(
                'http' => array(
                  'method'        => 'POST',
                  'header'        => 'Content-type: application/x-www-form-urlencoded',
                  'content'       => $post_text,
                  'max_redirects' => 0,
                  'timeout'       => 15,
                )
              ) ) );
            } elseif( defined( 'CURLOPT_POST' ) ) {
              $ch = curl_init( $URL );
              curl_setopt( $ch, CURLOPT_POST, true );
              curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
              curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-type: application/x-www-form-urlencoded' ) );
              curl_setopt( $ch, CURLOPT_POSTFIELDS, $post_text );
              curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, false );
              curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 15 );
              $contents = curl_exec( $ch );
              curl_close( $ch );
            } else {
              //"Could not make HTTP request: allow_url_open is false and cURL not available"
              $contents = $text;
            }
            if( $contents == false || ( trim( $contents ) == '' && $text != '' ) || strtolower( substr( trim( $contents ), 0, 5 ) ) == 'error' || strlen( $contents ) <= 50 ) {
              //No HTTP response from server or empty response or error
              $contents = $text;
            }
          }
          if( trim( $contents ) != '' ) {
            $contents = trim( $contents );
            $f        = fopen( $file_cache, 'w' );
            fwrite( $f, $contents );
            fclose( $f );
          }
        } else {
          touch( $file_cache ); //in the future I will add a timetout to the cache
          $contents = file_get_contents( $file_cache );
        }

        return $contents;
      }

      static function minify_html( $text ) {

        $file_cache = strtolower( md5( $text ) );
        $folder     = WP_CONTENT_DIR . '/cache' . DIRECTORY_SEPARATOR . substr( $file_cache, 0, 2 ) . DIRECTORY_SEPARATOR;

        if( !is_dir( $folder ) ) @mkdir( $folder, 0766, true );

        if( !is_dir( $folder ) ) {
          echo 'Impossible to create the cache folder:' . $folder;

          return 1;
        }

        $file_cache = $folder . $file_cache . '_content.html';

        if( !file_exists( $file_cache ) ) {
          //get CSS and save it
          $search_css = '/<\s*style\b[^>]*>(.*?)<\s*\/style>/is';
          $ret        = preg_match_all( $search_css, $text, $tmps );
          $t_css      = array();
          if( $ret !== false && $ret > 0 ) {
            foreach( $tmps as $k => $v ) {
              if( $k > 0 ) {
                foreach( $v as $kk => $vv ) {
                  $t_css[ ] = $vv;
                }
              }
            }
          }
          $css = self::minify_css( implode( '\n', $t_css ) );

          /*
                      //get external JS and save it
                      $search_js_ext = '/<\s*script\b.*?src=\s*[\'|"]([^\'|"]*)[^>]*>\s*<\s*\/script>/i';
                      $ret = preg_match_all($search_js_ext, $text, $tmps);
                      $t_js = array();
                      if($ret!==false && $ret>0){
                          foreach($tmps as $k=>$v){
                              if($k>0){
                                  foreach($v as $kk=>$vv){
                                      $t_js[] = $vv;
                                  }
                              }
                          }
                      }
                      $js_ext = $t_js;
          */
          //get inline JS and save it
          $search_js_ext = '/<\s*script\b.*?src=\s*[\'|"]([^\'|"]*)[^>]*>\s*<\s*\/script>/i';
          $search_js     = '/<\s*script\b[^>]*>(.*?)<\s*\/script>/is';
          $ret           = preg_match_all( $search_js, $text, $tmps );
          $t_js          = array();
          $js_ext        = array();

          if( $ret !== false && $ret > 0 ) {
            foreach( $tmps as $k => $v ) {
              if( $k == 0 ) {
                //let's check if we have a souce (src="")
                foreach( $v as $kk => $vv ) {
                  if( $vv != '' ) {
                    $ret = preg_match_all( $search_js_ext, $vv, $ttmps );
                    if( $ret !== false && $ret > 0 ) {
                      foreach( $ttmps[ 1 ] as $kkk => $vvv ) {
                        $js_ext[ ] = $vvv;
                      }
                    }
                  }
                }
              } else {
                foreach( $v as $kk => $vv ) {
                  if( $vv != '' ) {
                    $t_js[ ] = $vv;
                  }
                }
              }
            }
          }

          $js = self::minify_js( implode( '\n', $t_js ) );

          //get inline noscript and save it
          $search_no_js = '/<\s*noscript\b[^>]*>(.*?)<\s*\/noscript>/is';
          $ret          = preg_match_all( $search_no_js, $text, $tmps );
          $t_js         = array();
          if( $ret !== false && $ret > 0 ) {
            foreach( $tmps as $k => $v ) {
              if( $k > 0 ) {
                foreach( $v as $kk => $vv ) {
                  $t_js[ ] = $vv;
                }
              }
            }
          }
          $no_js = implode( '\n', $t_js );

          //remove CSS and JS
          $search  = array(
            $search_js_ext,
            $search_css,
            $search_js,
            $search_no_js,
            '/\>[^\S ]+/s', //strip whitespaces after tags, except space
            '/[^\S ]+\</s', //strip whitespaces before tags, except space
            '/(\s)+/s', // shorten multiple whitespace sequences
          );
          $replace = array(
            '',
            '',
            '',
            '',
            '>',
            '<',
            '\\1',
          );
          $buffer  = preg_replace( $search, $replace, $text );

          $append = '';
          //add CSS and JS at the bottom
          if( is_array( $js_ext ) && count( $js_ext ) > 0 ) {
            foreach( $js_ext as $k => $v ) {
              $append .= '<script type="text/javascript" language="javascript" src="' . $v . '" ></script>';
            }
          }
          if( $css != '' ) $append .= '<style>' . $css . '</style>';
          if( $js != '' ) {
            //remove weird '\n' strings
            $js = preg_replace( '/[\s]*\\\n/', "\n", $js );
            $append .= '<script>' . $js . '</script>';
          }
          if( $no_js != '' ) $append .= '<noscript>' . $no_js . '</noscript>';
          $buffer = preg_replace( '/(.*)(<\s*\/\s*body\s*>)(.*)/', '\\1' . $append . '\\2\\3', $buffer );
          if( trim( $buffer ) != '' ) {
            $f = fopen( $file_cache, 'w' );
            fwrite( $f, trim( $buffer ) );
            fclose( $f );
          }
        } else {
          touch( $file_cache ); //in the future I will add a timetout to the cache
          $buffer = file_get_contents( $file_cache );
        }

        return $buffer;

      }

      var $global_groups = array();

      var $no_mc_groups = array();

      var $cache = array();
      var $mc = array();
      var $stats = array();
      var $group_ops = array();

      var $cache_enabled = true;
      var $default_expiration = 0;

      /**
       * Initialize Cache
       *
       * @for Cache
       */
      function __construct() {
        global $memcached_servers;

        if( isset( $memcached_servers ) )
          $buckets = $memcached_servers;
        else
          $buckets = array( '127.0.0.1' );

        reset( $buckets );
        if( is_int( key( $buckets ) ) )
          $buckets = array( 'default' => $buckets );

        foreach( $buckets as $bucket => $servers ) {
          $this->mc[ $bucket ] = new Memcache();
          foreach( $servers as $server ) {
            list ( $node, $port ) = explode( ':', $server );
            if( !$port )
              $port = ini_get( 'memcache.default_port' );
            $port = intval( $port );
            if( !$port )
              $port = 11211;
            $this->mc[ $bucket ]->addServer( $node, $port, true, 1, 1, 15, true, array( $this, 'failure_callback' ) );
            $this->mc[ $bucket ]->setCompressThreshold( 20000, 0.2 );
          }
        }

        global $blog_id, $table_prefix;
        $this->global_prefix = '';
        $this->blog_prefix   = '';
        if( function_exists( 'is_multisite' ) ) {
          $this->global_prefix = ( is_multisite() || defined( 'CUSTOM_USER_TABLE' ) && defined( 'CUSTOM_USER_META_TABLE' ) ) ? '' : $table_prefix;
          $this->blog_prefix   = ( is_multisite() ? $blog_id : $table_prefix ) . ':';
        }

        $this->cache_hits   =& $this->stats[ 'get' ];
        $this->cache_misses =& $this->stats[ 'add' ];
      }

      function wp_cache_add( $key, $data, $group = '', $expire = 0 ) {
        global $wp_object_cache;

        return $wp_object_cache->add( $key, $data, $group, $expire );
      }

      function wp_cache_incr( $key, $n = 1, $group = '' ) {
        global $wp_object_cache;

        return $wp_object_cache->incr( $key, $n, $group );
      }

      function wp_cache_decr( $key, $n = 1, $group = '' ) {
        global $wp_object_cache;

        return $wp_object_cache->decr( $key, $n, $group );
      }

      function wp_cache_close() {
        global $wp_object_cache;

        return $wp_object_cache->close();
      }

      function wp_cache_delete( $key, $group = '' ) {
        global $wp_object_cache;

        return $wp_object_cache->delete( $key, $group );
      }

      function wp_cache_flush() {
        global $wp_object_cache;

        return $wp_object_cache->flush();
      }

      function wp_cache_get( $key, $group = '', $force = false ) {
        global $wp_object_cache;

        return $wp_object_cache->get( $key, $group, $force );
      }

      function wp_cache_init() {
        global $wp_object_cache;

        $wp_object_cache = new Cache();
      }

      function wp_cache_replace( $key, $data, $group = '', $expire = 0 ) {
        global $wp_object_cache;

        return $wp_object_cache->replace( $key, $data, $group, $expire );
      }

      function wp_cache_set( $key, $data, $group = '', $expire = 0 ) {
        global $wp_object_cache;

        if( defined( 'WP_INSTALLING' ) == false )
          return $wp_object_cache->set( $key, $data, $group, $expire );
        else
          return $wp_object_cache->delete( $key, $group );
      }

      function wp_cache_add_global_groups( $groups ) {
        global $wp_object_cache;

        $wp_object_cache->add_global_groups( $groups );
      }

      function wp_cache_add_non_persistent_groups( $groups ) {
        global $wp_object_cache;

        $wp_object_cache->add_non_persistent_groups( $groups );
      }

      function add( $id, $data, $group = 'default', $expire = 0 ) {
        $key = $this->key( $id, $group );

        if( is_object( $data ) )
          $data = clone $data;

        if( in_array( $group, $this->no_mc_groups ) ) {
          $this->cache[ $key ] = $data;

          return true;
        } elseif( isset( $this->cache[ $key ] ) && $this->cache[ $key ] !== false ) {
          return false;
        }

        $mc     =& $this->get_mc( $group );
        $expire = ( $expire == 0 ) ? $this->default_expiration : $expire;
        $result = $mc->add( $key, $data, false, $expire );

        if( false !== $result ) {
          @ ++$this->stats[ 'add' ];
          $this->group_ops[ $group ][ ] = "add $id";
          $this->cache[ $key ]          = $data;
        }

        return $result;
      }

      function add_global_groups( $groups ) {
        if( !is_array( $groups ) )
          $groups = (array) $groups;

        $this->global_groups = array_merge( $this->global_groups, $groups );
        $this->global_groups = array_unique( $this->global_groups );
      }

      function add_non_persistent_groups( $groups ) {
        if( !is_array( $groups ) )
          $groups = (array) $groups;

        $this->no_mc_groups = array_merge( $this->no_mc_groups, $groups );
        $this->no_mc_groups = array_unique( $this->no_mc_groups );
      }

      function incr( $id, $n = 1, $group = 'default' ) {
        $key                 = $this->key( $id, $group );
        $mc                  =& $this->get_mc( $group );
        $this->cache[ $key ] = $mc->increment( $key, $n );

        return $this->cache[ $key ];
      }

      function decr( $id, $n = 1, $group = 'default' ) {
        $key                 = $this->key( $id, $group );
        $mc                  =& $this->get_mc( $group );
        $this->cache[ $key ] = $mc->decrement( $key, $n );

        return $this->cache[ $key ];
      }

      function close() {

        foreach( $this->mc as $bucket => $mc )
          $mc->close();
      }

      function delete( $id, $group = 'default' ) {
        $key = $this->key( $id, $group );

        if( in_array( $group, $this->no_mc_groups ) ) {
          unset( $this->cache[ $key ] );

          return true;
        }

        $mc =& $this->get_mc( $group );

        $result = $mc->delete( $key );

        @ ++$this->stats[ 'delete' ];
        $this->group_ops[ $group ][ ] = "delete $id";

        if( false !== $result )
          unset( $this->cache[ $key ] );

        return $result;
      }

      function flush() {
        // Don't flush if multi-blog.
        if( function_exists( 'is_site_admin' ) || defined( 'CUSTOM_USER_TABLE' ) && defined( 'CUSTOM_USER_META_TABLE' ) )
          return true;

        $ret = true;
        foreach( array_keys( $this->mc ) as $group )
          $ret &= $this->mc[ $group ]->flush();

        return $ret;
      }

      function get( $id, $group = 'default', $force = false ) {
        $key = $this->key( $id, $group );
        $mc  =& $this->get_mc( $group );

        if( isset( $this->cache[ $key ] ) && ( !$force || in_array( $group, $this->no_mc_groups ) ) ) {
          if( is_object( $this->cache[ $key ] ) )
            $value = clone $this->cache[ $key ];
          else
            $value = $this->cache[ $key ];
        } else if( in_array( $group, $this->no_mc_groups ) ) {
          $this->cache[ $key ] = $value = false;
        } else {
          $value = $mc->get( $key );
          if( NULL === $value )
            $value = false;
          $this->cache[ $key ] = $value;
        }

        @ ++$this->stats[ 'get' ];
        $this->group_ops[ $group ][ ] = "get $id";

        if( 'checkthedatabaseplease' === $value ) {
          unset( $this->cache[ $key ] );
          $value = false;
        }

        return $value;
      }

      function get_multi( $groups ) {
        /*
        format: $get['group-name'] = array( 'key1', 'key2' );
        */
        $return = array();
        foreach( $groups as $group => $ids ) {
          $mc =& $this->get_mc( $group );
          foreach( $ids as $id ) {
            $key = $this->key( $id, $group );
            if( isset( $this->cache[ $key ] ) ) {
              if( is_object( $this->cache[ $key ] ) )
                $return[ $key ] = clone $this->cache[ $key ];
              else
                $return[ $key ] = $this->cache[ $key ];
              continue;
            } else if( in_array( $group, $this->no_mc_groups ) ) {
              $return[ $key ] = false;
              continue;
            } else {
              $return[ $key ] = $mc->get( $key );
            }
          }
          if( $to_get ) {
            $vals   = $mc->get_multi( $to_get );
            $return = array_merge( $return, $vals );
          }
        }
        @ ++$this->stats[ 'get_multi' ];
        $this->group_ops[ $group ][ ] = "get_multi $id";
        $this->cache                  = array_merge( $this->cache, $return );

        return $return;
      }

      function key( $key, $group ) {
        if( empty( $group ) )
          $group = 'default';

        if( false !== array_search( $group, $this->global_groups ) )
          $prefix = $this->global_prefix;
        else
          $prefix = $this->blog_prefix;

        return preg_replace( '/\s+/', '', WP_CACHE_KEY_SALT . "$prefix$group:$key" );
      }

      function replace( $id, $data, $group = 'default', $expire = 0 ) {
        $key    = $this->key( $id, $group );
        $expire = ( $expire == 0 ) ? $this->default_expiration : $expire;
        $mc     =& $this->get_mc( $group );

        if( is_object( $data ) )
          $data = clone $data;

        $result = $mc->replace( $key, $data, false, $expire );
        if( false !== $result )
          $this->cache[ $key ] = $data;

        return $result;
      }

      function set( $id, $data, $group = 'default', $expire = 0 ) {
        $key = $this->key( $id, $group );
        if( isset( $this->cache[ $key ] ) && ( 'checkthedatabaseplease' === $this->cache[ $key ] ) )
          return false;

        if( is_object( $data ) )
          $data = clone $data;

        $this->cache[ $key ] = $data;

        if( in_array( $group, $this->no_mc_groups ) )
          return true;

        $expire = ( $expire == 0 ) ? $this->default_expiration : $expire;
        $mc     =& $this->get_mc( $group );
        $result = $mc->set( $key, $data, false, $expire );

        return $result;
      }

      function colorize_debug_line( $line ) {
        $colors = array(
          'get'    => 'green',
          'set'    => 'purple',
          'add'    => 'blue',
          'delete' => 'red' );

        $cmd = substr( $line, 0, strpos( $line, ' ' ) );

        $cmd2 = "<span style='color:{$colors[$cmd]}'>$cmd</span>";

        return $cmd2 . substr( $line, strlen( $cmd ) ) . "\n";
      }

      function stats() {
        echo "<p>\n";
        foreach( $this->stats as $stat => $n ) {
          echo "<strong>$stat</strong> $n";
          echo "<br/>\n";
        }
        echo "</p>\n";
        echo "<h3>Memcached:</h3>";
        foreach( $this->group_ops as $group => $ops ) {
          if( !isset( $_GET[ 'debug_queries' ] ) && 500 < count( $ops ) ) {
            $ops = array_slice( $ops, 0, 500 );
            echo "<big>Too many to show! <a href='" . add_query_arg( 'debug_queries', 'true' ) . "'>Show them anyway</a>.</big>\n";
          }
          echo "<h4>$group commands</h4>";
          echo "<pre>\n";
          $lines = array();
          foreach( $ops as $op ) {
            $lines[ ] = $this->colorize_debug_line( $op );
          }
          print_r( $lines );
          echo "</pre>\n";
        }

        if( $this->debug )
          var_dump( $this->memcache_debug );
      }

      function &get_mc( $group ) {
        if( isset( $this->mc[ $group ] ) )
          return $this->mc[ $group ];

        return $this->mc[ 'default' ];
      }

      function failure_callback( $host, $port ) {
        //error_log("Connection failure for $host:$port\n", 3, '/tmp/memcached.txt');
      }

    }

  }

}