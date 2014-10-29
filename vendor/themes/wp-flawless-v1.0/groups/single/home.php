<?php
/**
 * Group Forum - Overview, Edit or single Topic Loader.
 *
 *
 *
 * @version 0.3.4
 * @author Usability Dynamics, Inc. <info@usabilitydynamics.com>
 * @package Flawless
 */

 global $bp;
 
?>

<?php get_header( 'buddypress' ); ?>

	<div class="<?php flawless_wrapper_class(); ?>">

    <?php flawless_widget_area('left_sidebar'); ?>

    <div class="<?php flawless_block_class( 'main cfct-block' ); ?>">

      <div class="<?php flawless_module_class( '' ); ?>">

    	<?php if ( bp_has_groups() ) : while ( bp_groups() ) : bp_the_group(); ?>

    	<?php do_action( 'bp_before_group_home_content' ) ?>

      <?php if( groups_get_groupmeta( $bp->groups->current_group->id, 'hide_header' ) != 'true' ): ?>
    	<div class="item-header" role="complementary" group="<?php bp_group_slug(); ?>">
        <?php locate_template( array( 'groups/single/group-header.php' ), true ); ?>
    	</div><!-- .item-header -->
      <?php endif; ?>

      <?php Flawless_BuddyPress::render_navigation(); ?>        

    	<div id="item-body" class="item-body">
 
        <?php do_action( 'bp_before_group_body' );

        if ( bp_is_group_admin_page() && bp_group_is_visible() ) :
        	locate_template( array( 'groups/single/admin.php' ), true );

        elseif ( bp_is_group_members() && bp_group_is_visible() ) :
        	locate_template( array( 'groups/single/members.php' ), true );

        elseif ( bp_is_group_invites() && bp_group_is_visible() ) :
        	locate_template( array( 'groups/single/send-invites.php' ), true );

        	elseif ( bp_is_group_forum() && bp_group_is_visible() && bp_is_active( 'forums' ) && bp_forums_is_installed_correctly() ) :
            locate_template( array( 'groups/single/forum.php' ), true );

        elseif ( bp_is_group_membership_request() ) :
        	locate_template( array( 'groups/single/request-membership.php' ), true );

        elseif ( bp_group_is_visible() && bp_is_active( 'activity' ) ) :
        	locate_template( array( 'groups/single/activity.php' ), true );

        elseif ( bp_group_is_visible() ) :
        	locate_template( array( 'groups/single/members.php' ), true );

        elseif ( !bp_group_is_visible() ) :
        	// The group is not visible, show the status message

        	do_action( 'bp_before_group_status_message' ); ?>

        	<div id="message" class="info">
            <p><?php bp_group_status_message(); ?></p>
        	</div>

        	<?php do_action( 'bp_after_group_status_message' );

        else :
        	// If nothing sticks, just load a group front template if one exists.
        	locate_template( array( 'groups/single/front.php' ), true );

        endif;

        do_action( 'bp_after_group_body' ); ?>

    	</div><!-- #item-body -->

    	<?php do_action( 'bp_after_group_home_content' ); ?>

    	<?php endwhile; endif; ?>

      </div><!-- .cfct-module  -->
    </div><!-- .main  -->

	 <?php flawless_widget_area('right_sidebar'); ?>

	</div><!-- #content -->

<?php get_footer( 'buddypress' ); ?>
