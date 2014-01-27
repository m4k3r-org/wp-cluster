<?php
/**
 * Festival News
 *
 * @author Usability Dynamics
 * @module festival
 * @since festival 0.1.0
 */

global $festival;

$the_query = new WP_Query( array(
  'post_type' => 'post',
  'category_name' => 'news',
  'post_status' => 'publish',
  'order' => 'DESC',
  'posts_per_page' => '3',
  'ignore_sticky_posts' => true,
) );

?>

<?php if( $the_query->have_posts() ) : ?>
  <ul>
    <?php $festival->set_excerpt_filter( '25', 'length' ); ?>
    <?php while( $the_query->have_posts() ) : $the_query->the_post(); ?>
      <li class="news-item">
        <div class="image">
          <img class="img-responsive" src="<?php echo $festival->get_image_link_by_post_id( get_the_ID(), array( 'width' => '738', 'height' => '415' ) ); ?>" />
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
    <?php $festival->set_excerpt_filter( false, 'length' ); ?>
  </ul>
<?php endif; ?>