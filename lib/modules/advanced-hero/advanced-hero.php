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
      
      // set up rich text editing if user has disabled preference that will not load tinymce
      if( !user_can_richedit() ) {
        add_action( 'admin_print_footer_scripts', array( $this, 'footer_js' ), 10 );
      }
    }
    
    /**
     * Return a textual representation of this module.
     *
     * @return null
     */
    public function text( $data ){
      return strip_tags( $data[ $this->get_field_name( 'content' ) ] );
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
			
			// normalize the selected image value in to a 'background_image' value for easy output
			if ( !empty( $new_data[ $this->get_field_name('post_image') ] ) ) {
				$new_data[ 'background_image' ] = $new_data[ $this->get_field_name('post_image') ];
			}
			elseif (!empty($new_data[$this->get_field_name('global_image')])) {
				$new_data[ 'background_image' ] = $new_data[ $this->get_field_name('global_image') ];
			}
      
      // normalize the selected image value in to a 'parallax_image' value for easy output
			if ( !empty( $new_data[ $this->get_field_name('parallax_post_image') ] ) ) {
				$new_data[ 'parallax_image' ] = $new_data[ $this->get_field_name('parallax_post_image') ];
			}
			elseif (!empty($new_data[$this->get_field_name('parallax_global_image')])) {
				$new_data[ 'parallax_image' ] = $new_data[ $this->get_field_name('parallax_global_image') ];
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
      $class_map = array(
        'left' => 'col-md-4',
        'right' => 'col-md-4 col-md-offset-8',
        'center' => 'col-md-4 col-md-offset-4',
        'full' => 'col-md-12',
      );
      
      $wp_query->data[ 'advanced-hero' ] = array(
        'content' => do_shortcode( $data[ $this->get_field_id( 'content' ) ] ),
        'column_class' => ( isset( $class_map[ $data[ 'content_position' ] ] ) ? $class_map[ $data[ 'content_position' ] ] : $class_map[ 'left' ] ),
        'background_color' => $data[ 'background_color' ],
        'background_image' => $data[ 'background_image' ],
        'parallax_rotation' => $data[ 'parallax_rotation' ],
        'parallax_position' => $data[ 'parallax_position' ],
        'parallax_image' => $data[ 'parallax_image' ],
      );
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
      /** Add Colorpicker */
      wp_enqueue_script('wp-color-picker');
      wp_enqueue_style( 'wp-color-picker' );
      $content_position = array(
        'left' => __( 'Left', wp_festival( 'domain' ) ),
        'right' => __( 'Right', wp_festival( 'domain' ) ),
        'center' => __( 'Center', wp_festival( 'domain' ) ),
        'full' => __( 'Full Width', wp_festival( 'domain' ) ),
      );
      $parallax_position = array(
        'left' => __( 'Left', wp_festival( 'domain' ) ),
        'right' => __( 'Right', wp_festival( 'domain' ) ),
        'full' => __( 'Full', wp_festival( 'domain' ) ),
      );
      /** Now get and return the template */
      ob_start();
      require_once( __DIR__ . '/admin/form.php' );
      return ob_get_clean();
    }
    
    /**
     * Add some admin CSS for formatting
     *
     * @return void
     */
    public function admin_css() {
      return '
				#' . $this->get_field_id( 'content' ) . ' {
					height: 200px;
				}
			';
    }
    
    /**
     * Set up tinymce
     *
     * @return string javascript
     */
    public function inline_js() {
      $mce_locale                 = ( '' == get_locale() ) ? 'en' : strtolower( substr( get_locale(), 0, 2 ) ); // only ISO 639-1
      $mce_spellchecker_languages = apply_filters( 'mce_spellchecker_languages', '+English=en,Danish=da,Dutch=nl,Finnish=fi,French=fr,German=de,Italian=it,Polish=pl,Portuguese=pt,Spanish=es,Swedish=sv' );

      $js = '
				<script type="text/javascript">
					//<![CDATA[
					// same as calling tinymce.EditorManager.init({});
					tinyMCE.init({ ';
      // compress output whitespace a bit...
      $js .= preg_replace( '/(\n|\t)/', '', '
						mode:"none",
						onpageload:"", 
						width:"100%", 
						theme:"advanced", 
						skin:"wp_theme", 
						theme_advanced_buttons1:"bold,italic,underline,|,bullist,numlist,blockquote,|,justifyleft,justifycenter,justifyright,|,outdent,indent,|,link,unlink,|,code,wp_help",
						theme_advanced_buttons2:"formatselect,underline,justifyfull,forecolor,|,pastetext,pasteword,removeformat,|,charmap,|,undo,redo,spellchecker", 
						theme_advanced_buttons3:"", 
						theme_advanced_buttons4:"", 
						language:"' . $mce_locale . '",
						spellchecker_languages:"' . $mce_spellchecker_languages . '",
						theme_advanced_toolbar_location:"top", 
						theme_advanced_toolbar_align:"left", 
						theme_advanced_statusbar_location:"bottom", 
						theme_advanced_resizing:"", 
						theme_advanced_resize_horizontal:"", 
						dialog_type:"modal", 
						relative_urls:"", 
						remove_script_host:"", 
						convert_urls:"", 
						apply_source_formatting:"", 
						remove_linebreaks:"0", 
						paste_convert_middot_lists:"1", 
						paste_remove_spans:"1", 
						paste_remove_styles:"1", 
						gecko_spellcheck:"1", 
						entities:"38,amp,60,lt,62,gt", 
						accessibility_focus:false, 
						tab_focus:":prev,:next", 
						save_callback:"", 
						wpeditimage_disable_captions:"", 
						plugins:"safari,inlinepopups,spellchecker,paste"
					' );
      $js .= '
					});
					//]]>
				</script>';

      return $js;
    }
    
    public function admin_js() {
			$js = '
				cfct_builder.addModuleLoadCallback("'.$this->id_base.'", function() {
					'.$this->cfct_module_tabs_js().'
				});
				
				cfct_builder.addModuleSaveCallback("'.$this->id_base.'", function() {
					// find the non-active image selector and clear his value
					$(".'.$this->id_base.'-image-selectors .cfct-module-tab-contents>div:not(.active)").find("input:hidden").val("");
					return true;
				});
        
        // automatically set focus on the rich text editor
				cfct_builder.addModuleLoadCallback("' . $this->id_base . '",function(form) {
					tinyMCE.execCommand("mceAddControl", false, "' . $this->get_field_id( 'content' ) . '");
					setTimeout(function() {tinyMCE.execCommand("mceFocus", true, "' . $this->get_field_id( 'content' ) . '");}, 10);

					// properly destroy the editor on cancel
					$("#cfct-edit-module-cancel").click(function() {
						var _ed = tinyMCE.get("' . $this->get_field_id( 'content' ) . '");
						tinyMCE.remove(_ed);						
					});
				});
				
				// we have to register a save callback so that tinyMCE pushes the data
				// back to the original textarea before the submit script gathers its content		
				cfct_builder.addModuleSaveCallback("' . $this->id_base . '",function(form) {
					var _ed = tinyMCE.get("' . $this->get_field_id( 'content' ) . '");
					_ed.save();
					tinyMCE.remove(_ed);
				});
			';
			$js .= $this->global_image_selector_js();
			return $js;
		}
    
    /**
     * Add tinyMCE to the footer
     * Only happens if user has DESELECTED the rich text editing
     * option in their user preference
     *
     * @return void
     */
    public function footer_js() {
      // wp_tiny_mce();
      global $tinymce_version;
      $baseurl = includes_url( 'js/tinymce' );
      echo '
				<script type="text/javascript" src="' . $baseurl . '/tiny_mce.js?ver=' . $tinymce_version . '"></script>
				<script type="text/javascript" src="' . $baseurl . '/langs/wp-langs-en.js?ver=' . $tinymce_version . '"></script>' . PHP_EOL;
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

		protected $reference_fields = array( 'global_image', 'post_image', 'parallax_image', 'background_image' );

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
