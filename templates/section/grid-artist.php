<?php global $wp_query; extract( $wp_query->data ); ?>

<?php if( isset( $title ) && $title ): ?>
  <h2 class="title"><?php echo $title; ?></h2>
<?php endif; ?>
<?php if( isset( $tagline ) && $tagline ): ?>
  <span class="tagline"><?php echo $tagline; ?></span>
<?php endif; ?>
<?php if( isset( $description ) && $description ): ?>
  <p class="description"><?php echo $description; ?></p>
<?php endif; ?>

<section class="container inner-wrapper entry-<?php echo get_post_type(); ?>">
  <div class="row">
    <div col-md-12 clearfix">
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