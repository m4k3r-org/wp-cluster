<?php
/**
 * Festival News
 *
 * @author Usability Dynamics
 * @module festival
 * @since festival 0.1.0
 */

global $wp_query;

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
              </li>
            <?php endwhile; ?>
            <?php wp_festival()->set_excerpt_filter( false, 'length' ); ?>
          </ul>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>