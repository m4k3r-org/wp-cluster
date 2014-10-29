<?php
/**
 * Share Buttons
 *
 * @author Usability Dynamics
 */
?>
<ul class="share-buttons">
  <li><a target="_blank" href="http://www.facebook.com/sharer.php?u=<?php the_permalink();?>" class="facebook-share" title="<?php _e( 'Share on Facebook', wp_festival2( 'domain' ) ); ?>"><?php _e( 'Share on Facebook', wp_festival2( 'domain' ) ); ?></a></li>
  <li><a target="_blank" href="http://twitter.com/home/?status=<?php echo urlencode( get_the_title() );?>%20<?php the_permalink();?>" class="twitter-share" title="<?php _e( 'Share on Twitter', wp_festival2( 'domain' ) ); ?>"><?php _e( 'Share on Twitter', wp_festival2( 'domain' ) ); ?></a></li>
  <li><a target="_blank" href="https://plusone.google.com/_/+1/confirm?hl=en&url=<?php the_permalink();?>" class="google-share" title="<?php _e( 'Share on Google', wp_festival2( 'domain' ) ); ?>"><?php _e( 'Share on Google', wp_festival2( 'domain' ) ); ?></a></li>
</ul>
