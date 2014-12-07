<?php
/**
 * This file contains a default subset of Advanced Options
 */

// Options Singleton-ish
class cfct_options {

	protected static $_extra_buttons = array(
		'module' => array(),
		'row' => array(),
	);
	protected static $_extras = array(
		'module' => array(),
		'row' => array(),
	);

	protected $_type = 'module'; // default to module
	protected $_id_key = 'module_id'; // default to module

	/**
	 * Creates Wrapping elements and Cog for options list
	 *
	 * @param mixed $data module/row data
	 * @param mixed $options_data
	 * @param mixed $options_list
	 * @access public
	 * @return string
	 */
	public function list_html($data, $options_data, $options_list) {

		$options = $this->options_html($data, $options_data, $options_list);
		$html = '';
		if (!empty($options)) {
			$html .= '
					<div id="cfct-options-layout-' . $data[$this->_id_key] . '" class="cfct-build-options cfct-build-options-'.$this->_type.' '.($this->_type == 'row' ? 'left-anchor':'').'" >
						<a href="#cfct-options-layout-inner-'.$data[$this->_id_key].'" class="cfct-options-layout-trigger-'.$this->_type.' '.($this->_type == 'row' ? 'left-anchor':'').' popover-trigger options-button">Advanced Options</a>
						<div id="cfct-options-layout-inner-' . $data[$this->_id_key] . '" class="cfct-options-layout-inner" style="display: none;">
							<div id="cfct-options-layout-wrapped-' . $data[$this->_id_key] . '" class="cfct-popup-anchored" >
								<div class="cfct-popup">
									<div class="cfct-popup-content">
										' . $options . '
									</div>
								</div>
							</div>
						</div>
					</div>';
		}
		return $html;
	}

	/**
	 * Produce options html
	 *
	 * @param mixed $data row/module data
	 * @param mixed $options_data
	 * @param mixed $options_list
	 * @access private
	 * @return string
	 */
	private function options_html($data, $options_data, $options_list) {
		$ret = '';
		if (count($this->extras($options_list))) {
			foreach ($this->extras($options_list) as $extra) {
				$option_data = (!empty($options_data[$extra->id_base]) ? $options_data[$extra->id_base] : array());
				$ret .= '<div class="cfct-option-wrapper">';
				$ret .= $extra->_html($data, $option_data);
				$ret .= '</div>';
			}
			foreach ($this->extra_buttons($options_list) as $extra) {
				$option_data = (!empty($options_data[$extra->id_base]) ? $options_data[$extra->id_base] : array());
				$ret .= '<div class="cfct-option-wrapper">';
				$ret .= $extra->_html($data, $option_data);
				$ret .= '</div>';
			}
		}
		if (trim($ret) != '') {
			$ret = '<div class="cfct-options-controls">' . $ret . '</div>';
		}
		return $ret;
	}

	public function update($new_data, $old_data) {
		$ret = array();
		if (count($this->extras())) {
			foreach ($this->extras() as $extra) {
				if (!empty($new_data[$extra->id_base])) {
					$old_data = (!empty($old_data[$extra->id_base]) ? $old_data[$extra->id_base] : null);
					$ret[$extra->id_base] = $extra->update($new_data[$extra->id_base], $old_data);
				}
			}
		}
		if (count($this->extra_buttons())) {
			foreach ($this->extra_buttons() as $extra) {
				if (!empty($new_data[$extra->id_base])) {
					$old_data = (!empty($old_data[$extra->id_base]) ? $old_data[$extra->id_base] : null);
					$ret[$extra->id_base] = $extra->update($new_data[$extra->id_base], $old_data);
				}
			}
		}
		return $ret;
	}

	/**
	 * Return any custom extra JS for the front end
	 *
	 * @return void
	 */
	public static function js($admin = false, $type) {
		$js = '';
		if (count(self::$_extras[$type])) {
			foreach (self::$_extras[$type] as $extra) {
				$method = ($admin ? 'admin_' : null).'js';
				$js .= PHP_EOL.PHP_EOL.$extra->$method();
			}
		}
		if (count(self::$_extra_buttons[$type])) {
			foreach (self::$_extra_buttons[$type] as $extra) {
				$method = ($admin ? 'admin_' : null).'js';
				$js .= PHP_EOL.PHP_EOL.$extra->$method();
			}
		}
		return $js;
	}

	/**
	 * Return any custom extra CSS for the front end
	 *
	 * @return string
	 */
	public static function css($admin = false, $type) {
		$css = '';
		if (count(self::$_extras[$type])) {
			foreach (self::$_extras[$type] as $extra) {
				$method = ($admin ? 'admin_' : null).'css';
				$css .= PHP_EOL.PHP_EOL.$extra->$method();
			}
		}
		if (count(self::$_extra_buttons[$type])) {
			foreach (self::$_extra_buttons[$type] as $extra) {
				$method = ($admin ? 'admin_' : null).'css';
				$css .= PHP_EOL.PHP_EOL.$extra->$method();
			}
		}
		return $css;
	}

