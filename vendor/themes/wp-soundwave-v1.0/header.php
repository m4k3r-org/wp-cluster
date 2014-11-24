<!DOCTYPE html>
<html>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		
		<title>
		  <?php if ( is_front_page() ) { ?>
  		  <?php bloginfo('name'); ?> - <?php bloginfo('description') ?>
		  <?php } elseif ( is_single() ) { ?>
		    <?php wp_title(''); ?>
		  <?php } else { ?>
		    <?php wp_title(''); ?> - <?php bloginfo('name'); ?>
		  <?php } ?>
		</title>
		
		<meta property="og:image" content="<?php bloginfo('template_url'); ?>/img/default_social.jpg" />
		
		<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url'); ?>/css/style.css" />
		<link rel="shortcut icon" type="image/x-icon" href="<?php bloginfo('template_url'); ?>/GFX/favicon.ico" />
		
		<?php wp_head(); ?>
	</head>
  <body id="home">
  	<!--BEGIN WRAPPER-->
  	<div id="wrapper">
  		<!--BEGIN HEADER-->
  		<div id="header">
  			<a id="islalogo" href="<?php bloginfo('url'); ?>"></a>
  			<ul id="social">
  				<li><a href="https://www.facebook.com/SoundWaveAZ" id="fb" title="Facebook"></a></li>
  				<li><a href="https://www.twitter.com/SoundWaveAZ" id="yt" title="Twitter"></a></li>
  			</ul>
  		</div>
  		<!--END HEADER-->
  		<!--BEGIN MAIN AREA-->
  		<div id="main">
  			<!--BEGIN NAV-->
  			<div id="nav">
  				<ul class="main_nav">
  					<li class="n1">
  						<?php if(is_single() || is_front_page()){ ?>
  							<img src="<?php bloginfo('template_url'); ?>/GFX/Nav/Home2.jpg" class="fade">
  						<?php } else { ?>
  							<a href="<?php bloginfo('url'); ?>" title="News"><img src="<?php bloginfo('template_url'); ?>/GFX/Nav/Home.jpg" class="fade"><img src="<?php bloginfo('template_url'); ?>/GFX/Nav/Home2.jpg" class="fade2"></a>
  						<?php } ?>
  					</li>
  					<li class="n2">
  						<?php if(is_page('talent')){ ?>
  							<img src="<?php bloginfo('template_url'); ?>/GFX/Nav/Talent2.jpg" class="fade">
  						<?php } else { ?>
  							<a href="<?php bloginfo('url'); ?>/talent" title="Talent"><img src="<?php bloginfo('template_url'); ?>/GFX/Nav/Talent.jpg" class="fade"><img src="<?php bloginfo('template_url'); ?>/GFX/Nav/Talent2.jpg" class="fade2"></a>
  						<?php } ?>
  					</li>
  					<li class="n3">
  						<?php if(is_page('tickets')){ ?>
  							<img src="<?php bloginfo('template_url'); ?>/GFX/Nav/Tickets2.jpg" class="fade">
  						<?php } else { ?>
  							<a href="<?php bloginfo('url'); ?>/tickets" title="Tickets"><img src="<?php bloginfo('template_url'); ?>/GFX/Nav/Tickets.jpg" class="fade"><img src="<?php bloginfo('template_url'); ?>/GFX/Nav/Tickets2.jpg" class="fade2"></a>
  						<?php } ?>
  					</li>
  					<li class="n4">
  						<?php if(is_page('info')){ ?>
  							<img src="<?php bloginfo('template_url'); ?>/GFX/Nav/Info2.jpg" class="fade">
  						<?php } else { ?>
  							<a href="<?php bloginfo('url'); ?>/info" title="Info"><img src="<?php bloginfo('template_url'); ?>/GFX/Nav/Info.jpg" class="fade"><img src="<?php bloginfo('template_url'); ?>/GFX/Nav/Info2.jpg" class="fade2"></a>
  						<?php } ?>
  					</li>
  					<li class="n5">
  						<?php if(is_page('venue')){ ?>
  							<img src="<?php bloginfo('template_url'); ?>/GFX/Nav/Venue2.jpg" class="fade">
  						<?php } else { ?>
  							<a href="<?php bloginfo('url'); ?>/venue" title="Venue"><img src="<?php bloginfo('template_url'); ?>/GFX/Nav/Venue.jpg" class="fade"><img src="<?php bloginfo('template_url'); ?>/GFX/Nav/Venue2.jpg" class="fade2"></a>
  						<?php } ?>
  					</li>
  					<li class="n6">
  						<?php if(is_page('contact')){ ?>
  							<img src="<?php bloginfo('template_url'); ?>/GFX/Nav/Contact2.jpg" class="fade">
  						<?php } else { ?>
  							<a href="<?php bloginfo('url'); ?>/contact" title="contact"><img src="<?php bloginfo('template_url'); ?>/GFX/Nav/Contact.jpg" class="fade"><img src="<?php bloginfo('template_url'); ?>/GFX/Nav/Contact2.jpg" class="fade2"></a>
  						<?php } ?>
  					</li>
  				</ul>
  			</div>
  			<!--BEGIN CONTENT-->
  			<div id="content">
  				<?php
  					if(is_page('talent')){
  						echo '<h2 id="talentttl">Talent</h2>';
  					} else if(is_page('tickets')){
  						echo '<h2 id="tixttl">Tickets</h2>';
  					} else if(is_page('info')){
  						echo '<h2 id="infottl">Info</h2>';
  					} else if(is_page('venue')){
  						echo '<h2 id="venuettl">Venues</h2>';
  					} else if(is_page('contact')){
  						echo '<h2 id="contactttl">Contact</h2>';
  					} else {
  						echo '<h2 id="news">Latest Updates</h2>';
  					}				
  				?>
  				<div id="in">