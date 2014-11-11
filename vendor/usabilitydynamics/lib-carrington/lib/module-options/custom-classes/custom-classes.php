<?php

/**
 * Custom Class Attributes
 *
 * Provides an input on modules to allow input of additional CSS classes
 * to be applied to the Module div when the HTML is rendered.
 *
 * @package Carrington Build
 */
class cfct_module_option_custom_classes extends cfct_module_option {

	public function __construct() {
		global $cfct_build;
		parent::__construct(__('Set CSS Classes', 'carrington-build'), 'custom-classes');
		$cfct_build->register_ajax_handler('option_custom_classes_update', array($this, 'ajax_update'));
		add_filter('cfct-build-module-class', array($this, 'apply_classes'), 10, 2);

		// Add to all modules
		add_filter(
			'cfct-build-module-options',
			create_function('$options',
				'return array_merge($options, array("cfct_module_option_custom_classes"));'
			)
		);
	}

	/**
	 * Non-standard module options method to filter in our custom classes in to the
	 * module's class attribute. Uses a standard filter in CB
	 *
	 * @param string $class
	 * @param array $data
	 * @return string
	 */
	public function apply_classes($class, $data) {
		global $cfct_build;
		$build_data = $cfct_build->get_postmeta();
		if (!empty($build_data['module-options'][$data['module_id']][$this->id_base]['custom-css'])) {
			$classes = cfct_tpl::extract_classes($class);
			$class = cfct_tpl::to_classname(
				$classes,
				$build_data['module-options'][$data['module_id']][$this->id_base]['custom-css']
			);
		}
		return $class;
	}

	public function html($data, $options_data) {
		$value = null;
		$btn_text = 'Add';
		if (isset($options_data['custom-css']) && is_array($options_data['custom-css'])) {
			$value = implode(' ', array_map('esc_attr', $options_data['custom-css']));
			$btn_text = 'Edit';
		} else {
			$value = '<span class="option-note"><em>none specified</em></span>';
		}
		$html = '<div class="cfct-rendering cfct-module-option-custom-css">
					<div class="cfct-option cfct-clearfix">
						<div class="class-list">Module Classes: <span class="custom-css">'.$value.'</span></div>
						<a href="#" class="cfct-button cfct-button-dark trigger add-class-btn">'.$btn_text.'</a>
					</div>
					<div class="form" style="display:none;">
						'.$this->form($options_data).'
						<div class="option-note add-class-note">Space seperated</div>
						<button class="save cfct-button cfct-button-dark">Save</button>
						<a class="cancel" href="#">Cancel</a>
					</div>
				</div>';
		return $html;
	}

	private function form($data) {
		$dropdown_opts = apply_filters('cfct-module-predefined-class-options', cfct_class_groups('wrapper'));
		$predefined_classes = array();
		$input_class = (empty($dropdown_opts) ? 'no-button' : null);

		$value = null;
		if (!isset($data['custom-css'])) {
			$data['custom-css'] = array();
		}
		if (!empty($data['custom-css'])) {
			$value = implode(' ', array_map('esc_attr', $data['custom-css']));
		}

		$html = '
				<label for="">Classes:</label>
				<div class="cfct-select-menu-wrapper">
					<input type="text" class="'.$input_class.' cfct_custom_class_input" name="'.$this->get_field_name('custom-css').'" id="'.$this->get_field_id('custom-css').'" value="'.$value.'"  autocomplete="off" />';
		if (is_array($dropdown_opts) && !empty($dropdown_opts)) {
		$html .= '<input type="button" name="" id="'.$this->get_field_id('class-list-toggle').'" class="cfct-button cfct-button-dark" value="">
					<div id="'.$this->get_field_id('class-list-menu').'" class="cfct-select-menu" style="display: none;">
						<ul>';
		foreach($dropdown_opts as $classname => $title) {
			$class = (in_array($classname, $data['custom-css']) ? 'inactive' : null);
			$html .= '
							<li><a class="'.$class.'" href="#'.esc_attr($classname).'" title="'.esc_attr($title).'">'.esc_html($classname).'</a></li>';
		}
		$html .= '
						</ul>
					</div>';
		}
		$html .= '
				</div>
			';
		return $html;
	}

	public function admin_js() {
		return $this->load_view('view-admin-js.php');
	}

	public function ajax_update($args) {
		global $cfct_build;
		$post_data = $cfct_build->get_postmeta($args['post_id']);

		// Module may not exist yet at all.
		if (!isset($post_data['module-options'][$args['module_id']])) {
			$post_data['module-options'][$args['module_id']] = array($this->id_base => array('custom-css' => array()));
		}

		if (!isset($post_data['module-options'][$args['module_id']][$this->id_base])) {
			$post_data['module-options'][$args['module_id']][$this->id_base] = array('custom-css' => array());
		}

		$post_data['module-options'][$args['module_id']][$this->id_base]['custom-css'] = array();

		if ($args['data']) {
			foreach(explode(' ', $args['data']) as $class) {
				$post_data['module-options'][$args['module_id']][$this->id_base]['custom-css'][] = sanitize_title_with_dashes(trim(strip_tags($class)));
			}
		}
		else {
			$post_data['module-options'][$args['module_id']][$this->id_base]['custom-css'] = array();
		}

		if (!$cfct_build->set_postmeta($args['post_id'], $post_data)) {
			throw new cfct_row_exception(__('Could not save postmeta for post on custom css update','carrington-build').' (post_id: '.$args['post_id'].')');
		}

		$cfct_build->set_post_content($args['post_id']);

		$ret = new cfct_message(array(
			'success' => true,
			'html' => __('CSS classes updated.', 'carrington-build'),
			'message' => 'module_id '.$args['module_id'].' '.sprintf(__('CSS classes: %s', 'carrington-build'), implode(' ', $post_data['module-options'][$args['module_id']][$this->id_base]['custom-css'])),
			'extra' => array(
				'module_id' => $args['module_id'],
				'data' => implode(' ', $post_data['module-options'][$args['module_id']][$this->id_base]['custom-css'])
			)
		));
		return $ret;
	}
}

cfct_module_register_extra('cfct_module_option_custom_classes');

