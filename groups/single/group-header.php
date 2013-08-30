<?php
/**
 * Standard Group Header
 *
 *
 *
 * @version 0.3.4
 * @author Usability Dynamics, Inc. <info@usabilitydynamics.com>
 * @package Flawless
 */
 
?>

<?php do_action( 'bp_before_group_header' ); ?>

<div id="item-header-avatar" class="item-header-avatar">
	<a href="<?php bp_group_permalink(); ?>" title="<?php bp_group_name(); ?>"><?php bp_group_avatar(); ?></a>
</div><!-- .item-header-avatar -->

<div id="item-meta" class="item-meta">

	<div class="group_type">
    <span class="group_type"><?php bp_group_type(); ?></span> 
  </div>
  
  <div class="activity">
    <span class="activity"><?php printf( __( 'active %s', 'buddypress' ), bp_get_group_last_active() ); ?></span>
  </div>
  
  <div id="item-buttons" class="item-buttons">
    <?php do_action( 'bp_group_header_actions' ); ?>
  </div><!-- .item-buttons -->

  <?php do_action( 'bp_group_header_meta' ); ?>

  <div id="item-actions" class="item-actions">

    <?php if ( bp_group_is_visible() ) : ?>

      <h3><?php _e( 'Group Admins', 'buddypress' ); ?></h3>

      <?php bp_group_list_admins();

      do_action( 'bp_after_group_menu_admins' );

      if ( bp_group_has_moderators() ) :
        do_action( 'bp_before_group_menu_mods' ); ?>

        <h3><?php _e( 'Group Mods' , 'buddypress' ) ?></h3>

        <?php bp_group_list_mods();

        do_action( 'bp_after_group_menu_mods' );

      endif;

    endif; ?>

  </div><!-- #item-actions -->
  
</div>

<div id="item-header-content" class="item-header-content">

	<h2 class="header-title">
    <a href="<?php bp_group_permalink(); ?>" title="<?php bp_group_name(); ?>"><?php bp_group_name(); ?></a>
  </h2>
  
	<?php do_action( 'bp_before_group_header_meta' ); ?>
  
  <div class="group-description header-note">
    <?php bp_group_description(); ?>
  </div>
</div><!-- .item-header-content -->
  

<?php do_action( 'bp_after_group_header' ); ?>