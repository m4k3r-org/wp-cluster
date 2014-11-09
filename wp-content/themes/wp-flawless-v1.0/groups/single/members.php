<?php if ( bp_group_has_members( 'exclude_admins_mods=0' ) ) : ?>

	<?php do_action( 'bp_before_group_members_content' ); ?>

	<div class="item-list-tabs" id="subnav" role="navigation">
    <ul class="pills">

    	<?php do_action( 'bp_members_directory_member_sub_types' ); ?>

    </ul>
	</div>

	<div id="page-top" class="pagination clearfix">

    <div id="member-count-top" class="member-count-top page-count">
    	<?php bp_members_pagination_count(); ?>
    </div>

    <div id="member-page-top" class="member-page-top pagination-links">
    	<?php bp_members_pagination_links(); ?>
    </div>

	</div>

	<?php do_action( 'bp_before_group_members_list' ); ?>

	<ul id="members-list" class="item-list group-members members-list clearfix" role="main">

    <?php while ( bp_group_members() ) : bp_group_the_member(); ?>

    	<li class="list-item cfct-block">
        <div class="cfct-module">
      
        <div class="item-avatar">
          <a href="<?php bp_group_member_domain(); ?>"><?php bp_group_member_avatar_thumb(); ?></a>
        </div>

        <h5><?php bp_group_member_link(); ?></h5>
        <span class="activity"><?php bp_group_member_joined_since(); ?></span>

        <?php do_action( 'bp_group_members_list_item' ); ?>

        <?php if ( bp_is_active( 'friends' ) ) : ?>

        	<div class="action">

            <?php bp_add_friend_button( bp_get_group_member_id(), bp_get_group_member_is_friend() ); ?>

            <?php do_action( 'bp_group_members_list_item_action' ); ?>

        	</div>

        <?php endif; ?>
        </div>
    	</li>

    <?php endwhile; ?>

	</ul>

	<?php do_action( 'bp_after_group_members_list' ); ?>

	<div id="page-bottom" class="pagination clearfix">

    <div id="member-count-bottom"  class="member-count-bottom page-count">
    	<?php bp_members_pagination_count(); ?>
    </div>

    <div id="member-page-bottom" class="member-page-bottom pagination-links">
    	<?php bp_members_pagination_links(); ?>
    </div>

	</div>

	<?php do_action( 'bp_after_group_members_content' ); ?>

<?php else: ?>

	<div id="message" class="info">
    <p><?php _e( 'This group has no members.', 'buddypress' ); ?></p>
	</div>

<?php endif; ?>
