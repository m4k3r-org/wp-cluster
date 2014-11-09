<?php
/**
 * Render Listings Based on Query
 *
 * @note Don't forget to rewind_posts()
 */
?>

<div id="upcomingEvents" class="entry-events clearfix">
  <?php if( $title ): ?>
  <h2><?php echo $title; ?></h2>
  <?php endif; ?>
  <?php while ( $query->have_posts() ): $query->the_post(); ?>
  <?php get_template_part( 'templates/article/listing', get_post_type() ); ?>
  <?php endwhile;  wp_reset_query(); ?>
  <?php if ( $see_all ): ?>
    <p class="readMore">
      <a data-period="<?php echo $period; ?>"
         data-limit="<?php echo $limit; ?>"
         data-direction="<?php echo $direction; ?>"
         data-order_by="<?php echo $order_by; ?>"
         data-city="<?php echo $city; ?>"
         data-archive_url="<?php echo get_post_type_archive_link( 'events' ); ?>"
         class="load-more-events"
         href="javascript:void(0);">
           <?php _e( 'Load More', 'carrington-build' ); ?>
      </a>
    </p>
  <?php endif; ?>
</div>