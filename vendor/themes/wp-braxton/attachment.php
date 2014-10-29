<?php get_header(); ?>
	<div id="content-wrapper">
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<div id="content-main">
			<div id="home-main">
				<div id="post-area" itemscope itemtype="http://schema.org/Article" <?php post_class(); ?>>
					<h1 class="story-title" itemprop="name"><?php the_title(); ?></h1>
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
								<?php $pinterestimage = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' ); ?>
<a href="http://pinterest.com/pin/create/button/?url=<?php echo urlencode(get_permalink($post->ID)); ?>&media=<?php echo $pinterestimage[0]; ?>&description=<?php the_title(); ?>" class="pin-it-button" count-layout="horizontal">Pin It</a>
							</li>
							<li>
								<g:plusone size="medium" annotation="inline" width="90"></g:plusone>
							</li>
						</ul>
					</div><!--social-box-->
					<?php } ?>
					<div id="content-area">
  					<?php if ( wp_attachment_is_image( $post->id ) ) : $att_image = wp_get_attachment_image_src( $post->id, "large"); ?>
					<a href="<?php echo wp_get_attachment_url($post->id); ?>" title="<?php the_title(); ?>" rel="attachment"><img src="<?php echo $att_image[0];?>" class="attachment-medium" alt="<?php the_title(); ?>" /></a>
					<?php else : ?>
					<a href="<?php echo wp_get_attachment_url($post->ID) ?>" title="<?php echo esc_html( get_the_title($post->ID), 1 ) ?>" rel="attachment"><?php echo basename($post->guid) ?></a>
					<?php endif; ?>
					</div><!--content-area-->
				</div><!--post-area-->
			</div><!--home-main-->
		</div><!--content-main-->
		<?php get_sidebar(); ?>
		<?php endwhile; endif; ?>
	</div><!--content-wrapper-->
</div><!--main-wrapper-->
<?php get_footer(); ?>