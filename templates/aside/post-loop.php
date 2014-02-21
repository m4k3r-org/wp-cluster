<?php
/**
 * Festival News
 *
 * @author Usability Dynamics
 * @module festival
 * @since festival 0.1.0
 */

global $wp_query;

extract( $data = wp_festival()->extend( array(
  'template' => 'default',
), (array)$wp_query->data ) );

//var_dump($template);

?>

<div class="posts-loop-module">
  <div class="row">
    <div class="col-md-12">
      <?php if( $wp_query->have_posts() ) : ?>
        <ul>
          <?php //wp_festival()->set_excerpt_filter( '25', 'length' ); ?>
          <?php while( $wp_query->have_posts() ) : $wp_query->the_post(); ?>
            <li class="item">
              <?php get_template_part( 'templates/article/listing-post', $template ); ?>
            </li>
          <?php endwhile; ?>
          <?php //wp_festival()->set_excerpt_filter( false, 'length' ); ?>
        </ul>
      <?php endif; ?>
    </div>
  </div>
</div>