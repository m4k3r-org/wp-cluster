<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article">

    <div class="entry-body">

        <header>
            <div class="post-meta">
                <div class="post-author"><span class="title"><?php _e("", "site5framework"); ?></span> <?php the_author_posts_link(); ?></div>
                <time class="post-date" datetime="<?php echo the_time('Y-m-d'); ?>">
                    <span class="post-month"><?php the_time('M j, Y'); ?></span>
                </time>
                <div class="tags">    <span class="title"><?php _e("", "site5framework"); ?></span>
                    <span class="tag">
                        <?php the_tags('', ', ', ''); ?>
                    </span>
                </div>
            </div>
        </header> <!-- end article header -->

        <!-- aside custom post-->
        <div class="entry-content">
            <?php echo get_post_meta($post->ID, 'sn_aside_post', true); ?>
        </div>
        <!-- aside custom post-->
        <div class="horizontal-line"> </div>
    </div> <!-- end article section -->
</article> <!-- end article -->