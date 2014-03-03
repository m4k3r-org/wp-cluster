<?php
/**
 *
 *
 */
namespace UsabilityDynamics\AMD {

  if( !class_exists( 'UsabilityDynamics\AMD\Bootstrap' ) ) {

    class Bootstrap {

      /**
       * Cluster core version.
       *
       * @static
       * @property $version
       * @type {Object}
       */
      public $version = '0.1.0';

      /**
       * Textdomain String
       *
       * @public
       * @property text_domain
       * @var string
       */
      public static $text_domain = 'wp-amd';

      /**
       * Singleton Instance Reference.
       *
       * @public
       * @static
       * @property $instance
       * @type {Object}
       */
      public static $instance = false;

      public $pages = array();

      public $path = null;
      public $option = array();
      public $file;
      public $js_min_file_name;

      /***************
       * Constructor *
       ***************/
      function __construct() {
        global $global_javascript_object;
        $wp_upload_dir = wp_upload_dir();

        $this->settings = new \UsabilityDynamics\Settings( array(
          'key'  => 'amd',
          'data' => array(
            'scripts' => array(
              'minify'   => false,
              'url'      => "/assets/",
              'filename' => 'app.js'
            ),
            'styles'  => array(
              'minify'   => true,
              'filename' => 'global-styles.css'
            ),
          )
        ) );

        $this->path = plugin_basename( dirname( __FILE__ ) );
        $this->file = plugin_basename( __FILE__ );

        $this->path_to_js = trailingslashit( $wp_upload_dir[ 'basedir' ] ) . "global-js/";

        add_action( 'init', array( $this, 'register_scripts' ) );
        add_action( 'wp_footer', array( $this, 'print_scripts' ) );

        // load the plugin
        add_action( 'admin_init', array( $this, 'init' ) );
        add_action( 'admin_menu', array( $this, 'add_menu' ) );

        // Override the edit link, the default link causes a redirect loop
        add_filter( 'get_edit_post_link', array( $this, 'revision_post_link' ) );

        $global_javascript_object = $this;

      }

      /**
       *
       */
      function register_scripts() {
        if( !is_admin() ) {
          $url          = $this->get_global_js_url();
          $dependencies = array();
          if( false !== ( $post_id = $this->get_plugin_post_id() ) ):
            $dependencies = $this->get_saved_dependencies( $post_id );
            $this->load_dependencies( $dependencies );
          endif;
          wp_register_script( 'add-global-javascript', $url, $dependencies, $this->get_latest_version_id(), true );
        }
      }

      /**
       * Get latest revision ID
       * @return string
       */
      public function get_latest_version_id() {
        if( $a = array_shift( get_posts( array( 'numberposts' => 1, 'post_type' => 'revision', 'post_status' => 'any', 'post_parent' => $this->get_plugin_post_id() ) ) ) ) {
          $post_row = get_object_vars( $a );
          return $post_row[ 'ID' ];
        }
        return false;
      }

      /**
       *
       */
      function print_scripts() {
        wp_enqueue_script( 'add-global-javascript' );
      }

      /**
       * @param $dependencies
       */
      function load_dependencies( $dependencies ) {
        $all_deps = $this->get_all_dependencies();
        foreach( $dependencies as $dependency ) {
          if( isset( $all_deps[ $dependency ][ 'url' ] ) ) {
            wp_register_script(
              $dependency,
              plugins_url( trailingslashit( $this->path ) . $all_deps[ $dependency ][ 'url' ] ),
              array(),
              '1.0',
              !$all_deps[ $dependency ][ 'load_in_head' ]
            );
            if( $all_deps[ $dependency ][ 'load_in_head' ] )
              wp_enqueue_script( $dependency );
          }
        }
      }

      /**
       * Add Administrative Menus
       *
       * @return array
       */
      public function add_menu() {

        $this->pages = (object) array(
          'scripts' => add_theme_page( 'Script Editor', 'Script Editor', 'edit_theme_options', 'amd-scripts', array( $this, 'admin_page' ) ),
          //'styles'  => add_theme_page( 'Styles', 'Styles', 'edit_theme', 'amd-styles', array( $this, 'admin_page' ) ),
        );

        if( isset( $this->pages->scripts ) ) {
          add_action( 'admin_print_scripts-' . $this->pages->scripts, array( $this, 'admin_scripts' ) );
        }

        if( isset( $this->pages->styles ) ) {
          add_action( 'admin_print_scripts-' . $this->pages->styles, array( $this, 'admin_scripts' ) );
        }

        return $this->pages;

      }

      /**
       * register_admin_styles function.
       * adds styles to the admin page
       *
       * @access public
       * @return void
       */
      public function admin_scripts() {

        wp_enqueue_style( 'global-javascript-admin-styles', plugins_url( '/styles/wp-amd.css', dirname( __DIR__ ) ) );

        wp_register_script( 'acejs', plugins_url( '/scripts/src/ace/ace.js', __DIR__ ), array(), $this->version, true );
        wp_register_script( 'wp-amd', plugins_url( '/scripts/wp-amd.js', __DIR__ ), array( 'acejs', 'jquery-ui-resizable' ), $this->version, true );

        wp_enqueue_script( 'wp-amd' );

      }

      /**
       * revision_post_link function.
       * Override the edit link, the default link causes a redirect loop
       *
       * @access public
       *
       * @param mixed $post_link
       *
       * @return void
       */
      public function revision_post_link( $post_link ) {
        global $post;

        if( isset( $post ) && ( 's-global-javascript' == $post->post_type ) )
          if( strstr( $post_link, 'action=edit' ) && !strstr( $post_link, 'revision=' ) )
            $post_link = 'themes.php?page=' . $this->file;

        return $post_link;

      }

      /**
       * init function.
       * Init plugin options to white list our options
       *
       * @access public
       * @return void
       */
      public function init() {
        /*
        register_setting( 'global_js_options', 'global_js_js');
        */
        $args = array(
          'public'          => false,
          'query_var'       => true,
          'capability_type' => 'nav_menu_item',
          'supports'        => array( 'revisions' )
        );

        register_post_type( 's-global-javascript', array(
          'supports' => array( 'revisions' )
        ) );

      }

      /**
       * save_revision function.
       * safe the revisoin
       *
       * @access public
       *
       * @param mixed $js
       *
       * @return void
       */
      public function save_revision( $js ) {

        $this->js_min_file_name = time() . $this->get( 'scripts.filename' );

        // If null, there was no original safejs record, so create one
        if( !$safejs_post = $this->get_js() ) {
          $post                   = array();
          $post[ 'post_content' ] = $js;
          $post[ 'post_title' ]   = 'Global Javascript Editor';
          $post[ 'post_status' ]  = 'publish';
          $post[ 'post_type' ]    = 's-global-javascript';
          $post[ 'post_excerpt' ] = $this->js_min_file_name;

          $post_id = wp_insert_post( $post );

          return $post_id;
        } // there is a javascript store in the custom post type

        $safejs_post[ 'post_content' ] = $js;
        $safejs_post[ 'post_excerpt' ] = $this->js_min_file_name;

        wp_update_post( $safejs_post );

        return $safejs_post[ 'ID' ];

      }

      /**
       * @param $post_id
       * @param $js_dependencies
       */
      function save_dependency( $post_id, $js_dependencies ) {

        add_post_meta( $post_id, 'dependency', $js_dependencies, true ) or update_post_meta( $post_id, 'dependency', $js_dependencies );

      }

      /**
       * save_to_external_file function
       * This function will be called to save the javascript to an external .js file
       *
       * currently not used
       *
       * @access private
       *
       * @param $js
       *
       * @return void
       */
      private function save_to_external_file( $js ) {

        if( !wp_mkdir_p( $this->get( 'scripts.path' ) ) )
          return 1; // we can't make the folder

        if( empty( $js ) ):
          $this->unlink_files( true );

          return 0;
        endif;
        // lets minify the javascript to save first to solve timing issues
        $js_min = $this->filter( $js );

        $js_file_path          = $this->get( 'scripts.path' ) . $this->get( 'scripts.filename' );
        $js_minified_file_path = $this->get( 'scripts.path' ) . $this->get( 'scripts.filename' );

        // if files saved proccess to the else statment
        if( !file_put_contents( $js_file_path, $js ) || !file_put_contents( $js_minified_file_path, $js_min ) ):
          return 1; // return an error upon failure
        else:
          // we created the new files
          // lets clear some cache
          if( function_exists( 'wp_cache_clear_cache' ) ):
            wp_cache_clear_cache();
          endif;
          // lets delete the old minified files
          $this->unlink_files();

          return 0;


        endif;
      }

      /**
       * Currently not used
       * @param type $all
       */
      function unlink_files( $all = false ) {

        if( $directory_handle = opendir( $this->get( 'scripts.path' ) ) ):

          while( false !== ( $js_file_handle = readdir( $directory_handle ) ) ):

            if( $all )
              $new_files = array( '.', '..' );
            else
              $new_files = array( $this->js_min_file_name, $this->get( 'scripts.filename' ), '.', '..' );

            if( !in_array( $js_file_handle, $new_files ) ):

              unlink( $this->get( 'scripts.path' ) . '/' . $js_file_handle );

            endif;

          endwhile;

          closedir( $directory_handle );
        endif;
      }

      /**
       * get_js function.
       * Get the custom js from posts table
       *
       * @access public
       * @return void
       */
      public function get_js() {
        if( $a = array_shift( get_posts( array( 'numberposts' => 1, 'post_type' => 's-global-javascript', 'post_status' => 'publish' ) ) ) )
          $safejs_post = get_object_vars( $a );
        else
          $safejs_post = false;

        return $safejs_post;
      }

      /**
       * get_plugin_post_id function
       * Gets the post id from posts table
       *
       * @access public
       * @return bool $post_id
       */
      public function get_plugin_post_id() {
        if( $a = array_shift( get_posts( array( 'numberposts' => 1, 'post_type' => 's-global-javascript', 'post_status' => 'publish' ) ) ) ):
          $post_row = get_object_vars( $a );

          return $post_row[ 'ID' ];
        else:
          return false;
        endif;
      }

      /**
       * @return bool|string
       */
      public function get_global_js_url() {
        return apply_filters( 'amd_scripts_url',      $this->get( 'scripts.url' ) )
              .apply_filters( 'amd_scripts_filename', $this->get( 'scripts.filename' ) );
      }

      /**
       *
       */
      public function admin_page() {
        $this->update_js();
        $js = $this->get_js();
        $this->add_metabox( $js );
        $dependency = get_post_meta( $js[ 'ID' ], 'dependency', true );
        if( !is_array( $dependency ) )
          $dependency = array();

        ?>

        <div class="wrap">

        <h2>JavaScript Editor</h2>

        <form action="themes.php?page=amd-scripts" method="post" id="global-javascript-form">
          <?php wp_nonce_field( 'update_global_js_js', 'update_global_js_js_field' ); ?>
          <div class="metabox-holder has-right-sidebar">

            <div class="inner-sidebar">

              <div class="postbox">
                <h3><span>Publish</span></h3>
                <div class="inside">
                  <input class="button-primary" type="submit" name="publish" value="<?php _e( 'Save Javascript' ); ?>"/>
                </div>
              </div>
              <div class="postbox">
                <h3><span>Dependency</span></h3>
                <div class="inside">
                  <?php foreach( $this->get_all_dependencies() as $dep => $dep_array ): ?>
                    <label><input type="checkbox" name="dependency[]" value="<?php echo $dep; ?>" <?php checked( in_array( $dep, $dependency ), true ); ?> /><a href="<?php echo $dep_array[ 'infourl' ]; ?>"> <?php echo $dep_array[ 'name' ]; ?> </a></label>
                    <br/>
                  <?php endforeach; ?>
                </div>
              </div>
              <!-- ... more boxes ... -->
              <?php do_meta_boxes( 's-global-javascript', 'normal', $js ); ?>

            </div> <!-- .inner-sidebar -->

            <div id="post-body">
              <div id="post-body-content">
                <div id="global-editor-shell">
                <textarea style="width:100%; height: 360px; resize: none;" id="global-javascript" class="wp-editor-area" name="global-javascript"><?php echo $js[ 'post_content' ]; ?></textarea>
                </div>
              </div> <!-- #post-body-content -->
            </div> <!-- #post-body -->

          </div> <!-- .metabox-holder -->
        </form>
      </div> <!-- .wrap -->

      <?php
      }

      /**
       * add_metabox function.
       *
       * @access public
       *
       * @param mixed $js
       *
       * @return void
       */
      function add_metabox( $js ) {

        if( 0 < $js[ 'ID' ] && wp_get_post_revisions( $js[ 'ID' ] ) ) {

          add_meta_box( 'revisionsdiv', __( 'JS Revisions', 'safejs' ), array( $this, 'post_revisions_meta_box' ), 's-global-javascript', 'normal' );

        }
      }

      /**
       * get_all_dependencies function.
       *
       * @access public
       */
      function get_all_dependencies() {

        return array(
          'backbone'               => array(
            'name'         => 'Backbone js',
            'load_in_head' => false,
            'infourl'      => 'http://backbonejs.com'
          ),
          'jquery'                 => array(
            'name'         => 'jQuery',
            'load_in_head' => false,
            'infourl'      => 'http://jquery.com'
          ),
          'jquery-ui-autocomplete' => array(
            'name'         => 'jQuery UI Autocomplete',
            'load_in_head' => false,
            'infourl'      => 'http://jqueryui.com/autocomplete'
          ),
          'json2'                  => array(
            'name'         => 'JSON for JS',
            'load_in_head' => false,
            'infourl'      => 'https://github.com/douglascrockford/JSON-js'
          ),
          'thickbox'               => array(
            'name'         => 'Thickbox',
            'load_in_head' => false,
            'infourl'      => 'http://codex.wordpress.org/ThickBox'
          ),
          'underscore'             => array(
            'name'         => 'Underscore js',
            'load_in_head' => false,
            'infourl'      => 'http://underscorejs.org'
          )
        );

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
       * @param $safejs_post
       */
      function post_revisions_meta_box( $safejs_post ) {

        // Specify numberposts and ordering args
        $args = array( 'numberposts' => 5, 'orderby' => 'ID', 'order' => 'DESC' );
        // Remove numberposts from args if show_all_rev is specified
        if( isset( $_GET[ 'show_all_rev' ] ) )
          unset( $args[ 'numberposts' ] );

        wp_list_post_revisions( $safejs_post[ 'ID' ], $args );
      }

      /**
       *
       */
      function update_js() {

        $updated = false;

        // the form has been submited save the options
        if( !empty( $_POST ) && check_admin_referer( 'update_global_js_js', 'update_global_js_js_field' ) ):

          $js_form        = stripslashes( $_POST [ 'global-javascript' ] );
          $post_id        = $this->save_revision( $js_form );
          //** Uncomment when need to save file */
          //$error_id       = $this->save_to_external_file( $js_form );
          $js_val[ 0 ]    = $js_form;
          $updated        = true;
          $message_number = 1;

          if( isset( $_POST[ 'dependency' ] ) ) {
            $this->save_dependency( $post_id, $_POST[ 'dependency' ] );
          }

        endif; // end of update

        if( isset( $_GET[ 'message' ] ) )
          $message_number = (int) $_GET[ 'message' ];

        if( isset( $error_id ) && $error_id )
          $message_number = 3;

        if( isset( $message_number ) && $message_number ):

          $messages[ 's-global-javascript' ] = array(
            1 => "Global Javascript saved",
            3 => "Failed to upload Javascript to server",
            5 => isset( $_GET[ 'revision' ] ) ? sprintf( __( 'Global Javascript restored to revision from %s, <em>Save Changes for the revision to take effect</em>' ), wp_post_revision_title( (int) $_GET[ 'revision' ], false ) ) : false
          );
          $messages                          = apply_filters( 'post_updated_messages', $messages );
          ?>
          <div class="updated"><p><strong><?php echo $messages[ 's-global-javascript' ][ $message_number ]; ?></strong></p></div>
        <?php
        endif;

      }

      /**
       * @param $_content
       *
       * @return mixed
       */
      function filter( $_content ) {
        // $_content = JSMin::minify( $_content );

        return $_content;
      }

      /**
       * @param null $key
       * @param null $value
       *
       * @return \UsabilityDynamics\Settings
       */
      public function set( $key = null, $value = null ) {
        return $this->settings->set( $key, $value );
      }

      /**
       * @param null $key
       * @param null $default
       *
       * @return \UsabilityDynamics\type
       */
      public function get( $key = null, $default = null ) {
        return $this->settings->get( $key, $default );
      }

    }

  }

}
