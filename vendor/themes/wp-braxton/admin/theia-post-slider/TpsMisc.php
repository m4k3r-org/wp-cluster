<?php
/*
 * Copyright 2012, Theia Post Slider, Liviu Cristian Mirea Ghiban.
 */

class TpsMisc {
    public static
        $beginComment = '<!-- BEGIN THEIA POST SLIDER -->',
        $endComment = '<!-- END THEIA POST SLIDER -->',
        $beginHeaderShortCode = '[tps_header]',
        $endHeaderShortCode = '[/tps_header]',
        $beginTitleShortCode = '[tps_title]',
        $endTitleShortCode = '[/tps_title]',
        $beginFooterShortCode = '[tps_footer]',
        $endFooterShortCode = '[/tps_footer]',
        // Set this to true to prevent the_content() from calling itself in an infinite loop.
        $theContentIsCalled = false,
        // The posts for which we have enabled the slider (i.e. the script has been appended).
        $postsWithSlider = array(),
	    // The slider won't append itself to posts.
		$forceDisable = false;

    /*
     * We want to enable sliders only for the main post on a post page. This usually means that is_singular() returns true
     * (i.e. the query is for only one post). But, some themes have single queries used only to display the excerpts.
     * So, here we'll prepare the post for sliders, but these sliders will be activated only if the_content() is also
     * called.
     */
    public static function the_post($post) {
	    if (self::$forceDisable) {
		    return;
	    }

        global $post, $page, $pages, $multipage;

	    // If a page does not exist, display the last page.
	    if ($page > count($pages)) {
		    $page = count($pages);
	    }

        // Get previous and next posts.
        $prevPost = self::getPrevNextPost(true);
        $nextPost = self::getPrevNextPost(false);

        /*
         * Prepare the sliders if
         * a) This is a single post with multiple pages.
         * - OR -
         * b) Previous/next post navigation is enabled and we do have a previous or a next post.
         */
        if (!(self::isCompatiblePost() && ($multipage || $prevPost || $nextPost))) {
            return;
        }

        // Save some variables that we'll also use in the_content()
        $post->theiaPostSlider = array(
            'srcId' => 'tps_src_' . $post->ID,
            'destId' => 'tps_dest_' . $post->ID,
            'navIdUpper' => 'tps_nav_upper_' . $post->ID,
            'navIdLower' => 'tps_nav_lower_' . $post->ID,
            'prevPostId' => $prevPost,
            'nextPostId' => $nextPost,
            'prevPostUrl' => $prevPost ? get_permalink($prevPost) : null,
            'nextPostUrl' => $nextPost ? get_permalink($nextPost) : null
        );

	    // Extract title, if present.
	    $title = TpsShortCodes::extractShortCode($pages[$page - 1], TpsMisc::$beginTitleShortCode, TpsMisc::$endTitleShortCode);

        // Add sliders to the page.
        $content = '';

	    // Header
	    $content .= '<div class="theiaPostSlider_header _header">' . TpsShortCodes::extractShortCode($pages[0], TpsMisc::$beginHeaderShortCode, TpsMisc::$endHeaderShortCode) . '</div>';

        // Top slider
        if (in_array(TpsOptions::get('nav_vertical_position'), array('top_and_bottom', 'top'))) {
            $content .= TpsMisc::getNavigationBar(array(
                'currentSlide' => $page,
                'totalSlides' => count($pages),
                'prevPostUrl' => $post->theiaPostSlider['prevPostUrl'],
                'nextPostUrl' => $post->theiaPostSlider['nextPostUrl'],
                'id' => $post->theiaPostSlider['navIdUpper'],
                'class' => '_upper',
	            'title' => $title
            ));
        }

	    // Empty destination slide
        $content .= '<div id="' . $post->theiaPostSlider['destId'] . '" class="theiaPostSlider_slides"></div>';

	    // Source slides
        $content .= '<div id="' . $post->theiaPostSlider['srcId'] . '" class="theiaPostSlider_slides"><div>';
        $content .= "\n\n" . trim($pages[$page - 1]) . "\n\n";
        $content .= '</div></div>';

        // Bottom slider
        if (in_array(TpsOptions::get('nav_vertical_position'), array('top_and_bottom', 'bottom'))) {
            $content .= TpsMisc::getNavigationBar(array(
                'currentSlide' => $page,
                'totalSlides' => count($pages),
                'prevPostUrl' => $post->theiaPostSlider['prevPostUrl'],
                'nextPostUrl' => $post->theiaPostSlider['nextPostUrl'],
                'id' => $post->theiaPostSlider['navIdLower'],
                'class' => '_lower',
	            'title' => $title
            ));
        }

	    // Footer
	    $content .= '<div class="theiaPostSlider_footer _footer">' . TpsShortCodes::extractShortCode($pages[count($pages) - 1], TpsMisc::$beginFooterShortCode, TpsMisc::$endFooterShortCode) . '</div>';

        // Save the page.
        $pages[$page - 1] = $content;

        // Set this to false so that the theme doesn't display pagination buttons. Kind of a hack.
        $multipage = false;
    }

