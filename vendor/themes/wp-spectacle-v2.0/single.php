<?php
get_header();
the_post();

// Get the featured image URL of the post
if ( has_post_thumbnail() )
{
  $image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'thumbnail-size', true);
  $image_url = $image_url[0];
}
?>

  <article class="blog-single">
    <div class="blog-single-featured-image" style="background-image: url('<?php echo $image_url; ?>');">
      <a href="/" class="logo-mini">
        <img src="<?php echo get_stylesheet_directory_uri(); ?>/static/images/logo.png" alt="Monster Block Party">
      </a>

      <?php

      $prev_post = get_previous_post();
      $prev_post_url = get_permalink($prev_post->ID);

      $next_post = get_next_post();
      $next_post_url = get_permalink($next_post->ID);

      ?>

      <?php if( ! empty( $next_post ) ) : ?>
        <a href="<?php echo $next_post_url; ?>" class="prev-next icon-spectacle-left-arrow"></a>
      <?php endif; ?>

      <?php if( ! empty( $prev_post ) ) : ?>
        <a href="<?php echo $prev_post_url; ?>" class="prev-next icon-spectacle-right-arrow"></a>
      <?php endif; ?>

      <div class="blog-single-gradient-fade"></div>
    </div>

    <div class="blog-single-content">
      <div class="container">
        <h3 class="title"><?php the_title(); ?></h3>
        <hr class="divider">

      <span class="meta clearfix">
        <span class="date">
          <i class="icon-spectacle-time"></i>
          <span><?php echo get_the_time('D, M d, Y'); ?></span>
        </span>

        <span class="comments-count">
          <i class="icon-spectacle-comment"></i>
          <a href="#"><?php comments_number( 'no comments', '1 comment', '% comments' ); ?></a>
        </span>
      </span>

        <div class="blog-single-content-container">
          <?php the_content(); ?>
        </div>

        <hr>

        <?php comments_template(); ?>
      </div>
    </div>

  </article>

<?php get_footer(); ?>