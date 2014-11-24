<?php 
/*
 * Template Name: Video 3 Cols
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
                    <?php if(of_get_option('sc_video_filterableon') == '1') { ?>
                    <ul class="filterable" id="<?php echo of_get_option('sc_portfoliofilters')=='javascript' ? 'filterable' : '' ?>">
                        <li class="active"><a href="javascript:;" data-filter="all" class="filter"><?php _e('all', 'site5framework'); ?></a></li>
                        <?php
                        $categories=  get_categories('taxonomy=collections&title_li='); foreach ($categories as $category){ ?>
                        <?php //print_r(get_term_link($category->slug, 'types')) ?>
                        <li><a href="javascript:;" class="filter" data-filter="<?php echo $category->category_nicename;?>"><?php echo $category->name;?></a></li>
                        <?php }?>

                    </ul>
                    <?php } ?><!-- end filterable section -->
                    <div class="portfolio-container">

                        <ul id="portfolio-items-one-third"  class="portfolio-items-one-third clearfix">

                            <?php
                            global $post;
                            $term = get_query_var('term');
                            $tax = get_query_var('taxonomy');
                            $args=array('post_type'=> 'video','post_status'=> 'publish', 'orderby'=> 'menu_order', 'caller_get_posts'=>1, 'paged'=>$paged, 'posts_per_page'=>of_get_option('sc_videoitemsperpage'));
                            $taxargs = array($tax=>$term);
                            if($term!='' && $tax!='') { $args  = array_merge($args, $taxargs); }

                            query_posts($args);

                            while ( have_posts()):the_post();
                                $categories = wp_get_object_terms( get_the_ID(), 'collections');
                                ?>

                                <!-- PROJECT ITEM STARTS -->
                                    <li class="item photo <?php foreach ($categories as $category) { echo $category->slug. ' '; } ?>" data-id="id-<?php the_ID(); ?>" data-type="<?php foreach ($categories as $category) { echo $category->slug. ' '; } ?>">
                                        <div class="item-content">
                                            <div class="link-holder">
                                                <div class="portfolio-item-holder">
                                                    <div class="portfolio-item-hover-content">
                                                        <?php
                                                        $thumbId = get_image_id_by_link ( get_post_meta($post->ID, 'snbp_pitemlink', true) );
                                                        $thumb = wp_get_attachment_image_src($thumbId, 'portfolio-thumbnail', false);
                                                        $large = wp_get_attachment_image_src($thumbId, 'large', false);

                                                        global $post;
                                                        $video = get_post_meta( $post->ID, 'snbp_video_link', true );



                                                        if (!$thumb == ''){ ?>
                                                            <a href="<?php echo $video ?>" title="<?php the_title(); ?>" data-rel="prettyPhoto" class="zoom video">View Image</a>
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