    /*
     * Append the JavaScript code only if the_content is called (i.e. the whole post is being displayed, not just the
     * excerpt).
     */
    public static function the_content($content) {
	    if (self::$forceDisable) {
		    return $content;
	    }

        global $post, $page, $pages, $multipage;

        if (!isset($post) || !property_exists($post, 'theiaPostSlider')) {
            return $content;
        }

        // Prevent this function from calling itself.
        if (self::$theContentIsCalled) {
            return $content;
        }
        self::$theContentIsCalled = true;

        $currentPage = min(max($page, 1), count($pages));

        $slides = array();

	    if (TpsOptions::get('slide_loading_mechanism', 'tps_advanced') == 'all') {
            // Get all slides except the current one, which will be echoed as actual HTML.
	        for ($i = 1; $i <= count($pages); $i++) {
		        if ($i == $currentPage) {
			        continue;
		        }

	            $slides[$i - 1] = TpsMisc::getSubPage($i, $currentPage);
	        }
	    }

        // Append the slider initialization script to the "theiaPostSlider.js" script.
        if (
            TpsOptions::get('slide_loading_mechanism', 'tps_advanced') != 'refresh' &&
            in_array($post->ID, self::$postsWithSlider) == false
        ) {
	        $nav = array();
            if (in_array(TpsOptions::get('nav_vertical_position'), array('top_and_bottom', 'top'))) {
                $nav[] = '#' . $post->theiaPostSlider['navIdUpper'];
            }
            if (in_array(TpsOptions::get('nav_vertical_position'), array('top_and_bottom', 'bottom'))) {
                $nav[] = '#' . $post->theiaPostSlider['navIdLower'];
	        }

			$sliderOptions = array(
                'src' => '#' . $post->theiaPostSlider['srcId'] . ' > div',
                'dest' => '#' . $post->theiaPostSlider['destId'],
                'nav' => $nav,
                'navText' => TpsOptions::get('navigation_text'),
                'helperText' => TpsOptions::get('helper_text'),
                'defaultSlide' => $currentPage - 1,
                'transitionEffect' => TpsOptions::get('transition_effect'),
                'transitionSpeed' => TpsOptions::get('transition_speed'),
                'keyboardShortcuts' => self::isCompatiblePost() ? 'true' : 'false',
				'numberOfSlides' => count($pages),
                'slides' => $slides,
                'prevPost' => $post->theiaPostSlider['prevPostUrl'],
                'nextPost' => $post->theiaPostSlider['nextPostUrl'],
                'prevText' => TpsOptions::get('prev_text'),
                'nextText' => TpsOptions::get('next_text'),
                'buttonWidth' => TpsOptions::get('button_width'),
                'prevText_post' => TpsOptions::get('prev_text_post'),
                'nextText_post' => TpsOptions::get('next_text_post'),
                'buttonWidth_post' => TpsOptions::get('button_width_post'),
                'postUrl' => get_permalink($post->ID),
                'postId' => $post->ID,
				'refreshAds' => TpsOptions::get('refresh_ads', 'tps_advanced'),
				'refreshAdsEveryNSlides' => TpsOptions::get('refresh_ads_every_n_slides', 'tps_advanced'),
				'siteUrl' => get_site_url()
			);

	        $vars = "
                var tpsInstance;
                var tpsOptions = " . json_encode($sliderOptions) . ";
            ";

	        // If there are multiple sliders on the page (i.e. the theme has compatibility issues), the plugin options get overwritten by each other.
	        $numberOfSliders = count(self::$postsWithSlider);

            $script = "
                " . ($numberOfSliders == 0 ? $vars : '')  . "
                (function($) {
	                $(document).ready(function() {
                        " . ($numberOfSliders != 0 ? $vars : '')  . "
	                    tpsInstance = new tps.createSlideshow(tpsOptions);
	                });
	            }(jQuery));
            ";

            global $wp_scripts;
            $data = $wp_scripts->get_data('theiaPostSlider.js', 'data');
            if ($data) {
                $script = "$data\n$script";
            }
            $wp_scripts->add_data('theiaPostSlider.js', 'data', $script);
            self::$postsWithSlider[] = $post->ID;
        }

        // Return the unchanged content.
        self::$theContentIsCalled = false;
        return $content;
    }

