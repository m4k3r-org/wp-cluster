<?php

/**
 * Plain Text Carrington Build Module
 * Simple plain text box that stores and displays exactly what it is given.
 * Good for displaying raw HTML and/or JavaScript
 */
if (!class_exists('cfct_module_rich_text')) {
	class cfct_module_rich_text extends cfct_build_module {
		protected $_deprecated_id = 'cfct-rich-text-module'; // deprecated property, not needed for new module development

		// remove padding from the popup-content form
		protected $admin_form_fullscreen = true;

		public function __construct() {
			$opts = array(
				'description' => __('Provides a WYSIWYG editor.', 'carrington-build'),
				'icon' => 'rich-text/icon.png'
			);
			parent::__construct('cfct-rich-text', __('Rich Text', 'carrington-build'), $opts);
		}

		public function display($data) {
			$text = do_shortcode($data[$this->get_field_id('content')]);
			return $this->load_view($data, compact('text'));
		}

		public function admin_form($data) {
			add_action('admin_footer', array($this, 'admin_footer_wp_editor'), 1);
			$content = (isset($data[$this->get_field_name('content')]) ? $data[$this->get_field_name('content')] : '');
			ob_start();
			wp_editor($content, $this->get_field_id('content'), array(
				'media_buttons' => false,
				'textarea_name' => $this->get_field_name('content'),
			));
			$ret = ob_get_clean();
			return $ret;
		}

		/**
		 * Return a textual representation of this module.
		 *
		 * @param array $data
		 * @return string
		 */
		public function text($data) {
			return strip_tags($data[$this->get_field_name('content')]);
		}


		/**
		 * Modify the data before it is saved, or not
		 *
		 * @param array $new_data
		 * @param array $old_data
		 * @return array
		 */
		public function update($new_data, $old_data) {
			return $new_data;
		}

		/**
		 * Add some admin CSS for formatting
		 *
		 * @return void
		 */
		public function admin_css() {
			return '
				#'.$this->get_field_id('content').' {
					height: 300px;
				}
				#cfct-rich-text-content_tbl {
					background-color: #fff;
				}
				#cfct-rich-text-edit-form .wp-editor-tabs {
					display: none;
				}
			';
		}

		public function admin_js() {
			$js = '
				// automatically set focus on the rich text editor

				cfct_builder.addModuleLoadCallback("'.$this->id_base.'",function(form) {
					tinymce.init({
						selector : "#'.$this->get_field_id('content').'",
						menubar : false,
						plugins : tinyMCEPreInit.mceInit.content.plugins,
						toolbar1 : tinyMCEPreInit.mceInit.content.toolbar1.replace("wp_more", "").replace("fullscreen", ""),
						toolbar2 : tinyMCEPreInit.mceInit.content.toolbar2,
						toolbar3 : tinyMCEPreInit.mceInit.content.toolbar3,
						toolbar4 : tinyMCEPreInit.mceInit.content.toolbar4
					});
					setTimeout(function() {tinyMCE.execCommand("mceFocus", true, "'.$this->get_field_id('content').'");}, 10);

					// properly destroy the editor on cancel
					$("#cfct-edit-module-cancel").click(function() {
						var _ed = tinyMCE.get("'.$this->get_field_id('content').'");
						tinyMCE.remove(_ed);
					});
				});

				// we have to register a save callback so that tinyMCE pushes the data
				// back to the original textarea before the submit script gathers its content
				cfct_builder.addModuleSaveCallback("'.$this->id_base.'",function(form) {
					var _ed = tinyMCE.get("'.$this->get_field_id('content').'");
					_ed.save();
					tinyMCE.remove(_ed);
				});
			';
			return $js;
		}

		public function admin_footer_wp_editor() {
			$set = _WP_Editors::parse_settings($this->get_field_id('content'), array(
				'dfw' => true,
				'editor_height' => 300,
				'tinymce' => array(
					'resize' => false,
					'add_unload_trigger' => false,
				),
			));
			_WP_Editors::editor_settings($this->get_field_id('content'), $set);
		}
	}

	cfct_build_register_module('cfct_module_rich_text');
}
?>
