<?php
/*
 * Copyright 2012, Theia Post Slider, Liviu Cristian Mirea Ghiban.
 */

class TpsOptions {
	public static function addMetaBoxes() {
	    add_meta_box(
	        'tps_options', // id, used as the html id att
	        __( 'Theia Post Slider' ), // meta box title
	        'TpsOptions::addMetaBoxesCallback', // callback function, spits out the content
	        null, // post type or page. This adds to posts only
	        'side', // context, where on the screen
	        'low' // priority, where should this go in the context
	    );
	}

	public static function addMetaBoxesCallback($post) {
	    $options = TpsOptions::getPostOptions($post->ID);

		?>
		<p>
			<label>
				<input type="checkbox" name="tps_options[enabled]" value="true" <?=$options['enabled'] == true ? 'checked' : ''?>>
				Enable on this post.
			</label>
		</p>
		<?
	}

	public static function savePost($postId) {
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return false;
		if (!current_user_can('edit_page', $postId)) return false;
		if (empty($postId)) return false;

		// Create meta if it doesn't exist yet.
		$firstRun = add_post_meta($postId, 'tps_options', array(
			'enabled' => TpsOptions::get('default_activation_behavior', 'tps_advanced') == 0 ? false : true
		), true);

		if (!$firstRun) {
			$defaults = array(
				'enabled' => false
			);

			$options = array_key_exists('tps_options', $_REQUEST) ? $_REQUEST['tps_options'] : array();
			foreach ($options as $optionKey => $option) {
				if (!array_key_exists($optionKey, $defaults)) {
					unset($options[$optionKey]);
				}
			}
			$options = array_merge($defaults, $options);
			update_post_meta($postId, 'tps_options', $options);
		}
	}

	// Get all available transition effects.
	public static function getTransitionEffects() {
		$options = array(
			'none' => 'None',
			'simple' => 'Simple',
			'slide' => 'Slide',
			'fade' => 'Fade'
		);
		return $options;
	}

	// Get button horizontal positions.
	public static function getButtonHorizontalPositions() {
		$options = array(
			'left' => 'Left',
			'center' => 'Center',
			'right' => 'Right'
		);
		return $options;
	}

	// Get button vertical positions.
	public static function getButtonVerticalPositions() {
		$options = array(
			'top_and_bottom' => 'Top and bottom',
			'top' => 'Top',
			'bottom' => 'Bottom'
		);
		return $options;
	}

	// Get all available themes.
	public static function getThemes() {
		$themes = array();

		// Special files to ignore
		$ignore = array('admin.css');

		// Get themes corresponding to .css files.
		$dir = dirname(__FILE__) . '/css';
		if ($handle = opendir($dir)) {
		    while (false !== ($entry = readdir($handle))) {
			    if (in_array($entry, $ignore)) {
				    continue;
			    }

			    $file = $dir . '/' . $entry;
			    if (!is_file($file)) {
				    continue;
			    }

			    // Beautify name
			    $name = substr($entry, 0, -4); // Remove ".css"
			    $name = str_replace('--', ', ', $name);
			    $name = str_replace('-', ' ', $name);
				$name = ucwords($name);

			    // Add theme
	            $themes[$entry] = $name;
		    }
		    closedir($handle);
		}

		$themes['none'] = 'None';

		// Sort alphabetically
		asort($themes);

		return $themes;
	}

	public static function get($optionId, $optionGroups = array('tps_general', 'tps_nav', 'tps_advanced')) {
		if (!is_array($optionGroups)) {
			$optionGroups = array($optionGroups);
		}

		foreach ($optionGroups as $groupId) {
			$options = get_option($groupId);

			if (!is_array($options)) {
				continue;
			}

			if (array_key_exists($optionId, $options)) {
				return $options[$optionId];
			}
		}
		return null;
	}

	// Initialize options
	public static function initOptions() {
		$defaults = array(
			'tps_general' => array(
				'transition_effect' => 'slide',
				'transition_speed' => 400,
				'theme' => 'buttons-orange.css'
			),
			'tps_nav' => array(
				'navigation_text' => '%{currentSlide} of %{totalSlides}',
				'helper_text' => 'Use your ← → (arrow) keys to browse',
				'prev_text' => 'Prev',
				'next_text' => 'Next',
				'button_width' => 0,
				'prev_text_post' => 'Prev post',
				'next_text_post' => 'Next post',
				'button_width_post' => 0,
				'post_navigation' => false,
				'post_navigation_same_category' => false,
				'nav_horizontal_position' => 'right',
				'nav_vertical_position' => 'top_and_bottom',
				'enable_on_pages' => false
			),
			'tps_advanced' => array(
				'default_activation_behavior' => 1,
				'slide_loading_mechanism' => 'ajax',
				'refresh_ads' => false,
				'refresh_ads_every_n_slides' => 1
			)
		);

		// Transfer legacy options.
		$options = get_option('tps_nav');
		if (is_array($options) && array_key_exists('refresh_page_on_slide', $options) && $options['refresh_page_on_slide'] == true) {
			$defaults['tps_advanced']['slide_loading_mechanism'] = 'refresh';
		}

		foreach ($defaults as $groupId => $groupValues) {
			$options = get_option($groupId);
			$changed = false;

			// Add missing options
			foreach ($groupValues as $key => $value) {
				if (isset($options[$key]) == false) {
					$changed = true;
					$options[$key] = $value;
				}
			}

			// Remove surplus options
			foreach ($options as $key => $value) {
				if (isset($defaults[$groupId][$key]) == false) {
					$changed = true;
					unset($options[$key]);
				}
			}

			// Validate options
			if ($groupId == 'tps_general') {
				if (array_key_exists($options['transition_effect'], TpsOptions::getTransitionEffects()) == false) {
					$options['transition_effect'] = $groupValues['transition_effect'];
					$changed = true;
				}

				if ($options['transition_speed'] < 0) {
					$options['transition_speed'] = $groupValues['transition_speed'];
					$changed = true;
				}
			}

			if ($groupId == 'tps_nav') {
				if ($options['button_width'] < 0) {
					$options['button_width'] = $groupValues['button_width'];
					$changed = true;
				}
			}

			// Save options
			if ($changed) {
				update_option($groupId, $options);
			}
		}
	}

	// Get post options
	public static function getPostOptions($postId) {
		$defaults = array(
			'enabled' => TpsOptions::get('default_activation_behavior', 'tps_advanced') == 0 ? false : true
		);

		$options = get_post_meta($postId, 'tps_options', true);
		if (!is_array($options)) {
			$options = array();
		}

		$options = array_merge($defaults, $options);

		return $options;
	}
}