<?php get_header(); ?>

<?php get_template_part( 'attention', 'artist' ); ?>

<?php
  $credit = new \DiscoDonniePresents\Credit( get_the_ID(), false ); the_post();
  echo '<pre>'; print_r( $credit ); echo '</pre>';
?>

<?php $image = wp_get_attachment_image( $credit->meta('logo'), $size = 'sidebar_poster' ); ?>

<div class="<?php flawless_wrapper_class( 'tabbed-content' ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>" itemscope itemtype="http://schema.org/Promoter">

  <div class="cfct-block sidebar-left span4 first visible-desktop">
    <div class="cfct-module" style="padding: 0; margin: 0;">

    <div class="visible-desktop dd_featured_image_wrap <?php echo $image ? 'have_image' : 'no_image'; ?>">
      <?php echo $image; ?>
    </div>

    <ul class="dd_side_panel_nav">

      <li class="visible-desktop link first ui-tabs-selected"><a href="#section_credit_details"><i class="icon-info-blue icon-dd"></i> Info</a></li>

      <li class="visible-desktop link">
        <a href="#section_photos">
          <i class="icon-hdp_photo_gallery icon-dd"></i> <?php _e('Photos'); ?>
          <span class="comment_count"><?php echo count( $credit->photos() ); ?></span>
        </a>
      </li>

      <li class="visible-desktop link">
        <a href="#section_videos">
          <i class="icon-hdp_video icon-dd"></i> <?php _e('Videos'); ?>
          <span class="comment_count"><?php echo count( $credit->videos() ); ?></span>
        </a>
      </li>

    </ul>

    <ul class="dd_side_panel_actions">
      <?php if( $credit->meta('officialLink') ) { ?>
        <li class=""><a class="btn btn-purple" href="<?php echo $credit->meta('officialLink'); ?>">Official Page</a></li>
      <?php } ?>
    </ul>

    <div class="visible-desktop" style="height: 50px;"></div>

    </div>
  </div>

  <div class="<?php flawless_block_class( 'main cfct-block span8' ); ?>">

    <div class="<?php flawless_module_class( 'taxonomy-archive' ); ?>">

      <div id="section_credit_details">

        <header class="entry-title-wrapper term-title-wrapper">
          <?php flawless_breadcrumbs(); ?>
          <h1 class="entry-title"><?php echo $credit->post('post_title'); ?></h1>
        </header>

        <div class="entry-content clearfix">

          <?php if( $image ) { ?>
            <div class="poster-iphone hidden-desktop">
              <?php echo $image; ?>
            </div>
            <hr class="hidden-desktop"/>
          <?php } ?>

          <div class="category_description taxonomy">
          <?php the_content(); ?>
          </div>
          <hr class="dotted visible-desktop" style="margin-top:5px;"/>

          <?php if ( $credit->meta( 'socialLinks' ) ) { ?>
          <ul class="tax_meta">
            <?php foreach( $credit->meta( 'socialLinks' ) as $link ) : ?>
            <li><a href="<?php echo $link; ?>" target="_blank"><?php echo $link; ?></a></li>
            <?php endforeach; ?>
          </ul>
          <?php } ?>

        </div>

      </div>

      <div id="section_photos">
        <h1><?php echo $credit->post('post_title'); ?> <?php _e('Photos'); ?></h1>



      </div>

      <div id="section_videos">
        <h1><?php echo $credit->post('post_title'); ?> <?php _e('Photos'); ?></h1>



      </div>

    </div>

  </div>

</div>

<?php get_footer(); ?>
