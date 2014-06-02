<?php
$permalink = get_permalink($photo->post('ID'));
?>

<li id="df_id_<?php echo $photo->post('ID'); ?>" class="hdp_results_item">
  <ul class="df_result_data">
    <li attribute_key="raw_html">
      <ul>

        <li>

          <ul class="hdp_photo clearfix">
            <li class="hdp_photo_thumbnail"><a href="<?php echo $permalink; ?>" title="Photos from <?php echo $photo->post('post_title'); ?>"><div class="overlay"></div><img src="<?php echo array_shift( wp_get_attachment_image_src( $photo->meta('primaryImageOfPage') , 'hd_small')); ?>" alt="<?php echo $photo->post('post_title'); ?>"/></a></li>
            <li class="hdp_photo_title"><a href="<?php echo $permalink; ?>" title="Photos from <?php echo $photo->post('post_title'); ?>"><?php echo $photo->post('post_title'); ?></a></li>
            <li class="hdp_photo_date"><?php echo $photo->event()->meta('eventDateHuman'); ?></li>
            <li class="hdp_photo_location"><?php echo $photo->event()->venue()->meta('locationAddress'); ?></li>
          </ul>

        </li>

      </ul>
    </li>
  </ul>
</li>
