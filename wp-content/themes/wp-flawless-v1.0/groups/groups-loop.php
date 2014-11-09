<?php

/**
 * BuddyPress - Groups Loop
 *
 * Querystring is set via AJAX in _inc/ajax.php - bp_dtheme_object_filter()
 *
 * @package BuddyPress
 * @subpackage bp-default
 */

?>

<?php do_action( 'bp_before_groups_loop' ); ?>

<?php if ( bp_has_groups( bp_ajax_querystring( 'groups' ) ) ) : ?>


  <?php bp_groups_pagination_count(); ?>    
  <?php bp_groups_pagination_links(); ?>	


	<?php do_action( 'bp_before_directory_groups_list' ); ?>

	<ul id="groups-list" class="groups-list item-list" role="main">

	<?php while ( bp_groups() ) : bp_the_group(); ?>

    <li class="list-item cfct-block">
      <div class="cfct-module">
        
        <div class="item-avatar">
          <a href="<?php bp_group_permalink(); ?>"><?php bp_group_avatar(  array( 'type' => 'full' )  ); ?></a>
        </div>

        <div class="item">
          <div class="item-title"><a href="<?php bp_group_permalink(); ?>"><?php bp_group_name(); ?></a></div>
          <div class="item-meta"><span class="activity"><?php printf( __( 'active %s', 'buddypress' ), bp_get_group_last_active() ); ?></span></div>

          <div class="item-desc"><?php bp_group_description_excerpt(); ?></div>

          <?php do_action( 'bp_directory_groups_item' ); ?>

        </div>

        <div class="action">

          <?php do_action( 'bp_directory_groups_actions' ); ?>

          <div class="meta">

            <?php bp_group_type(); ?> / <?php bp_group_member_count(); ?>

          </div>

        </div>

    	</div><?php /* .cfct-module */ ?>
    </li><?php /* .list-item */ ?>

	<?php endwhile; ?>

	</ul>

	<?php do_action( 'bp_after_directory_groups_list' ); ?>

	<div class="page-bottom pagination clearfix">

    <div class="group-dir-count-bottom page-count">
    	<?php bp_groups_pagination_count(); ?>
    </div>

    <div class="group-dir-pag-bottom pagination-links">
    	<?php bp_groups_pagination_links(); ?>
    </div>

	</div>

<?php else: ?>

	<div id="message" class="info">
    <p><?php _e( 'There were no groups found.', 'buddypress' ); ?></p>
	</div>

<?php endif; ?>

<?php do_action( 'bp_after_groups_loop' ); ?>
