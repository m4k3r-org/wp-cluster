<?php

/**
 * Custom Class Attributes
 *
 * Provides an input on rows to allow input of additional CSS classes
 * to be applied to the row div when the HTML is rendered.
 *
 * @package Carrington Build
 */
class cfct_row_option_custom_classes extends cfct_row_option {

	public function __construct() {
		global $cfct_build;
		parent::__construct(__('Set CSS Classes', 'carrington-build'), 'row-custom-classes');
		$cfct_build->register_ajax_handler('row_option_custom_classes_update', array($this, 'ajax_update'));
		add_filter('cfct-build-row-class', array($this, 'apply_classes'), 10, 2);

		// Add this option to all rows
		add_filter(
			'cfct-build-row-options',
			create_function('$options',
				'return array_merge($options, array("cfct_row_option_custom_classes"));'
			)
		);
	}

	/**
	 * Non-standard row options method to filter in our custom classes in to the
	 * row's class attribute. Uses a standard filter in CB
	 *
	 * @param string $class
	 * @param array $data
	 * @return string
	 */
	public function apply_classes($class, $data) {
		global $cfct_build;
		$build_data = $cfct_build->get_postmeta();
		if (!empty($build_data['row-options'][$data['guid']][$this->id_base]['custom-classes'])) {
			$classes = cfct_tpl::extract_classes($class);
			$class = cfct_tpl::to_classname(
				$classes,
				$build_data['row-options'][$data['guid']][$this->id_base]['custom-classes']
			);
		}
		return $class;
	}

	public function html($data, $options_data) {
		$value = null;
		if (isset($options_data['custom-classes']) && is_array($options_data['custom-classes'])) {
			$value = implode(' ', array_map('esc_attr', $options_data['custom-classes']));
		} else {
			$value = '<span class="option-note"><em>none specified</em></span>';
		}
		$html = '<div class="cfct-rendering cfct-row-option-custom-classes">
					<a class="cfct-option cfct-clearfix trigger">
						<div class="class-list">Row Classes<br /><span class="row-custom-classes">'.$value.'</span></div>
					</a>
					<div class="form" style="display:none;">
						<div class="class-list">Row Classes</div>
						'.$this->form($options_data).'
						<div class="option-note add-class-note">Space seperated</div>
						<button class="save cfct-button cfct-button-dark">Save</button>
						<a class="cancel" href="#">Cancel</a>
					</div>
				</div>';

		return $html;
	}

	private function form($data) {
		$dropdown_opts = apply_filters('cfct-row-predefined-class-options', cfct_class_groups('wrapper'));
		$predefined_classes = array();
		$input_class = (empty($dropdown_opts) ? 'no-button' : null);

		$value = null;
		if (!isset($data['custom-classes'])) {
			$data['custom-classes'] = array();
		}
		if (!empty($data['custom-classes'])) {
			$value = implode(' ', array_map('esc_attr', $data['custom-classes']));
		}

		$html = '<div class="cfct-select-menu-wrapper">
					<input type="text" class="'.$input_class.' cfct_custom_class_input" name="'.$this->get_field_name('custom-classes').'" id="'.$this->get_field_id('custom-classes').'" value="'.$value.'"  autocomplete="off" />';
		if (is_array($dropdown_opts) && !empty($dropdown_opts)) {
		$html .= '<input type="button" name="" id="'.$this->get_field_id('class-list-toggle').'" class="cfct-button cfct-button-dark" value="">
					<div id="'.$this->get_field_id('class-list-menu').'" class="cfct-select-menu" style="display: none;">
						<ul>';
		foreach($dropdown_opts as $classname => $title) {
			$class = (in_array($classname, $data['custom-classes']) ? 'inactive' : null);
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
		if (!isset($post_data['row-options'][$args['row_id']])) {
			$post_data['row-options'][$args['row_id']] = array($this->id_base => array('custom-classes' => array()));
		}

		if (!isset($post_data['row-options'][$args['row_id']][$this->id_base])) {
			$post_data['row-options'][$args['row_id']][$this->id_base] = array('custom-classes' => array());
		}

		$post_data['row-options'][$args['row_id']][$this->id_base]['custom-classes'] = array();
		if ($args['data']) {
			foreach(explode(' ', $args['data']) as $class) {
				$post_data['row-options'][$args['row_id']][$this->id_base]['custom-classes'][] = sanitize_title_with_dashes(trim(strip_tags($class)));
			}
		}
		else {
			$post_data['row-options'][$args['row_id']][$this->id_base]['custom-classes'] = array();
		}

		if (!$cfct_build->set_postmeta($args['post_id'], $post_data)) {
			throw new cfct_row_exception(__('Could not save postmeta for post on custom css update','carrington-build').' (post_id: '.$args['post_id'].')');
		}
		$cfct_build->set_post_content($args['post_id']);

		$ret = new cfct_message(array(
			'success' => true,
			'html' => __('CSS classes updated.', 'carrington-build'),
			'extra' => array(
				'row_id' => $args['row_id'],
				'css_classes' => $args['data']
			)
		));
		return $ret;
	}
}

cfct_row_register_extra('cfct_row_option_custom_classes');

