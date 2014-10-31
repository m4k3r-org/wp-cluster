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
      
      public $args = NULL;
      
      public static $query_vars = array(
        'amd_asset_type',
        'amd_is_asset',
      );
      
      /**
       * Constructor
       * Must be called in child constructor firstly!
       */
      public function __construct( $args = array() ) {
      
        $this->args = wp_parse_args( $args, array(
          'version' => '1.0', 
          'type' => '', // style, script
          'minify' => false,
          'load_in_head' => true,
          'permalink' => '', // assets/amd.js
          'dependencies' => array(),
          'admin_menu' => true,
          'post_type' => false,
        ) );
        
        //** Add hooks only if type is allowed */
        if( in_array( $this->get( 'type' ), array( 'script', 'style' ) ) ) {
        
          if( !$this->get( 'post_type' ) ) {
            $this->args[ 'post_type' ] = self::get_post_type( $this->get( 'type' ) );
          }
          
          //** rewrite and respond */
          add_action( 'query_vars', array( __CLASS__, 'query_vars' ) );
          add_filter( 'pre_update_option_rewrite_rules', array( &$this, 'update_option_rewrite_rules' ), 1 );
          add_filter( 'template_include', array( __CLASS__, 'return_asset' ), 1, 1 );
          
          //** Register assets and post_type */
          add_action( 'admin_init', array( &$this, 'register_post_type' ) );
          
          switch( $this->get( 'type' ) ) {
            case 'style':
              add_action( 'wp_print_styles', array( &$this, 'register_asset' ), 999 );
              break;
            case 'script':
            default:
              add_action( 'wp_enqueue_scripts', array( &$this, 'register_asset' ), 999 );
              break;
          }
          
          //** Determine if Admin Menu is enabled */
          if( $this->get( 'admin_menu' ) ) {
            add_action( 'admin_menu', array( &$this, 'add_admin_menu' ) );
            // Override the edit link, the default link causes a redirect loop
            add_filter( 'get_edit_post_link', array( __CLASS__, 'revision_post_link' ) );
          }
        
        }
        
      }
      
      /**
       *
       */
      public function register_post_type() {
        register_post_type( $this->get( 'post_type' ), array(
          'supports' => array( 'revisions' )
        ) );
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
        //** Add action to denqueue or deregister scripts which cause conflicts and errors. */
        do_action( 'amd::admin_scripts::edit_page' );
        
        wp_register_script( 'wp-amd-ace', WP_AMD_URL . '/scripts/src/ace/ace.js', array(), $this->get( 'version' ), true );
        wp_enqueue_script( 'wp-amd-admin-scripts', WP_AMD_URL . '/scripts/wp-amd.js', array( 'wp-amd-ace', 'jquery-ui-resizable' ), $this->get( 'version' ), true );
        wp_enqueue_script( 'wp-amd-admin-scripts' );
        wp_enqueue_style( 'wp-amd-admin-styles', WP_AMD_URL . '/styles/wp-amd.css' );
      }
      
      /**
       * 
       */
      public function admin_edit_page() {
        $msg = 0;
        
        // the form has been submited save the options
        if( !empty( $_POST ) && check_admin_referer( 'update_amd_' . $this->get( 'type' ), 'update_amd_' . $this->get( 'type' ) . '_nonce' ) ) {
          $data = stripslashes( $_POST [ 'content' ] );
          $post_id = $this->save_asset( $data );
          $updated = true;
          $msg = 1;
          if( isset( $_POST[ 'dependency' ] ) ) {
            add_post_meta( $post_id, 'dependency', $_POST[ 'dependency' ], true ) or update_post_meta( $post_id, 'dependency', $_POST[ 'dependency' ] );
          }
        }

        if( isset( $_GET[ 'message' ] ) ) {
          $msg = (int) $_GET[ 'message' ];
        }

        $messages = array(
          0 => false,
          1 => sprintf( __( "Global %s saved", get_wp_amd( 'text_domain' ) ), ucfirst( $this->get( 'type' ) ) ),
          5 => isset( $_GET[ 'revision' ] ) ? sprintf( __( '%s restored to revision from %s, <em>Save Changes for the revision to take effect</em>', get_wp_amd( 'text_domain' ) ), ucfirst( $this->get( 'type' ) ), wp_post_revision_title( (int) $_GET[ 'revision' ], false ) ) : false
        );
        
        $data = self::get_asset( $this->get( 'type' ) );
        $data = $data ? $data : array();
        $data[ 'msg' ] = $messages[ $msg ];
        $data[ 'post_content' ] = isset( $data[ 'post_content' ] ) ? $data[ 'post_content' ] : '';
        
        $post_id = !empty( $data[ 'ID' ] ) ? $data[ 'ID' ] : false;
        
        $this->add_metaboxes( $post_id );
        
        $template = WP_AMD_DIR . 'templates/' . $this->get( 'type' ) . '_edit_page.php';
        
        if( file_exists( $template ) ) {
          include( $template );
        }
      }
      
      /**
       * Saves/updates asset.
       *
       * @access public
       * @param mixed $js
       * @return void
       */
      public function save_asset( $value ) {
        if( !$post = self::get_asset( $this->get( 'type' )  ) ) {
          $post_id = wp_insert_post( array(
            'post_title' => ( 'Global AMD ' . ucfirst( $this->get( 'type' ) ) ),
            'post_content' => $value,
            'post_status' => 'publish',
            'post_type' => $this->get( 'post_type' ),
          ) );
          if( $post_id ) {
            add_post_meta( $post_id, 'theme_relation', sanitize_key( wp_get_theme()->get( 'Name' ) ), true );
          }
        } else {
          $post[ 'post_content' ] = $value;
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
      public function add_metaboxes( $post_id ) {
        if( $this->get( 'dependencies' ) ) {
          add_meta_box( 'dependencies', __( 'Dependency', get_wp_amd( 'text_domain' ) ), array( $this, 'render_metabox_dependencies' ), $this->get( 'post_type' ), 'normal' );
        }
        if( $post_id && wp_get_post_revisions( $post_id ) ) {
          add_meta_box( 'revisionsdiv', __( 'Revisions', get_wp_amd( 'text_domain' ) ), array( $this, 'render_metabox_revisions' ), $this->get( 'post_type' ), 'normal' );
        }
      }
      
      /**
       * @param $_post
       */
      public function render_metabox_dependencies( $post ) {
        $dependency = array();
        if( !empty( $post[ 'ID' ] ) ) {
          $dependency = get_post_meta( $post[ 'ID' ], 'dependency', true );
          $dependency = !is_array( $dependency ) ? array() : $dependency;
        }
        ?>
        <ul>
        <?php foreach( (array)$this->get( 'dependencies' ) as $key => $data ) : ?>
          <?php if( !$this->is_wp_dependency( $key ) && empty( $data[ 'url' ] ) ) continue; ?>
          <li>
            <label>
              <input type="checkbox" name="dependency[]" value="<?php echo $key; ?>" <?php checked( in_array( $key, $dependency ), true ); ?> />
              <a href="<?php echo $data[ 'infourl' ]; ?>"> <?php echo $data[ 'name' ]; ?> </a>
            </label>
          </li>
        <?php endforeach; ?>
        </ul>
        <?php
      }
      
      /**
       * @param $_post
       */
      public function render_metabox_revisions( $post ) {
        // Specify numberposts and ordering args
        $args = array( 'numberposts' => 5, 'orderby' => 'ID', 'order' => 'DESC' );
        // Remove numberposts from args if show_all_rev is specified
        if( isset( $_GET[ 'show_all_rev' ] ) ) {
          unset( $args[ 'numberposts' ] );
        }
        wp_list_post_revisions( $post[ 'ID' ], $args );
      }
      
      /**
       * Determine if dependency belongs to Wordpress ( already registered by WordPress )
       *
       * @param $dependency
       */
      public function is_wp_dependency( $dep ) {
        switch( $this->get( 'type' ) ) {
          case 'script':
            if( wp_script_is( $dep, 'registered' ) ) {
              return true;
            }
            break;
          case 'style':
            if( wp_style_is( $dep, 'registered' ) ) {
              return true;
            }
            break;
        }        
        return false;
      }
      
      /**
       * Register dependencies
       *
       * @param $dependencies
       */
      public function register_dependencies( $dependencies ) {
        $all_deps = $this->get( 'dependencies' );
        $registered = array();
        foreach( $dependencies as $dependency ) {
          if( isset( $all_deps[ $dependency ] ) ) {
            if( $this->is_wp_dependency( $dependency ) ) {
              array_push( $registered, $dependency );
            } 
            else if( !empty( $all_deps[ $dependency ][ 'url' ] ) ) {
              switch( $this->get( 'type' ) ) {
                case 'script':
                  wp_register_script( $dependency, $all_deps[ $dependency ][ 'url' ], array(), $this->get( 'version' ) );
                  array_push( $registered, $dependency );
                  break;
                case 'style':
                  wp_register_style( $dependency, $all_deps[ $dependency ][ 'url' ], array(), $this->get( 'version' ) );
                  array_push( $registered, $dependency );
                  break;
                default: break;
              }
            }
          }
        }
        return $registered;
      }
      
      /**
       * New query vars
       *
       * @param type $query_vars
       * @return string
       */
      public static function query_vars( $query_vars ) {
        return array_unique( array_merge( $query_vars, self::$query_vars ) );
      }
      
      /**
       * Dynamic Rules
       *
       * @param type $current
       * @return type
       */
      public function update_option_rewrite_rules( $rules ) {
        $new_rules = array( '' . $this->get( 'permalink' ) . '$' => 'index.php?' . self::$query_vars[0] . '=' . $this->get( 'type' ) . '&' . self::$query_vars[1] . '=1' );
        $rules =  array_merge_recursive( (array) $new_rules, (array) $rules );
        return $rules;
      }
      
      /**
       *
       * @global type $wp_query
       * @param type $template
       * @return type
       */
      public static function return_asset( $template ) {
        global $wp_query;
        
        if ( ( $type = get_query_var( self::$query_vars[0] ) ) && in_array( $type, array( 'script', 'style' ) ) ) {
        
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
          
          $data = self::get_asset( $type );
          if ( !empty( $data[ 'post_content' ] ) ) {
            die( $data[ 'post_content' ] );
          } else {
            die('/** Global asset is empty */');
          }
        }

        return $template;
      }
      
      /**
       * Registers asset with all selected dependencies
       *
       */
      public function register_asset() {
        $url = $this->get_asset_url();
        $dependencies = array();
        $post = self::get_asset( $this->get( 'type' ) );
        if( !empty( $post ) ) {
          $dependencies = $this->get_saved_dependencies( $post[ 'ID' ] );
          $dependencies = $this->register_dependencies( $dependencies );
        }
        switch( $this->get( 'type' ) ) {
          case 'script':
            wp_enqueue_script( 'wp-amd-script', $url, $dependencies, $this->get_latest_version_id( $post[ 'ID' ] ), !$this->get( 'load_in_head' ) );
            break;
          case 'style':
            wp_enqueue_style( 'wp-amd-style', $url, $dependencies, $this->get_latest_version_id( $post[ 'ID' ] ), !$this->get( 'load_in_head' ) );
            break;
        }
      }
      
      /**
       * Get latest revision ID
       * @return string
       */
      public function get_latest_version_id( $post_id ) {
        $posts = get_posts( array( 'numberposts' => 1, 'post_type' => 'revision', 'post_status' => 'any', 'post_parent' => $post_id ) );
        $post = !empty( $posts ) ? array_shift( $posts ) : false;
        if( $post ) {
          return $post->ID;
        }
        return 'unknown';
      }
      
      /**
       * get_saved_dependencies function
       *
       * @access public
       *
       * @param $post_id
       *
       * @return array|mixed $dependency_arr
       */
      function get_saved_dependencies( $post_id ) {
        $dependency_arr = get_post_meta( $post_id, 'dependency', true );
        if( !is_array( $dependency_arr ) )
          $dependency_arr = array();

        return $dependency_arr;
      }
      
      /**
       * Global JS URL
       * @return bool|string
       */
      public function get_asset_url() {
        global $wp_rewrite;
        
        $url = home_url() . '?' . self::$query_vars[0] . '=' . $this->get( 'type' ) . '&' . self::$query_vars[1] . '=1';
        switch( true ) {
          case ( empty( $wp_rewrite->permalink_structure ) ):
            // Do nothing.
            break;
          case ( !key_exists( '^' . $this->get( 'permalink' ), $wp_rewrite->rules ) ):
            // Looks like permalink structure is set, but our rules are not.
            // Flush rewrite rules to have correct permalink next time.
            flush_rewrite_rules( );
            break;
          default:
            $url = home_url( $this->get( 'permalink' ) );
            break;
        }
        
        return $url;
      }
      
      /**
       * Returns asset by type ( script, style )
       *
       * @access public
       * @return void
       */
      public static function get_asset( $type ) {
        $posts = get_posts( array( 
          'numberposts' => 1, 
          'post_type' => self::get_post_type( $type ), 
          'post_status' => 'publish',
          'meta_key' => 'theme_relation',
          'meta_value' => sanitize_key( wp_get_theme()->get( 'Name' ) ),
        ) );
        $post = !empty( $posts ) ? array_shift( $posts ) : false;        
        return $post ? get_object_vars( $post ) : false;
      }
      
      public static function get_post_type( $type ) {
        return 'amd_' . $type;
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


      