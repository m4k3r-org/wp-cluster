<?php

/**
 * BuddyPress - Forums Loop
 *
 * Querystring is set via AJAX in _inc/ajax.php - bp_dtheme_object_filter()
 *
 * @package BuddyPress
 * @subpackage bp-default
 */

?>


<?php if ( bp_has_forum_topics( bp_ajax_querystring( 'forums' ) ) ) : ?>

	<table class="forum zebra-striped">

    <tbody>

    	<?php while ( bp_forum_topics() ) : bp_the_forum_topic(); ?>

    	<tr class="<?php bp_the_topic_css_class(); ?>">
        <td class="td-title">
        	<a class="topic-title" href="<?php bp_the_topic_permalink(); ?>" title="<?php bp_the_topic_title(); ?> - <?php _e( 'Permalink', 'buddypress' ); ?>">

            <?php bp_the_topic_title(); ?>

        	</a>

        	<p class="topic-meta">
            <span class="topic-by"></span>

            <?php if ( !bp_is_group_forum() ) : ?>

            	<span class="topic-in">                

            	</span>

            <?php endif; ?>

        	</p>
        </td>

        <td class="td-lastposter">
        <span class="freshness-author"><a href="<?php bp_the_topic_permalink(); ?>"><?php bp_the_topic_last_poster_avatar( 'type=thumb&width=20&height=20' ); ?></a><?php bp_the_topic_last_poster_name(); ?></span>
        </td>
        <td class="td-postcount">
        	<?php bp_the_topic_total_posts(); ?>
        </td>
        <td class="td-freshness">
        	<span class="time-since"><?php bp_the_topic_time_since_last_post(); ?></span>
        </td>

        <?php do_action( 'bp_directory_forums_extra_cell' ); ?>

    	</tr>

    	<?php do_action( 'bp_directory_forums_extra_row' ); ?>

    	<?php endwhile; ?>

    </tbody>
	</table>


<?php endif; ?>

