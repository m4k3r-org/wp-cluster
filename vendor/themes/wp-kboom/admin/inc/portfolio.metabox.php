<?php
$portfolio_prefix = 'snbp_';

$portfolio_meta_box = array(
	'id' => 'portfoliobox',
	'title' => 'Portfolio Item Image Details',
	'page' => 'portfolio',
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(

		array(
			'name' => 'Add Portfolio Item Image',
			'desc' => 'The image should be 900px wide for a proper display. This image will be also resized and used as thumbnail on portfolio list.',
			'id' => $portfolio_prefix . 'pitembutton',
			'type' => 'upload',
			'std' => ''
		),
		
		array(
			'name' => '',
			'id' => $portfolio_prefix . 'pitemlink',
			'type' => 'hidden',
			'std' => ''
		)

	),

);


add_action('admin_menu', 'portfolio_add_box');

// Add meta box
function portfolio_add_box() {
	global $portfolio_meta_box;

	add_meta_box($portfolio_meta_box['id'], $portfolio_meta_box['title'], 'portfolio_show_box', $portfolio_meta_box['page'], $portfolio_meta_box['context'], $portfolio_meta_box['priority']);
}

// Callback function to show fields in meta box
function portfolio_show_box() {
	global $portfolio_meta_box, $post;

	// Use nonce for verification
	echo '<input type="hidden" name="portfolio_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';

	echo '<table class="form-table">';

	foreach ($portfolio_meta_box['fields'] as $field) {
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
            echo '<div class="upload_button_div"> <span id="', $field['id'], '"><a href="#" id="set-post-thumbnail" onclick="jQuery(\'#add_image\').click();return true;" class="button-primary">Add Media</a></b></span><br /><small>'.$field['desc'].'<small></div>';
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

add_action('save_post', 'portfolio_save_data');

// Save data from meta box
function portfolio_save_data($post_id) {
	global $portfolio_meta_box;

	// verify nonce
	if (!wp_verify_nonce($_POST['portfolio_meta_box_nonce'], basename(__FILE__))) {
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

	foreach ($portfolio_meta_box['fields'] as $field) {
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