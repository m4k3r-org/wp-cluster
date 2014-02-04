<?php global $wp_query; extract( $wp_query->data ); ?>

<div class="artists-list <?php echo $artist_type; ?>">
  <header class="container">
    <?php if( isset( $title ) && $title ): ?>
      <h2 class="title"><?php echo $title; ?></h2>
      <div class="hr"></div>
    <?php endif; ?>
    <?php if( isset( $tagline ) && $tagline ): ?>
      <span class="tagline"><?php echo $tagline; ?></span>
    <?php endif; ?>
    <?php if( isset( $description ) && $description ): ?>
      <p class="description"><?php echo $description; ?></p>
    <?php endif; ?>
  </header>

  <section class="container inner-wrapper entry-<?php echo get_post_type(); ?>">
    <div class="row">
      <div class="col-md-12 clearfix">
        <?php if( !have_posts() ) : ?>
          <?php get_template_part( 'templates/article/listing-artist', $wp_query->data[ 'artist-type' ] ); ?>
        <?php else : ?>
          <?php while( have_posts() ) : the_post(); ?>
            <?php get_template_part( 'templates/article/listing-artist', $wp_query->data[ 'artist-type' ] ); ?>
          <?php endwhile; ?>
        <?php endif; ?>
      </div>
    </div>
  </section>
</div>