    // Is this post a "post" or a "page" (i.e. should we display the slider)?
    public static function isCompatiblePost() {
	    $value =
		    TpsOptions::get('enable_on_pages') ?
			(is_single() || is_page()) :
            is_single();
	    if ($value == false) {
		    return false;
	    }

	    global $post;
	    if ($post) {
		    $options = TpsOptions::getPostOptions($post->ID);
		    if ($options['enabled'] == false) {
		        return false;
		    }
	    }

        return true;
    }

    // Get HTML for a navigation bar.
    public static function getNavigationBar($options) {
        $defaults = array(
            'currentSlide' => null,
            'totalSlides' => null,
            'prevPostId' => null,
            'nextPostId' => null,
            'prevPostUrl' => null,
            'nextPostUrl' => null,
            'id' => null,
            'class' => null,
            'style' => null,
	        'title' => null
        );
        $options = array_merge($defaults, $options);

        // Get text
	    if ($options['totalSlides'] == 1) {
		    $text = '';
	    }
	    else {
	        $text = TpsOptions::get('navigation_text');
	        $text = str_replace('%{currentSlide}', $options['currentSlide'], $text);
	        $text = str_replace('%{totalSlides}', $options['totalSlides'], $text);
	    }

        // Get button URLs
	    $prev = TpsMisc::getNavigationBarButton($options, false);
	    $next = TpsMisc::getNavigationBarButton($options, true);

	    // Title
	    if (!$options['title']) {
	        $options['title'] = '<span class="_helper">' . TpsOptions::get('helper_text') . '</span>';
	    }

        // Final HTML
        $class = array('theiaPostSlider_nav');
        $class[] = '_' . TpsOptions::get('nav_horizontal_position');
        if ($options['class'] != null) {
            $class[] = $options['class'];
        }

        $html =
            '<div' . ($options['id'] !== null ? ' id="' . $options['id'] . '"' : '') . ($options['style'] !== null ? ' style="' . $options['style'] . '"' : '') . ' class="' . implode($class, ' ') . '">' .
	        '<div class="_buttons">' . $prev . '<span class="_text">' . $text . '</span>' . $next . '</div>' .
	        '<div class="_title">' . $options['title'] . '</div>' .
            '</div>';

        return $html;
    }

