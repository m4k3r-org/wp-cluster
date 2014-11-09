<div class="artists-carousel">
  <?php
  $type = 'artist';
  $args = array(
    'post_type' => $type,
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'caller_get_posts' => 1
  );
  $my_query = null;
  $my_query = new WP_Query( $args );
  if( $my_query->have_posts() ){
    while( $my_query->have_posts() ) : $my_query->the_post(); ?>
      <?php
      $image_portrait = wp_festival2()->get_artist_image_link( get_the_ID(), array(
        'type' => 'portraitImage',
        'width' => 150,
        'height' => 300
      ) );
      ?>

      <a href="<?php the_permalink(); ?>" class="artist">
        <div class="photo">
          <div class="resp-content">
            <img src="<?php echo $image_portrait; ?>" alt="<?php the_title(); ?>">
          </div>
        </div>
        <h3><?php the_title(); ?></h3>
      </a>

    <?php
    endwhile;
  }
  ?>

</div>