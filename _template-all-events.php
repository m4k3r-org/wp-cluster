<?php
/**
 * Template Name: Events
 * Description: A template for Events, Photos and Videos
 *
 * @version 0.10
 * @author Insidedesign
 * @subpackage Flawless
 * @package Flawless
 */

$event = get_event();

if( $event[ 'event_poster_id' ] ) {
  $image_url = wp_get_attachment_image_src( $event[ 'event_poster_id' ], $size = 'full' );
  $image_url = $image_url[0];
}

switch( $event[ 'post_type' ] ) {

  case 'hdp_event':
    $event[ 'main_section_label' ] = 'Event Details';
    $event[ 'main_section_icon' ] = 'icon-events';
  break;

  case 'hdp_video':
    $event[ 'main_section_label' ] = 'Video';
    $event[ 'main_section_icon' ] = 'icon-video';
  break;

  case 'hdp_photo_gallery':
    $event[ 'main_section_label' ] = 'Gallery';
    $event[ 'main_section_icon' ] = 'icon-gallery';
  break;

}

?>

<?php get_header(); ?>

<?php get_template_part('attention', 'post'); ?>

<div class="<?php flawless_wrapper_class( 'tabbed-content' ); ?>">

  <div class="cfct-block sidebar-left span4 first">
    <div class="cfct-module" style="padding: 0; margin: 0;">

    <div class="visible-desktop dd_featured_image_wrap <?php echo $event[ 'event_poster_id' ] ? 'have_image' : 'no_image'; ?>">
      <a href="<?php echo  $image_url; ?>">
      <?php echo wp_get_attachment_image( $event[ 'event_poster_id' ], $size = 'sidebar_poster' ); ?>
      </a>
    </div>

    <ul class="dd_side_panel_nav">
      <li class="visible-desktop link first ui-tabs-active"><a href="#section_event_details"><i class="<?php echo $event[ 'main_section_icon' ]; ?> icon-dd"></i> <?php echo $event[ 'main_section_label' ]; ?></a></li>

      <?php if( post_type_supports( $event['post_type'], 'comments') && $event[ 'comment_status'] == 'open' ) { ?>
      <li class="visible-desktop link"><a href="#section_comments"><i class="icon-comments-blue icon-dd"></i> Comments</a></li>
      <?php } ?>

      <?php if( $event[ 'geo_located' ] ) { ?>
      <li class="visible-desktop link"><a href="#section_map"><i class="hdp_venue icon-dd"></i> Location Map</a></li>
      <?php } ?>

    </ul>

    <ul class="dd_side_panel_actions">
      <?php $time = strtotime( date( 'Y-m-d', current_time('timestamp') ).' 00:00:01 +3 hour' ); if( $event['meta']['hdp_purchase_url'] && strtotime( $event['meta']['hdp_event_date'].' '.$event['meta']['hdp_event_time'] ) > $time ) { ?>
      <li class=""><a class="btn btn-blue" href="<?php echo $event['meta']['hdp_purchase_url']; ?>" <?php if( $event[ 'meta' ][ 'disable_cross_domain_tracking' ] !== 'true' ) { ?>onclick="_gaq.push(['_link', '<?php echo $event['meta']['hdp_purchase_url']; ?>']); return false;"<?php } ?>>Buy Tickets</a></li>
      <?php } ?>

      <?php if( $event['meta']['hdp_facebook_rsvp_url'] ) { ?>
      <li class=""><a class="btn btn-purple" href="<?php echo $event['meta']['hdp_facebook_rsvp_url']; ?>">RSVP On Facebook</a></li>
      <?php } ?>

      <?php if( $event['meta']['hdp_facebook_url'] ) { ?>
      <li class=""><a class="btn btn-purple" href="<?php echo $event['meta']['hdp_facebook_url']; ?>">View Facebook Gallery</a></li>
      <?php } ?>

      <?php if( $event['meta']['hdp_video_url'] ) { ?>
      <li class=""><a class="btn btn-red" href="<?php echo $event['meta']['hdp_video_url']; ?>">Watch at YouTube</a></li>
      <?php } ?>

    </ul>

    <div class="visible-desktop" style="height: 50px;"></div>

    </div>
  </div>

  <div class="<?php flawless_block_class( 'main cfct-block span8' ); ?>">

    <div id="post-<?php the_ID(); ?>" class="<?php flawless_module_class( '' ); ?>">

      <header class="entry-title-wrapper">
          <?php flawless_breadcrumbs(); ?>
          <?php flawless_page_title(); ?>
          <p class="event_tagline"><?php echo $event[ 'post_excerpt' ]; ?> <span class="event_credit"><?php echo $event[ 'attributes' ][ 'print_credit' ]; ?></span></p>
      </header>

      <hr class="dotted" />

      <div class="poster-iphone hidden-desktop">
        <?php echo wp_get_attachment_image( get_post_thumbnail_id( $post_ID ), $size = 'sidebar_poster' ); ?>
      </div>

      <hr class="dotted hidden-desktop" />

      <div id="section_event_details" class="inner">

        <div class="event_meta_wrapper row-fluid"> <?php
          /** Loop through twice, onces for 100's, and once for 200's */
          ksort( $event[ 'summary' ] );
          for ($i = 1; $i <= 2; $i++) { ?>
            <div class="span6"> <?php
              foreach( $event[ 'summary' ] as $key => $row ) {
                if( (int) $i.'00' <= (int) $key && (int) $key <= (int) $i.'99' ){ ?>
                  <span class="event_meta_label" attribute="<?php echo $row[ 'slug' ]; ?>">
                    <i class="<?php echo $row[ 'slug' ]; ?> icon-dd"></i> <?php echo $row[ 'label' ]; ?>
                  </span>
                  <span class="event_meta_value">
                    <?php echo $row[ 'value' ]; ?>
                  </span> <?php
                }
              } ?>
            </div> <?php
          } ?>
        </div>

        <hr class="dotted" />

        <div class="entry-content clearfix">

          <?php the_content( 'More Info' ); ?>

          <?php if( $event['images'] ) { ?>
          <div class="gallery gallery-columns-0">
          <?php foreach( $event['images'] as $image ) { $image_url = wp_get_attachment_image_src( $image->ID, 'full' ); $image_url = $image_url['0']; ?>
            <div class="gallery-item"><a href="<?php echo $image_url; ?>" rel="gallery"><?php echo wp_get_attachment_image( $image->ID, 'gallery' ); ?></a></div>
          <?php } ?>
          </div>
          <?php } ?>

        </div>

      </div>

      <?php if( post_type_supports( $event['post_type'], 'comments') && $event[ 'comment_status'] == 'open' ) { ?>
      <div id="section_comments" class="inner">
        <?php comments_template(); ?>
      </div>
      <?php } ?>

      <?php if ($event[ 'geo_located' ] ) { ?>
      <div id="section_map" class="inner not-for-iphone not-for-ipad">
        <div id="event_location" style="height: 400px; width: 100%;"></div>
      </div>
      <?php } ?>

    </div>

  </div>


  <?php flawless_widget_area('right_sidebar'); ?>

</div>

<?php if( $event[ 'json' ] ) { echo '<script type="text/javascript">var hdp_current_event = jQuery.parseJSON( ' . json_encode( json_encode( $event[ 'json' ] ) ) . ' ); </script>'; } ?>
<?php get_footer() ?>
