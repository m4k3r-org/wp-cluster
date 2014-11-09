<?php get_header(); ?>
	<div id="content-wrapper">
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<div id="content-main">
			<?php $mvp_featured_img = get_option('mvp_featured_img'); if ($mvp_featured_img == "true") { ?>
			<?php if (  (function_exists('has_post_thumbnail')) && (has_post_thumbnail())  ) { ?>
			<div id="featured-image">
				<?php the_post_thumbnail('post-thumb'); ?>
				<?php if(get_post_meta($post->ID, "mvp_photo_credit", true)): ?>
				<span class="photo-credit"><?php _e( 'Photo:', 'mvp-text' ); ?> <?php echo get_post_meta($post->ID, "mvp_photo_credit", true); ?></span>
				<?php endif; ?>
			</div><!--featured-image-->
			<?php } ?>
			<?php } ?>
			<div id="home-main">
				<div id="post-area" <?php post_class(); ?>>
					<h1 class="story-title"><?php the_title(); ?></h1>
					<?php $socialbox = get_option('mvp_social_box'); if ($socialbox == "true") { ?>
					<div class="social-box">
						<ul class="post-social">
							<li>
								<div class="fb-like" data-send="false" data-layout="button_count" data-width="90" data-show-faces="false"></div>
							</li>
							<li>
								<a href="http://twitter.com/share" class="twitter-share-button" data-lang="en" data-count="horizontal">Tweet</a>
							</li>
							<li>
								<g:plusone size="medium" annotation="bubble" width="90"></g:plusone>
							</li>
							<li>
								<?php $pinterestimage = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' ); ?>
<a href="http://pinterest.com/pin/create/button/?url=<?php echo urlencode(get_permalink($post->ID)); ?>&media=<?php echo $pinterestimage[0]; ?>&description=<?php the_title(); ?>" class="pin-it-button" count-layout="horizontal">Pin It</a>
							</li>
						</ul>
					</div><!--social-box-->
					<?php } ?>
					<div id="content-area">
						<?php the_content(); ?>
						<?php wp_link_pages(); ?>
					</div><!--content-area-->
				</div><!--post-area-->
			</div><!--home-main-->
		</div><!--content-main-->
		<?php get_sidebar(); ?>
		<?php endwhile; endif; ?>
	</div><!--content-wrapper-->
</div><!--main-wrapper-->
<?php get_footer(); ?>