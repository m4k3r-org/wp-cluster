<?php
$share_count = do_shortcode('[social_share_count total="true" url="http://' .$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] .'"]');
//$share_count = do_shortcode('[social_share_count total="true" url="http://suncitymusicfestival.com"]');
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
  <a href="#" class="icon-close"></a>

  <div class="overlay-content">

		<div class="share-count">
			<h2><?php echo $share_count['total']; ?></h2>
			<h3><?php _e('Total Shares', wp_festival2( 'domain' )); ?></h3>
		</div>

		<div class="share-wrapper clearfix">
			<a href="https://twitter.com/intent/tweet?original_referer=http://<?php echo $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; ?>&text=<?php wp_title('|', true, 'right'); ?>&url=http://<?php echo $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; ?>" target="_blank" class="twitter">
				<span class="icon-twitter"></span>

				<em><?php echo $share_count['twitter']; ?></em>
			</a>

			<a href="https://www.facebook.com/sharer/sharer.php?u=http://<?php echo $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; ?>" target="_blank" class="facebook">
				<span class="icon-facebook"></span>

				<em><?php echo $share_count['facebook']; ?></em>
			</a>

			<a href="https://plus.google.com/share?url=http://<?php echo $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; ?>" target="_blank" class="google-plus">
				<span class="icon-google-plus"></span>

				<em><?php echo $share_count['google_plus']; ?></em>
			</a>

			<a href="http://pinterest.com/pin/create/button/?url=http://<?php echo $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; ?>&media=<?php echo $image[0] ?>&description=<?php wp_title('|', true, 'right'); ?>" target="_blank" class="pinterest">
				<span class="icon-pinterest"></span>

				<em><?php echo $share_count['pinterest']; ?></em>
			</a>
		</div>
  </div>

  <div class="bg"></div>
</div>


<?php /*


    <div class="container share-wrapper">
      <a target="_blank" onclick="popUp = window.open('https://twitter.com/intent/tweet?original_referer=http://<?php echo $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; ?>&text=<?php wp_title('|', true, 'right'); ?>&url=http://<?php echo $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; ?>', 'popupwindow', 'scrollbars=yes,width=800,height=400');
              popUp.focus();
              return false" href="#" class="col-sm-3 col-xs-6 share-btn twitter">
        <i class="icon hover-pop"></i>
      </a>

      <a target="_blank" onclick="popUp = window.open('https://www.facebook.com/sharer/sharer.php?u=http://<?php echo $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; ?>', 'popupwindow', 'scrollbars=yes,width=800,height=400');
              popUp.focus();
              return false" href="#" class="col-sm-3 col-xs-6 share-btn facebook">
        <i class="icon hover-pop"></i>
      </a>

      <a target="_blank" onclick="popUp = window.open('https://plus.google.com/share?url=http://<?php echo $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; ?>', 'popupwindow', 'scrollbars=yes,width=800,height=400');
              popUp.focus();
              return false" href="#" class="col-sm-3 col-xs-6 share-btn google">
        <i class="icon hover-pop"></i>
      </a>


      <?php
      $image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');
      if (empty($image[0])) {
        $image[0] = get_post_meta(get_option('page_for_posts'), 'headerImage', true);
      }
      ?>
      <a target="_blank" onclick="popUp = window.open('http://pinterest.com/pin/create/button/?url=http://<?php echo $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; ?>&media=<?php echo $image[0] ?>&description=<?php wp_title('|', true, 'right'); ?>', 'popupwindow', 'scrollbars=yes,width=800,height=400');
              popUp.focus();
              return false" href="#" class="col-sm-3 col-xs-6 share-btn pinterest">
        <i class="icon hover-pop"></i>
      </a>


    </div>
*/ ?>
