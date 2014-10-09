<div class="organizer-item <?php if( $is_sponsor_leadin )  echo 'sponsor-lead-in'; ?> <?php if( $background ) echo 'organizer-item-darker'; ?>">

  <?php if ( !empty($image_source) ): ?>
  <div class="organizer-photo-container">
      <img src="<?php echo $image_source; ?>">
  </div>
  <?php endif; ?>

  <?php if ( !empty( $title ) ): ?>
    <h3><?php echo $title; ?></h3>
  <?php endif; ?>

  <?php if ( !empty( $description ) ): ?>
    <p><?php echo $description; ?></p>
  <?php endif; ?>

  <?php if ( !empty( $url ) ): ?>
    <a href="<?php echo $url; ?>" class="button"><?php echo $button_text; ?></a>
  <?php endif; ?>

</div>