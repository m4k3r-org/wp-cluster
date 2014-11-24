<?php get_header(); ?>

<?php global $author; $userdata = get_userdata($author); ?>

	<div id="content-wrapper">
		<div id="content-main">
			<div id="home-main">
				<h1 class="archive-header"><?php _e( 'All posts by', 'mvp-text' ); ?> <?php echo $userdata->display_name; ?></h1>
				<?php if ( $paged < 2 ) : ?>
					<?php $authorbox = get_option('ht_author_box'); if ($authorbox == "true") { ?>
					<div id="author-wrapper">
						<div id="author-info">
							<?php echo get_avatar( $userdata->user_email, '100' ); ?>
							<div id="author-text">
								<p><?php echo $userdata->description; ?></p>
								<ul>
									<?php $authordesc = $userdata->facebook; if ( ! empty ( $authordesc ) ) { ?>
									<li class="fb-item">
										<a href="http://www.facebook.com/<?php echo $userdata->facebook; ?>" alt="Facebook" class="fb-but" target="_blank"></a>
									</li>
									<?php } ?>
									<?php $authordesc = $userdata->twitter; if ( ! empty ( $authordesc ) ) { ?>
									<li class="twitter-item">
										<a href="http://www.twitter.com/<?php echo $userdata->twitter; ?>" alt="Twitter" class="twitter-but" target="_blank"></a>
									</li>
									<?php } ?>
									<?php $authordesc = $userdata->pinterest; if ( ! empty ( $authordesc ) ) { ?>
									<li class="pinterest-item">
										<a href="http://www.pinterest.com/<?php echo $userdata->pinterest; ?>" alt="Pinterest" class="pinterest-but" target="_blank"></a>
									</li>
									<?php } ?>
									<?php $authordesc = $userdata->googleplus; if ( ! empty ( $authordesc ) ) { ?>
									<li class="google-item">
										<a href="<?php echo $userdata->googleplus; ?>" alt="Google Plus" class="google-but" target="_blank"></a>
									</li>
									<?php } ?>
									<?php $authordesc = $userdata->instagram; if ( ! empty ( $authordesc ) ) { ?>
									<li class="instagram-item">
										<a href="http://www.instagram.com/<?php echo $userdata->instagram; ?>" alt="Instagram" class="instagram-but" target="_blank"></a>
									</li>
									<?php } ?>
									<?php $authordesc = $userdata->linkedin; if ( ! empty ( $authordesc ) ) { ?>
									<li class="linkedin-item">
										<a href="http://www.linkedin.com/company/<?php echo $userdata->linkedin; ?>" alt="Linkedin" class="linkedin-but" target="_blank"></a>
									</li>
									<?php } ?>
								</ul>
							</div><!--author-text-->
						</div><!--author-info-->
					</div><!--author-wrapper-->
					<?php } ?>
				<?php endif; ?>
				<div class="home-widget">
					<ul class="home-list cat-home-widget infinite-content">
						<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
						<li class="infinite-post">
							<a href="<?php the_permalink(); ?>" rel="bookmark">
							<div class="home-list-img">
								<?php if (  (function_exists('has_post_thumbnail')) && (has_post_thumbnail())  ) { ?>
								<?php $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'medium-thumb' ); ?>
								<img class="lazy" src="<?php echo get_template_directory_uri(); ?>/images/trans.gif" data-original="<?php echo $thumb['0']; ?>" />

								<?php } ?>
								<noscript><?php the_post_thumbnail('medium-thumb'); ?></noscript>
								<?php if(get_post_meta($post->ID, "mvp_video_embed", true)): ?>
									<div class="video-button">
										<img src="<?php echo get_template_directory_uri(); ?>/images/video-but.png" alt="<?php the_title(); ?>" />
									</div><!--video-button-->
								<?php endif; ?>
							</div><!--home-list-img-->
							<div class="home-list-content">
								<span class="widget-info"><span class="widget-author"><?php the_author(); ?></span> | <?php the_time(get_option('date_format')); ?></span>
								<h2><?php the_title(); ?></h2>
								<p><?php echo excerpt(19); ?></p>
							</div><!--home-list-content-->
							</a>
						</li>
						<?php endwhile; endif; ?>
					</ul>
					<div class="nav-links">
						<?php if (function_exists("pagination")) { pagination($wp_query->max_num_pages); } ?>
					</div><!--nav-links-->
				</div><!--home-widget-->
			</div><!--home-main-->
		</div><!--content-main-->
		<?php get_sidebar(); ?>
	</div><!--content-wrapper-->
</div><!--main-wrapper-->
<?php get_footer(); ?>