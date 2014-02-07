<?php 

global $wp_query; 

extract( $wp_query->data );

?>
<div class="artists-list <?php echo $artist_type; ?>">

  <div class="container">

    <header class="row">
      <div class="col-md-12 col-sm-12 text-center">
        <?php if( isset( $title ) && $title ): ?>
          <h3><?php echo $title; ?></h3>
          <span class="hr"></span>
        <?php endif; ?>
        <?php if( isset( $tagline ) && $tagline ): ?>
          <p class="tagline"><?php echo $tagline; ?></p>
        <?php endif; ?>
        <?php if( isset( $description ) && $description ): ?>
          <p class="description"><?php echo $description; ?></p>
        <?php endif; ?>
      </div>
    </header>

    <section class="the-list">
      <div class="row">
        <div class="col-md-12 clearfix">
          <?php $counter = 0; ?>
          <?php if( have_posts() ) : ?>
            <?php while( have_posts() ) : the_post(); ?>
              <?php if ( !( $counter % $artist_columns ) ) : ?>
                <div class="row"><div class="row-artists clearfix">
              <?php endif; ?>
              <div class="<?php echo $map[0]; ?> <?php echo !( $counter % $artist_columns ) ? $map[1] : ''; ?>">
                <?php get_template_part( 'templates/article/listing-artist', $artist_type ); ?>
              </div>
              <?php $counter++; ?>
              <?php if ( !( $counter % $artist_columns ) ) : ?>
                </div></div>
              <?php endif; ?>
            <?php endwhile; ?>
          <?php endif; ?>      
        </div>
      </div>
    </section>
  
  </div>
  
</div>