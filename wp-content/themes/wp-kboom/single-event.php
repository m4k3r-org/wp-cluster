<?php get_header(); ?>

<div id="white-background">
    <!-- content -->
    <div id="content" class="container clearfix">

        <div class="container">
            <div class="page-title-heading">
                <h2><?php the_title(); ?><?php if ( !get_post_meta($post->ID, 'snbpd_pagedesc', true)== '') { ?> / <?php }?> <span><?php echo get_post_meta($post->ID, 'snbpd_pagedesc', true); ?></span></h2>
                <div class="container">
                    <div class="content">
                        <div id="single-portfolio-pagination">
                            <div class="project-pagination">
                                <div class="project-pagination-prev">
                                    <?php previous_post_link('%link', ''.__('', 'site5framework')) ?>
                                </div>
                                <div class="project-pagination-next">
                                    <?php next_post_link('%link', __('', 'site5framework') . ''); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="title-border"></div>

        <div class="container">
                    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

                    <div class="container">
                        <div class="page-body clearfix">
                            <div class="event-single">
                                <div class="one-third" style="margin-bottom: 0;">
                                    <!--Event image cover section begin here-->
                                    <?php
                                    //check if map section exists and show it
                                    global $post;
                                    $thumbId = get_post_meta($post->ID, 'snbp_pitemlink', TRUE);
                                    $thumbId = get_post_meta($post->ID, 'snbp_pitemlink', TRUE);

                                    if ($thumbId != '') : ?>
                                    <div class="one-third-block">
                                        <div class="portfolio-image resize">
                                            <?php
                                            $thumbId = get_image_id_by_link ( get_post_meta($post->ID, 'snbp_pitemlink', true) );
                                            $thumb = wp_get_attachment_image_src($thumbId, 'full', false);
                                            $large = wp_get_attachment_image_src($thumbId, 'full', false);

                                            if (!$thumb == ''){ ?>
                                                <a href="<?php echo $large[0] ?>" data-rel="prettyPhoto" title="<?php the_title(); ?>"><img src="<?php echo $thumb[0] ?>" alt="<?php the_title(); ?>"  /></a>
                                                <?php }  ?>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    <!--Event image cover section end here-->

                                    <!--Event map section begin here-->
                                    <?php
                                    //check if map section exists and show it
                                    global $post;
                                    $event_map = get_post_meta($post->ID, 'snbp_event_map', TRUE);
                                    $event_map = get_post_meta($post->ID, 'snbp_event_map', TRUE);

                                    if ($event_map != '') : ?>
                                    <div class="one-third-block">
                                        <div class="portfolio-image resize" style="position: relative; overflow: hidden;">
                                            <?php
                                            global $post;
                                            $event_map = get_post_meta( $post->ID, 'snbp_event_map', true );
                                            echo $event_map
                                            ?>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    <!--Event map section end here-->
                                </div>

                                <ul id="event-items" >
                                <!-- PROJECT ITEM STARTS -->
                                <li class="two-third-block last <?php foreach ($categories as $category) { echo $category->slug. ' '; } ?>" data-id="id-<?php the_ID(); ?>" data-type="<?php foreach ($categories as $category) { echo $category->slug. ' '; } ?>">
                                    <div class="event-date">
                                        <?php
                                        global $post;
                                        $date = get_post_meta( $post->ID, 'snbp_event_date', true );
                                        echo $date
                                        ?>
                                    </div>

                                    <div class="event-title">
                                        <h4>
                                            <a href="<?php the_permalink() ?>" title="<?php the_title(); ?>" title="<?php the_title(); ?>"> <?php the_title(); ?> </a>
                                        </h4>
                                    </div>

                                    <div class="event-location">
                                        <?php
                                        global $post;
                                        $location = get_post_meta( $post->ID, 'snbp_event_location', true );
                                        echo $location;
                                        ?>
                                    </div>

                                    <div class="event-venue">
                                        <?php
                                        global $post;
                                        $venue = get_post_meta( $post->ID, 'snbp_event_venue', true );
                                        echo $venue;
                                        ?>
                                    </div>

                                    <div class="event-time">
                                        <?php
                                        global $post;
                                        $time = get_post_meta( $post->ID, 'snbp_event_time', true );
                                        echo $time
                                        ?>
                                    </div>

                                    <div class="ticket-button">
                                        <?php

                                        if (get_post_meta($post->ID, 'snbp_ticket_sold_out', true)) {
                                            echo '
                                            <div class="event-cancel-out"><p>Sold Out</p></div><!-- end #event-cancel-out -->';
                                        } elseif (get_post_meta($post->ID, 'snbp_ticket_canceled', true)) {
                                            echo '
                                            <div class="event-cancel-out"><p>Canceled</p></div><!-- end #event-cancel-out -->';
                                        } elseif (get_post_meta($post->ID, 'snbp_ticket_free', true)) {
                                            echo '
                                            <div class="event-cancel-out"><p>Free Entry</p></div><!-- end #event-cancel-out -->';
                                        } else {
                                            global $post;
                                            $event_ticket = get_post_meta( $post->ID, 'snbp_event_ticket', true );
                                            echo '
                                            <div class="event-ticket"><a href="' . $event_ticket . '" >Buy Tickets</a></div><!-- end .event-tickets -->';
                                        }

                                        ?>
                                    </div>
                                </li>
                                <!-- PROJECT ITEM ENDS -->
                                </ul>

                            </div><!-- end .event-single -->
                            <?php if ($post->post_content != '') : ?>
                            <div class="two-third-block last" style="float: right;">
                                <?php the_content(); ?>
                            </div>
                            <?php endif; ?>
                        </div> <!-- end .page-body section -->
                    </div> <!-- end .container section -->

                    <?php endwhile; ?>
                            </article>
                            <?php else : ?>
                    <article id="post-not-found">
                        <header>
                            <h1><?php _e("Not Found", "site5framework"); ?></h1>
                        </header>
                        <section class="post_content">
                            <p><?php _e("Sorry, but the requested resource was not found on this site.", "site5framework"); ?></p>
                        </section>
                        <footer>
                        </footer>
                    </article>
                    <?php endif; ?>
                </div>
            </div> <!-- end content -->
        </div><!-- end #white-background -->

<?php get_footer(); ?>