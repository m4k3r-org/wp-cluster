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
      global $wp_query;
      /** Backup wp_query */
      $_wp_query = $wp_query;
      /** Now run our query */
      $wp_query = new WP_Query( array(
        'ID' => false,
      ) );
      $wp_query->data = $data;
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
      /** Add DatePicker */
      //wp_enqueue_script('jquery-ui-datepicker');
      //wp_enqueue_style( 'jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css' );
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
					$("#car-items ol").append(_insert).find(".no-items").hide().end();
					$("a.carousel-post-item-ident", _insert).trigger("click");
					$("body").trigger("click");
					$("#car-item-search #car-search-term").val("");
				};
        
        // set up post edit
				$("#car-items li.carousel-post-item .carousel-post-item-ident, #car-items li.carousel-post-item .carousel-item-img").live("click", function() {
					$(this).closest(".carousel-post-item").addClass("carousel-item-edit");
					return false;
				});
								
				// set up post done edit
				$("#car-items li.carousel-post-item .carousel-edit-done").live("click", function() {
					$(this).closest(".carousel-post-item").removeClass("carousel-item-edit");
					return false;
				});
								
				// set up post remove
				$("#car-items li.carousel-post-item .carousel-edit-remove a").live("click", function() {
					if (confirm("Do you really want to remove this item?")) {
						$(this).closest(".carousel-post-item").remove();
						_parent = $("#car-items ol");
						if (_parent.children().length == 1) {
							$(".no-items", _parent).show();
						}
					}
					return false;
				});
			';
			$js .= $this->global_image_selector_js('global_image', array('direction' => 'horizontal'));
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
			
			// ONLY PULLS POSTS THAT HAVE A FEATURED IMAGE
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
						'title' => get_the_title(),
						'link' => get_permalink(),
						'content' => get_the_excerpt()
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
      ob_start();
      require_once( __DIR__ . '/admin/item.php' );
      return ob_get_clean();
    }
    
    function post_image_selector($data = false) {
			if (isset($_POST['args'])) {
				$ajax_args = cfcf_json_decode(stripslashes($_POST['args']), true);
			}
			else {
				$ajax_args = null;
			}

			$selected = 0;
			if (!empty($data[$this->get_field_id('post_image')])) {
				$selected = $data[$this->get_field_id('post_image')];
			}

			$selected_size = null;
			if (!empty($data[$this->get_field_name('post_image').'-size'])) {
				$selected_size = $data[$this->get_field_name('post_image').'-size'];
			}

			$args = array(
				'field_name' => 'post_image',
				'selected_image' => $selected,
				'selected_size' => $selected_size,
				'post_id' => isset($ajax_args['post_id']) ? $ajax_args['post_id'] : null,
				'select_no_image' => true,
				'suppress_size_selector' => true
			);

			return $this->image_selector('post', $args);
		}
		
		function global_image_selector($data = false) {		
			$selected = 0;
			if (!empty($data[$this->get_field_id('global_image')])) {
				$selected = $data[$this->get_field_id('global_image')];
			}

			$selected_size = null;
			if (!empty($data[$this->get_field_name('global_image').'-size'])) {
				$selected_size = $data[$this->get_field_name('global_image').'-size'];
			}

			$args = array(
				'field_name' => 'global_image',
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
      
      if( !empty( $data[ $this->gfn( 'posts' ) ] ) ) {
        $references[ 'posts' ] = array();
        foreach( $data[ $this->gfn( 'posts' ) ] as $post_id => $post_info ) {
          $post                              = get_post( $post_id );
          $references[ 'posts' ][ $post_id ] = array(
            'type'      => 'post_type',
            'type_name' => $post->post_type,
            'value'     => $post_info[ 'id' ]
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
