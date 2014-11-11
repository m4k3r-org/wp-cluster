<?php
/**
 * Menufication additional elements
 */

$logo = ( $logo = get_theme_mod( 'menufication_logo' ) ) ? $logo : false;

?>
<?php if ( $logo ) : ?>
<div id="menufication_block_logo">
  <a href="<?php echo site_url(); ?>">
    <img id="menufication_logo" class="img-responsive" src="<?php echo $logo; ?>" alt="" />
  </a>
</div>
<?php endif; ?>

<div id="menufication_block_tickets_url">
  <a class="btn btn-default" role="button" href="<?php echo wp_festival2()->get( 'configuration.links.buy_tickets', '#' ); ?>" data-track><?php _e( 'Buy Tickets', wp_festival2( 'domain' ) ); ?></a>
</div>

<div id="menufication_block_social">
  <?php echo wp_festival2()->nav( 'social', 2 ); ?>
</div>

