<?php
class TpsShortCodes {
	// Add buttons to the post editor
	public static function add_button() {
		if (current_user_can('edit_posts') || current_user_can('edit_pages')) {
			add_filter('mce_external_plugins', 'TpsShortCodes::add_plugin');
			add_filter('mce_buttons', 'TpsShortCodes::register_button');
		}
	}

	public static function add_plugin($plugin_array) {
		$plugin_array['theiaPostSlider'] = TPS_PLUGINS_URL . 'js/tps-tinymce-customcodes.js';
		return $plugin_array;
	}

	public static function register_button($buttons) {
		array_push($buttons, 'separator', 'tps_header', 'tps_title', 'tps_footer');
		return $buttons;
	}

	// Extract a shortcode from a string.
	public static function extractShortCode(&$content, $beginShortCode, $endShortCode) {
		// Find the opening tag
		$begin = TpsMisc::mb_strpos($content, $beginShortCode);
		if ($begin === false) {
			return null;
		}

		// Find the closing tag
		$end = TpsMisc::mb_strpos($content, $endShortCode, $begin);
		if ($end === false) {
			return null;
		}

		// Cache some string lengths
		$lenBegin = TpsMisc::mb_strlen($beginShortCode);
		$lenEnd = TpsMisc::mb_strlen($endShortCode);

		// If the shortcodes are surrounded by header tags, then extract them too.
		$beginHeadingTag = $endHeadingTag = '';
		if (
			preg_match('(<h([1-6])>)', TpsMisc::mb_substr($content, $begin - 4, 4), $beginMatches) &&
			preg_match('(</h([1-6])>)', TpsMisc::mb_substr($content, $end + $lenEnd, 5), $endMatches) &&
			$beginMatches[1] === $endMatches[1]
		) {
			$beginHeadingTag = $beginMatches[0];
			$endHeadingTag = $endMatches[0];
		}

		$shortCode = $beginHeadingTag . trim(TpsMisc::mb_substr($content, $begin + $lenBegin, $end - $begin - $lenBegin)) . $endHeadingTag;
		$content = TpsMisc::mb_substr($content, 0, $begin - TpsMisc::mb_strlen($beginHeadingTag)) . TpsMisc::mb_substr($content, $end + $lenEnd + TpsMisc::mb_strlen($endHeadingTag));
		return $shortCode;
	}

	public static function tps_header($atts, $content = null) {
		return $content;
	}

	public static function tps_footer($atts, $content = null) {
		return $content;
	}

	public static function tps_title($atts, $content = null) {
		return $content;
	}
}