</div> <!-- #doc -->
<?php
$menu = new Spectacle_Navigation_Builder();
?>

<?php if( has_nav_menu( 'footer-navigation' ) || has_nav_menu( 'footer-profiles-navigation' ) ): ?>
  <footer>

    <?php if( has_nav_menu( 'footer-navigation' ) ): ?>
      <div class="footer-navigation">
        <?php
        echo $menu->get( 'footer-navigation' );
        ?>
      </div>
    <?php endif; ?>

    <?php if( has_nav_menu( 'footer-profiles-navigation' ) ): ?>
      <div class="footer-profile-navigation">
        <ul>
          <?php
          $social_menu = $menu->get( 'footer-profiles-navigation', false );
          ?>
          <?php foreach( $social_menu as $item ) : ?>
            <?php if( in_array( $item->post_title, array( 'facebook', 'twitter', 'youtube', 'instagram' ) ) ): ?>
              <li>
                <a href="<?php echo $item->url; ?>">
                  <span class="icon-spectacle-<?php echo $item->post_title; ?>"></span>
                </a>
              </li>
            <?php endif; ?>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

  </footer>
<?php endif; ?>

<?php if( has_nav_menu( 'main-navigation' ) ): ?>

  <div class="navigation-overlay overlay">

    <a href="#" class="overlay-close">
      <span class="icon-spectacle-close"></span>
      Close
    </a>

    <div class="overlay-content">
      <nav class="clearfix">
        <?php
        echo $menu->get( 'main-navigation' );
        ?>
      </nav>
    </div>
  </div>

<?php endif; ?>

<?php
$share_count = do_shortcode('[social_share_count total="true" url="http://' .$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] .'"]');

$share_count = json_decode( $share_count, true );

foreach ($share_count as $key => $value )
{
  if ( ($value >= 1000) && ($value < 1000000) )
  {
    $share_count[ $key ] = number_format( $value / 1000, 1) .'k';
  }
  elseif ( ($value >= 1000000) && ($num < 1000000000) )
  {
    $share_count[ $key ] = number_format( $value / 1000000, 1) . 'm';
  }
}

$image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');
if (empty($image[0])) {
  $image[0] = get_post_meta(get_option('page_for_posts'), 'headerImage', true);
}

?>
<div class="share-overlay overlay">
  <a href="#" class="overlay-close">
    <span class="icon-spectacle-close"></span>
    Close
  </a>

  <div class="overlay-content">

    <div class="share-count">
      <h2><?php echo $share_count['total']; ?></h2>
      <h3>Total Shares</h3>
    </div>

    <div class="share-wrapper clearfix">
      <a href="https://twitter.com/intent/tweet?original_referer=http://<?php echo $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; ?>&text=<?php wp_title('|', true, 'right'); ?>&url=http://<?php echo $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; ?>" target="_blank" class="twitter">
        <span class="icon-spectacle-twitter"></span>

        <em><?php echo $share_count['twitter']; ?></em>
      </a>

      <a href="https://www.facebook.com/sharer/sharer.php?u=http://<?php echo $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; ?>" target="_blank" class="facebook">
        <span class="icon-spectacle-facebook"></span>

        <em><?php echo $share_count['facebook']; ?></em>
      </a>

      <a href="https://plus.google.com/share?url=http://<?php echo $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; ?>" target="_blank" class="google-plus">
        <span class="icon-spectacle-google-plus"></span>

        <em><?php echo $share_count['google_plus']; ?></em>
      </a>

      <a href="http://pinterest.com/pin/create/button/?url=http://<?php echo $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; ?>&media=<?php echo $image[0] ?>&description=<?php wp_title('|', true, 'right'); ?>" target="_blank" class="pinterest">
        <span class="icon-spectacle-pinterest"></span>

        <em><?php echo $share_count['pinterest']; ?></em>
      </a>
    </div>
  </div>

</div>

<?php wp_footer(); ?>
</body>
</html>