<div id="sidebar-wrapper">
	<?php if ( ! dynamic_sidebar( 'sidebar-widget' ) ) : ?>
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
		<span class="sidebar-widget-header"><h3 class="sidebar-widget-header">Facebook</h3></span>
		<iframe src="//www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2Fenvato&amp;width=300&amp;height=258&amp;colorscheme=light&amp;show_faces=true&amp;header=false&amp;stream=false&amp;show_border=true" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:300px; height:258px;" allowTransparency="true"></iframe>
	</div><!--sidebar-widget-->
	<div class="sidebar-widget">
		<span class="sidebar-widget-header"><h3 class="sidebar-widget-header">Style</h3></span>
		<ul class="home-list">
			<?php $recent = new WP_Query(array( 'category_name' => 'style', 'posts_per_page' => '4' )); while($recent->have_posts()) : $recent->the_post();?>
			<li>
				<a href="<?php the_permalink(); ?>" rel="bookmark">
				<div class="home-list-img">
					<?php if (  (function_exists('has_post_thumbnail')) && (has_post_thumbnail())  ) { ?>
					<?php the_post_thumbnail('medium-thumb'); ?>
					<?php } ?>
				</div><!--home-list-img-->
				<div class="home-list-content">
					<span class="widget-info"><span class="widget-author"><?php the_author(); ?></span> | <?php the_time(get_option('date_format')); ?></span>
					<h2><?php the_title(); ?></h2>
					<p><?php echo excerpt(19); ?></p>
				</div><!--home-list-content-->
				</a>
			</li>
			<?php endwhile; ?>
		</ul>
	</div><!--sidebar-widget-->

	<?php endif; ?>
 	<?php if ( is_active_sidebar( 'sidebar-widget' ) ) : ?>
	<?php endif; ?>
</div><!--sidebar-wrapper-->