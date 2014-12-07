<?php get_header(); ?>

<?php get_template_part( 'attention', 'video' ); ?>

<?php $video = new \DiscoDonniePresents\VideoObject( get_the_ID(), false ); the_post(); ?>

<div class="<?php flawless_wrapper_class( 'tabbed-content' ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>">

  <div class="cfct-block sidebar-left span4 first">
    <div class="cfct-module" style="padding: 0; margin: 0;">
    
    <?php if ( $video->meta( 'primaryImageOfPage' ) ) : ?>
    <div class="visible-desktop dd_featured_image_wrap <?php echo $video->meta('primaryImageOfPage') ? 'have_image' : 'no_image'; ?>">
      <?php
      $img_src = wp_get_attachment_image_src( $video->meta( 'primaryImageOfPage' ), 'full' );
      echo '<a href="' . $img_src[0] . '">';
      echo wp_get_attachment_image( $video->meta('primaryImageOfPage'), $size = 'sidebar_poster' );
      echo '</a>';
      ?>
    </div>
    <?php endif; ?>

    <ul class="dd_side_panel_nav">
      <li class="visible-desktop link first ui-tabs-active"><a href="#section_video_details"><i class="icon-video icon-dd"></i> <?php _e('Video'); ?></a></li>

      <?php if( post_type_supports( $video->type(), 'comments' ) && $video->post('comment_status') == 'open' ) { ?>
        <li class="visible-desktop link"><a href="#section_comments"><i class="icon-comments-blue icon-dd"></i> Comments</a></li>
      <?php } ?>

      <?php if( $video->event()->venue()->meta('locationGoogleMap') ) { ?>
        <li class="visible-desktop link"><a href="#section_map"><i class="hdp_venue icon-dd"></i> Location Map</a></li>
      <?php } ?>

    </ul>

    <ul class="dd_side_panel_actions">
      <?php if( $video->meta('isBasedOnUrl') ) { ?>
        <li class=""><a class="btn btn-purple" href="<?php echo $video->meta('isBasedOnUrl'); ?>">View on YouTube</a></li>
      <?php } ?>
    </ul>

    <div class="visible-desktop" style="height: 50px;"></div>

    </div>
  </div>

  <div class="<?php flawless_block_class( 'main cfct-block span8' ); ?>">

    <div id="post-<?php the_ID(); ?>" class="<?php flawless_module_class( '' ); ?>">

      <header class="entry-title-wrapper">
        <?php flawless_breadcrumbs(); ?>
        <h1 class="entry-title"><?php echo $video->post('post_title'); ?></h1>
        <p class="event_tagline"><?php echo $video->post('post_excerpt'); ?>
          <span class="event_credit">
            <?php _e('Videos by'); ?> <a href="<?php echo get_permalink( $video->credit()->post('ID') ); ?>" target="_blank"><?php echo $video->credit()->post('post_title'); ?></a>
          </span>
        </p>
      </header>

      <hr class="dotted"/>
      
      <?php if ( $video->meta( 'primaryImageOfPage' ) ) : ?>
      <div class="poster-iphone hidden-desktop">
        <?php echo wp_get_attachment_image( $video->meta('primaryImageOfPage'), $size = 'sidebar_poster' ); ?>
      </div>
      <?php endif; ?>

      <hr class="dotted hidden-desktop"/>

      <div id="section_video_details" class="inner">

        <div class="event_meta_wrapper row-fluid">

          <div class="span6">

            <span class="event_meta_label"><i class="hdp_event_date icon-dd"></i> <?php _e('Date'); ?></span>
            <span class="event_meta_value"><?php echo $video->event()->meta('eventDateHuman'); ?></span>

            <span class="event_meta_label"><i class="hdp_event_time icon-dd"></i> <?php _e('Time'); ?></span>
            <span class="event_meta_value"><?php echo $video->event()->meta('eventTimeHuman'); ?></span>
            
            <?php if( $video->event->taxonomies( 'age-limit' ) != '' ) : ?>
            <span class="event_meta_label"><i class="hdp_age_limit icon-dd"></i> <?php _e('Age Limit'); ?></span>
            <span class="event_meta_value"><?php echo $video->event()->taxonomies( 'age-limit' ); ?></span>
            <?php endif; ?>

            <span class="event_meta_label"><i class="hdp_venue icon-dd"></i> <?php _e('Venue'); ?></span>
            <span class="event_meta_value">
              <a href="<?php echo get_permalink( $video->event()->venue()->post( 'ID' ) ); ?>">
                <?php echo $video->event()->venue()->post( 'post_title' ); ?>
              </a><br />
              <?php echo $video->event()->venue()->taxonomies( 'city' ); ?>, <?php echo $video->event()->venue()->taxonomies( 'state' ); ?>
            </span>

          </div>

          <div class="span6">

            <?php if ( $video->event->promoters() != '' ): ?>
            <span class="event_meta_label"><i class="hdp_artist icon-dd"></i> <?php _e('Promoter'); ?></span>
            <span class="event_meta_value"><?php echo $video->event->promoters(); ?></span>
            <?php endif; ?>
            
            <?php if ( $video->event->taxonomies( 'event-type' ) != '' ): ?>
            <span class="event_meta_label"><i class="hdp_type icon-dd"></i> <?php _e('Type'); ?></span>
            <span class="event_meta_value"><?php echo $video->event()->taxonomies( 'event-type' ); ?></span>
            <?php endif; ?>
            
            <?php if ( $video->event->genre() != '' ): ?>
            <span class="event_meta_label"><i class="hdp_genre icon-dd"></i> <?php _e('Genre'); ?></span>
            <span class="event_meta_value"><?php echo $video->event()->genre(); ?></span>
            <?php endif; ?>

            <?php if( $video->event->tour() ): ?>
            <span class="event_meta_label"><i class="hdp_tour icon-dd"></i> <?php _e('Tour'); ?></span>
            <span class="event_meta_value">
              <a href="<?php echo get_permalink( $video->event()->tour()->post( 'ID' ) ); ?>">
                <?php echo $video->event()->tour()->post( 'post_title' ); ?>
              </a>
            </span>
            <?php endif; ?>
            
            <?php if ( $video->event->artists() != '' ): ?>
            <span class="event_meta_label"><i class="hdp_artist icon-dd"></i> <?php _e('Artist'); ?></span>
            <span class="event_meta_value"><?php echo $video->event()->artists(); ?></span>
            <?php endif; ?>

          </div>

        </div>

        <hr class="dotted"/>

        <div class="entry-content clearfix">

          <?php the_content( 'More Info' ); ?>

        </div>

      </div>

      <?php if( post_type_supports( $video->type(), 'comments' ) && $video->post('comment_status') == 'open' ) { ?>
        <div id="section_comments" class="inner">
        <?php comments_template(); ?>
      </div>
      <?php } ?>

      <?php if( $video->event()->venue()->meta('locationGoogleMap') ) { ?>
        <div id="section_map" class="inner not-for-iphone not-for-ipad">
        <div id="event_location" style="height: 400px; width: 100%;"></div>
      </div>
      <?php } ?>

    </div>

  </div>


  <?php flawless_widget_area( 'right_sidebar' ); ?>

</div>

<?php echo '<script type="text/javascript">var hdp_current_event = jQuery.parseJSON( ' . json_encode( json_encode( $video->event() ) ) . ' ); </script>'; ?>

<?php get_footer(); ?>