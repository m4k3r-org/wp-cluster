<?php 
/*
 * Template Name: Events
 */
get_header(); ?>

            <div id="white-background">
                <!-- content -->
                <div id="content" class="container clearfix">

                    <div class="container">
                        <div class="page-title-heading">
                            <h2><?php the_title(); ?><?php if ( !get_post_meta($post->ID, 'snbpd_pagedesc', true)== '') { ?> / <?php }?> <span><?php echo get_post_meta($post->ID, 'snbpd_pagedesc', true); ?></span></h2>
                        </div>
                    </div>
                    <div class="title-border"></div>

                    <div class="container clearfix">
                        <ul id="event-items"  class="three-fourth" style="margin-bottom: 0;">
                            <?php
                            global $post;
                            $term = get_query_var('term');
                            $tax = get_query_var('taxonomy');
                            $args=array('post_type'=> 'event','post_status'=> 'publish', 'orderby'=> 'menu_order', 'caller_get_posts'=>1, 'paged'=>$paged, 'posts_per_page'=>of_get_option('sc_eventitemsperpage'));
                            $taxargs = array($tax=>$term);
                            if($term!='' && $tax!='') { $args  = array_merge($args, $taxargs); }

                            query_posts($args);

                            while ( have_posts()):the_post();
                                $categories = wp_get_object_terms( get_the_ID(), 'event_types');
                                ?>

                                <!-- PROJECT ITEM STARTS -->
                                    <li class="three-fourth-block <?php foreach ($categories as $category) { echo $category->slug. ' '; } ?>" data-id="id-<?php the_ID(); ?>" data-type="<?php foreach ($categories as $category) { echo $category->slug. ' '; } ?>">
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
                                <?php endwhile;  ?>

                            <div class="three-fourth">
                                <div class="space"></div>
                                <div class="title-border" style="float: left;"></div>
                                <!-- begin #pagination -->
                                <?php if (function_exists("wpthemess_paginate")) { wpthemess_paginate(); } ?>
                                <!-- end #pagination -->
                            </div>

                        </ul>

                        <?php
                        wp_reset_query();
                        wp_reset_postdata();
                        ?>

                        <!-- Begin Sidebar -->
                        <div class="one-fourth-block last" style="margin-top: 20px;">
                            <?php get_template_part( 'blog', 'sidebar' ); ?>
                        </div>
                        <!-- End Sidebar -->

                    </div><!-- end .container -->
                </div><!-- end content -->

            </div><!-- end #white-background -->

<?php get_footer(); ?>