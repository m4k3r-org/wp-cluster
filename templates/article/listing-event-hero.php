<?php
/**
 * Listing Event Hero
 *
 * @see carrington builder module Event Hero
 * @author Usability Dynamics
 * @module festival  
 * @since festival 0.1.0
 */

global $wp_query; 

extract( $data = wp_festival()->extend( array(
  'postdata' => array(),
  'background_image' => false,
  'background_color' => false,
  'font_color' => false,
  'title_color' => false,
  'desc_color' => false,
  'logo_image' => false,
  'enable_links' => false,
  'artist_image_type' => 'featured',
  'artist_image_width' => '',
  'artist_image_height' => '',
  'artist_columns' => 3,
  'class_col' => 'col-md-4',
  'class_offset' => 'col-md-offset-0',
), $wp_query->data ) );

//echo "<pre>"; print_r( $data ); echo "</pre>";

$bgi_url = !empty( $background_image ) ? wp_festival()->get_image_link_by_attachment_id( $background_image, array( 'default' => false ) ) : false;
$logo_url = !empty( $logo_image ) ? wp_festival()->get_image_link_by_attachment_id( $logo_image, array( 'default' => false ) ) : false;

$bimage = $bgi_url ? "background-image: url( {$bgi_url} );" : "";
$bcolor = !empty( $background_color ) ? "background-color: {$background_color} !important;" : "";
$fcolor = !empty( $font_color ) ? "color: {$font_color} !important;" : "";
$dcolor = !empty( $desc_color ) ? "color: {$desc_color} !important;" : $fcolor;
$tcolor = !empty( $title_color ) ? "color: {$title_color} !important;" : $fcolor;

?>
<?php if( have_posts() ) : ?>
  <div class="event-hero" style="<?php echo $bimage; ?><?php echo $bcolor; ?>" >
    <div class="container">
      <div class="row">
        <div class="col-md-10 col-md-offset-1">
          <?php while( have_posts() ) : the_post(); ?>
            <?php $post = wp_festival()->get_post_data( get_the_ID() ); ?> 
            <?php $post = wp_festival()->extend( $post, $postdata ); ?>
            <?php //echo "<pre>"; print_r( $post ); echo "</pre>"; ?>
            <section class="event-hero-block text-center">
              <h1 class="h-color text-center" style="<?php echo $tcolor; ?>"><?php echo $post[ 'post_title' ]; ?></h1>
              <?php if( $logo_url ) : ?>
                <div class="logo-image text-center">
                  <img src="<?php echo $logo_url; ?>" alt="<?php echo $post[ 'post_title' ]; ?>" />
                </div>
              <?php endif; ?>
              <?php if( !empty( $post[ 'ticketURL' ] ) ) : ?>
                <a href="<?php echo $post[ 'ticketURL' ]; ?>" class="btn btn-default"><?php _e( 'Buy Ticket', wp_festival( 'domain' ) ); ?></a>
              <?php endif; ?>
              <div class="row location">
                <div class="col-md-10 col-md-offset-1">
                  <div class="row">
                    <div class="col-md-4">
                      <h5 style="<?php echo $tcolor; ?>" class="h-color text-center"><?php _e( 'Date', wp_festival( 'domain' ) ); ?></h5>
                      <span style="<?php echo $fcolor; ?>"><?php echo date( 'l, F jS', strtotime( $post[ 'startDateTime' ] ) ); ?></span>
                    </div>
                    <div class="col-md-4">
                      <h5 style="<?php echo $tcolor; ?>" class="h-color text-center"><?php _e( 'Time', wp_festival( 'domain' ) ); ?></h5>
                      <span class="text-center" style="<?php echo $fcolor; ?>"><?php echo date( 'g:i a', strtotime( $post[ 'startDateTime' ] ) ); ?></span>
                    </div>
                    <div class="col-md-4">
                      <h5 style="<?php echo $tcolor; ?>" class="h-color text-center"><?php _e( 'Location', wp_festival( 'domain' ) ); ?></h5>
                      <span class="text-center" style="<?php echo $fcolor; ?>"><?php echo $post[ 'locationAddress' ]; ?></span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-8 col-md-offset-2">
                  <div class="text-center content" style="<?php echo $dcolor; ?>"><?php echo $post[ 'post_excerpt' ]; ?></div>
                </div>
              </div>
              <?php if( !empty( $post[ 'enabledArtists' ] ) ) : ?>
                <div class="artists clearfix">
                  <?php $counter = 0; ?>
                  <?php foreach( $post[ 'enabledArtists' ] as $artist_id ) : $artist = wp_festival()->get_post_data( $artist_id ); ?>
                    <?php if ( !( $counter % $artist_columns ) ) : ?>
                      <div class="row clearfix">
                    <?php endif; ?>
                    <div class="<?php echo $class_col; ?> <?php echo !( $counter % $artist_columns ) ? $class_offset : ''; ?>">
                      <article class="artist-preview">
                        <?php if ( $enable_links ) : ?>
                          <a href="<?php echo get_permalink( $artist_id ); ?>">
                        <?php endif; ?>
                        <div class="image">
                          <?php $src = wp_festival()->get_artist_image_link( $artist[ 'ID' ], array( 'type' => $artist_image_type, 'width' => $artist_image_width, 'height' => $artist_image_height ) ); ?>
                          <img class="img-responsive" src="<?php echo $src; ?>" alt="<?php echo $artist[ 'post_title' ]; ?>"/>
                          <div class="text-center caption"><?php echo $artist[ 'post_title' ]; ?></div>
                        </div>
                        <?php if ($enable_links) : ?>
                            </a>
                        <?php endif; ?>
                      </article>
                    </div>
                    <?php $counter++; ?>
                    <?php if ( !( $counter % $artist_columns ) ) : ?>
                      </div>
                    <?php endif; ?>
                  <?php endforeach; ?>     
                </div>
              <?php endif; ?>
            </section>
          <?php endwhile; ?>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>