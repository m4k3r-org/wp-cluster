<?php
global $wp_query, $_col;
extract( $wp_query->data );

/** Check for title and tagline */
$show_callout = true;
if( !$title && !$tagline ){
  $show_callout = false;
} ?>
<div class="artists-list new-artist-list">
  
  <?php if( $show_callout ): ?>
    <section class="tier2-artist-header">
      <div class="container">
        <?php if (isset($title) && $title): ?>
          <h2><?php echo $title; ?></h2>
        <?php endif; ?>
        <?php if (isset($tagline) && $tagline): ?>
          <p><?php echo $tagline; ?></p>
        <?php endif; ?>
      </div>
    </section>
  <?php endif; ?>

  <section class="the-list clearfix">
      <?php $counter = 0; ?>
      <?php if (have_posts()) : ?>
        <?php while (have_posts()) : the_post(); ?>
          <?php if (!( $counter % $artist_columns )) : ?>
            <div class="row-artists clearfix">
            <?php endif; ?>
            <div class="col col-lg-<?php echo 12/$artist_columns; ?> col-md-6 col-sm-6 col-xs-12">
              <?php get_template_part('templates/article/listing-artist', 'alphabetical'); ?>
            </div>
            <?php $counter++; ?>
            <?php if (!( $counter % $artist_columns )) : ?>
            </div>
          <?php endif; ?>
        <?php endwhile; ?>
      <?php endif; ?>
  </section>

</div>
<div class="clearfix"></div>