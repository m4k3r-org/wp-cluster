<?php get_header( 'taxonomy' ) ?>

<?php get_template_part( 'attention', 'taxonomy' ); ?>

<?php $term = new \DiscoDonniePresents\EventTaxonomy(); ?>

<div class="<?php flawless_wrapper_class( 'tabbed-content' ); ?>">

  <div class="cfct-block sidebar-left span4 first visible-desktop">
    <div class="cfct-module" style="padding: 0; margin: 0;">

    <ul class="dd_side_panel_nav">

      <li class="visible-desktop link first ui-tabs-selected"><a href="#section_event_details"><i class="icon-info-blue icon-dd"></i> Info</a></li>

      <li class="visible-desktop link">
        <a href="#section_event">
          <i class="icon-hdp_event icon-dd"></i> <?php _e('Events'); ?>
          <span class="comment_count"><?php echo count( $term->events() ); ?></span>
        </a>
      </li>

      <li class="visible-desktop link">
        <a href="#section_hdp_photo_gallery">
          <i class="icon-hdp_photo_gallery icon-dd"></i> <?php _e('Photos'); ?>
          <span class="comment_count"><?php echo count( $term->photos() ); ?></span>
        </a>
      </li>

      <li class="visible-desktop link">
        <a href="#section_hdp_video">
          <i class="icon-hdp_video icon-dd"></i> <?php _e('Videos'); ?>
          <span class="comment_count"><?php echo count( $term->videos() ); ?></span>
        </a>
      </li>

    </ul>

    <div class="visible-desktop" style="height: 50px;"></div>

    </div>
  </div>

  <div class="<?php flawless_block_class( 'main cfct-block span8' ); ?>">

    <div class="<?php flawless_module_class( 'taxonomy-archive' ); ?>">

      <div id="section_event_details">

        <header class="entry-title-wrapper term-title-wrapper">
          <?php flawless_breadcrumbs(); ?>
          <h1 class="entry-title"><?php echo $term->_term->name; ?></h1>
        </header>

        <div class="entry-content clearfix">

          <?php if( $image ) { ?>
            <div class="poster-iphone hidden-desktop">
              <?php echo $image; ?>
            </div>
            <hr class="hidden-desktop"/>
          <?php } ?>

          <?php if( term_description() != '' ) { ?>
            <div class="category_description taxonomy">
            <?php echo do_shortcode( term_description() ); ?>
            </div>
            <hr class="dotted visible-desktop" style="margin-top:5px;"/>
          <?php
          } ?>

        </div>

      </div>

      <div id="section_event">
        <h1><?php echo $term->_term->name; ?> <?php _e('Events'); ?></h1>

        <ul id="hdp_results_header_event" class="hdp_results_header clearfix">
          <li class="hdp_event_time">Date</li>
          <li class="hdp_event_name">Name</li>
          <li class="hdp_event_city">City</li>
          <li class="hdp_event_state">State</li>
        </ul>

        <div id="dynamic_filter" class="dynamic_filter df_element df_top_wrapper df_element df_top_wrapper clearfix" dynamic_filter="hdp_event">
          <div class="df_element hdp_results clearfix">
            <ul class="df_element hdp_results_items">

              <?php if ( $term->events() ): ?>

              <?php
                foreach( $term->events() as $event ) {
                  include( locate_template('templates/loop/event.php') );
                }
              ?>

              <?php else: ?>

              <li><?php _e( 'No events found' ); ?></li>

              <?php endif; ?>

            </ul>
          </div>
        </div>

      </div>

      <div id="section_hdp_photo_gallery">
        <h1><?php echo $term->_term->name; ?> <?php _e('Photos'); ?></h1>

        <div id="dynamic_filter" class="dynamic_filter df_element df_top_wrapper df_element df_top_wrapper clearfix" dynamic_filter="hdp_photo_gallery">
          <div class="df_element hdp_results clearfix">
            <ul class="df_element hdp_results_items">

              <?php if ( $term->photos() ): ?>

              <?php
                foreach( $term->photos() as $photo ) {
                  include( locate_template('templates/loop/imagegallery.php') );
                }
              ?>

              <?php else: ?>

              <li><?php _e( 'No photos found' ); ?></li>

              <?php endif; ?>

            </ul>
          </div>
        </div>

      </div>

      <div id="section_hdp_video">
        <h1><?php echo $term->_term->name; ?> <?php _e('Videos'); ?></h1>

        <div id="dynamic_filter" class="dynamic_filter df_element df_top_wrapper df_element df_top_wrapper clearfix" dynamic_filter="hdp_video">
          <div class="df_element hdp_results clearfix">
            <ul class="df_element hdp_results_items">

              <?php if ( $term->videos() ): ?>

              <?php
                foreach( $term->videos() as $video ) {
                  include( locate_template('templates/loop/videoobject.php') );
                }
              ?>

              <?php else: ?>

              <li><?php _e( 'No videos found' ); ?></li>

              <?php endif; ?>

            </ul>
          </div>
        </div>

      </div>


    </div>

  </div>

</div>

<?php get_footer(); ?>
