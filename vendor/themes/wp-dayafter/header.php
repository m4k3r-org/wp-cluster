<!doctype html>
<!--[if lt IE 7]><html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:og="http://ogp.me/ns#" xmlns:fb="http://www.facebook.com/2008/fbml"><![endif]-->
<!--[if IE 7]><html class="no-js ie7 lt-ie9 lt-ie8" lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:og="http://ogp.me/ns#" xmlns:fb="http://www.facebook.com/2008/fbml"><![endif]-->
<!--[if IE 8]><html class="no-js ie8 lt-ie9" lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:og="http://ogp.me/ns#" xmlns:fb="http://www.facebook.com/2008/fbml"><![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" <?php language_attributes(); ?> xmlns="http://www.w3.org/1999/xhtml" xmlns:og="http://ogp.me/ns#" xmlns:fb="http://www.facebook.com/2008/fbml"><!--<![endif]-->
	<head>
		<meta charset="<?php bloginfo('charset'); ?>" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<title><?php tfg_title(); ?></title>
		<meta name="description" content="<?php tfg_description(); ?>" />
		<meta property="og:url" content="<?php echo current_url(); ?>" />
		<meta property="og:title" content="<?php tfg_title(); ?>" />
		<meta property="og:description" content="<?php tfg_description(); ?>" />
		<meta property="og:site_name" content="<?php bloginfo(); ?>" />
		<meta property="og:image" content="<?php tfg_image(); ?>" />
		<meta property="og:type" content="website" />
		<meta property="fb:app_id" content="305661039550261" />
		<meta property="fb:admins" content="632804651" />
		<link href="<?php bloginfo('template_url'); ?>/favicon.ico" rel="shortcut icon" type="image/x-icon" />
		<?php wp_head(); ?>
	</head>
	<body <?php body_class(); ?>>
		<div id="fb-root"></div>
		<script>
			(function(d, s, id) {
				var js, fjs = d.getElementsByTagName(s)[0];
				if (d.getElementById(id)) return;
				js = d.createElement(s); js.id = id;
				js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=305661039550261";
				fjs.parentNode.insertBefore(js, fjs);
			}(document, 'script', 'facebook-jssdk'));
		</script>
		<div id="wrapper">
			<div id="container">
				<div id="container-inner">
					<header>
						<div class="social clearfix">
							<a href="https://www.facebook.com/TheDayAfterPanama" class="facebook" target="_blank">Something Wicked Facebook</a>
							<a href="https://twitter.com/tdapanama" class="twitter" target="_blank">Something Wicked Twitter</a>
            </div>
						<h1><a href="<?php bloginfo('url'); ?>"><?php echo bloginfo(); ?></a></h1>

            <div class="locale-selection">
              <?php do_action('icl_language_selector'); ?>
            </div>

					</header>
					<!-- <nav>
						<ul>
							<li<?php if(is_home() || (is_single() && get_post_type() == 'post')){ echo ' class="current"'; } ?>><a href="<?php bloginfo('url');?>">HOME</a></li>
							<li<?php if(is_tree(get_page_id('tickets'))){ echo ' class="current"'; } ?>><a href="<?php bloginfo('url');?>/tickets/">TICKETS</a></li>
              <li<?php if(is_tree(get_page_id('talent'))){ echo ' class="current"'; } ?>><a href="<?php bloginfo('url');?>/talent/">TALENT</a></li>
              <li<?php if(is_tree(get_page_id('panama'))){ echo ' class="current"'; } ?>><a href="<?php bloginfo('url');?>/panama/">PANAMA</a></li>
              <li<?php if(is_tree(get_page_id('travel'))){ echo ' class="current"'; } ?>><a href="<?php bloginfo('url');?>/travel/">TRAVEL</a></li>
							<li<?php if(is_tree(get_page_id('info'))){ echo ' class="current"'; } ?>><a href="<?php bloginfo('url');?>/info/">INFO</a></li>
							<li<?php if(is_tree(get_page_id('contact'))){ echo ' class="current"'; } ?>><a href="<?php bloginfo('url');?>/contact/">CONTACT</a></li>
						</ul>
					</nav> -->
          <nav>
            <?php wp_nav_menu(array('theme_location' => 'primary', 'container' => '')); ?>
          </nav>
					<div id="main" class="clearfix">