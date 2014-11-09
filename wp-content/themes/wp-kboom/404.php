<?php
/*
* Template Name: 404 Error Page
*/
    get_header(); ?>

            <div id="white-background">
                <!-- content -->
                <div id="content" class="clearfix">

                    <div class="container">
                        <div class="page-title-heading">
                            <h2><?php the_title(); ?><?php if ( !get_post_meta($post->ID, 'snbpd_pagedesc', true)== '') { ?> / <?php }?> <span><?php echo get_post_meta($post->ID, 'snbpd_pagedesc', true); ?></span></h2>
                        </div>
                    </div>
                    <div class="title-border"></div>

                    <div id="main" role="main" class="container clearfix">

                        <article id="post-not-found">


                            <header>

                                <h1 class="not-found-text"> · <span>404</span> <?php _e("Error", "site5framework"); ?> · </h1>

                            </header> <!-- end article header -->

                            <section class="post_content">

                                <p align="center"><?php _e("This file may have been moved or deleted. Be sure to check your spelling", "site5framework"); ?></p>

                            </section> <!-- end article section -->

                        </article> <!-- end article -->

                    </div> <!-- end #main -->

                </div> <!-- end #content -->
            </div><!-- end #white-background -->

<?php get_footer(); ?>
