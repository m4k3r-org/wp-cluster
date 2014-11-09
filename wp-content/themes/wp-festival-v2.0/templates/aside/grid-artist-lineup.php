<?php
global $wp_query, $_col;
extract( $wp_query->data );

/** Check for title and tagline */
$show_callout = true;
if( !$title && !$tagline ){
  $show_callout = false;
} ?>

<div class="artists-list lineup <?php if( ! $show_callout ) echo 'tier2-artists'; ?>">

  <section class="artist-lineup <?php echo $show_callout ? 'tier-one' : 'tier-two' ?>">
    
    <?php if( $show_callout ): ?>
      <div class="col-xs-12 col-sm-4 callout">
        <div class="callout-content">
          <?php if (isset($title) && $title): ?>
            <h2><?php echo $title; ?></h2>
          <?php endif; ?>
  
          <div class="separator-mini"></div>
          <?php if (isset($tagline) && $tagline): ?>
            <p><?php echo $tagline; ?></p>
          <?php endif; ?>

					<?php if ( (isset($complete_lineup_button)) && ((bool)$complete_lineup_button === true) ): ?>
						<a href="<?php echo $complete_lineup_page_url; ?>" class="button"><?php _e('Complete Lineup', wp_festival2( 'domain' )); ?></a>
					<?php endif; ?>

          <div class="stickit-filler"></div>
        </div>
      </div>
    <?php endif; ?>

    <?php if( $show_callout ) : ?>
     <div class="col-xs-12 col-sm-<?php if( $show_callout ): ?>8<?php else: ?>12<?php endif; ?> main-artists <?php if( !$show_callout ): ?>the-list-tier2<?php endif; ?>">
    <?php endif; ?>
      <?php $_col = $show_callout ? 6 : 4; ?>
      <?php for( $i=0; $i<4; $i++ ) : ?>
        <?php if (have_posts()) : the_post(); ?>
          <?php get_template_part('templates/article/listing-artist', 'lineup'); ?>
        <?php endif; ?>
      <?php endfor; ?>

    <?php if( $show_callout ) : ?>
    </div>
    <?php endif; ?>

  </section>

  <?php if( ! $show_callout ):	?>
    <div class="clearfix"></div>
    <div class="indicator-container">
      <div class="indicator-parent">
        <div class="indicator">
          <span class="icon-indicator"></span>
        </div>
      </div>
    </div>

  <?php endif; ?>


</div>
<div class="clearfix"></div>