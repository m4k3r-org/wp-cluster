<?php get_header(); ?>

<?php get_template_part( 'attention', 'artist' ); ?>

<?php
  $artist = new \DiscoDonniePresents\Artist( get_the_ID(), false ); the_post();
  //echo '<pre>'; print_r( $artist->meta() ); echo '</pre>';
?>

<?php $image = wp_get_attachment_image( $artist->meta('logo'), $size = 'sidebar_poster' ); ?>

<div class="<?php flawless_wrapper_class( 'tabbed-content' ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>" itemscope itemtype="http://schema.org/Artist">

  <div class="cfct-block sidebar-left span4 first visible-desktop">
    <div class="cfct-module" style="padding: 0; margin: 0;">

    <div class="visible-desktop dd_featured_image_wrap <?php echo $image ? 'have_image' : 'no_image'; ?>">
      <?php echo $image; ?>
    </div>

    <ul class="dd_side_panel_nav">

      <li class="visible-desktop link first ui-tabs-selected"><a href="#section_event_details"><i class="icon-info-blue icon-dd"></i> Info</a></li>

      <li class="visible-desktop link">
        <a href="#section_event">
          <i class="icon-hdp_event icon-dd"></i> <?php _e('Events'); ?>
          <span class="comment_count"><?php echo count( $artist->events() ); ?></span>
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
          <h1 class="entry-title"><?php echo $artist->post('post_title'); ?></h1>
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

        </div>

      </div>

      <div id="section_event">
        <h1><?php echo $artist->post('post_title'); ?> <?php _e('Events'); ?></h1>

        <ul id="hdp_results_header_event" class="hdp_results_header clearfix">
          <li class="hdp_event_time">Date</li>
          <li class="hdp_event_name">Name</li>
          <li class="hdp_event_city">City</li>
          <li class="hdp_event_state">State</li>
        </ul>

        <div id="dynamic_filter" class="dynamic_filter df_element df_top_wrapper df_element df_top_wrapper clearfix" dynamic_filter="hdp_event">
          <div class="df_element hdp_results clearfix">
            <ul class="df_element hdp_results_items">

              <?php if ( $artist->events() ): ?>

              <?php
                foreach( $artist->events() as $event ) {
                  include( locate_template('templates/loop/event.php') );
                }
              ?>

              <?php endif; ?>

            </ul>
          </div>
        </div>

      </div>

    </div>

  </div>

</div>

<?php get_footer(); ?>
