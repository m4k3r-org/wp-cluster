<?php
/**
 * Default Post Content
 *
 * @author Usability Dynamics
 * @module wp-festival
 * @since wp-festival 2.0.0
 */

?>
<article class="listing-post default clearfix">
  <div class="featured-image">
    <img class="img-responsive" src="<?php echo wp_festival2()->get_image_link_by_post_id( get_the_ID(), array( 'width' => '738', 'height' => '465' ) ); ?>" />
  </div>
  <div class="category">
    <?php the_category(); ?>
  </div>
  <span class="hr"></span>
  <h4 class="title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
  <?php get_template_part( 'templates/aside/share', get_post_type() ); ?>
</article>