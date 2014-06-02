<?php
$permalink = get_permalink($video->post('ID'));
?>

<li id="df_id_<?php echo $video->post('ID'); ?>" class="hdp_results_item">
  <ul class="df_result_data">
    <li attribute_key="raw_html">
      <ul>

        <li>

          <ul class="hdp_video clearfix">
            <li class="hdp_video_thumbnail"><a href="<?php echo $permalink; ?>" title="Photos from <?php echo $video->post('post_title'); ?>"><div class="overlay"></div><img src="<?php echo array_shift( wp_get_attachment_image_src( $video->meta('primaryImageOfPage') , 'hd_small') ); ?>" alt="<?php echo $video->post('post_title'); ?>"/></a></li>
            <li class="hdp_video_title"><a href="<?php echo $permalink; ?>" title="Photos from <?php echo $video->post('post_title'); ?>"><?php echo $video->post('post_title'); ?></a></li>
            <li class="hdp_video_date"><?php echo $video->event()->meta('eventDateHuman'); ?></li>
            <li class="hdp_video_location"><?php echo $video->event()->venue()->meta('locationAddress'); ?></li>
          </ul>

        </li>

      </ul>
    </li>
  </ul>
</li>
