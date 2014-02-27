<?php
/*
Plugin Name: Global Javascript
Plugin URI: https://github.com/psmagicman/global_javascript
Description: Allows the creation and editing of Javascript on Wordpress powered sites
Version: 1.0
Author: Julien Law, CTLT
Author URI: https://github.com/ubc/global_javascript

*/

/*  Copyright 2013  Julien Law

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

class Global_Javascript {

  public $path = null;

  public $option = array();

  public $file;

  public $js_file_name;
  public $js_min_file_name_prefix;
  public $js_min_file_name;
  public $path_to_js;
  public $url_to_js;

  /***************
   * Constructor *
   ***************/
  function __construct() {

    $this->path = plugin_basename( dirname( __FILE__ ) );
    $this->file = plugin_basename( __FILE__ );

    $this->js_file_name            = 'global-javascript.js';
    $this->js_min_file_name_prefix = '-global-javascript.min.js';

    $wp_upload_dir = wp_upload_dir();

    $this->path_to_js = trailingslashit( $wp_upload_dir[ 'basedir' ] ) . "global-js/";
    $this->url_to_js  = trailingslashit( $wp_upload_dir[ 'baseurl' ] ) . "global-js/";

    add_action( 'init', array( $this, 'register_scripts' ) );
    add_action( 'wp_footer', array( $this, 'print_scripts' ) );

    // load the plugin
    add_action( 'admin_init', array( $this, 'init' ) );
    add_action( 'admin_menu', array( $this, 'add_menu' ) );

    // Override the edit link, the default link causes a redirect loop
    add_filter( 'get_edit_post_link', array( $this, 'revision_post_link' ) );
  }

  function register_scripts() {
    if( !is_admin() ) {
      $url          = $this->get_global_js_url();
      $dependencies = array();
      if( false !== ( $post_id = $this->get_plugin_post_id() ) ):
        $dependencies = $this->get_saved_dependencies( $post_id );
        $this->load_dependencies( $dependencies );
      endif;
      wp_register_script( 'add-global-javascript', $url, $dependencies, '1.0', true );
    }
  }

  function print_scripts() {
    wp_enqueue_script( 'add-global-javascript' );
  }

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

  public function add_menu() {
    $page = add_theme_page( 'Global Javascript', 'Global Javascript', 8, __FILE__, array( $this, 'admin_page' ) );
    add_action( 'admin_print_scripts-' . $page, array( $this, 'admin_scripts' ) );
  }

  /**
   * register_admin_styles function.
   * adds styles to the admin page
   *
   * @access public
   * @return void
   */
  public function admin_scripts() {

    wp_enqueue_style( 'global-javascript-admin-styles', plugins_url( $this->path . '/css/admin.css' ) );

    wp_register_script( 'acejs', plugins_url( '/ace/ace.js', __FILE__ ), '', '1.0', 'true' );
    wp_enqueue_script( 'acejs' );

    wp_register_script( 'aceinit', plugins_url( '/js/admin.js', __FILE__ ), array( 'acejs', 'jquery-ui-resizable' ), '1.1', 'true' );
    wp_enqueue_script( 'aceinit' );

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

    $this->js_min_file_name = time() . $this->js_min_file_name_prefix;

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

  function save_dependency( $post_id, $js_dependencies ) {

    add_post_meta( $post_id, 'dependency', $js_dependencies, true ) or update_post_meta( $post_id, 'dependency', $js_dependencies );

  }

  /**
   * save_to_external_file function
   * This function will be called to save the javascript to an external .js file
   *
   * @access private
   * @return void
   */
  private function save_to_external_file( $js ) {

    if( !wp_mkdir_p( $this->path_to_js ) )
      return 1; // we can't make the folder

    if( empty( $js ) ):
      $this->unlink_files( true );

      return 0;
    endif;
    // lets minify the javascript to save first to solve timing issues
    $js_min = $this->filter( $js );

    $js_file_path          = $this->path_to_js . $this->js_file_name;
    $js_minified_file_path = $this->path_to_js . $this->js_min_file_name;

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

  function unlink_files( $all = false ) {

    if( $directory_handle = opendir( $this->path_to_js ) ):

      while( false !== ( $js_file_handle = readdir( $directory_handle ) ) ):

        if( $all )
          $new_files = array( '.', '..' );
        else
          $new_files = array( $this->js_min_file_name, $this->js_file_name, '.', '..' );

        if( !in_array( $js_file_handle, $new_files ) ):

          unlink( $this->path_to_js . '/' . $js_file_handle );

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
   * @return $post_id
   */
  public function get_plugin_post_id() {
    if( $a = array_shift( get_posts( array( 'numberposts' => 1, 'post_type' => 's-global-javascript', 'post_status' => 'publish' ) ) ) ):
      $post_row = get_object_vars( $a );

      return $post_row[ 'ID' ];
    else:
      return false;
    endif;
  }

  public function get_global_js_url() {
    if( $a = get_posts( array( 'numberposts' => 1, 'post_type' => 's-global-javascript', 'post_status' => 'publish' ) ) ):
      return $this->url_to_js . $a[ 0 ]->post_excerpt;
    else:
      return false;
    endif;
  }

  public function admin_page() {
    $this->update_js();
    $js = $this->get_js();
    $this->add_metabox( $js );
    $dependency = get_post_meta( $js[ 'ID' ], 'dependency', true );
    if( !is_array( $dependency ) )
      $dependency = array();

    ?>

    <div class="wrap">
		
			<div id="icon-themes" class="icon32"></div>
			<h2>Global Javascript Editor</h2>
			
			<form action="themes.php?page=<?php echo $this->file; ?>" method="post" id="global-javascript-form">
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
   * @return void
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
      'modernizer'             => array(
        'name'         => 'Modernizr',
        'load_in_head' => true,
        'url'          => 'js/dependencies/modernizer.min.js',
        'infourl'      => 'http://modernizr.com'
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
   * @return $dependency_arr
   */
  function get_saved_dependencies( $post_id ) {
    $dependency_arr = get_post_meta( $post_id, 'dependency', true );
    if( !is_array( $dependency_arr ) )
      $dependency_arr = array();

    return $dependency_arr;
  }

  function post_revisions_meta_box( $safejs_post ) {

    // Specify numberposts and ordering args
    $args = array( 'numberposts' => 5, 'orderby' => 'ID', 'order' => 'DESC' );
    // Remove numberposts from args if show_all_rev is specified
    if( isset( $_GET[ 'show_all_rev' ] ) )
      unset( $args[ 'numberposts' ] );

    wp_list_post_revisions( $safejs_post[ 'ID' ], $args );
  }

  function update_js() {

    $updated = false;

    // the form has been submited save the options
    if( !empty( $_POST ) && check_admin_referer( 'update_global_js_js', 'update_global_js_js_field' ) ):

      $js_form        = stripslashes( $_POST [ 'global-javascript' ] );
      $post_id        = $this->save_revision( $js_form );
      $error_id       = $this->save_to_external_file( $js_form );
      $js_val[ 0 ]    = $js_form;
      $updated        = true;
      $message_number = 1;

      $this->save_dependency( $post_id, $_POST[ 'dependency' ] );
    endif; // end of update

    if( isset( $_GET[ 'message' ] ) )
      $message_number = (int) $_GET[ 'message' ];

    if( $error_id )
      $message_number = 3;

    if( $message_number ):

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

  function filter( $_content ) {
    /*require_once ( 'min/lib/Minify/JS/ClosureCompiler.php' );
    $_return = Minify_JS_ClosureCompiler::minify( $_content, array( 'compilation_level' => 'SIMPLE_OPTIMIZATIONS' ) );*/
    require_once( 'min/lib/JSMin.php' );
    $_return = JSMin::minify( $_content );

    return $_return;
  }
}

$global_javascript_object = new Global_Javascript();