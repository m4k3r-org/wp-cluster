<?php 

global $wp_query; 

extract( $wp_query->data ); 

$classes_map = array(
  1   => array( 'col-md-4', 'col-md-offset-4' ),
  2   => array( 'col-md-3', 'col-md-offset-3' ),
  3   => array( 'col-md-4', '' ),
  4   => array( 'col-md-3', '' ),
  5   => array( 'col-md-2', '' ),
  6   => array( 'col-md-2', '' ),
  8   => array( 'col-md-1', '' ),
  10  => array( 'col-md-1', '' ),
  12  => array( 'col-md-1', '' ),
);

$class = isset( $classes_map[ $artist_columns ] ) ? $classes_map[ $artist_columns ][0] : '';
$offset = isset( $classes_map[ $artist_columns ] ) ? $classes_map[ $artist_columns ][1] : '';;

?>
<div class="artists-list <?php echo $artist_type; ?>">

  <div class="container">

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
                <div class="row"><div class="row-artists clearfix">
              <?php endif; ?>
              <div class="<?php echo $class; ?> <?php echo !( $counter % $artist_columns ) ? $offset : ''; ?>">
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