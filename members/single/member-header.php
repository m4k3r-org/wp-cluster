<?php do_action( 'bp_before_member_header' ); ?>

<div class="item-header-avatar">
  <a href="<?php bp_displayed_user_link(); ?>"><?php bp_displayed_user_avatar( array( 'width' => '120px', 'height' => '120px' ) ); ?></a>
</div><!-- .item-header-avatar -->

<div class="item-meta">

  <div class="item-buttons">
    <?php do_action( 'bp_member_header_actions' ); ?>
  </div>

  <div class="activity">
    <span class="activity"><?php bp_last_activity( bp_displayed_user_id() ); ?></span>
  </div>

  <?php do_action( 'bp_profile_header_meta' ); ?>

</div>

<div id="item-header-content" class="item-header-content">

	<h2 class="header-title">
    <a href="<?php bp_displayed_user_link(); ?>"><?php bp_displayed_user_fullname(); ?></a><?php do_action( 'flawless::bp_item_header' ); ?>
  </h2>

	<span class="user-nicename">@<?php bp_displayed_user_username(); ?></span>

	<?php do_action( 'bp_before_member_header_meta' ); ?>

  <?php if ( bp_is_active( 'activity' ) ) : ?>

    <div class="header-note latest-update">
      <?php bp_activity_latest_update( bp_displayed_user_id() ); ?>
    </div>

  <?php endif; ?>

</div><!-- .item-header-content -->

<?php do_action( 'bp_after_member_header' ); ?>


