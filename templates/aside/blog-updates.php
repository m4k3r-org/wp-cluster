<?php
/**
 * Blog Updates
 *
 * @author Usability Dynamics
 * @module festival
 * @since festival 0.1.0
 */

global $festival;

// Ignore News category.
$idObj = get_category_by_slug( 'news' ); 
$id = $idObj->term_id;

$the_query = new WP_Query( array(
  'post_type' => array( 'post' ),
  'category__not_in' => array( $id ),
  'cache_results' => false,
  'post_status' => 'publish',
  'order' => 'DESC',
  'posts_per_page' => '5',
  'ignore_sticky_posts' => true,
) );

?>

<?php if( $the_query->have_posts() ) : ?>
  <ul>
    <?php while( $the_query->have_posts() ) : $the_query->the_post(); ?>
      <li class="blog-item">
        <div class="featured-image">
          <img class="img-responsive" src="<?php echo $festival->get_image_link_by_post_id( get_the_ID(), array( 'width' => '738', 'height' => '465' ) ); ?>" />
        </div>
        <div class="category">
          <?php the_category(); ?>
        </div>
        <span class="hr"></span>
        <h4 class="title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
        <?php get_template_part( 'templates/aside/share', get_post_type() ); ?>
      </li>
    <?php endwhile; ?>
  </ul>
<?php endif; ?>