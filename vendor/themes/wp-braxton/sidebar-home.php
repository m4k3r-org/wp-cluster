<div id="sidebar-wrapper">
	<?php if ( ! dynamic_sidebar( 'sidebar-home-widget' ) ) : ?>
	<div class="sidebar-widget">
		<div class="widget-ad">
			<h4 class="ad-header"><?php _e( 'Advertisement', 'mvp-text' ); ?></h4>
			<div class="widget-ad">
				<img src="http://www.mvpthemes.com/gameday/wp-content/uploads/2013/01/ad300.gif" />
			</div><!--widget-ad-->
		</div><!--widget-ad-->
	</div><!--sidebar-widget-->
	<div class="sidebar-widget">
		<span class="sidebar-widget-header"><h3 class="sidebar-widget-header">Connect</h3></span>
				<div id="content-social">
					<ul>
						<?php if(get_option('mvp_facebook')) { ?>
						<li class="fb-item">
							<a href="http://www.facebook.com/<?php echo get_option('mvp_facebook'); ?>" alt="Facebook" class="fb-but" target="_blank"></a>
						</li>
						<?php } ?>
						<?php if(get_option('mvp_twitter')) { ?>
						<li class="twitter-item">
							<a href="http://www.twitter.com/<?php echo get_option('mvp_twitter'); ?>" alt="Twitter" class="twitter-but" target="_blank"></a>
						</li>
						<?php } ?>
						<?php if(get_option('mvp_pinterest')) { ?>
						<li class="pinterest-item">
							<a href="http://www.pinterest.com/<?php echo get_option('mvp_pinterest'); ?>" alt="Pinterest" class="pinterest-but" target="_blank"></a>
						</li>
						<?php } ?>
						<?php if(get_option('mvp_google')) { ?>
						<li class="google-item">
							<a href="<?php echo get_option('mvp_google'); ?>" alt="Google Plus" class="google-but" target="_blank"></a>
						</li>
						<?php } ?>
						<?php if(get_option('mvp_instagram')) { ?>
						<li class="instagram-item">
							<a href="http://www.instagram.com/<?php echo get_option('mvp_instagram'); ?>" alt="Instagram" class="instagram-but" target="_blank"></a>
						</li>
						<?php } ?>
						<?php if(get_option('mvp_youtube')) { ?>
						<li class="youtube-item">
							<a href="http://www.youtube.com/user/<?php echo get_option('mvp_youtube'); ?>" alt="YouTube" class="youtube-but" target="_blank"></a>
						</li>
						<?php } ?>
						<?php if(get_option('mvp_linkedin')) { ?>
						<li class="linkedin-item">
							<a href="http://www.linkedin.com/company/<?php echo get_option('mvp_linkedin'); ?>" alt="Linkedin" class="linkedin-but" target="_blank"></a>
						</li>
						<?php } ?>
						<?php if(get_option('mvp_rss')) { ?>
						<li><a href="<?php echo get_option('mvp_rss'); ?>" alt="RSS Feed" class="rss-but"></a></li>
						<?php } else { ?>
						<li><a href="<?php bloginfo('rss_url'); ?>" alt="RSS Feed" class="rss-but"></a></li>
						<?php } ?>
					</ul>
				</div><!--content-social-->
	</div><!--sidebar-widget-->
	<div class="sidebar-widget">
		<h4 class="ad-header"><?php _e( 'Advertisement', 'mvp-text' ); ?></h4>
		<div class="ad125-wrapper">
			<ul class="ad125">
				<li class="ad125-1"><img src="http://www.phillygameday.com/dev/wp-content/uploads/2013/10/ad125.gif" /></li>
				<li class="ad125-2"><img src="http://www.phillygameday.com/dev/wp-content/uploads/2013/10/ad125.gif" /></li>
				<li class="ad125-3"><img src="http://www.phillygameday.com/dev/wp-content/uploads/2013/10/ad125.gif" /></li>
				<li class="ad125-4"><img src="http://www.phillygameday.com/dev/wp-content/uploads/2013/10/ad125.gif" /></li>
			</ul>
		</div><!--ad125-wrapper-->
	</div><!--sidebar-widget-->
	<?php endif; ?>
 	<?php if ( is_active_sidebar( 'sidebar-home-widget' ) ) : ?>
	<?php endif; ?>
</div><!--sidebar-wrapper-->