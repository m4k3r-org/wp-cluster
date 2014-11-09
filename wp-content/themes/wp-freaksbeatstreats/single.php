<?php get_header(); ?>
<div class="post-wrapper">
  <section class="container">
    
    <div id="post-single" class="">
      <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
        <div id="post_id_<?php the_ID(); ?>" <?php post_class( 'post' ); ?>>
        
          <?php
            if ( has_post_thumbnail() ) {
              $image_id  = get_post_thumbnail_id();
              $image_url = wp_get_attachment_image_src( $image_id, 'post-image' );
              $image_url = $image_url[ 0 ];
              echo '<img src="' . $image_url . '" alt="" class="featured-image" />';
            }
          ?>
          <h1 class="title post-title">
            <?php the_title(); ?>
          </h1>
          <div class="meta post-meta">
            <span class="date">
              <?php the_date(); ?>
            </span>
          </div>
          <div class="entry post-entry">
          	<?php the_content(); ?>
          </div>

      </div>
      <?php endwhile; else : ?>
        <p><?php _e( 'Sorry, no posts matched your criteria.', 'framework' ) ?></p>
      <?php endif; ?>

    </div>
    
    <?php //get_template_part( 'comments' ); ?>

</section>

    <?php query_posts( 'post_type=post&posts_per_page=10' ); ?>
    <?php get_template_part( 'loop' ); ?>

</div>

<?php get_footer(); ?>