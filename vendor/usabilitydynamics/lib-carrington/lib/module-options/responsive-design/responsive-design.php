<?php

/**
 * Responsive Design Classes
 *
 * @package Carrington Build
 */
class cfct_module_option_responsive_design extends cfct_module_option {

	const CFCT_RESPONSIVE_DEBUG = false;

	public function __construct() {
		global $cfct_build;
		parent::__construct(__('Responsive Design', 'carrington-build'), 'responsive-design');
		add_filter('cfct-build-module-class', array($this, 'apply_classes'), 10, 2);
		$cfct_build->register_ajax_handler('responsive_update', array($this, 'ajax_responsive_update'));
		add_filter('cfct-module-state-classes', array($this, 'apply_hidden_state_classes'), 10, 3);
		add_filter('cfct-module-state-html', array($this, 'apply_hidden_state_html'));

		// Add to all modules
		add_filter(
			'cfct-build-module-options',
			create_function('$options',
				'return array_merge($options, array("cfct_module_option_responsive_design"));'
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

		if (!empty($build_data['module-options'][$data['module_id']][$this->id_base]['responsive-classes'])) {

			$classes = cfct_tpl::extract_classes($class);
			$responsive_classes = $build_data['module-options'][$data['module_id']][$this->id_base]['responsive-classes'];

			$class = cfct_tpl::to_classname(
				$classes,
				$responsive_classes
			);
		}
		return $class;
	}

	/**
	 * Responsive design CSS classes and descriptions
	 * Use filter `cfct-build-responsive-design-classes` to modify.
	 *
	 * @return array
	 **/
	public function available_classes() {
		$classes = array();
		if (self::CFCT_RESPONSIVE_DEBUG) {
			$classes = array(
				'cfct-responsive-mobile-hide' => array(
					'icon' => 'img/responsive/icon-mobile.png',
					'name' => __('Mobile', 'carrington-build'),
				),
				'cfct-responsive-tablet-hide' => array(
					'icon' => 'img/responsive/icon-tablet.png',
					'name' => __('Tablet', 'carrington-build'),
				),
				'cfct-responsive-desktop-hide' => array(
					'icon' => 'img/responsive/icon-desktop.png',
					'name' => __('Desktop', 'carrington-build'),
				),
			);
		}
		$classes = apply_filters('cfct-build-responsive-design-classes', $classes);

		// Sanitization does not need to be applied to classes unless
		// functionality to manage response classes in the admin is added.
		return $classes;
	}

	public function ajax_responsive_update($args) {
		global $cfct_build;
		$post_data = $cfct_build->get_postmeta($args['post_id']);

		// Ensure our data structure
		if (!isset($post_data['module-options'][$args['module_id']])) {
			$post_data['module-options'][$args['module_id']] = array($this->id_base => array('responsive-classes' => array()));
		}

		if (!isset($post_data['module-options'][$args['module_id']][$this->id_base])) {
			$post_data['module-options'][$args['module_id']][$this->id_base] = array('responsive-classes' => array());
		}

		if (!isset($post_data['module-options'][$args['module_id']][$this->id_base]['responsive-classes'])) {
			$post_data['module-options'][$args['module_id']][$this->id_base]['responsive-classes'] = array();
		}

		$responsive_classes = $post_data['module-options'][$args['module_id']][$this->id_base]['responsive-classes'];
		$available_classes = $this->available_classes();

		foreach ($args['class_data'] as $css_class => $state) {
			$css_class = sanitize_title_with_dashes(trim(strip_tags($css_class)));

			if ($state) {
				if (!in_array($css_class, $responsive_classes)) {
					$responsive_classes[] = $css_class;
				}
			}
			else {
				$found_keys = array_keys($responsive_classes, $css_class);
				if (!empty($found_keys)) {
					foreach ($found_keys as $key) {
						unset($responsive_classes[$key]);
					}
				}
			}
		}

		$responsive_classes = apply_filters('cfct-build-responsive-design-update-classes', $responsive_classes, $post_data['module-options'][$args['module_id']]);

		$responsive_classes = array_intersect($responsive_classes, array_keys($available_classes));

		$post_data['module-options'][$args['module_id']][$this->id_base]['responsive-classes'] = $responsive_classes;

		if (!$cfct_build->set_postmeta($args['post_id'], $post_data)) {
			throw new cfct_row_exception(__('Could not save postmeta for post on responsive class update','carrington-build').' (post_id: '.$args['post_id'].')');
		}
		$cfct_build->set_post_content($args['post_id']);

		$ret = new cfct_message(array(
			'success' => true,
			'html' => __('CSS classes updated.', 'carrington-build'),
			'message' => 'module_id '.$args['module_id'].' '.sprintf(__('Responsive Design CSS classes: %s', 'carrington-build'), implode(' ', $responsive_classes)),
			'extra' => array(
				'module_id' => $args['module_id'],
				'row_id' => $args['row_id'],
				'block_id' => $args['block_id'],
				'css_classes' => $responsive_classes
			)
		));
		return $ret;
	}

	public function html($data, $options_data) {
		$responsive_classes = isset($options_data['responsive-classes']) ? (array) $options_data['responsive-classes'] : array();
		$class_select = '';
		foreach ($this->available_classes() as $css_class => $details) {
			$name = $details['name'];

			$is_checked = in_array($css_class, $responsive_classes);

			$class_select .= '<li class="' . ($is_checked ? 'cfct-responsive-disabled' : '') . '">' .
				'<a class="' . (empty($details['icon']) ? 'cfct-checkbox-mode' : '') . '" href="#' . $css_class . '">' .
				'<input type="checkbox" name="' . esc_attr($css_class) . '"' .
				($is_checked ? ' checked="checked"' : '') . '/><span class="cfct-responsive-icon-container"> ';

			if (!empty($details['icon'])) {
				$icon_path = esc_attr(CFCT_BUILD_URL . $details['icon']);
				$class_select .= '<img class="cfct-il-icon cfct-responsive-icon" '.
					'src="' . $icon_path . '" ' .
					'alt="' . $name . '" />';
			}
			else {
				$class_select .= $name;
			}

			$class_select .= '</span></a></li>' . PHP_EOL;
		}

		$html = '<div class="cfct-option cfct-clearfix' . (empty($class_select) ? ' cfct-module-rendering-single' : '') . '">
					<a href="#'.$data['module_id'].'" class="cfct-module-toggle-render cfct-button cfct-button-dark option-button-left disable-enable-button">'.__((!isset($data['render']) || $data['render']) ? 'Disable Module' : 'Enable Module', 'carrington-build').'</a> <div class="option-note">Disabled modules are not output on the page.</div>
				</div>';

		if (!empty($class_select)) {
			$html .=
				'<div class="cfct-responsive-list">
					Hide for these devices:
					<ul class="cfct-responsive-classes cfct-clearfix">' .
						$class_select .
					'</ul>
				</div>';
		}

		return $html;
	}

	/**
	 * Add hidden devices CSS to classes array when there is at least one
	 * device that is disabled.
	 * @param string $module_state_classes
	 * @param type $data
	 * @param type $options_data
	 * @return string
	 */
	public function apply_hidden_state_classes($module_state_classes, $data, $options_data) {
		if (!empty($options_data) && !empty($options_data[$this->id_base])) {

			$options_data_module = (array) $options_data[$this->id_base];

			if (!empty($options_data_module) && !empty($options_data_module['responsive-classes'])) {

				$responsive_classes = (array) $options_data_module['responsive-classes'];

				$hidden_devices = false;

				foreach ($this->available_classes() as $css_class => $details) {
					$is_checked = in_array($css_class, $responsive_classes);
					if($is_checked) {
						$hidden_devices = true;
						break;
					}
				}

				if ($hidden_devices && !in_array('hidden-devices', $module_state_classes)) {
					$module_state_classes[] = 'hidden-devices';
				}
			}
		}

		return $module_state_classes;
	}

	public function apply_hidden_state_html($html) {
		return $html . '<span class="hidden-note">hidden on some devices</span>' . PHP_EOL;
	}

	public function admin_css() {
		return $this->load_view('view-admin-css.php');
	}

	public function admin_js() {
		return $this->load_view('view-admin-js.php');
	}
}

cfct_module_register_extra('cfct_module_option_responsive_design');

