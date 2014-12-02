<?php
/**
 * Carrington Build Loop Module
 * Performs a loop based on several different filter criteria
 * set via admin interface.
 * There's a base class that outputs full loop content, but 2 class
 * extensions which extend it, but change it to "excerpts" or "titles"
 * Don't forget to call ArtistListModule::init() in your constructor if you
 * derive from this class!
 */
if( !class_exists( 'UsabilityDynamics_Festival2_ArtistListNewModule' ) ){

  class UsabilityDynamics_Festival2_NewArtistListModule extends \UsabilityDynamics\Theme\Module {

    protected $content_support = array(
      'title',
      'content',
      'url',
      'images'
    );

    public function __construct(){
      $opts = array(
        'description' => __( 'Choose and display a list of artists.', 'wp-festival' ),
        'icon' => plugins_url( basename( __DIR__ ) . '/icon.png', __DIR__ . '/' )
      );
      parent::__construct( 'cfct-module-loop', __( 'Artist List New', 'wp-festival' ), $opts );

    }

    /**
     * Modify the data before it is saved, or not
     *
     * @param array $new_data
     * @param array $old_data
     * @return array
     */
    public function update( $new_data, $old_data ) {
      // keep the image search field value from being saved
      unset( $new_data[ $this->get_field_name('global_image-image-search') ] );

      // normalize the selected image value in to a 'featured_image' value for easy output
      if ( !empty( $new_data[ $this->get_field_name('post_image') ] ) ) {
        $new_data[ 'featured_image' ] = $new_data[ $this->get_field_name('post_image') ];
      }
      elseif (!empty($new_data[$this->get_field_name('global_image')])) {
        $new_data[ 'featured_image' ] = $new_data[ $this->get_field_name('global_image') ];
      }
      return $new_data;
    }

    /**
     * Display the module
     *
     * @param array $data - saved module data
     * @param array $args - previously set up arguments from a child class
     *
     * @return string HTML
     */
    public function display( $data ){ 
      global $wp_query, $post;
      /** Backup wp_query */
      $_wp_query = $wp_query;
      $_post = $post;
      /** Ok, so the first thing we're going to do is create a WP_Query object */
      $artists = array( 0 );
      /** Ok, go through the sorting array */
      if( isset( $data[ 'sorting' ] ) && is_array( $data[ 'sorting' ] ) ){
        foreach( $data[ 'sorting' ] as $post_id => $sort_value ){
          if( !( isset( $sort_value ) && is_numeric( $sort_value ) ) ){
            unset( $data[ 'sorting' ][ $post_id ] );
          }
        }
        asort( $data[ 'sorting' ] );
      }else{
        $data[ 'sorting' ] = array();
      }
      /** No go through the sorting array, and see if they're selected */
      if( isset( $data[ 'artists' ] ) && is_array( $data[ 'artists' ] ) ){
        foreach( $data[ 'sorting' ] as $post_id => $sort_value ){
          if( in_array( $post_id, $data[ 'artists' ] ) ){
            $artists[] = $post_id;
            unset( $data[ 'artists' ][ $post_id ] );
          }
        }
      }
      /** Finally, go through data artists, and append them */
      $data[ 'artists' ] = array_values( array_filter( array_unique( array_merge( $artists, (array)$data[ 'artists' ] ) ) ) );
      /** Now run our query */
      $wp_query = new WP_Query( array(
        'post__in' => $data[ 'artists' ],
        'post_type' => 'artist',
        'orderby' => $data['order_by'],
        'order' => 'ASC',
        'nopaging' => true,
      ) );
      $wp_query->data = $data;
      /** Get our template */
      ob_start();
      get_template_part( 'templates/aside/grid-artist-rows-new' );
      /** Restore our wp_query */
      $wp_query = $_wp_query;
      $post = $_post;
      /** Return our string */
      return ob_get_clean();
    }

    /**
     * 
     * @global type $post
     * @param type $data
     * @return type
     */
    protected function set_display_args( $data ){
      // Set default
      $args = $this->default_display_args;

      // Figure out post type or use default
      if( isset( $data[ $this->get_field_name( 'post_type' ) ] ) ){
        $post_type = $data[ $this->get_field_name( 'post_type' ) ];
        if( !empty( $post_type ) ){
          $args[ 'post_type' ] = $post_type;
        }
      }

      $tax_input = $this->get_data( 'tax_input', $data );
      if( !empty( $tax_input ) ){
        $relation = $this->get_data( 'relation', $data, $this->default_relation );
        if( !empty( $relation ) ){
          $args[ 'tax_query' ][ 'relation' ] = $relation;
        }
        foreach( $tax_input as $taxonomy => $terms ){
          $taxonomy = get_taxonomy( $taxonomy );
          $args[ 'tax_query' ][ ] = array(
            'taxonomy' => $taxonomy->name,
            'terms' => $terms,
            'field' => 'term_id'
          );
        }
      }

      // Post Parent
      // @deprecated? @TODO check if any child-modules are using this ~sp
      $args[ 'post_parent' ] = !empty( $data[ $this->get_field_name( 'parent' ) ] ) ? $data[ $this->get_field_name( 'parent' ) ] : null;

      // Filter by Author
      $args[ 'author' ] = !empty( $data[ $this->get_field_name( 'author' ) ] ) ? $data[ $this->get_field_name( 'author' ) ] : null;

      // Number of items
      $args[ 'posts_per_page' ] = intval( !empty( $data[ $this->get_field_name( 'item_count' ) ] ) ? $data[ $this->get_field_name( 'item_count' ) ] : $this->default_item_count );

      // Item offset
      $args[ 'offset' ] = intval( isset( $data[ $this->get_field_name( 'item_offset' ) ] ) ? $data[ $this->get_field_name( 'item_offset' ) ] : $this->default_item_offset );

      // Don't include this post, otherwise we'll get an infinite loop
      global $post;
      $args[ 'post__not_in' ] = array( $post->ID );
      $args[ 'display' ] = $data[ $this->get_field_name( 'display_type' ) ];

      return $args;
    }

    # Admin Form

    /**
     * Output the Admin Form
     *
     * @param array $data - saved module data
     *
     * @return string HTML
     */
    public function admin_form( $data ){
      global $wpdb;
      /** Add Colorpicker */
      wp_enqueue_script('wp-color-picker');
      wp_enqueue_style( 'wp-color-picker' );
      /** Add DatePicker */
      wp_enqueue_script('jquery-ui-datepicker');
      wp_enqueue_style( 'jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css' );
      /** Ok, I'm going to get all the artists now */
      $query = "SELECT * FROM {$wpdb->posts} WHERE post_type = 'artist' AND post_status = 'publish' ORDER BY post_title ASC";
      $artists = $wpdb->get_results( $query, ARRAY_A );
      
      /** Amount of columns per line */
      $artist_columns = array( '1', '2', '3', '4', '6', '12' );
      /** Which image should be shown */
      $artist_images = array(
        'featured'      => __( 'Featured', wp_festival2( 'domain' ) ),
        'headshotImage' => __( 'Headshot', wp_festival2( 'domain' ) ),
        'portraitImage' => __( 'Portrait', wp_festival2( 'domain' ) ),
        'logoImage'     => __( 'Logo', wp_festival2( 'domain' ) ),
      );
      
      $layout_types = array(
        'lineup' =>  __( 'Lineup', wp_festival2( 'domain' ) ),
        'rows' =>  __( 'Rows', wp_festival2( 'domain' ) )
      );
      
      /** Now get and return the template */
      ob_start();
      require_once( __DIR__ . '/admin/form.php' );
      return ob_get_clean();
    }

    /**
     * Don't contribute to the post_content stored in the database
     * @return null
     */
    public function text( $data ){
      return null;
    }

    /** Return the title */
    public function admin_text( $data ){
      return strip_tags( $data[ 'title' ] );
    }

    public function admin_js() {
      $js = '
        cfct_builder.addModuleLoadCallback("'.$this->id_base.'", function() {
          '.$this->cfct_module_tabs_js().'
        });

        cfct_builder.addModuleSaveCallback("'.$this->id_base.'", function() {
          // find the non-active image selector and clear his value
          $("#'.$this->id_base.'-image-selectors .cfct-module-tab-contents>div:not(.active)").find("input:hidden").val("");
          return true;
        });
      ';
      $js .= $this->global_image_selector_js('global_image', array('direction' => 'horizontal'));
      return $js;
    }

    function post_image_selector( $data = false, $prefix = '' ) {
      $name = !empty( $prefix ) ? $prefix . '_post_image' : 'post_image';
      if (isset($_POST['args'])) {
        $ajax_args = cfcf_json_decode(stripslashes($_POST['args']), true);
      }
      else {
        $ajax_args = null;
      }

      $selected = 0;
      if (!empty($data[$this->get_field_id( $name )])) {
        $selected = $data[$this->get_field_id( $name )];
      }

      $selected_size = null;
      if (!empty($data[$this->get_field_name( $name ).'-size'])) {
        $selected_size = $data[$this->get_field_name( $name ).'-size'];
      }

      $args = array(
        'field_name' => $name,
        'selected_image' => $selected,
        'selected_size' => $selected_size,
        'post_id' => isset($ajax_args['post_id']) ? $ajax_args['post_id'] : null,
        'select_no_image' => true,
        'suppress_size_selector' => true
      );

      return $this->image_selector('post', $args);
    }

    function global_image_selector( $data = false, $prefix = '' ) {
      $name = !empty( $prefix ) ? $prefix . '_global_image' : 'global_image';
      $selected = 0;
      if (!empty($data[$this->get_field_id( $name )])) {
        $selected = $data[$this->get_field_id( $name )];
      }

      $selected_size = null;
      if (!empty($data[$this->get_field_name( $name ).'-size'])) {
        $selected_size = $data[$this->get_field_name( $name ).'-size'];
      }

      $args = array(
        'field_name' => $name,
        'selected_image' => $selected,
        'selected_size' => $selected_size,
        'suppress_size_selector' => true
      );

      return $this->image_selector('global', $args);
    }

    // Content Move Helpers

    protected $reference_fields = array( 'global_image', 'post_image', 'featured_image' );

    public function get_referenced_ids($data) {
      $references = array();
      foreach ($this->reference_fields as $field) {
        $id = $this->get_data($field, $data);
        if ($id) {
          $references[$field] = array(
            'type' => 'post_type',
            'type_name' => 'attachment',
            'value' => $id
          );
        }
      }

      return $references;
    }

    public function merge_referenced_ids($data, $reference_data) {
      if (!empty($reference_data) && !empty($data)) {
        foreach ($this->reference_fields as $field) {
          if (isset($data[$this->gfn($field)]) && isset($reference_data[$field])) {
            $data[$this->gfn($field)] = $reference_data[$field]['value'];
          }
        }
      }

      return $data;
    }

  }
}