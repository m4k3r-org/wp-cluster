<?php
/**
 * Hero Module View
 *
 * @see carrington builder module Hero
 * @author Usability Dynamics
 * @module festival  
 * @since festival 0.1.0
 */
 
global $wp_query; 

extract( $data = wp_festival()->extend( array(
  'image_src' => '',
  'image_alignment' => '',
  'title' => '',
  'content' => '',
  'box_height' => '',
  'id_base' => '',
  'url' => '',
  'fb_like' => false,
  'fb_app_id' => '',
  'tw_share' => false,
  'tw_account' => '',
  'tw_hashtag' => '',
  'gp_share' => false,
), (array)$wp_query->data[ 'hero' ] ) );

?>
<div class="<?php echo $id_base; ?>-image" style="min-height: <?php echo $box_height; ?>px;<?php if (!empty($image_src)) { ?> background-image: url(<?php echo $image_src[0]; ?>); background-position: <?php echo $image_alignment; ?>; background-repeat: no-repeat;<?php } ?>">
	<div class="<?php echo $id_base; ?>-gradient">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="<?php echo $id_base; ?>-wrap" >
            <div class="<?php echo $id_base; ?>-content">
              <div class="row">
                <div class="col-md-7">
                  <?php if (!empty($title)) : ?>
                    <h2 class="cfct-mod-title"><?php echo $title; ?></h2>
                  <?php endif; ?>
                  <?php if (!empty($content)) : ?>
                    <div class="cfct-mod-content"><?php echo $content; ?></div>
                  <?php endif; ?>
                  <?php if (!empty($url)) : ?>
                    <p><a href="<?php echo $url; ?>" class="more-link"><?php _e( 'Read More', wp_festival( 'domain' ) ); ?></a></p>
                  <?php endif; ?>
                </div>
                <div class="col-md-5 share-buttons-wrapper">
                  <ul class="share-buttons-list">
                    <?php if ( $fb_like && !empty( $fb_app_id ) ) : ?>
                      <li class="fb-share">
                        <div class="fb-share-button" data-href="http://developers.facebook.com/docs/plugins/" data-type="button_count"></div>
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
