<?php get_header( 'taxonomy' ) ?>

<?php get_template_part( 'attention', 'taxonomy' ); ?>

<?php $term = new \DiscoDonniePresents\EventTaxonomy(); echo '<pre>';
print_r( $term->photos() );
echo '</pre>'; ?>

<div class="<?php flawless_wrapper_class( 'tabbed-content' ); ?>">

  <div class="cfct-block sidebar-left span4 first visible-desktop">
    <div class="cfct-module" style="padding: 0; margin: 0;">

    <ul class="dd_side_panel_nav">

      <li class="visible-desktop link first ui-tabs-selected"><a href="#section_event_details"><i class="icon-info-blue icon-dd"></i> Info</a></li>

      <li class="visible-desktop link">
        <a href="#section_event">
          <i class="icon-hdp_event icon-dd"></i> <?php _e('Events'); ?>
          <span class="comment_count"><?php echo count( $venue->events( array( 'period' => 'upcoming' ) ) ); ?></span>
        </a>
      </li>

      <li class="visible-desktop link">
        <a href="#section_hdp_photo_gallery">
          <i class="icon-hdp_photo_gallery icon-dd"></i> <?php _e('Photos'); ?>
          <span class="comment_count"><?php echo count( $venue->photos() ); ?></span>
        </a>
      </li>

      <li class="visible-desktop link">
        <a href="#section_hdp_video">
          <i class="icon-hdp_video icon-dd"></i> <?php _e('Videos'); ?>
          <span class="comment_count"><?php echo count( $venue->videos() ); ?></span>
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
          <h1 class="entry-title"><?php echo $term->name; ?></h1>
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
          }

          /** Go through the meta, and print it out */
          if( isset( $meta[ 'formatted_address' ] ) && is_array( $meta[ 'formatted_address' ] ) && isset( $meta[ 'formatted_address' ][ 0 ] ) && !empty( $meta[ 'formatted_address' ][ 0 ] ) ) {
            ?>
            <div class="tax_address">
              <span>Address:</span>
              <?php echo $meta[ 'formatted_address' ][ 0 ]; ?>
            </div> <?php
          }

          /** Do the loop */
          $found = false;
          $map = array(
            'hdp_website_url'     => 'Official Website',
            'hdp_facebook_url'    => 'on Facebook',
            'hdp_twitter_url'     => 'on Twitter',
            'hdp_google_plus_url' => 'on Google Plus',
            'hdp_youtube_url'     => 'on YouTube',
          );
          foreach ($map as $slug => $text){
          if( !is_array( $meta ) ) continue;
          if( !in_array( $slug, array_keys( $meta ) ) ) continue;
          if( !is_array( $meta[ $slug ] ) || !isset( $meta[ $slug ][ 0 ] ) || trim( $meta[ $slug ][ 0 ] ) == '' ) continue;

          if (!$found){
          ?>
          <ul class="tax_meta"> <?php
            $found = true;
            } ?>

            <li class="<?php echo $slug; ?>"><a href="<?php echo addcslashes( trim( $meta[ $slug ][ 0 ] ), '"' ); ?>" target="_blank"><?php echo $term->name; ?> <?php echo $text; ?></a></li> <?php
            }
            if ($found) {
            ?>
            </ul> <?php
        } ?>

        </div>

      </div>

      <?php foreach( (array) $found_content as $post_type => $data ) { ?>
        <div id="section_<?php echo $post_type; ?>">
          <h1><?php echo $term->name; ?> <?php echo $post_types_objects[ $post_type ]->labels->name; ?></h1>

          <?php echo do_shortcode( "[hdp_custom_loop do_shortcode=false post_type={$post_type} per_page=0 {$taxonomy->name}={$term->term_id}] " ); ?>
        </div>
      <?php } ?>


    </div>

  </div>

</div>

<?php get_footer(); ?>
