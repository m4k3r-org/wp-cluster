<?php get_header( 'buddypress' ) ?>

	<div class="<?php flawless_wrapper_class(); ?>">
    <div class="<?php flawless_block_class( 'main cfct-block' ); ?>">
    	<?php if ( bp_has_groups() ) : while ( bp_groups() ) : bp_the_group(); ?>

    	<?php do_action( 'bp_before_group_plugin_template' ) ?>

    	<div id="item-header" class="item-header">
        <?php locate_template( array( 'groups/single/group-header.php' ), true ) ?>
    	</div><!-- #item-header -->

    	<div id="item-nav" class="item-nav">
        <div class="item-list-tabs no-ajax" id="object-nav" role="navigation">
        	<ul class="tabs">
            <?php bp_get_options_nav() ?>

            <?php do_action( 'bp_group_plugin_options_nav' ) ?>
        	</ul>
        </div>
    	</div><!-- #item-nav -->

    	<div id="item-body" class="item-body">

        <?php do_action( 'bp_before_group_body' ) ?>

        <?php do_action( 'bp_template_content' ) ?>

        <?php do_action( 'bp_after_group_body' ) ?>
    	</div><!-- #item-body -->

    	<?php do_action( 'bp_after_group_plugin_template' ) ?>

    	<?php endwhile; endif; ?>

    </div><!-- .main  --> 

	 <?php flawless_widget_area( 'buddypress' ) ?>

	</div><!-- #content -->

	<?php get_footer( 'buddypress' ) ?>
