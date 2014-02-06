<?php 

global $wp_query; 

extract( $wp_query->data ); 

$classes_map = array(
  1 => 'col-md-12',
  2 => 'col-md-6',
  3 => 'col-md-4',
  4 => 'col-md-3',
  5 => 'col-md-2',
  6 => 'col-md-2',
  8 => 'col-md-1',
  10 => 'col-md-1',
  12 => 'col-md-1',
);

$class = isset( $classes_map[ $artist_columns ] ) ? $classes_map[ $artist_columns ] : '';

?>
<div class="artists-list <?php echo $artist_type; ?>">

  <header class="row heading">
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
              <div class="row row-artists">
            <?php endif; ?>
            <?php $counter++; ?>
            <div class="<?php echo $class; ?>">
              <?php get_template_part( 'templates/article/listing-artist', $artist_type ); ?>
            </div>
            <?php if ( !( $counter % $artist_columns ) ) : ?>
              </div>
            <?php endif; ?>
          <?php endwhile; ?>
        <?php endif; ?>      
      </div>
    </div>
  </section>
</div>