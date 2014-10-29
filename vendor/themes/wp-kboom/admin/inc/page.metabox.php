<?php
$prefix = 'snbpd_';

$pagedescription_box = array(
	'id' => 'pagebox',
	'title' => 'Page Description',
	'page' => 'page',
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(

		array(
			'name' => 'Page Description',
			'desc' => 'Optional, you can enter a page description here. It will appear in the title, with a different style "e.g. Portfolio /  Your description here".',
			'id' => $prefix . 'pagedesc',
			'type' => 'text',
			'std' => ''
		),
	),

);


add_action('admin_menu', 'pagedescription_add_box');

// Add meta box
function pagedescription_add_box() {
	global $pagedescription_box;

	add_meta_box($pagedescription_box['id'], $pagedescription_box['title'], 'pagedescription_show_box', $pagedescription_box['page'], $pagedescription_box['context'], $pagedescription_box['priority']);
}

// Callback function to show fields in meta box
function pagedescription_show_box() {
	global $pagedescription_box, $post;

	// Use nonce for verification
	echo '<input type="hidden" name="pagedescription_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';

	echo '<table class="form-table">';

	foreach ($pagedescription_box['fields'] as $field) {
		// get current post meta data
		$meta = get_post_meta($post->ID, $field['id'], true);

		echo '<tr>',
				'<th style="width:20%"><label for="', $field['id'], '">', $field['name'], '</label></th>',
				'<td>';
		switch ($field['type']) {
			case 'text':
				echo '<input type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" size="30" style="width:97%" />',
					'<br />', $field['desc'];
				break;
			case 'textarea':
				echo '<textarea class="theEditor" name="', $field['id'], '" id="', $field['id'], '" cols="60" rows="4" style="width:97%">', $meta ? $meta : $field['std'], '</textarea>',
					'<br />', $field['desc'];

				break;
			case 'select':
				echo '<select name="', $field['id'], '" id="', $field['id'], '">';
				foreach ($field['options'] as $option) {
					echo '<option', $meta == $option ? ' selected="selected"' : '', '>', $option, '</option>';
				}
				echo '</select>',
				'<br />', $field['desc'];
				break;
			case 'radio':
				foreach ($field['options'] as $option) {
					echo '<input type="radio" name="', $field['id'], '" value="', $option['value'], '"', $meta == $option['value'] ? ' checked="checked"' : '', ' />', $option['name'];
				}
				break;
            case "upload":
            echo '<div class="upload_button_div"> <span id="', $field['id'], '"><a href="#" id="set-post-thumbnail" onclick="jQuery(\'#add_image\').click();return true;" class="button-primary">Add Media</a></b></span></div>';
                break;
			case 'checkbox':
				echo '<input type="checkbox" name="', $field['id'], '" id="', $field['id'], '"', $meta ? ' checked="checked"' : '', ' />';
				break;
		}
		echo 	'<td>',
			'</tr>';
	}

	echo '</table>';
}

add_action('save_post', 'pagedescription_save_data');

// Save data from meta box
function pagedescription_save_data($post_id) {
	global $pagedescription_box;

	// verify nonce
	if (!wp_verify_nonce($_POST['pagedescription_meta_box_nonce'], basename(__FILE__))) {
		return $post_id;
	}

	// check autosave
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return $post_id;
	}

	// check permissions
	if ('page' == $_POST['post_type']) {
		if (!current_user_can('edit_page', $post_id)) {
			return $post_id;
		}
	} elseif (!current_user_can('edit_post', $post_id)) {
		return $post_id;
	}

	foreach ($pagedescription_box['fields'] as $field) {
		$old = get_post_meta($post_id, $field['id'], true);
		$new = $_POST[$field['id']];

		if ($new && $new != $old) {
			update_post_meta($post_id, $field['id'], $new);
		} elseif ('' == $new && $old) {
			delete_post_meta($post_id, $field['id'], $old);
		}
	}
}



/*
Upload image header for page 
*/


