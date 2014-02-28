<?php
/**
 * Slider Post Content
 *
 * @author Usability Dynamics
 * @module festival
 * @since festival 0.1.0
 */

?>
<li class="post-slider">
  <div class="image">
    <a href="<?php the_permalink(); ?>">
      <img class="img-responsive" src="<?php echo wp_festival()->get_image_link_by_post_id( get_the_ID(), array( 'width' => '738', 'height' => '415' ) ); ?>" />
    </a>
  </div>
  <div class="category">
    <?php the_category(', '); ?>
    <span class="hr"></span>
  </div>
  <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
</li>