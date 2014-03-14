<?php
/**
 * Scaffold for Styles and Scripts classes
 * Uses theme customization API
 *
 * @author usabilitydynamics@UD
 * @see https://codex.wordpress.org/Theme_Customization_API
 * @version 0.1
 * @module UsabilityDynamics\AMD
 */
namespace UsabilityDynamics\AMD {

  if( !class_exists( '\UsabilityDynamics\AMD\Scaffold' ) ) {

    abstract class Scaffold {
      
      public static $query_vars = array(
        'amd_asset_type',
        'amd_is_asset',
      );
      
      public $post_type = NULL;
    
      /**
       * Constructor
       * Must be called in child constructor firstly!
       */
      public function __construct( $args = array() ) {
      
        $this->args = wp_parse_args( $args, array(
          'version' => '1.0', 
          'type' => '', // style, script
          'minify' => false,
          'permalink' => '', // assets/amd.js
          'dependencies' => array(),
          'admin_menu' => true,
        ) );
        
        $this->post_type = self::get_post_type( $this->get( 'type' ) );
        
        //** rewrite and respond */
        add_action( 'query_vars', array( $this, 'query_vars' ) );
        add_filter( 'pre_update_option_rewrite_rules', array( $this, 'update_option_rewrite_rules' ), 1 );
        add_filter( 'template_include', array( __CLASS__, 'return_asset' ), 1, 1 );
        
        add_action( 'init', array( $this, 'register_assets' ) );
        
        
        //**  */
        add_action( 'admin_init', array( $this, 'register_post_type' ) );
        
        //** Determine if Admin Menu is enabled */
        if( $this->get( 'admin_menu' ) ) {
          add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
          // Override the edit link, the default link causes a redirect loop
          add_filter( 'get_edit_post_link', array( __CLASS__, 'revision_post_link' ) );
        }
      }
      
      /**
       * Register scripts
       */
      function register_assets() {
        if( !is_admin() ) {
          $url = $this->get_asset_url();
          $dependencies = array();
          if( false !== ( $post_id = $this->get_plugin_post_id( 'javascript' ) ) ):
            $dependencies = $this->get_saved_dependencies( $post_id );
            $this->load_dependencies( $dependencies, 'javascript' );
          endif;
          wp_register_script( 'add-global-javascript', $url, $dependencies, $this->get_latest_version_id( $post_id ) );

          wp_register_style( 'add-global-stylesheet', $url, $dependencies, $this->get_latest_version_id( $post_id ) );
        }
      }
      
      /**
       * Global JS URL
       * @return bool|string
       */
      public function get_asset_url() {
        global $wp_rewrite;
        if ( empty( $wp_rewrite->permalink_structure ) ) {
          return '?' . self::$query_vars[0] . '=' . $this->get( 'type' ) . '&' . self::$query_vars[1] . '=1';
        }
        return '/'. $this->get( 'permalink' );
      }
      
      /**
       * Add Administrative Menus
       *
       * @return array
       */
      public function add_admin_menu() {
        $name = ucfirst( $this->get( 'type' ) ) . ' ' . __( 'Editor', 'amd' );
        $id = add_theme_page( $name, $name, 'edit_theme_options', 'amd-page-' . $this->get( 'type' ), array( $this, 'admin_edit_page' ) );
        add_action( 'admin_print_scripts-' . $id, array( $this, 'admin_scripts' ) );
      }
      
      /**
       * register_admin_styles function.
       * adds styles to the admin page
       *
       * @access public
       * @return void
       */
      public function admin_scripts() {
        wp_register_script( 'wp-amd-ace', WP_AMD_URL . '/scripts/src/ace/ace.js', array(), $this->get( 'version' ), true );
        wp_enqueue_script( 'wp-amd-admin-scripts', WP_AMD_URL . '/scripts/wp-amd.js', array( 'wp-amd-ace', 'jquery-ui-resizable' ), $this->get( 'version' ), true );
        wp_enqueue_script( 'wp-amd-admin-scripts' );
        wp_enqueue_style( 'wp-amd-admin-styles', WP_AMD_URL . '/styles/wp-amd.css' );
      }
      
      /**
       * 
       */
      public function admin_edit_page() {
        
        // the form has been submited save the options
        if( !empty( $_POST ) && check_admin_referer( 'update_amd_' . $this->get( 'type' ), 'update_amd_' . $this->get( 'type' ) . '_nonce' ) ) {
          $data = stripslashes( $_POST [ 'content' ] );
          $post_id = $this->save_revision( $data );
          $updated = true;
          $msg = 1;
          if( isset( $_POST[ 'dependency' ] ) ) {
            $this->save_dependency( $post_id, $_POST[ 'dependency' ] );
          }
        }

        if( isset( $_GET[ 'message' ] ) ) {
          $msg = (int) $_GET[ 'message' ];
        }

        $messages = array(
          1 => __( "Global Javascript saved", get_wp_amd( 'text_domain' ) ),
          5 => isset( $_GET[ 'revision' ] ) ? sprintf( __( '%s restored to revision from %s, <em>Save Changes for the revision to take effect</em>', get_wp_amd( 'text_domain' ) ), ucfirst( $this->get( 'type' ) ), wp_post_revision_title( (int) $_GET[ 'revision' ], false ) ) : false
        );
        
        $data = $this->get_post( $this->get( 'type' ) );
        $data[ 'msg' ] = $messages[ $msg ];
        
        $post_id = !empty( $data[ 'ID' ] ) ? $data[ 'ID' ] : false;
        
        $this->add_metabox( $post_id );
        $dependency = get_post_meta( $css[ 'ID' ], 'dependency', true );
        if( !is_array( $dependency ) ) {
          $dependency = array();
        }
        
        $template = WP_AMD_DIR . 'templates/' . $this->get( 'type' ) . '_edit_page.php';
        
        if( file_exists( $template ) ) {
          include( $template );
        }
      }
      
      /**
       * save_revision function.
       *
       * @access public
       * @param mixed $js
       * @return void
       */
      public function save_revision( $data ) {
        if( !$post = self::get_post( $this->get( 'type' )  ) ) {
          $post_id = wp_insert_post( array(
            'post_title' => ( 'Global AMD' . ucfirst( $this->get( 'type' ) ) ),
            'post_content' => $data,
            'post_status' => 'publish',
            'post_type' => $this->post_type,
          ) );
        } else {
          $post[ 'post_content' ] = $data;
          $post_id = wp_update_post( $post );
        }
        return $post_id;
      }
      
      /**
       * revision_post_link function.
       * Override the edit link, the default link causes a redirect loop
       *
       * @access public
       * @param mixed $post_link
       * @return void
       */
      public static function revision_post_link( $post_link ) {
        global $post;
        if( isset( $post ) && strstr( $post_link, 'action=edit' ) && !strstr( $post_link, 'revision=' ) ) {
          switch( true ) {
            case ( self::get_post_type( 'script' ) == $post->post_type ):
              $post_link = 'themes.php?page=amd-page-script';
              break;
            case ( self::get_post_type( 'style' ) == $post->post_type ):
              $post_link = 'themes.php?page=amd-page-style';
              break;
          }
        }
        return $post_link;
      }
      
      /**
       * add_metabox function.
       *
       * @access public
       * @param mixed $js
       * @return void
       */
      public function add_metabox( $post_id ) {
        if( $post_id && wp_get_post_revisions( $post_id ) ) {
          add_meta_box( 'revisionsdiv', __( 'Revisions', get_wp_amd( 'text_domain' ) ), array( $this, 'post_revisions_meta_box' ), $this->post_type, 'normal' );
        }
      }
      
      /**
       * @param $_post
       */
      function post_revisions_meta_box( $post ) {
        // Specify numberposts and ordering args
        $args = array( 'numberposts' => 5, 'orderby' => 'ID', 'order' => 'DESC' );
        // Remove numberposts from args if show_all_rev is specified
        if( isset( $_GET[ 'show_all_rev' ] ) ) {
          unset( $args[ 'numberposts' ] );
        }
        wp_list_post_revisions( $post[ 'ID' ], $args );
      }

      /**
       * @param $post_id
       */
      public function save_dependency( $post_id, $dependencies ) {
        add_post_meta( $post_id, 'dependency', $dependencies, true ) or update_post_meta( $post_id, 'dependency', $dependencies );
      }
      
      /**
       * @param $dependencies
       */
      function load_dependencies( $dependencies ) {
        $all_deps = $this->get( 'dependencies' );
        foreach( $dependencies as $dependency ) {
          if( isset( $all_deps[ $dependency ] ) ) {
            $current = wp_parse_args( $all_deps[ $dependency ], array(
              'load_in_head' => ( $this->get( 'type' ) == 'script' ? false : true ),
              'url' => '',
            ) );
            if( !empty( $current[ 'url' ] ) ) {
              switch( $this->get( 'type' ) ) {
                case 'script':
                  wp_register_script( $dependency, $current[ 'url' ], array(), $this->get( 'version' ), !$current[ 'load_in_head' ] );
                  wp_enqueue_script( $dependency );
                  break;
                case 'style':
                  wp_register_style( $dependency, $current[ 'url' ], array(), $this->get( 'version' ), !$all_deps[ $dependency ][ 'load_in_head' ] );
                  wp_enqueue_style( $dependency );
                  break;
                default: break;
              }
            }
          }
        }
      }
      
      /**
       * New query vars
       *
       * @param type $query_vars
       * @return string
       */
      public function query_vars( $query_vars ) {
        return array_unique( $query_vars + self::$query_vars );
      }
      
      /**
       * Dynamic Rules
       *
       * @param type $current
       * @return type
       */
      public function update_option_rewrite_rules( $rules ) {     
        return array_unique( array(
          '^' . $this->get( 'permalink' ) => 'index.php?' . self::$query_vars[0] . '=' . $this->get( 'type' ) . '&' . self::$query_vars[1] . '=1',
        ) + (array)$rules );
      }
      
      /**
       *
       * @global type $wp_query
       * @param type $template
       * @return type
       */
      public static function return_asset( $template ) {
        global $wp_query;

        if ( $type = get_query_var( self::$query_vars[0] ) && in_array( $type, array( 'style', 'script' ) ) ) {

          $headers = apply_filters( 'amd:' . $type . ':headers', array(
            'Cache-Control'   => 'public',
            'Pragma'          => 'cache',
            'X-Frame-Options' => 'SAMEORIGIN',
            'Vary'            => 'Accept-Encoding'
          ) );

          switch( $type ) {
            case 'script':
              $headers[ 'Content-Type' ] = isset( $headers[ 'Content-Type' ] ) && $headers[ 'Content-Type' ] ? $headers[ 'Content-Type' ] : 'application/javascript; charset=' . get_bloginfo( 'charset' );
              break;
            case 'style':
              $headers[ 'Content-Type' ] = isset( $headers[ 'Content-Type' ] ) && $headers[ 'Content-Type' ] ? $headers[ 'Content-Type' ] : 'text/css; charset=' . get_bloginfo( 'charset' );
            default: break;
          }

          foreach( (array) $headers as $_key => $field_value ) {
            @header( "{$_key}: {$field_value}" );
          }
          
          if ( $data = self::get_post( $type ) && !empty( $data[ 'post_content' ] ) ) {
            die( $data[ 'post_content' ] );
          } else {
            die('/** Global asset is empty */');
          }
        }

        return $template;
      }
      
      /**
       * Get 
       *
       * @access public
       * @return void
       */
      public static function get_post( $type ) {
        $post = array_shift( get_posts( array( 
          'numberposts' => 1, 
          'post_type' => self::get_post_type( $type ), 
          'post_status' => 'publish' 
        ) ) );
        return $post ? get_object_vars( $post ) : false;
      }
      
      public static function get_post_type( $type ) {
        return 'amd_' . $type;
      }
      
      /**
       *
       */
      public function register_post_type() {
        register_post_type( $this->post_type, array(
          'supports' => array( 'revisions' )
        ) );
      }
      
      /**
       * Returns required argument
       */
      public function get( $arg ) {
        return isset( $this->args[ $arg ] ) ? $this->args[ $arg ] : NULL;
      }
      
    }
    
    
    
  }

}


      