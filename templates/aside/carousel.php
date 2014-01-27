<?php
/**
 * Carousel
 *
 * @author Usability Dynamics
 * @module wp-escalade
 * @since wp-escalade 0.1.0
 */

global $festival;

// Be sure that sticky posts exist
$post__in = get_option( 'sticky_posts' );
if( empty( $post__in ) ) {
  return null;
}

$the_query = new WP_Query( array(
  'post_type' => 'post',
  'post_status' => 'publish',
  'order' => 'DESC',
  'post__in' => $post__in,
  'posts_per_page' => '9'
) );

$counter = 0;

?>
<?php if( $the_query->have_posts() ) : ?>
<div class="carousel-wrap">
  <h3>Praesent vulputate libero a est consequat<br/><small>Nam sodales pellentesque lorem vel tristique. Integer a malesuada purus, in dapibus mi!</small></h3>
  <div id="carousel-generic" class="carousel slide" data-ride="carousel">
    <div class="carousel-inner">
      <div class="item active">
        <div class="row">
          <?php $festival->set_excerpt_filter( '25', 'length' ); ?>
          <?php while( $the_query->have_posts() ) : $the_query->the_post(); ?>
            <?php if( $counter && !( $counter % 3 ) ) : ?>
              </div></div>
              <div class="item <?php $counter ? '' : 'active'; ?>">
                <div class="row">
            <?php endif; ?>
            <div class="col-md-4 col-sm-4">
              <?php $img = $festival->get_image_link_by_post_id( get_the_ID(), array( 'width' => '738', 'height' => '350' ) ); ?>
              <?php if( !empty( $img ) ) : ?>
                <a href="<?php the_permalink(); ?>"><img class="img-responsive" src="<?php echo $img; ?>" alt="" /></a>
              <?php endif; ?>
              <div class="description">
                <span class="date"><?php the_time('l, F j'); ?></span>
                <span class="hr"></span>
                <a href="<?php the_permalink(); ?>"><h5><?php the_title(); ?></h5></a>
                <p><?php the_excerpt(); ?></p>
              </div>
            </div>
            <?php $counter++; ?>
          <?php endwhile; ?>
          <?php $festival->set_excerpt_filter( false, 'length' ); ?>
        </div>
      </div>
    </div>
    <?php if( $counter >= 3 ) : ?>
    <ol class="carousel-indicators">
      <?php $step = 0; ?>
      <?php for( $i = 0; $i <= $counter; $i+=3 ) : ?>
        <li data-target="#carousel-generic" data-slide-to="<?php echo $step ?>" class="<?php echo $step++ ? '' : 'active'; ?>"></li>
      <?php endfor; ?>
    </ol>
    <?php endif; ?>
  </div>
</div><!-- /carousel-wrap -->
<?php endif; ?>