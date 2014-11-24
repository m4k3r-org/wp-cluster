<?php if( $is_sponsor_leadin ) : ?>

  <div class="organizer-item sponsor-lead-in <?php if( $background ) echo 'organizer-item-darker'; ?>">
    <div class="leadin-container">
      <?php if( !empty( $title ) ): ?>
        <h3><?php echo $title; ?></h3>
      <?php endif; ?>

      <?php if( !empty( $url ) ): ?>
        <a href="<?php echo $url; ?>" class="button"><?php echo $button_text; ?></a>
      <?php endif; ?>
    </div>

  </div>

<?php else: ?>

  <div class="organizer-item <?php if( $background ) echo 'organizer-item-darker'; ?>">

    <?php if( !empty( $image_source ) ): ?>
      <div class="organizer-photo-container">
        <img src="<?php echo $image_source; ?>">
      </div>
    <?php endif; ?>

    <?php if( !empty( $title ) ): ?>
      <div class="organizer-title-container">
        <h3><?php echo $title; ?></h3>
      </div>
    <?php endif; ?>

    <?php if( !empty( $description ) ): ?>
      <div class="organizer-description-container">
        <p><?php echo $description; ?></p>
      </div>
    <?php endif; ?>

    <?php if( !empty( $url ) ): ?>
      <div class="organizer-url-container">
        <a href="<?php echo $url; ?>" class="button"><?php echo $button_text; ?></a>
      </div>
    <?php endif; ?>

  </div>

<?php endif; ?>