	/*
	 * Get a button for a navigation bar.
	 * @param direction boolean False = prev; True = next;
	 */
	public static function getNavigationBarButton($options, $direction) {
		$directionName = $direction ? 'next' : 'prev';
		$url = self::getPostPageUrl($options['currentSlide'] + ($direction ? 1 : -1));
		// If there isn't another page but there is another post.
		if (!$url && $options[$directionName . 'PostUrl']) {
            $url = $options[$directionName . 'PostUrl'];
	        $text = TpsOptions::get($directionName . '_text_post');
		    $width = TpsOptions::get('button_width_post');
		}
		else {
            $text = TpsOptions::get($directionName . '_text');
	        $width = TpsOptions::get('button_width');
        }

        $style = $width == 0 ? '' : 'style="width: ' . $width . 'px"';
        $htmlPart1 = '<span class="_1"></span><span class="_2" ' . $style . '>';
        $htmlPart2 = '</span><span class="_3"></span>';

        // HTML
        $html = $htmlPart1 . $text . $htmlPart2;
        if ($url) {
            $button = '<a href="' . $url . '" class="_' . $directionName . '">' . $html . '</a>';
        }
        else {
            $button = '<span class="_' . $directionName . ' _disabled">' . $html . '</span>';
        }

		return $button;
	}

    // Get the previous or next post.
    public static function getPrevNextPost($previous) {
        if (!TpsOptions::get('post_navigation')) {
	        return null;
        }
        $post = get_adjacent_post(TpsOptions::get('post_navigation_same_category'), '', $previous);
        if (!$post) {
            return null;
        }
        return $post->ID;
    }

	public static function getSubPage($pageNumber, $currentPageNumber = null) {
		global $page;

		// Set new page number
        $page = $pageNumber;
        $slide = array();
        $slide['title'] = self::getPageTitle();
        $slide['permalink'] = self::getPostPageUrl($page);

		// Get content
		if ($pageNumber != $currentPageNumber) {
	        $slideContent = get_the_content();

	        // Save the shortcode title, if present.
	        $slide['shortCodeTitle'] = TpsShortCodes::extractShortCode($slideContent, TpsMisc::$beginTitleShortCode, TpsMisc::$endTitleShortCode);

	        // Apply filters.
	        $slideContent = TpsMisc::$beginComment . $slideContent . TpsMisc::$endComment;
	        $slideContent = apply_filters('the_content', $slideContent);
	        $slideContent = str_replace(']]>', ']]&gt;', $slideContent);

	        /*
	         * Leave only the actual text. Aditional headers or footers will be discarded. Plugins like "video quicktags"
	         * will be left intact, while plugins like "related posts thumbnails" and "better author bio" will be discarded.
	         */
	        $begin = TpsMisc::mb_strpos($slideContent, TpsMisc::$beginComment);
	        $end = TpsMisc::mb_strpos($slideContent, TpsMisc::$endComment);

	        if ($begin !== false && $end !== false) {
		        $replace = false;

	            // Preserve beginning <p> tag.
	            if (TpsMisc::mb_substr($slideContent, $begin - 3, 3) == '<p>') {
	                if (TpsMisc::mb_substr($slideContent, $begin + TpsMisc::mb_strlen(TpsMisc::$beginComment), 4) == '</p>') {
	                    $begin += TpsMisc::mb_strlen(TpsMisc::$beginComment) + 4;
	                }
	                else {
	                    $begin -= 3;
		                $replace = true;
	                }
	            }
	            else {
	                $begin += TpsMisc::mb_strlen(TpsMisc::$beginComment);
	            }

	            // Preserve ending <p> tag.
	            if (TpsMisc::mb_substr($slideContent, $end + TpsMisc::mb_strlen(TpsMisc::$endComment), 4) == '</p>') {
	                if (TpsMisc::mb_substr($slideContent, $end - 3, 3) == '<p>') {
	                    $end -= 3;
	                }
	                else {
	                    $end += TpsMisc::mb_strlen(TpsMisc::$endComment) + 4;
		                $replace = true;
	                }
	            }

	            // Cut!
	            $slideContent = TpsMisc::mb_substr($slideContent, $begin, $end - $begin);

		        // Remove substrings, if needed.
		        if ($replace) {
			        $slideContent = str_replace(array(TpsMisc::$beginComment, TpsMisc::$endComment), '', $slideContent);
		        }
	        }

	        // Trim left and right whitespaces.
	        $slideContent = trim($slideContent);

	        /*
	         * Bug fix for WordPress. Sometimes it adds an invalid "</p>" closing tag at the beginning and/or an
	         * opening "<p>" tag at the end.
	         */
	        if (TpsMisc::mb_substr($slideContent, 0, 4) == '</p>') {
	            $slideContent = TpsMisc::mb_substr($slideContent, 4);
	        }
	        if (TpsMisc::mb_substr($slideContent, -3) == '<p>') {
	            $slideContent = TpsMisc::mb_substr($slideContent, 0, -3);
	        }
			$slide['content'] = $slideContent;
		}

		// Set back page number.
        $page = $currentPageNumber;

		return $slide;
	}

