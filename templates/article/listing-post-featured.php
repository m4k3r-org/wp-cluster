<?php
/**
 * Featured Post Content
 *
 * @author Usability Dynamics
 * @module festival
 * @since festival 0.1.0
 */

?>
<article class="listing-post featured clearfix">
  <div class="image">
    <a href="<?php the_permalink(); ?>">
      <img class="img-responsive" src="<?php echo wp_festival()->get_image_link_by_post_id( get_the_ID(), array( 'width' => '738', 'height' => '415' ) ); ?>" />
    </a>
    <h4 class="category">
      <?php the_category(', '); ?>
      <span class="hr"></span>
    </h4>
  </div>
  <div class="text">
    <h4 class="title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
    <div class="content"><?php the_excerpt(); ?></div>
    <div class="date"><?php the_time(get_option('date_format')); ?></div>
  </div>
</article>