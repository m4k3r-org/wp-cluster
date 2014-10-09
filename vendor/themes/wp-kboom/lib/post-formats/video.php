<?php
$postid = $post->ID;
$embed = get_post_meta($post->ID, 'sn_video_post_embed', $single = true);
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article">

    <div class="entry-body">
            <div class="post-meta">
                <div class="post-author"><span class="title"><?php _e("", "site5framework"); ?></span> <?php the_author_posts_link(); ?></div>
                <time class="post-date" datetime="<?php echo the_time('Y-m-d'); ?>">
                    <span class="post-month"><?php the_time('M j, Y'); ?></span>
                </time>
                <div class="tags"><span class="title"><?php _e("", "site5framework"); ?></span>
                    <span class="tag">
                        <?php the_tags('', ', ', ''); ?>
                    </span>
                </div>
                <div class="comments-link"><span class="title"><?php _e("", "site5framework"); ?></span> <?php comments_popup_link(__('0', 'site5framework'), __('1', 'site5framework'), __('%', 'site5framework')); ?></div>
            </div>


        <div class="entry-content">
            <?php if ( !is_singular() ) { ?>
                <?php
                    if( !empty( $embed ) ) {
                        echo stripslashes(htmlspecialchars_decode($embed));
                    } else {
                        player_video($postid);
                     }
                ?>
            <?php } else { ?>
                <?php
                    if( !empty( $embed ) ) {
                        $embed = get_post_meta($post->ID, 'sn_video_post_embed', $single = true);
                        echo stripslashes(htmlspecialchars_decode($embed));
                    } else {
                        player_video($postid);
                    }?>
            <?php }?>

            <h2 class="permalink"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'site5framework' ); ?> <?php the_title(); ?>"><?php the_title(); ?></a></h2>
            <?php if (is_singular()): the_content();
                else : ?>
            <?php the_excerpt(); ?>
                <?php endif;?>
        </div>
        <div class="horizontal-line"> </div>
    </div> <!-- end article section -->

</article> <!-- end article -->