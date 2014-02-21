<?php
/**
 * Featured Post Content
 *
 * @author Usability Dynamics
 * @module festival
 * @since festival 0.1.0
 */

?>
<article class="post-featured">
  <div class="image">
    <img class="img-responsive" src="<?php echo wp_festival()->get_image_link_by_post_id( get_the_ID(), array( 'width' => '738', 'height' => '415' ) ); ?>" />
    <h4 class="category">
      <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
      <span class="hr"></span>
    </h4>
  </div>
  <div class="text">
    <div class="content"><?php the_excerpt(); ?></div>
    <?php get_template_part( 'templates/aside/share', get_post_type() ); ?>
  </div>
</article>