/*$pageheader_box = array(
	'id' => 'pageheader',
	'title' => 'Page Header Image',
	'page' => 'page',
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(

		array(
			'name' => 'Add Header Image',
			'desc' => 'The image should be 1020 x 200px for a proper display.',
			'id' => $prefix . 'phitembutton',
			'type' => 'upload',
			'std' => ''
		),
		
		array(
			'name' => '',
			'id' => $prefix . 'phitemlink',
			'type' => 'hidden',
			'std' => ''
		),

		array(
			'name' => 'Disable Image Header',
			'desc' => 'Disable Image Header for this page',
			'id' => $prefix . 'ph_disabled',
			'type' => 'checkbox',
			'std' => 'on'
		),
		
	),

);*/




/*add_action('admin_menu', 'pageheader_add_box');

// Add meta box
function pageheader_add_box() {
	global $pageheader_box;

	add_meta_box($pageheader_box['id'], $pageheader_box['title'], 'pageheader_show_box', $pageheader_box['page'], $pageheader_box['context'], $pageheader_box['priority']);
}

// Callback function to show fields in meta box
function pageheader_show_box() {
	global $pageheader_box, $post;

	// Use nonce for verification
	echo '<input type="hidden" name="pageheader_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';

	echo '<table class="form-table">';

	foreach ($pageheader_box['fields'] as $field) {
		// get current post meta data
		$meta = get_post_meta($post->ID, $field['id'], true);

		echo '<tr>',
				'<th style="width:20%"><label for="', $field['id'], '">', $field['name'], '</label></th>',
				'<td>';
		switch ($field['type']) {
			case 'hidden':
                echo '<img src="', $meta ? $meta : $field['std'], '" id="', $field['id'], '_img" style="width:600px"/>';
				echo '<input type="hidden" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" size="30" style="width:600px" />',
					'<br />', $field['desc'];
				break;
			case 'text':
				echo '<input type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" size="30" style="width:97%" />',
					'<br />', $field['desc'];
				break;
			case 'textarea':
				echo '<textarea class="theEditor" name="', $field['id'], '" id="', $field['id'], '" cols="60" rows="4" style="width:97%">', $meta ? $meta : $field['std'], '</textarea>',
					'<br />', $field['desc'];

				break;
			case 'select':
				echo '<select name="', $field['id'], '" id="', $field['id'], '">';
				foreach ($field['options'] as $option) {
					echo '<option', $meta == $option ? ' selected="selected"' : '', '>', $option, '</option>';
				}
				echo '</select>',
				'<br />', $field['desc'];
				break;
			case 'radio':
				foreach ($field['options'] as $option) {
					echo '<input type="radio" name="', $field['id'], '" value="', $option['value'], '"', $meta == $option['value'] ? ' checked="checked"' : '', ' />', $option['name'];
				}
				break;
            case "upload":
            echo '<div class="upload_button_div"> <span id="', $field['id'], '"><a href="#" id="set-post-thumbnail" onclick="jQuery(\'#add_image\').click();return true;" class="button-primary">Add Media</a></b></span> <a href="#" id="remove-post-thumbnail" onclick="jQuery(\'#add_image\').click();return true;" class="button-primary snbpd_remove_image">Remove</a>
                <p><small>'.$field['desc'].'<small></p></div>';
                break;
			case 'checkbox':
				echo '<input type="checkbox" name="', $field['id'], '" id="', $field['id'], '"', $meta ? ' checked="yes"' : '', ' />';
				break;
		}
		echo 	'<td>',
			'</tr>';
	}

	echo '</table>';
}

add_action('save_post', 'pageheader_save_data');*/

// Save data from meta box
function pageheader_save_data($post_id) {
	global $pageheader_box;

	// verify nonce
	if (!wp_verify_nonce($_POST['pageheader_meta_box_nonce'], basename(__FILE__))) {
		return $post_id;
	}

	// check autosave
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return $post_id;
	}

	// check permissions
	if ('page' == $_POST['post_type']) {
		if (!current_user_can('edit_page', $post_id)) {
			return $post_id;
		}
	} elseif (!current_user_can('edit_post', $post_id)) {
		return $post_id;
	}

	foreach ($pageheader_box['fields'] as $field) {
		$old = get_post_meta($post_id, $field['id'], true);
		$new = $_POST[$field['id']];

		if ($new && $new != $old) {
			update_post_meta($post_id, $field['id'], $new);
		} elseif ('' == $new && $old) {
			delete_post_meta($post_id, $field['id'], $old);
		}
	}
}

?>