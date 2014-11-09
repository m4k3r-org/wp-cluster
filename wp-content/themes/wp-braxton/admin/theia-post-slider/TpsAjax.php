<?php
/*
 * Copyright 2012, Theia Post Slider, Liviu Cristian Mirea Ghiban.
 */

class TpsAjax {
	public static function query_vars($vars) {
	    $vars[] = 'theiaPostSlider';
	    $vars[] = 'postId';
	    $vars[] = 'slides';
	    return $vars;
	}

	public static function parse_request($wp) {
	    if (!array_key_exists('theiaPostSlider', $wp->query_vars)) {
		    return;
	    }

		switch ($wp->query_vars['theiaPostSlider']) {
			case 'get-slides':
				self::getSlides($wp);
				break;
		}
	}

	private static function getSlides($wp) {
		if (
			!array_key_exists('postId', $wp->query_vars) ||
			!array_key_exists('slides', $wp->query_vars)
		) {
			return;
		}

		TpsMisc::$forceDisable = true;

		// Get post.
		global $post, $pages;
		$post = get_post($wp->query_vars['postId']);
		if ($post === null) {
			exit();
		}
		setup_postdata($post);
		query_posts('p=' . $wp->query_vars['postId']);

		// Get and process each slide.
		$requestedSlides = $wp->query_vars['slides'];
        $slides = array();
        foreach ($requestedSlides as $i) {
			// Extract header and footer shortcodes
			if ($i == 0) {
		        TpsShortCodes::extractShortCode($pages[0], TpsMisc::$beginHeaderShortCode, TpsMisc::$endHeaderShortCode);
			}

			if ($i == count($pages) - 1) {
				TpsShortCodes::extractShortCode($pages[count($pages) - 1], TpsMisc::$beginFooterShortCode, TpsMisc::$endFooterShortCode);
			}

            $slides[$i] = TpsMisc::getSubPage($i + 1, null);
        }

		$result = array(
			'postId' => $post->ID,
			'slides' => $slides
		);

		header('Content-Type: text/html; charset=utf-8');
		echo json_encode($result);

		exit();
	}
}