	/**
	 * Produce filtered extras
	 *
	 * @param array $filter Array of whitelisted options
	 * @access public
	 * @return array
	 */
	public function extras($filter = false) {
		if ($filter) {
			$filter_assoc = array_fill_keys($filter, 1);
			return array_intersect_key(self::$_extras[$this->_type], $filter_assoc);
		}

		return self::$_extras[$this->_type];
	}

	/**
	 * Produce filtered extra buttons
	 *
	 * @param array $filter Array of whitelisted options
	 * @access public
	 * @return array
	 */
	public function extra_buttons($filter = false) {
		if ($filter) {
			$filter_assoc = array_fill_keys($filter, 1);
			return array_intersect_key(self::$_extra_buttons[$this->_type], $filter_assoc);
		}

		return self::$_extra_buttons[$this->_type];
	}

	/**
	 * Register an extra
	 *
	 * @param $id
	 * @param $classname
	 * @return bool
	 */
	public static function register($classname, $type) {
		if (!class_exists($classname)) {
			return false;
		}
		$_ex = new $classname;
		if ($_ex->is_button()) {
			self::$_extra_buttons[$type][$classname] = $_ex;
		}
		else {
			self::$_extras[$type][$classname] = $_ex;
		}
		unset($_ex);
		return true;
	}

	/**
	 * De-register an extra
	 *
	 * @param $id
	 * @param $classname
	 * @return bool
	 */
	public static function deregister($classname, $type) {
		if (isset(self::$_extras[$type][$classname]) && (self::$_extras[$type][$classname] instanceof $classname)) {
			unset(self::$_extras[$type][$classname]);
			return true;
		}
		return false;
	}
}

class cfct_module_options extends cfct_options {
	protected $_type = 'module';
	protected $_id_key = 'module_id';

	public static function css($admin = false) {
		return parent::css($admin, 'module');
	}

	public static function js($admin = false) {
		return parent::js($admin, 'module');
	}

	public static function register($classname) {
		return parent::register($classname, 'module');
	}

	public static function deregister($classname) {
		return parent::deregister($classname, 'module');
	}
}

class cfct_row_options extends cfct_options {
	protected $_type = 'row';
	protected $_id_key = 'guid';

	public static function css($admin = false) {
		return parent::css($admin, 'row');
	}

	public static function js($admin = false) {
		return parent::js($admin, 'row');
	}

	public static function register($classname) {
		return parent::register($classname, 'row');
	}

	public static function deregister($classname) {
		return parent::deregister($classname, 'row');
	}
}

// Standard Options class
abstract class cfct_option {
	public $name;
	public $id_base;
	public $is_header_row_button;

	public function __construct($name, $id_base, $button = false) {
		$this->name = $name;
		$this->id_base = $id_base;
		$this->is_header_row_button = $button;
	}

	public function is_button() {
		return $this->is_header_row_button && method_exists($this, 'button');
	}

	public function _html($data, $options_data) {
		$html = $this->html($data, $options_data);
		if (trim($html) == '') {
			return '';
		}
		$ret = '
			<div id="cfct-module-options-layout-'.$this->id_base.'">
				'.$html.'
			</div>';
		return $ret;
	}
	abstract public function html($data, $options_data);

	public function update($new_data, $old_data) {
		return $new_data;
	}

	public function button() {
		if ($this->is_button()) {
			return null;
		}
		return false;
	}

	public function get_field_name($field_name) {
		return $this->id_base.'-'.$field_name;
	}

	public function get_field_id($field_name) {
		return $this->id_base.'-'.$field_name;
	}

	public function js() {
		return null;
	}

	public function css() {
		return null;
	}

	public function admin_js() {
		return null;
	}

	public function admin_css() {
		return null;
	}

	/**
	 * Load the view
	 *
	 * $params is an associative array that will be extracted for the view
	 * All keys in the array will become available variables in the view in
	 * addition to the $data variable
	 *
	 * @param string $view
	 * @param string $params - additional params to be made available to the template
	 * @return void
	 */
	public function load_view($view, $params = array(), $data = null) {
		global $cfct_build;

		$view = apply_filters('cfct-options-'.$this->id_base.'-view', $view, $data);

		// find file
		$view_path = '';
		if (is_file($view)) {
			// full path to view given
			$view_path = $view;
		}
		else {
			// look for view in module folder
			global $cfct_build;
			$path = dirname($cfct_build->get_module_options_path($this->id_base));
			if (is_file(trailingslashit($path).$view)) {
				$view_path = trailingslashit($path).$view;
			}
		}
		// render
		if (!empty($view_path)) {
			extract($params);
			ob_start();

			include($view_path);

			$buffer = ob_get_clean();
			return $buffer;
		}
		else {
			return null;
		}
	}
}

abstract class cfct_module_option extends cfct_option {

	/*
	 * Backwards compatible filter
	 */
	public function load_view($view, $params = array(), $data = null) {
		$view = apply_filters('cfct-module-options-'.$this->id_base.'-view', $view, $data);
		return parent::load_view($view, $params, $data);
	}

	public function get_field_name($field_name) {
		return 'cfct-module-options['.$this->id_base.']['.$field_name.']';
	}
}

abstract class cfct_row_option extends cfct_option {

	public function get_field_name($field_name) {
		return 'cfct-row-options['.$this->id_base.']['.$field_name.']';
	}
}
