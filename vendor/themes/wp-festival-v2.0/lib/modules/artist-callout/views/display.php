<?php
$image_headshot = wp_festival2()->get_artist_image_link( $artist_id, array(
  'type' => 'headshotImage',
  'width' => 1024,
  //  'height' => 960
) );

$image_logo = wp_festival2()->get_artist_image_link( $artist_id, array(
  'type' => 'logoImage',
  'width' => 550,
  'height' => 100
) );

$permalink = get_permalink( $artist_id );
$title = get_the_title( $artist_id );

$share_count = do_shortcode( '[social_share_count total="true" url="' . $permalink . '"]' );
$share_count = json_decode( $share_count, true );

?>
<div class="row artist-callout">
  <div class="col-md-6 col-sm-6 col-lg-6 c4-12 artist-callout-equal-height">
    <div class="artist-image">
      <div class="flip-container">
        <div class="flipper">
          <div class="front">
            <img src="<?php echo $image_headshot; ?>">
            <a href="#" class="share news-single-share"><i class="icon-share"></i></a>
          </div>
          <div class="back">
            <img src="<?php echo $image_headshot; ?>">

            <div class="social-share-overlay">

              <div class="social-share-overlay-content">

                <div class="share-wrapper clearfix">
                  <a href="https://twitter.com/intent/tweet?original_referer=<?php echo $permalink; ?>&text=<?php echo $title; ?>&url=<?php echo $permalink; ?>" target="_blank" class="twitter">
                    <span class="icon-twitter"></span>

                    <em><?php if( empty( $share_count[ 'twitter' ] ) ) $share_count[ 'twitter' ] = 0;
                      echo $share_count[ 'twitter' ] ?></em>
                  </a>

                  <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $permalink ?>" target="_blank" class="facebook">
                    <span class="icon-facebook"></span>

                    <em><?php if( empty( $share_count[ 'facebook' ] ) ) $share_count[ 'facebook' ] = 0;
                      echo $share_count[ 'facebook' ] ?></em>
                  </a>

                  <a href="https://plus.google.com/share?url=<?php echo $permalink; ?>" target="_blank" class="google-plus">
                    <span class="icon-google-plus"></span>

                    <em><?php if( empty( $share_count[ 'google_plus' ] ) ) $share_count[ 'google_plus' ] = 0;
                      echo $share_count[ 'google_plus' ] ?></em>
                  </a>

                  <a href="http://pinterest.com/pin/create/button/?url=<?php echo $permalink; ?>&media=<?php echo $image_headshot; ?>&description=<?php echo $title; ?>" target="_blank" class="pinterest">
                    <span class="icon-pinterest"></span>

                    <em><?php if( empty( $share_count[ 'pinterest' ] ) ) $share_count[ 'pinterest' ] = 0;
                      echo $share_count[ 'pinterest' ] ?></em>
                  </a>
                </div>

              </div>

              <a href="#" class="share-close"><i class="icon-close"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-sm-6 col-lg-6 c4-12 artist-callout-equal-height">
    <div class="artist-info">
      <div class="artist-callout-logo">
        <img src="<?php echo $image_logo; ?>">
      </div>
      <div class="artist-callout-text">
        <?php echo $text; ?>
      </div>
      <div class="info-separator"></div>

      <div class="artist-callout-buttons row">
        <div class="col-md-6 col-sm-12 col-lg-6 c4-12">
          <a class="button button1" target="_blank" href="<?php echo $button1_link ?>"><?php echo $button1_text ?></a>
        </div>
        <div class="col-md-6 col-sm-12 col-lg-6 c4-12">
          <a class="button button2" href="<?php echo $button2_link ?>"><?php echo $button2_text ?></a>
        </div>
      </div>
    </div>
  </div>
</div>
