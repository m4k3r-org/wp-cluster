<?php
/**
 * Carrington Build Advanced Hero Module
 *
 * 
 * There's a base class that outputs full loop content, but 2 class
 * extensions which extend it, but change it to "excerpts" or "titles"
 * Don't forget to call AdvancedHeroModule::init() in your constructor if you
 * derive from this class!
 */
if( !class_exists( 'AdvancedHeroModule' ) ){

  class AdvancedHeroModule extends \UsabilityDynamics\Theme\Module {
  
    protected $content_support = array(
			'title',
			'content',
			'url',
			'images'
		);
  
    public function __construct(){
      $opts = array(
        'description' => __( 'Display Text Block with Parallax effect.', wp_festival( 'domain' ) ),
        'icon' => plugins_url( '/icon.png', __DIR__ )
      );
      parent::__construct( 'cfct-module', __( 'Advanced Hero', wp_festival( 'domain' ) ), $opts );
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
			
			// normalize the selected image value in to a 'parallax_image' value for easy output
			if ( !empty( $new_data[ $this->get_field_name('post_image') ] ) ) {
				$new_data[ 'parallax_image' ] = $new_data[ $this->get_field_name('post_image') ];
			}
			elseif (!empty($new_data[$this->get_field_name('global_image')])) {
				$new_data[ 'parallax_image' ] = $new_data[ $this->get_field_name('global_image') ];
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
      $wp_query->data[ 'parallax' ] = $data;
      /** Get our template */
      ob_start();
      get_template_part( 'templates/aside/advanced-hero' );
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
      /** Now get and return the template */
      ob_start();
      require_once( __DIR__ . '/admin/form.php' );
      return ob_get_clean();
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

		protected $reference_fields = array( 'global_image', 'post_image', 'parallax_image' );

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
