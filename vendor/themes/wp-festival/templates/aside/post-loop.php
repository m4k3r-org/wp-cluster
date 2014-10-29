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
  'title' => '',
  'content' => '',
), (array)$wp_query->data ) );

?>

<?php if( $wp_query->have_posts() ) : ?>
<div class="posts-loop-module">
  <div class="row">
    <div class="col-md-12">
        <h3><?php echo $title; ?><br/><small><?php echo $content; ?></small></h3>
        <span class="hr"></span>
        <ul>
          <?php //wp_festival()->set_excerpt_filter( '25', 'length' ); ?>
          <?php while( $wp_query->have_posts() ) : $wp_query->the_post(); ?>
            <li class="item">
              <?php get_template_part( 'templates/article/listing-post', $template ); ?>
            </li>
          <?php endwhile; ?>
          <?php //wp_festival()->set_excerpt_filter( false, 'length' ); ?>
        </ul>
    </div>
  </div>
</div>
<?php endif; ?>