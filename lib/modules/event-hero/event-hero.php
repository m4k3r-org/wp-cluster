<?php
/**
 * Carrington Build Event Hero Module
 *
 * 
 * There's a base class that outputs full loop content, but 2 class
 * extensions which extend it, but change it to "excerpts" or "titles"
 * Don't forget to call EventHeroModule::init() in your constructor if you
 * derive from this class!
 */
if( !class_exists( 'EventHeroModule' ) ){

  class EventHeroModule extends \UsabilityDynamics\Theme\Module {
  
    protected $content_support = array(
			'title',
			'content',
			'url',
			'images'
		);
  
    public function __construct(){
      $opts = array(
        'description' => __( 'Choose Event to display as Hero widget.', 'wp-festival' ),
        'icon' => plugins_url( '/icon.png', __DIR__ )
      );
      parent::__construct( 'cfct-module', __( 'Event Hero', wp_festival( 'domain' ) ), $opts );
      
      //
      add_filter('wp_ajax_cfct_event_post_search', array( $this, '_handle_request' ) );
    }
    
    /**
     * Don't contribute to the post_content stored in the database
     * @return null
     */
    public function text( $data ){
      return null;
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
      unset( $new_data[ $this->get_field_name('logo_global_image-image-search') ] );
			
			// normalize the selected image value in to a 'background_image' value for easy output
			if ( !empty( $new_data[ $this->get_field_name('post_image') ] ) ) {
				$new_data[ 'background_image' ] = $new_data[ $this->get_field_name('post_image') ];
			}
			elseif (!empty($new_data[$this->get_field_name('global_image')])) {
				$new_data[ 'background_image' ] = $new_data[ $this->get_field_name('global_image') ];
			}
      
      // normalize the selected image value in to a 'featured_image' value for easy output
			if ( !empty( $new_data[ $this->get_field_name('logo_post_image') ] ) ) {
				$new_data[ 'logo_image' ] = $new_data[ $this->get_field_name('logo_post_image') ];
			}
			elseif (!empty($new_data[$this->get_field_name('logo_global_image')])) {
				$new_data[ 'logo_image' ] = $new_data[ $this->get_field_name('logo_global_image') ];
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
      global $wp_query;
      /** Backup wp_query */
      $_wp_query = $wp_query;
      /** Now run our query */
      if( !isset( $data[ $this->get_field_name( 'posts' ) ] ) ) {
        return false;
      }
      reset( $data[ $this->get_field_name( 'posts' ) ] );
      $event_id = key( $data[ $this->get_field_name( 'posts' ) ] );
      /** Get event */
      $wp_query = new WP_Query( array(
        'post__in' => array( $event_id ),
        'post_type' => 'event'
      ) );
      /** Add map for classes and images based on columns amount */
      $mapping = array(
        1   => array( 'col-md-2', 'col-md-offset-5', '250', '220' ), // array( 'column_class', 'first_column_class', 'image_width', 'image_height' )
        2   => array( 'col-md-2', 'col-md-offset-4', '250', '220' ),
        3   => array( 'col-md-2', 'col-md-offset-3', '250', '220' ),
        4   => array( 'col-md-2', 'col-md-offset-2', '250', '220' ),
        5   => array( 'col-md-2', 'col-md-offset-1', '250', '220' ),
        6   => array( 'col-md-2', 'col-md-offset-0', '250', '220' ),
        8   => array( 'col-md-1', 'col-md-offset-2', '250', '220' ),
        10  => array( 'col-md-1', 'col-md-offset-1', '250', '220' ),
        12  => array( 'col-md-1', 'col-md-offset-0', '250', '220' ),
      );
      /** Set templates data */
      $map = isset( $mapping[ $data[ 'artist_columns' ] ] ) ? $mapping[ $data[ 'artist_columns' ] ] : $mapping[4];
      $_data = array(
        'postdata' => ( array_shift( $data[ $this->get_field_name( 'posts' ) ] ) ),
        'logo_image' => $data[ 'logo_image' ],
        'background_image' => $data[ 'background_image' ],
        'background_color' => $data[ 'background_color' ],
        'font_color' => $data[ 'font_color' ],
        'title_color' => $data[ 'title_color' ],
        'desc_color' => $data[ 'desc_color' ],
        'enable_links' => $data[ 'enable_links' ],
        'artist_image_type' => $data[ 'artist_image_type' ],
        'artist_columns' => $data[ 'artist_columns' ],
        'class_col' => $map[ 0 ],
        'class_offset' => $map[ 1 ],
        'artist_image_width' => $map[ 2 ],
        'artist_image_height' => $map[ 3 ],
      );
      $wp_query->data[ 'event-hero' ] = $_data;
      /** Get our template */
      ob_start();
      get_template_part( 'templates/article/listing-event', 'hero' );
      /** Restore our wp_query */
      $wp_query = $_wp_query;
      /** Return our string */
      return ob_get_clean();
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
      /** Amount of columns per line */
      $artist_columns = array( '1', '2', '3', '4', '5', '6', '8', '10', '12' );
      /** Which image should be shown */
      $artist_images = array(
        'featured'      => __( 'Featured', wp_festival( 'domain' ) ),
        'headshotImage' => __( 'Headshot', wp_festival( 'domain' ) ),
        'portraitImage' => __( 'Portrait', wp_festival( 'domain' ) ),
        'logoImage'     => __( 'Logo', wp_festival( 'domain' ) ),
      );
      /** Now get and return the template */
      ob_start();
      require_once( __DIR__ . '/admin/form.php' );
      return ob_get_clean();
    }
    
    public function admin_js() {
			$js = '
				cfct_builder.addModuleLoadCallback("'.$this->id_base.'", function() {
					'.$this->cfct_module_tabs_js().'
          
          var cfct_event_link_search_results = function(target) {
            $(target).unbind().bind("otypeahead-select", function() {
							var _insert = $(this).find("li.otypeahead-current").clone().removeClass("otypeahead-current");
							' . $js_base . '_insert_selected_item(_insert);
						}).find(".car-search-elements a").click(function() {
							var _insert = $(this).closest("li").clone();
							' . $js_base . '_insert_selected_item(_insert);
							return false;
						});
          };
          
          // set up search
					$("#car-item-search #car-search-term").oTypeAhead({
						searchParams: {
							action: "cfct_event_post_search",
							method: "do_search"
						},
						url: cfct_builder.opts.ajax_url,
						loading: "<div class=\"' . $this->id_base . '-loading\">' . __( 'Loading...', wp_festival( 'domain' ) ) . '<\/div>",
						form: ".car-item-search-container",
						disableForm: false,
						resultsCallback: cfct_event_link_search_results
					});
          
				});
				
				cfct_builder.addModuleSaveCallback("'.$this->id_base.'", function() {
					// find the non-active image selector and clear his value
					$("#'.$this->id_base.'-image-selectors .cfct-module-tab-contents>div:not(.active)").find("input:hidden").val("");
          $("#car-item-search .otypeahead-target").children().remove();
					return true;
				});
        
        var ' . $js_base . '_insert_selected_item = function(_insert) {
          $("#car-item-search").addClass( "hidden" );
					$("#car-items ol").append(_insert).find(".no-items").hide().end();
					$("a.event-post-item-ident", _insert).trigger("click");
					$("body").trigger("click");
					$("#car-item-search #car-search-term").val("");
				};
								
				// set up post remove
				$("#car-items li.event-post-item .event-edit-remove a").live("click", function() {
					if (confirm("Do you really want to remove this item?")) {
						$(this).closest(".event-post-item").remove();
						_parent = $("#car-items ol");
						if (_parent.children().length <= 1) {
							$("#car-item-search").removeClass( "hidden" );
						}
					}
					return false;
				});
			';
			$js .= $this->global_image_selector_js('global_image', array('direction' => 'horizontal'));
      $js .= $this->global_image_selector_js('logo_global_image', array('direction' => 'horizontal'));
			return $js;
		}
    
    public function _handle_request() {
			if (!empty( $_POST[ 'method' ] ) ) {
				switch( $_POST[ 'method' ] ) {
					case 'do_search':
						$this->_do_search();
						break;
				}
				exit;
			}
		}
    
    protected function _do_search() {
			$posts_per_page = 8;
			$page = isset( $_POST['car_search_page'] ) ? absint( $_POST['car_search_page'] ) : 1;
			
			$s = new WP_Query(array(
				's' => $_POST[ 'car-search-term' ],
				'post_type' => 'event',
				'posts_per_page' => $posts_per_page,
				'paged' => $page,
			));
			
			$ids = array();
			$ret = array(
				'html' => '',
				'key' => isset( $_POST['key'] ) ? $_POST['key'] : ''
			);
			
			
			$html = '';
			if ( $s->have_posts() ) {
				$html = '<ul class="car-search-elements">';
				while ($s->have_posts()) {
					$s->the_post();
					$post_id = get_the_id();
					if (in_array($post_id, $ids)) {
						continue;
					}
					$ids[] = $post_id;
					remove_filter( 'the_content', 'wptexturize' );
					$postdata = array(
						'id' => get_the_id(),
						'post_title' => get_the_title(),
						'post_excerpt' => get_the_excerpt()
					);
					add_filter( 'the_content', 'wptexturize' );
					$html .= $this->get_event_admin_item( $postdata );
				}
				$html .= '</ul>';
				if ($s->found_posts > $posts_per_page) {
					$paginate_args = array(
						'base' => '#%_%',
						'format' => '?car_search_page=%#%',
						'total' => $s->max_num_pages,
						'current' => $page,
						);
					$paginate_html = paginate_links($paginate_args);
					$html .= '<span class="car-search-pagination">'.$paginate_html.'</span>';
				}
			}
			$ret['html'] .= $html;

			if (empty($ret['html'])) {
				$ret['html'] = '<ul><li class="no-items-found">No items found for search: "'.esc_html($_POST['car-search-term']).'"</li></ul>';
			}
						
			header('content-type: application/javascript');
			echo json_encode($ret);
			exit;
		}
    
    /**
     * Formats the data for admin editing
     *
     * @param $postdata - pro-processed post information
     *
     * @return string HTML
     */
    protected function get_event_admin_item( $postdata ) {
      $post = wp_festival()->get_post_data( $postdata[ 'id' ] );
      $postdata = wp_festival()->extend( $post, $postdata );
      
      ob_start();
      //echo "<pre>"; print_r( $postdata ); echo "</pre>";
      require_once( __DIR__ . '/admin/item.php' );
      return ob_get_clean();
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

		protected $reference_fields = array( 'global_image', 'post_image', 'background_image' );

		public function get_referenced_ids($data) {
			$references = array();
			
      foreach ($this->reference_fields as $field) {
				$id = $this->get_data($field, $data);
				if ($id) {
					$references[$field] = array(
						'type' => 'post_type',
						'value' => $id
					);
				}
			}
      
      if( !empty( $data[ $this->gfn( 'posts' ) ] ) ) {
        $references[ 'posts' ] = array();
        foreach( $data[ $this->gfn( 'posts' ) ] as $post_id => $post_info ) {
          $post = get_post( $post_id );
          $references[ 'posts' ][ $post_id ] = array(
            'type'      => 'post_type',
            'value'     => $post_info[ 'ID' ]
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
      
      if( !empty( $reference_data[ 'posts' ] ) && !empty( $data ) ) {
        foreach( $reference_data[ 'posts' ] as $key => $r_data ) {
          // Data here is stored with the post_id in the data as well as being the array key,
          // so we need to nuke the old value with the old post_id key and replace it with
          // the new post_id as the key and the updated post_info
          $_post_info = $data[ $this->gfn( 'posts' ) ][ $key ];
          unset( $data[ $this->gfn( 'posts' ) ][ $key ] );
          $_post_info[ 'id' ]                                  = $r_data[ 'value' ];
          $data[ $this->gfn( 'posts' ) ][ $r_data[ 'value' ] ] = $_post_info;
        }
      }

			return $data;
		}
    
  }
}
