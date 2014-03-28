<?php
/**
 * Advanced Hero
 * Shows parallax block
 *
 * @author Usability Dynamics
 * @module wp-escalade
 * @since wp-escalade 0.1.0
 */
 
global $wp_query;

extract( $data = wp_festival()->extend( array(
  // Artist data
  'title' => get_the_title(),
  'bg_img_src' => wp_festival()->get_artist_image_link( get_the_ID(), array( 'type' => 'landscapeImage', 'width' => 890, 'height' => 460 ) ),
  'art_img_src' => wp_festival()->get_artist_image_link( get_the_ID(), array( 'type' => 'headshotImage', 'width' => 150, 'height' => 150 ) ),
  'date' => ( wp_festival()->get_artist_perfomance_date( get_the_ID() ) ),
  
  // Styles
  'image_alignment' => 'center center',
  'box_height' => 460,
  
  // Social data
  'fb_like' => true,
  'fb_app_id' => ( wp_festival()->get( 'configuration.social_stream.facebook.application_id' ) ),
  'fb_url' => ( get_permalink( get_the_ID() ) ),
  'tw_share' => true,
  'tw_account' => '', // @todo
  'tw_hashtag' => '', // @todo
  'gp_share' => true,
), (array)$wp_query->data[ 'artist-hero' ] ) );

//echo "<pre>"; print_r( $data ); echo "</pre>"; die();

?>
<div class="artist-hero cfct-module-hero-image" style="min-height: <?php echo $box_height; ?>px;<?php if (!empty($bg_img_src)) { ?> background-image: url(<?php echo $bg_img_src; ?>); background-position: <?php echo $image_alignment; ?>; background-repeat: no-repeat;<?php } ?>">
	<div class="cfct-module-hero-gradient">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="cfct-module-hero-wrap" >
            <div class="cfct-module-hero-content">
              <div class="row">
                <div class="col-md-6">
                  <?php if (!empty($title)) : ?>
                    <div class="row">
                      <?php if( !empty( $art_img_src ) ) : ?>
                        <div class="col-md-4">
                          <img class="img-responsive" src="<?php echo $art_img_src; ?>" alt="<?php echo $title; ?>" />
                        </div>
                        <div class="col-md-8">
                          <h2 class="cfct-mod-title"><?php echo $title; ?></h2>
                        </div>
                      <?php else : ?>
                        <div class="col-md-12">
                          <h2 class="cfct-mod-title"><?php echo $title; ?></h2>
                        </div>
                      <?php endif; ?>
                    </div>
                  <?php endif; ?>
                </div>
                <div class="col-md-6 share-buttons-wrapper">
                  <ul class="share-buttons-list">
                    <?php if ( $fb_like && !empty( $fb_app_id ) ) : ?>
                      <li class="fb-share">
                        <div class="fb-share-button" data-href="<?php echo $fb_url; ?>" data-type="button_count"></div>
                        <div id="fb-root"></div>
                        <script>
                          (function(d, s, id) {
                            var js, fjs = d.getElementsByTagName(s)[0];
                            if (d.getElementById(id)) return;
                            js = d.createElement(s); js.id = id;
                            js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=<?php echo $fb_app_id; ?>";
                            fjs.parentNode.insertBefore(js, fjs);
                          }(document, 'script', 'facebook-jssdk'));
                        </script>
                      </li>
                    <?php endif; ?>
                    <?php if ( $tw_share ) : ?>
                      <li class="tw-share">
                        <a href="https://twitter.com/share" class="twitter-share-button" data-related="<?php echo $tw_account; ?>" data-hashtags="<?php echo $tw_hashtag; ?>"><?php _e( 'Tweet', wp_festival( 'domain' ) ); ?></a>
                        <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
                      </li>
                    <?php endif; ?>
                    <?php if ( $gp_share ) : ?>
                      <li class="gp-share">
                        <div class="g-plusone" data-size="medium"></div>
                        <script type="text/javascript">
                          (function() {
                            var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
                            po.src = 'https://apis.google.com/js/platform.js';
                            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
                          })();
                        </script>
                      </li>
                    <?php endif; ?>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>