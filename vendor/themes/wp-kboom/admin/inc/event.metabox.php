<?php
$event_prefix = 'snbp_';

$event_meta_box = array(
	'id' => 'eventbox',
	'title' => 'Event Details',
	'page' => 'event',
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(

        array(
            'name' => 'Add Event Poster',
            'desc' => 'The image should be 900px wide for a proper display. This image will be also resized and used as thumbnail on event list.',
            'id' => $event_prefix . 'pitembutton',
            'type' => 'upload',
            'std' => ''
        ),

        array(
            'name' => '',
            'id' => $event_prefix . 'pitemlink',
            'type' => 'hidden',
            'std' => ''
        ),

		array(
			'name' => 'Event Venue:',
			'desc' => 'E.G. Club Galaxy',
			'id' => $event_prefix . 'event_venue',
			'type' => 'text',
			'std' => ''
		),

        array(
            'name' => 'Event Location:',
            'desc' => 'E.G. Miami, USA',
            'id' => $event_prefix . 'event_location',
            'type' => 'text',
            'std' => ''
        ),

        array(
            'name' => 'Event Date:',
            'desc' => 'E.G. 23 &lt;br /&gt; &lt;strong&gt;Jan &lt;/strong&gt; &lt;br /&gt;2013',
            'id' => $event_prefix . 'event_date',
            'type' => 'text',
            'std' => ''
        ),

        array(
            'name' => 'Event Time:',
            'desc' => 'E.G. 07:00 pm - 06:00 am',
            'id' => $event_prefix . 'event_time',
            'type' => 'text',
            'std' => ''
        ),

        array(
            'name' => 'Event Map:',
            'desc' => 'Enter your google embeding map code here',
            'id' => $event_prefix . 'event_map',
            'type' => 'textarea',
            'std' => ''
        ),

        array(
            'name' => 'Ticket Url:',
            'desc' => 'E.G. http://ticket.com ',
            'id' => $event_prefix . 'event_ticket',
            'type' => 'text',
            'std' => ''
        ),

        array(
            'name' => 'Check if show is sold out:',
            'desc' => '',
            'id' => $event_prefix . 'ticket_sold_out',
            'type' => 'checkbox',
            'std' => ''
        ),

        array(
            'name' => 'Check if show is canceled:',
            'desc' => '',
            'id' => $event_prefix . 'ticket_canceled',
            'type' => 'checkbox',
            'std' => ''
        ),

        array(
            'name' => 'Check if show is free:',
            'desc' => '',
            'id' => $event_prefix . 'ticket_free',
            'type' => 'checkbox',
            'std' => ''
        ),

	),

);


add_action('admin_menu', 'event_add_box');

// Add meta box
function event_add_box() {
	global $event_meta_box;

	add_meta_box($event_meta_box['id'], $event_meta_box['title'], 'event_show_box', $event_meta_box['page'], $event_meta_box['context'], $event_meta_box['priority']);
}

// Callback function to show fields in meta box
function event_show_box() {
	global $event_meta_box, $post;

	// Use nonce for verification
	echo '<input type="hidden" name="event_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';

	echo '<table class="form-table">';

	foreach ($event_meta_box['fields'] as $field) {
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

add_action('save_post', 'event_save_data');

// Save data from meta box
function event_save_data($post_id) {
	global $event_meta_box;

	// verify nonce
	if (!wp_verify_nonce($_POST['event_meta_box_nonce'], basename(__FILE__))) {
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

	foreach ($event_meta_box['fields'] as $field) {
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