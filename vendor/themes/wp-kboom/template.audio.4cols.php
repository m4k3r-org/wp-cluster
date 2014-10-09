<?php
/*
* Template Name: Audio 4 Cols
*/
get_header(); ?>

            <div id="white-background">
                <!-- content -->
                <div id="content" class="container clearfix" style="padding-bottom: 20px;">

                    <div class="container">
                        <div class="page-title-heading">
                            <h2><?php the_title(); ?><?php if ( !get_post_meta($post->ID, 'snbpd_pagedesc', true)== '') { ?> / <?php }?> <span><?php echo get_post_meta($post->ID, 'snbpd_pagedesc', true); ?></span></h2>
                        </div>
                    </div>
                    <div class="title-border"></div>

                    <!-- begin filterable section -->
                    <?php if(of_get_option('sc_audio_filterableon') == '1') { ?>
                    <ul class="filterable" id="<?php echo of_get_option('sc_portfoliofilters')=='javascript' ? 'filterable' : '' ?>">
                        <li class="active"><a href="javascript:;" data-filter="all" class="filter"><?php _e('all', 'site5framework'); ?></a></li>
                        <?php
                        $categories=  get_categories('taxonomy=albums&title_li='); foreach ($categories as $category){ ?>
                        <?php //print_r(get_term_link($category->slug, 'types')) ?>
                        <li><a href="javascript:;" class="filter" data-filter="<?php echo $category->category_nicename;?>"><?php echo $category->name;?></a></li>
                        <?php }?>

                    </ul>
                    <?php } ?><!-- end filterable section -->
                    <div class="portfolio-container">

                        <ul id="portfolio-items-one-fourth"  class="portfolio-items-one-fourth clearfix">

                            <?php
                            global $post;
                            $term = get_query_var('term');
                            $tax = get_query_var('taxonomy');
                            $args=array('post_type'=> 'audio','post_status'=> 'publish', 'orderby'=> 'menu_order', 'caller_get_posts'=>1, 'paged'=>$paged, 'posts_per_page'=>of_get_option('sc_audioitemsperpage'));
                            $taxargs = array($tax=>$term);
                            if($term!='' && $tax!='') { $args  = array_merge($args, $taxargs); }

                            query_posts($args);

                            while ( have_posts()):the_post();
                                $categories = wp_get_object_terms( get_the_ID(), 'albums');
                                ?>

                                <!-- PROJECT ITEM STARTS -->
                                <li class="item <?php foreach ($categories as $category) { echo $category->slug. ' '; } ?>" data-id="id-<?php the_ID(); ?>" data-type="<?php foreach ($categories as $category) { echo $category->slug. ' '; } ?>">
                                    <div class="item-content">
                                        <div class="link-holder">
                                            <div class="portfolio-item-holder">
                                                <div class="portfolio-item-hover-content">
                                                    <?php
                                                    $thumbId = get_image_id_by_link ( get_post_meta($post->ID, 'snbp_pitemlink', true) );

                                                    $thumb = wp_get_attachment_image_src($thumbId, 'portfolio-thumbnail', false);
                                                    $large = wp_get_attachment_image_src($thumbId, 'large', false);

                                                    if (!$thumb == ''){ ?>
                                                        <a href="<?php the_permalink() ?>" class="zoom">View Image</a>
                                                        <img src="<?php echo $thumb[0] ?>" alt="<?php the_title(); ?>" width="270" class="portfolio-img" />
                                                        <?php } else { ?>
                                                        <img src="<?php echo get_template_directory_uri(); ?>/library/images/sampleimages/portfolio-img.jpg" alt="<?php the_title(); ?>" width="220"  class="portfolio-img" />
                                                        <?php }?>

                                                    <div class="hover-options">
                                                        <div class="carousel-hover-title">
                                                            <h3>
                                                                <a href=" <?php echo $large[0] ?> " data-rel="prettyPhoto" title="<?php the_title(); ?>"> <?php the_title(); ?> </a>
                                                            </h3>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <?php
                                    //check if pricing URL exists and show link
                                    global $post;
                                    $button1_url = get_post_meta($post->ID, 'snbp_button1_url', TRUE);
                                    $button1 = get_post_meta($post->ID, 'snbp_button1', TRUE);

                                    if($button1_url != '') { ?>
                                        <div class="audio-buy-button"><a href="<?php echo $button1_url ?>" title="<?php echo $button1 ?>"><?php echo $button1 ?></a></div>
                                        <?php } ?>

                                    <?php
                                    //check if pricing URL exists and show link
                                    global $post;
                                    $button2_url = get_post_meta($post->ID, 'snbp_button2_url', TRUE);
                                    $button2 = get_post_meta($post->ID, 'snbp_button2', TRUE);

                                    if($button2_url != '') { ?>
                                        <div class="audio-buy-button"><a href="<?php echo $button2_url ?>" title="<?php echo $button2 ?>"><?php echo $button2 ?></a></div>
                                        <?php } ?>

                                    <?php
                                    //check if pricing URL exists and show link
                                    global $post;
                                    $button3_url = get_post_meta($post->ID, 'snbp_button3_url', TRUE);
                                    $button3 = get_post_meta($post->ID, 'snbp_button3', TRUE);

                                    if($button3_url != '') { ?>
                                        <div class="audio-buy-button"><a href="<?php echo $button3_url ?>" title="<?php echo $button3 ?>"><?php echo $button3 ?></a></div>
                                        <?php } ?>

                                    <?php
                                    //check if pricing URL exists and show link
                                    global $post;
                                    $button4_url = get_post_meta($post->ID, 'snbp_button4_url', TRUE);
                                    $button4 = get_post_meta($post->ID, 'snbp_button4', TRUE);

                                    if($button4_url != '') { ?>
                                        <div class="audio-buy-button"><a href="<?php echo $button4_url ?>" title="<?php echo $button4 ?>"><?php echo $button4 ?></a></div>
                                        <?php } ?>

                                    <?php
                                    //check if pricing URL exists and show link
                                    global $post;
                                    $button5_url = get_post_meta($post->ID, 'snbp_button5_url', TRUE);
                                    $button5 = get_post_meta($post->ID, 'snbp_button5', TRUE);

                                    if($button5_url != '') { ?>
                                        <div class="audio-buy-button"><a href="<?php echo $button5_url ?>" title="<?php echo $button5 ?>"><?php echo $button5 ?></a></div>
                                        <?php } ?>
                                </li>
                                <!-- PROJECT ITEM ENDS -->
                                <?php endwhile;  ?>
                        </ul>

                        <div class="container clearfix">
                            <div class="space"></div>
                            <div class="title-border" style="float: left;"></div>
                            <!-- begin #pagination -->
                            <?php if (function_exists("wpthemess_paginate")) { wpthemess_paginate(); } ?>
                            <!-- end #pagination -->
                        </div>

                        <?php
                        wp_reset_query();
                        wp_reset_postdata();
                        ?>
                    </div>
                </div><!-- end content -->

            </div><!-- end #white-background -->

<?php get_footer(); ?>