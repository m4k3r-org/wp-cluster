<?php get_header(); ?>
<div class="container-wrapper">
  <section class="container">
    
    <div id="blog" class="">
			<?php while ( have_posts() ) : the_post(); ?>
        <div id="post_id_<?php the_ID(); ?>" <?php post_class( 'post' ); ?>>
        
          <?php
            if ( has_post_thumbnail() ) {
              $image_id  = get_post_thumbnail_id();
              $image_url = wp_get_attachment_image_src( $image_id, 'post-image' );
              $image_url = $image_url[ 0 ];
              echo '<img src="' . $image_url . '" alt="" class="thumbnail featured-image" />';
            }
          ?>

          <?php the_content(); ?>

      </div>
      <?php endwhile; ?>
    </div>

    <?php query_posts( 'post_type=post&posts_per_page=10' ); ?>

    <?php get_template_part( 'loop' ); ?>

  </section>
</div>
<?php get_footer(); ?>
