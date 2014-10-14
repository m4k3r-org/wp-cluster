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

  <img class="logo-mini" src="<?php echo get_stylesheet_directory_uri(); ?>/static/images/logo-mini.png" alt="CHMF">

  <?php

  $prev_post = get_previous_post();
  $prev_post = get_permalink($prev_post->ID);

  $next_post = get_next_post();
  $next_post = get_permalink($next_post->ID);

  ?>

  <a href="<?php echo $prev_post; ?>" class="prev-next icon-spectacle-left-arrow"></a>
  <a href="<?php echo $next_post; ?>" class="prev-next icon-spectacle-right-arrow"></a>


</header>