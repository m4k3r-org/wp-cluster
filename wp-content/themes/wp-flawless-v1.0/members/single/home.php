<?php

/**
 * BuddyPress - Users Home
 *
 * @package BuddyPress
 * @subpackage bp-default
 */

 ?>

<?php get_header( 'buddypress' ); ?>

	<div class="<?php flawless_wrapper_class(); ?>">

    <?php flawless_widget_area('left_sidebar'); ?>

    <div class="<?php flawless_block_class( 'main cfct-block' ); ?>">

      <div class="<?php flawless_module_class( '' ); ?>">

    	<?php do_action( 'bp_before_member_home_content' ); ?>

    	<div id="item-header" class="item-header" role="complementary">

        <?php locate_template( array( 'members/single/member-header.php' ), true ); ?>

    	</div><!-- #item-header -->

      <?php Flawless_BuddyPress::render_navigation(); ?>

    	<div class="item-body clearfix">

        <?php do_action( 'bp_before_member_body' );

        if ( bp_is_user_activity() || !bp_current_component() ) :
        	locate_template( array( 'members/single/activity.php'  ), true );

         elseif ( bp_is_user_blogs() ) :
        	locate_template( array( 'members/single/blogs.php'     ), true );

        elseif ( bp_is_user_friends() ) :
        	locate_template( array( 'members/single/friends.php'   ), true );

        elseif ( bp_is_user_groups() ) :
        	locate_template( array( 'members/single/groups.php'    ), true );

        elseif ( bp_is_user_messages() ) :
        	locate_template( array( 'members/single/messages.php'  ), true );

        elseif ( bp_is_user_profile() ) :
        	locate_template( array( 'members/single/profile.php'   ), true );

        elseif ( bp_is_user_forums() ) :
        	locate_template( array( 'members/single/forums.php'    ), true );

        elseif ( bp_is_user_settings() ) :
        	locate_template( array( 'members/single/settings.php'  ), true );

        // If nothing sticks, load a generic template
        else :
        	locate_template( array( 'members/single/plugins.php'   ), true );

        endif;

        do_action( 'bp_after_member_body' ); ?>

    	</div><!-- #item-body -->

    <?php do_action( 'bp_after_member_home_content' ); ?>

      </div><!-- .cfct-module  -->
    </div><!-- .main  -->

	 <?php flawless_widget_area('right_sidebar'); ?>

	</div><!-- #content -->

<?php get_footer( 'buddypress' ); ?>
