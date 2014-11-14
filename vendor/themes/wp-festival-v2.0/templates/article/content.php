<?php
/**
 * The default template for displaying content. Used for both single and index/archive/search.
 *
 * @author Usability Dynamics
 * @module wp-festival
 * @since wp-festival 2.0.0
 */
?>

<section class="article-content" data-type="content">

	<div class="container tabbed-content">
		<div class="row">
			<div class="col-xs-12">
				<div class="tab-header">
					<a href="#details" class="selected"><?php _e('Details', wp_festival2( 'domain' )); ?></a>
					<a href="#comments"><?php _e('Comments', wp_festival2( 'domain' )); ?></a>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-8 col tab-content" id="details">
		<article class="content">
			<?php the_content(__('Continue reading <span class="meta-nav">&rarr;</span>', wp_festival2('domain'))); ?>
			<div class="meta">
				<span><?php _e('Topics', wp_festival2( 'domain' )); ?>: </span>
				<?php the_category(); ?>
			</div>
			<div class="clearfix"></div>
		</article>
  </div>
  <div class="col-md-4 col tab-content" id="comments">
    <?php comments_template(); ?>
  </div>
  <div class="clearfix"></div>

	<h2 class="latest-blog-posts"><?php _e('Latest Blog Posts', wp_festival2( 'domain' )); ?></h2>

	<div class="latest-blog-posts-no-padding">
		<?php echo do_shortcode( '[widget_news_block]' );?>
	</div>

</section>