<?php

/**
 * BuddyPress - Members Directory
 *
 * @package BuddyPress
 * @subpackage bp-default
 */

?>

<?php get_header( 'buddypress' ); ?>

<?php do_action( 'bp_before_directory_members_page' ); ?>

<div class="<?php flawless_wrapper_class(); ?>">

  <?php flawless_widget_area('left_sidebar'); ?>

  <div class="<?php flawless_block_class( 'main cfct-block' ); ?>">

    <div class="<?php flawless_module_class( '' ); ?>">

    <?php do_action( 'bp_before_directory_members' ); ?>

    <form action="" method="post" id="members-directory-form" class="dir-form">

      <h3><?php _e( 'Members Directory', 'buddypress' ); ?></h3>

      <?php do_action( 'bp_before_directory_members_content' ); ?>

      <?php if( !$flawless['buddypress']['hide_member_search'] ) { ?>
      <div id="members-dir-search" class="dir-search" role="search">
        <?php bp_directory_members_search_form(); ?>
      </div><!-- #members-dir-search -->
      <?php } ?>

      <div class="item-list-tabs" role="navigation">
        <ul class="nav nav-tabs tabs">
          <li class="selected" id="members-all"><a href="<?php echo trailingslashit( bp_get_root_domain() . '/' . bp_get_members_root_slug() ); ?>"><?php printf( __( 'All Members <span class="label notice">%s</span>', 'buddypress' ), bp_get_total_member_count() ); ?></a></li>

          <?php if ( is_user_logged_in() && bp_is_active( 'friends' ) && bp_get_total_friend_count( bp_loggedin_user_id() ) ) : ?>

            <li id="members-personal"><a href="<?php echo bp_loggedin_user_domain() . bp_get_friends_slug() . '/my-friends/' ?>"><?php printf( __( 'My Friends <span class="label notice">%s</span>', 'buddypress' ), bp_get_total_friend_count( bp_loggedin_user_id() ) ); ?></a></li>

          <?php endif; ?>

          <?php do_action( 'bp_members_directory_member_types' ); ?>

        </ul>
      </div><!-- .item-list-tabs -->

      <div class="item-list-tabs" id="subnav" role="navigation">
        <ul class="pills">

          <?php do_action( 'bp_members_directory_member_sub_types' ); ?>

          <li class="members-order-select order-select last filter">

            <label for="members-order-by" class="order-select"><?php _e( 'Order By:', 'buddypress' ); ?>
            <select id="members-order-by" >
              <option value="active"><?php _e( 'Last Active', 'buddypress' ); ?></option>
              <option value="newest"><?php _e( 'Newest Registered', 'buddypress' ); ?></option>

              <?php if ( bp_is_active( 'xprofile' ) ) : ?>

                <option value="alphabetical"><?php _e( 'Alphabetical', 'buddypress' ); ?></option>

              <?php endif; ?>

              <?php do_action( 'bp_members_directory_order_options' ); ?>

            </select>
            </label>
          </li>
        </ul>
      </div>

      <div id="members-dir-list" class="members dir-list">

        <?php locate_template( array( 'members/members-loop.php' ), true ); ?>

      </div><!-- #members-dir-list -->

      <?php do_action( 'bp_directory_members_content' ); ?>

      <?php wp_nonce_field( 'directory_members', '_wpnonce-member-filter' ); ?>

      <?php do_action( 'bp_after_directory_members_content' ); ?>

    </form><!-- #members-directory-form -->

    <?php do_action( 'bp_after_directory_members' ); ?>

    </div><!-- .cfct-module  -->
  </div><!-- .main  -->

 <?php flawless_widget_area('right_sidebar'); ?>

</div><!-- #content -->

<?php do_action( 'bp_after_directory_members_page' ); ?>

<?php get_footer( 'buddypress' ); ?>
