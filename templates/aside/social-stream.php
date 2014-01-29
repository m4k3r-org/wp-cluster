<?php
/**
 * Social Stream
 *
 * @author Usability Dynamics
 * @module festival
 * @since festival 0.1.0
 */

$festival = wp_festival();

$the_query = new WP_Query( array(
  'post_type' => 'social',
  'post_status' => 'publish',
  'order' => 'DESC',
  'posts_per_page' => '5',
  'ignore_sticky_posts' => true,
) );

?>

<?php if( $the_query->have_posts() ) : ?>
<ul class="stream-items">

  <?php while( $the_query->have_posts() ) : $the_query->the_post(); ?>
  
    <?php $network = get_post_meta( get_the_ID(), '_ss_network', true ); ?>
    <?php $content = get_post_meta( get_the_ID(), '_ss_content', true ); ?>
    <?php $source = get_post_meta( get_the_ID(), '_ss_url', true ); ?>
    <?php $created = get_post_meta( get_the_ID(), '_ss_created_at', true ); ?>
    
    <li class="stream-item <?php echo $network; ?>">
      <div class="icon"></div>
      <div class="text">
      <div class="content">
        <p><?php echo $content; ?></p>
      </div>
      <div class="meta">
        <span class="date"><?php echo $created; ?></span> on <span class="network"><?php echo $network; ?></span>
      </div>
    </div>
    </li>
  
  <?php endwhile; ?>
  
  <!--
  <li class="stream-item instagram">
    <div class="photo">
      <img class="img-responsive" src="<?php echo $festival->get_image_link_by_post_id( -1, array( 'width' => '738', 'height' => '585' ) ); ?>" />
      <div class="icon"></div>
    </div>
    <div class="text">
      <p>Nulla urna leoultrices pretium tincidunt turpis.</p>
    </div>
  </li>

  <li class="stream-item facebook">
    <div class="icon"></div>
    <div class="text">
      <h4 class="title"><a href="#">Nulla urna leoultrices pretium tincidunt turpis.</a></h4>
      <div class="content">
        <p>Lorem ipsum dolor sit  consectetur adipiscing elit. pulvinar arcu nequeeget aliquam sapien euismod sed. Etiam consectetur</p>
      </div>
      <div class="meta">
        <span class="date">Nov, 15 2013</span> on <span class="network">Facebook</span>
      </div>
    </div>
  </li>

  <li class="stream-item twitter">
    <div class="icon"></div>
    <div class="text">
      <h4 class="title"><a href="#">Nulla urna leoultrices pretium tincidunt turpis.</a></h4>
      <div class="content">
        <p>Lorem ipsum dolor sit  consectetur adipiscing elit. pulvinar arcu nequeeget aliquam sapien euismod sed. Etiam consectetur</p>
      </div>
      <div class="meta">
        <span class="date">Nov, 15 2013</span> on <span class="network">Twitter</span>
      </div>
    </div>
  </li>
  -->

</ul>
<?php endif; ?>