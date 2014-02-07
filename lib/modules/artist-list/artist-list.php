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
if( !class_exists( 'ArtistListModule' ) ){

  class ArtistListModule extends \UsabilityDynamics\Theme\Module
  {
    public function __construct(){
      $opts = array(
        'description' => __( 'Choose and display a list of artists.', 'wp-festival' ),
        'icon' => plugins_url( '/icon.png', __DIR__ )
      );
      parent::__construct( 'cfct-module-loop', __( 'Artist List', 'wp-festival' ), $opts );
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
      global $wp_query;
      /** Backup wp_query */
      $_wp_query = $wp_query;
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
      $data[ 'artists' ] = array_values( array_unique( array_merge( $artists, $data[ 'artists' ] ) ) );
      /** Now run our query */
      $wp_query = new WP_Query( array(
        'post__in' => $data[ 'artists' ],
        'post_type' => 'artist',
        'orderby' => 'post__in'
      ) );
      /** Add map for classes and images based on columns amount */
      $mapping = array(
        1   => array( 'col-md-4', 'col-md-offset-4', '487', '368' ), // array( 'column_class', 'first_column_class', 'image_width', 'image_height' )
        2   => array( 'col-md-4', 'col-md-offset-2', '487', '368' ),
        3   => array( 'col-md-4', 'col-md-offset-0', '487', '368' ),
        4   => array( 'col-md-3', 'col-md-offset-0', '517', '616' ),
        5   => array( 'col-md-2', 'col-md-offset-0', '224', '267' ),
        6   => array( 'col-md-2', 'col-md-offset-0', '224', '267' ),
        8   => array( 'col-md-1', 'col-md-offset-0', '224', '267' ),
        10  => array( 'col-md-1', 'col-md-offset-0', '224', '267' ),
        12  => array( 'col-md-1', 'col-md-offset-0', '224', '267' ),
      );
      $data[ 'map' ] = isset( $mapping[ $data[ 'artist_columns' ] ] ) ? $mapping[ $data[ 'artist_columns' ] ] : $mapping[4];
      $wp_query->data = $data;
      /** Get our template */
      ob_start();
      get_template_part( 'templates/section/grid', 'artist' );
      /** Restore our wp_query */
      $wp_query = $_wp_query;
      /** Return our string */
      return ob_get_clean();
    }

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
      /** Ok, I'm going to get all the artists now */
      $query = "SELECT * FROM {$wpdb->posts} WHERE post_type = 'artist' AND post_status = 'publish' ORDER BY post_title ASC";
      $artists = $wpdb->get_results( $query, ARRAY_A );
      /** Ok, setup our artist types */
      $artist_types = array(
        'alist' => __( 'A-List', 'wp-festival' ),
        'blist' => __( 'B-List', 'wp-festival' ),
        'local' => __( 'Local Talent', 'wp-festival' )
      );
      $artist_columns = array( '1', '2', '3', '4', '5', '6', '8', '10', '12' );
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
  }
}
