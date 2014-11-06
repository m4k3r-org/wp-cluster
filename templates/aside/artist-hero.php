<?php
/**
 * Advanced Hero
 * Shows parallax block
 * @author Usability Dynamics
 * @module wp-escalade
 * @since wp-escalade 0.1.0
 */

global $wp_query;

extract( $data = wp_festival2()->extend( array(
  // Artist data
  'title' => get_the_title(),
  'bg_img_src' => wp_festival2()->get_artist_image_link( get_the_ID(), array(
      'type' => 'landscapeImage',
      'width' => 890,
      'height' => 460
    ) ),
  'art_img_src' => wp_festival2()->get_artist_image_link( get_the_ID(), array(
      'type' => 'headshotImage',
      'width' => 150,
      'height' => 150
    ) ),

), (array) $wp_query->data[ 'artist-hero' ] ) );

$artist = wp_festival2()->get_post_data( get_the_ID() );
$perfomance = !empty( $artist[ 'perfomances' ][ 0 ] ) ? $artist[ 'perfomances' ][ 0 ] : false;
//* Get information about perfomance */
$pdate = $perfomance ? strtotime( $perfomance[ 'startDateTime' ] ) : false;
$pday = ( $pdate && ( $date_format = get_option( 'date_format' ) ) ) ? date( $date_format, $pdate ) : false;
$ptime = ( $pdate && (int) date( 'G', $pdate ) && ( $time_format = get_option( 'time_format' ) ) ) ? date( $time_format, $pdate ) : false;?>

<div class="container">
  <div class="row">
    <div class="col-xs-12">
      <div class="artist-profile-photo">
        <?php if (!empty( $title )) : ?>
        <?php if( !empty( $art_img_src ) ) : ?>
          <img src="<?php echo $art_img_src; ?>" alt="<?php echo $title; ?>" />
        <?php endif; ?>
      </div>
      <h2><?php echo $title; ?></h2>
      <?php endif; ?>
      <h4 class="clearfix">
        <time datetime="2014-05-24">

            <span class="date">
              <?php if( ! empty( $artist['startDateTime'] ) ) : ?>
                <span class="icon-date"></span>
                 <?php echo date('l, F j, Y', strtotime($artist['startDateTime']));
              endif;?>
            </span>

            <span class="time">
               <?php if( ! empty( $artist['startDateTime'] ) ): ?>
                 <span class="icon-time"></span>
                 <?php echo date('h:i A', strtotime($artist['startDateTime']));
               endif;?>
            </span>

        </time>

          <span class="stage">
            <?php if( ! empty( $artist['location'] ) ): ?>
              <span class="icon-location"></span>
               <?php echo $artist['location'];
            endif;?>
          </span>
      </h4>

      <div class="buttons">
        <a href="#" class="artist-profile button"><?php _e('Biography', wp_festival2( 'domain' ));?></a>
        <a href="#" class="artist-share button"><?php _e('Follow', wp_festival2( 'domain' )); ?></a>
      </div>

    </div>
  </div>
</div>
<div class="clearfix"></div>

<div class="artist-profile-overlay overlay">

  <a href="#" class="icon-close"></a>

  <div class="overlay-content">

    <div class="artist-profile-photo">
      <?php if (!empty( $title )) : ?>
      <?php if( !empty( $art_img_src ) ) : ?>
        <img src="<?php echo $art_img_src; ?>" alt="<?php echo $title; ?>" />
      <?php endif; ?>
    </div>
    <h2 class="overlay-artist-post-title"><?php echo $title; ?></h2>
    <?php endif; ?>

    <div class="overlay-artist-post-content">
      <p><?php echo nl2br( $artist[ 'post_content' ] ); ?></p>
    </div>

  </div>
  <div class="artist-overlay-bg"></div>
</div>

<?php

$facebook_link = false;
$twitter_link = false;
$instagram_link = false;
$youtube_link = false;

for( $i = 0, $mi = count( $artist[ 'socialLinks' ] ); $i < $mi; $i++ ){
  if( strpos( $artist[ 'socialLinks' ][ $i ], 'facebook' ) ){
    $facebook_link = $artist[ 'socialLinks' ][ $i ];
  }

  if( strpos( $artist[ 'socialLinks' ][ $i ], 'twitter' ) ){
    $twitter_link = $artist[ 'socialLinks' ][ $i ];
  }

  if( strpos( $artist[ 'socialLinks' ][ $i ], 'instagram' ) ){
    $instagram_link = $artist[ 'socialLinks' ][ $i ];
  }

  if( strpos( $artist[ 'socialLinks' ][ $i ], 'youtube' ) ){
    $youtube_link = $artist[ 'socialLinks' ][ $i ];
  }
}

?>

<div class="artist-profile-share-overlay overlay">

  <a href="#" class="icon-close"></a>

  <div class="overlay-content">

    <div class="share-container">
      <?php if( $twitter_link !== false ): ?>
        <a target="_blank" href="<?php echo $twitter_link ?>" class="twitter"><span class="icon-twitter"></span></a>
      <?php endif; ?>

      <?php if( $facebook_link !== false ): ?>
        <a target="_blank" href="<?php echo $facebook_link ?>" class="facebook"><span class="icon-facebook"></span></a>
      <?php endif; ?>

      <?php if( $instagram_link !== false ): ?>
        <a target="_blank" href="<?php echo $instagram_link ?>" class="instagram"><span class="icon-instagram"></span></a>
      <?php endif; ?>

      <?php if( $youtube_link !== false ): ?>
        <a target="_blank" href="<?php echo $youtube_link ?>" class="youtube"><span class="icon-youtube"></span></a>
      <?php endif; ?>
    </div>

  </div>
  
  <div class="artist-overlay-bg"></div>
</div>

<?php /** @todo Fix this later, looks back on pages with lots of content <link rel="stylesheet" href="http://malihu.github.io/custom-scrollbar/jquery.mCustomScrollbar.min.css" />
<script src="http://malihu.github.io/custom-scrollbar/jquery.mCustomScrollbar.concat.min.js"></script> */ ?>