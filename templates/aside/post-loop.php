<?php
/**
 * Festival News
 *
 * @author Usability Dynamics
 * @module festival
 * @since festival 0.1.0
 */

global $wp_query;

$type = 'featured';

?>

<div class="posts-loop-module container">
  <div class="row">
    <div class="col-md-12">
      <div class="featured">
        <?php if( $wp_query->have_posts() ) : ?>
          <ul>
            <?php wp_festival()->set_excerpt_filter( '25', 'length' ); ?>
            <?php while( $wp_query->have_posts() ) : $wp_query->the_post(); ?>
              <li class="news-item">
                <?php get_template_part( 'templates/article/listing-post', $type ); ?>
              </li>
            <?php endwhile; ?>
            <?php wp_festival()->set_excerpt_filter( false, 'length' ); ?>
          </ul>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>