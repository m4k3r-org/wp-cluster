<?php get_header(); ?>
	<div id="content-wrapper">
		<div id="content-main">
			<div id="home-main">
				<div class="home-widget">
					<ul class="home-list infinite-content">
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
								<span class="widget-cat-contain"><h3 class="widget-cat"><?php $category = get_the_category(); echo $category[0]->cat_name; ?></h3></span>
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
		<?php get_sidebar('home'); ?>
	</div><!--content-wrapper-->
</div><!--main-wrapper-->
<?php get_footer(); ?>