<?php
$prefix = 'snbp_';

$meta_box = array(
	'id' => 'audiobox',
	'title' => 'Audio Item Image Details',
	'page' => 'audio',
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(

		array(
			'name' => 'Add Album Cover Image',
			'desc' => 'Here you should upload your Album Cover',
			'id' => $prefix. 'pitembutton',
			'type' => 'upload',
			'std' => ''
		),

		array(
			'name' => '',
			'id' => $prefix. 'pitemlink',
			'type' => 'hidden',
			'std' => ''
		),

        array(
            'name' => 'Buy Button Name 1:',
            'desc' => 'Enter the button name 1 here.',
            'id' => $prefix. 'button1',
            'type' => 'text',
            'std' => ''
        ),

        array(
            'name' => 'Buy Button Url 1:',
            'desc' => 'Enter the button url 1 here.',
            'id' => $prefix. 'button1_url',
            'type' => 'text',
            'std' => ''
        ),

        array(
            'name' => 'Buy Button Name 2:',
            'desc' => 'Enter the button name 2 here.',
            'id' => $prefix. 'button2',
            'type' => 'text',
            'std' => ''
        ),

        array(
            'name' => 'Buy Button Url 2:',
            'desc' => 'Enter the button url 2 here.',
            'id' => $prefix. 'button2_url',
            'type' => 'text',
            'std' => ''
        ),

        array(
            'name' => 'Buy Button Name 3:',
            'desc' => 'Enter the button name 3 here.',
            'id' => $prefix. 'button3',
            'type' => 'text',
            'std' => ''
        ),

        array(
            'name' => 'Buy Button Url 3:',
            'desc' => 'Enter the button url 3 here.',
            'id' => $prefix. 'button3_url',
            'type' => 'text',
            'std' => ''
        ),

        array(
            'name' => 'Buy Button Name 4:',
            'desc' => 'Enter the button name 4 here.',
            'id' => $prefix. 'button4',
            'type' => 'text',
            'std' => ''
        ),

        array(
            'name' => 'Buy Button Url 4:',
            'desc' => 'Enter the button url 4 here.',
            'id' => $prefix. 'button4_url',
            'type' => 'text',
            'std' => ''
        ),

        array(
            'name' => 'Buy Button Name 5:',
            'desc' => 'Enter the button name 5 here.',
            'id' => $prefix. 'button5',
            'type' => 'text',
            'std' => ''
        ),

        array(
            'name' => 'Buy Button Url 5:',
            'desc' => 'Enter the button url 5 here.',
            'id' => $prefix. 'button5_url',
            'type' => 'text',
            'std' => ''
        ),

        array(
            'name' => 'Release Date:',
            'desc' => 'Enter the date here. ',
            'id' => $prefix. 'release_date',
            'type' => 'text',
            'std' => ''
        ),

        array(
            'name' => 'Genre of Music:',
            'desc' => 'Enter the genre of music. ',
            'id' => $prefix. 'genre',
            'type' => 'text',
            'std' => ''
        ),

        array(
            'name' => 'Stream Embedding Code:',
            'desc' => 'Enter the stream embedding code right here. ',
            'id' => $prefix. 'soundcloud',
            'type' => 'textarea',
            'std' => ''
        ),

    ),

);

add_action('admin_menu', 'audio_add_box');

// Add meta box
function audio_add_box() {
	global $meta_box;

	add_meta_box($meta_box['id'], $meta_box['title'], 'audio_show_box', $meta_box['page'], $meta_box['context'], $meta_box['priority']);
}

// Callback function to show fields in meta box
function audio_show_box() {
	global $meta_box, $post;

	// Use nonce for verification
	echo '<input type="hidden" name="audio_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';

	echo '<table class="form-table">';

	foreach ($meta_box['fields'] as $field) {
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

add_action('save_post', 'audio_save_data');

// Save data from meta box
function audio_save_data($post_id) {
	global $meta_box;

	// verify nonce
	if (!wp_verify_nonce($_POST['audio_meta_box_nonce'], basename(__FILE__))) {
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

	foreach ($meta_box['fields'] as $field) {
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