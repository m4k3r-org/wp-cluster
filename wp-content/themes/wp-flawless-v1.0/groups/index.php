<?php

/**
 * BuddyPress - Groups Directory
 *
 * @package BuddyPress
 * @subpackage bp-default
 */

?>

<?php get_header( 'buddypress' ); ?>

<?php do_action( 'bp_before_directory_groups_page' ); ?>

<div class="<?php flawless_wrapper_class(); ?>">

  <?php flawless_widget_area( 'left_sidebar' ); ?>

  <div class="<?php flawless_block_class( 'main cfct-block' ); ?>">
  
    <div class="<?php flawless_module_class( '' ); ?>">

    <?php do_action( 'bp_before_directory_groups' ); ?>

    <form action="" method="post" id="groups-directory-form" class="dir-form">

      <h1 clas="entry-title"><?php _e( 'Groups Directory', 'buddypress' ); ?><?php if ( is_user_logged_in() && bp_user_can_create_groups() ) : ?> &nbsp;<a class="button" href="<?php echo trailingslashit( bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/create' ); ?>"><?php _e( 'Create a Group', 'buddypress' ); ?></a><?php endif; ?></h1>

      <?php do_action( 'bp_before_directory_groups_content' ); ?>

      <?php if( !$flawless['buddypress']['hide_group_search'] ) { ?>
      <div id="group-dir-search" class="dir-search" role="search">
        <?php bp_directory_groups_search_form() ?>
      </div><!-- #group-dir-search -->
      <?php } ?>

      <div class="item-list-tabs" role="navigation">
        <ul class="nav nav-tabs tabs">
          <li class="selected" id="groups-all"><a href="<?php echo trailingslashit( bp_get_root_domain() . '/' . bp_get_groups_root_slug() ); ?>"><?php printf( __( 'All Groups <span class="label notice">%s</span>', 'buddypress' ), bp_get_total_group_count() ); ?></a></li>

          <?php if ( is_user_logged_in() && bp_get_total_group_count_for_user( bp_loggedin_user_id() ) ) : ?>

            <li id="groups-personal"><a href="<?php echo trailingslashit( bp_loggedin_user_domain() . bp_get_groups_slug() . '/my-groups' ); ?>"><?php printf( __( 'My Groups <span class="label notice">%s</span>', 'buddypress' ), bp_get_total_group_count_for_user( bp_loggedin_user_id() ) ); ?></a></li>

          <?php endif; ?>

          <?php do_action( 'bp_groups_directory_group_filter' ); ?>

        </ul>
      </div><!-- .item-list-tabs -->

      <div class="item-list-tabs" id="subnav" role="navigation">
        <ul class="pills">

          <?php do_action( 'bp_groups_directory_group_types' ); ?>

          <li class="groups-order-select order-select last filter">

            <label for="groups-order-by" class="order-select"><?php _e( 'Order By:', 'buddypress' ); ?>
            <select id="groups-order-by">
              <option value="active"><?php _e( 'Last Active', 'buddypress' ); ?></option>
              <option value="popular"><?php _e( 'Most Members', 'buddypress' ); ?></option>
              <option value="newest"><?php _e( 'Newly Created', 'buddypress' ); ?></option>
              <option value="alphabetical"><?php _e( 'Alphabetical', 'buddypress' ); ?></option>
              <?php do_action( 'bp_groups_directory_order_options' ); ?>
            </select>
            </label>
          </li>
        </ul>
      </div>

      <div id="groups-dir-list" class="groups dir-list">

        <?php locate_template( array( 'groups/groups-loop.php' ), true ); ?>

      </div><!-- #groups-dir-list -->

      <?php do_action( 'bp_directory_groups_content' ); ?>

      <?php wp_nonce_field( 'directory_groups', '_wpnonce-groups-filter' ); ?>

      <?php do_action( 'bp_after_directory_groups_content' ); ?>

    </form><!-- #groups-directory-form -->

    <?php do_action( 'bp_after_directory_groups' ); ?>

    </div><!-- .cfct-module  --> 
  </div><!-- .main  --> 

 <?php flawless_widget_area( 'right_sidebar' ); ?>

</div><!-- #content -->

<?php do_action( 'bp_after_directory_groups_page' ); ?>

<?php get_footer( 'buddypress' ); ?>
