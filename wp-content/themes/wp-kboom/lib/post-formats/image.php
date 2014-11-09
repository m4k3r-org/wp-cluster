            <div class="image-post-format">
                <div class="resize">
                    <?php
                    $image_post_preview = get_post_meta($post->ID,'sn_image_post_preview',true);
                    if($image_post_preview !=''){
                        echo '<a data-rel="prettyPhoto" href="'.$image_post_preview['src'].'"><img src="'.$image_post_preview['src'].'"></a>';
                        ?>
                        <?php }?>
                </div>
            </div>

            <div class="blog-post-title">
                <h2 class="permalink"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'site5framework' ); ?> <?php the_title(); ?>"><?php the_title(); ?></a></h2>
            </div>

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
                    <div class="comments-link"><span class="title"><?php _e("", "site5framework"); ?></span> <?php comments_popup_link(__('0', 'site5framework'), __('1', 'site5framework'), __('%', 'site5framework')); ?></div>
                </div>
            </header> <!-- end article header -->