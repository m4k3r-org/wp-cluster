<?php

// Get the featured image of the post
if ( has_post_thumbnail() )
{
  $image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'thumbnail-size', true);
  $image_url = $image_url[0];
}
?>

<header class="single-post-header" style="background-image: url('<?php echo $image_url; ?>');">
  <div class="gradient-fade"></div>

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

</header>