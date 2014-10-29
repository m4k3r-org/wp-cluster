<?php

global $wp_query;

$attachments = $wp_query->data;
?>
<div class="page-photo-gallery">
  <section class="photo-grid">
    <?php
    foreach( $attachments as $id => $attachment ):
      ?>
      <div class="item col-xs-12 col-sm-6 col-md-4">
        <img src="<?php echo $attachment->guid ?>">
        <a href="<?php echo $attachment->guid ?>" class="pg-overlay"><span class="icon-resize"></span></a>
      </div>
    <?php
    endforeach;
    ?>

    <div class="clearfix"></div>

  </section>
</div>

