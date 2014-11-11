<?php

/**
 * Disable Row
 *
 * Allows rows to be prevented from rendering to the page
 *
 * @package Carrington Build
 */
class cfct_row_option_disable extends cfct_row_option {

	public function __construct() {
		global $cfct_build;
		parent::__construct(__('Enable/Disable Rows', 'carrington-build'), 'row-disable');
		$cfct_build->register_ajax_handler('row_option_disable_update', array($this, 'ajax_disable'));

		// Add this option to all rows
		add_filter(
			'cfct-build-row-options',
			create_function('$options',
				'return array_merge($options, array("cfct_row_option_disable"));'
			)
		);
	}

	public function html($data, $options_data) {
		return $this->load_view('view-admin-html.php', array(
			"enabled" => (bool)(!isset($data['render']) || $data['render']),
		));
	}

	public function admin_js() {
		return $this->load_view('view-admin-js.php');
	}

	public function ajax_disable($args) {
		global $cfct_build;
		$post_data = $cfct_build->get_postmeta($args['post_id']);

		$post_data['template']['rows'][$args['row_id']]['render'] = $args["enabled"];

		if (!$cfct_build->set_postmeta($args['post_id'], $post_data)) {
			throw new cfct_row_exception(__('Could not save postmeta for row rendering for','carrington-build').' (row_id: '.$args['_id'].')');
		}
		$cfct_build->set_post_content($args['post_id']);

		$ret = new cfct_message(array(
			'success' => true,
			'html' => __('Row Rendering Updated.', 'carrington-build'),
			'extra' => array(
				'row_id' => $args['row_id'],
				'enabled' => $args['enabled'],
			)
		));
		return $ret;
	}

	public function update($new_data, $old_data) {
		$ret = array();

		$classes = explode(' ', $new_data['custom-css']);
		if (is_array($classes)) {
			foreach($classes as $class) {
				$ret['custom-css'][] = sanitize_title_with_dashes(trim(strip_tags($class)));
			}
		}

		return $ret;
	}
}

cfct_row_register_extra('cfct_row_option_disable');

