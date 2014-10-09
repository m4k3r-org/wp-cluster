<div class="organizer-item <?php if( $background ) echo 'organizer-item-darker'; ?>">
  <div class="organizer-photo-container">
    <img src="<?php echo $image_source; ?>">
  </div>
  <?php if ( !empty( $title ) ): ?>
    <h3><?php echo $title; ?></h3>
  <?php endif; ?>
  <p><?php echo $description; ?></p>

  <?php if ( !empty( $url ) ): ?>
    <a href="<?php echo $url; ?>" class="button">Learn More</a>
  <?php endif; ?>
</div>