    // Add the "next page" button to the post editor.
    public static function wysiwyg_editor($mce_buttons) {
        $pos = array_search('wp_more', $mce_buttons, true);
        if ($pos !== false) {
            $tmp_buttons = array_slice($mce_buttons, 0, $pos + 1);
            $tmp_buttons[] = 'wp_page';
            $mce_buttons = array_merge($tmp_buttons, array_slice($mce_buttons, $pos + 1));
        }
        return $mce_buttons;
    }

    // Get the URL of a post's page.
    public static function getPostPageUrl($i) {
        global $post, $wp_rewrite, $pages;
        if ($i < 1 || $i > count($pages)) {
            return null;
        }
        if ( 1 == $i ) {
            $url = get_permalink();
        } else {
            if ( '' == get_option('permalink_structure') || in_array($post->post_status, array('draft', 'pending')) )
                $url = add_query_arg( 'page', $i, get_permalink() );
            elseif ( 'page' == get_option('show_on_front') && get_option('page_on_front') == $post->ID )
                $url = trailingslashit(get_permalink()) . user_trailingslashit("$wp_rewrite->pagination_base/" . $i, 'single_paged');
            else
                $url = trailingslashit(get_permalink()) . user_trailingslashit($i, 'single_paged');
        }
        return $url;
    }

    /**
     * Tries to get the correct title of the page. Very hackish, but there's no other way.
     * @return string
     */
    public static function getPageTitle() {
        // Set the current page of the WP query since it's used by SEO plugins.
        global $wp_query, $page;
        $oldPage = $wp_query->get('page');
        if ($page > 1) {
            $wp_query->set('page', $page);
        }
        else {
            $wp_query->set('page', null);
        }

        // Get the title.
        $title = self::getPageTitleHelper();
        $title = html_entity_decode($title, ENT_QUOTES, 'UTF-8');

        // Set back the current page.
        $wp_query->set('page', $oldPage);

        // Return the title.
        return $title;
    }

    private static function getPageTitleHelper() {
        // If the WordPress SEO plugin is active and compatible.
        global $wpseo_front;
        if (
            isset($wpseo_front) &&
            method_exists($wpseo_front, 'title')
        ) {
            return $wpseo_front->title('', false);
        }

        // If the SEO Ultimate plugin is active and compatible.
        global $seo_ultimate;
        if (
            isset($seo_ultimate) &&
            property_exists($seo_ultimate, 'modules') &&
            isset($seo_ultimate->modules['titles']) &&
            method_exists($seo_ultimate->modules['titles'], 'get_title')
        ) {
            @$title = $seo_ultimate->modules['titles']->get_title();
	        return $title;
        }

        // If all else fails, return the standard WordPress title. Unfortunately, most theme hard-code their <title> tag.
        return wp_title('', false);
    }

	public static function mb_substr() {
		$args = func_get_args();
		if (function_exists('mb_substr')) {
			return call_user_func_array('mb_substr', $args);
		}
		return call_user_func_array('substr', $args);
	}

	public static function mb_strlen() {
		$args = func_get_args();
		if (function_exists('mb_strlen')) {
			return call_user_func_array('mb_strlen', $args);
		}
		return call_user_func_array('strlen', $args);
	}

	public static function mb_strpos() {
		$args = func_get_args();
		if (function_exists('mb_strpos')) {
			return call_user_func_array('mb_strpos', $args);
		}
		return call_user_func_array('strpos', $args);
	}
}
