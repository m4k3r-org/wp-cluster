<?php 
/*
 * Template Name: Blog
*/
get_header(); ?>
        <div id="white-background">
            <!-- content -->
            <div id="content" class="container clearfix" style="padding-bottom: 0;">

                <div class="container">
                    <div class="page-title-heading">
                        <h2><?php the_title(); ?><?php if ( !get_post_meta($post->ID, 'snbpd_pagedesc', true)== '') { ?> / <?php }?> <span><?php echo get_post_meta($post->ID, 'snbpd_pagedesc', true); ?></span></h2>
                    </div>
                </div>
                <div class="title-border"></div>

                <div class="three-fourth" style="margin-bottom: 0;">
					<div id="main" role="main" class="sidebar-line-left">

							<?php
								// WP 3.0 PAGED BUG FIX
								if ( get_query_var('paged') )
								$paged = get_query_var('paged');
								elseif ( get_query_var('page') )
								$paged = get_query_var('page');
								else
								$paged = 1;

								$args = array(

								'post_type' => 'post',
								'paged' => $paged );
								query_posts($args);
							?>

							<?php if (have_posts()) : $count = 0; ?>
							<?php while (have_posts()) : the_post(); $count++; global $post; ?>

                            <ul>
                                <!-- article begin -->
                                <li class="three-fourth-block <?php foreach ($categories as $category) { echo $category->slug. ' '; } ?>" data-id="id-<?php the_ID(); ?>" data-type="<?php foreach ($categories as $category) { echo $category->slug. ' '; } ?>">
                                    <!-- begin article -->
                                    <?php if ( has_post_format( 'image' ) ) : { ?>
                                    <article class="image-post">
                                        <div class="entry-body">
                                            <!-- image custom post-->
                                            <div class="entry-content">
                                                <?php  get_template_part( 'lib/post-formats/image' ); ?>
                                                <!-- home custom excerpt -->
                                                <div class="excerpt">
                                                    <?php if (is_singular()): the_content();
                                                else : ?>
                                                    <?php echo excerpt(55); ?>
                                                    <?php endif;?>
                                                </div>
                                                <!-- home custom excerpt -->
                                            </div>
                                            <!-- image custom post-->
                                        </div> <!-- end article section -->
                                    </article>

                                    <?php }	elseif ( has_post_format( 'gallery' ) ) : { ?>
                                    <article class="gallery-post">
                                        <div class="entry-body">
                                            <!-- gallery custom post-->
                                            <div class="entry-content">
                                                <?php  get_template_part( 'lib/post-formats/gallery' ); ?>
                                                <!-- home custom excerpt -->
                                                <div class="excerpt">
                                                    <?php if (is_singular()): the_content();
                                                else : ?>
                                                    <?php echo excerpt(55); ?>
                                                    <?php endif;?>
                                                </div>
                                                <!-- home custom excerpt -->
                                            </div>
                                            <!-- gallery custom post-->
                                        </div> <!-- end article section -->
                                    </article>
                                    <?php }	elseif ( has_post_format( 'video' ) ) : { ?>
                                    <article class="video-post">
                                        <?php  get_template_part( 'lib/post-formats/video' ); ?>
                                    </article>
                                    <?php }	elseif ( has_post_format( 'link' ) ) : { ?>
                                    <article class="link-post">
                                        <?php  get_template_part( 'lib/post-formats/link' ); ?>
                                    </article>
                                    <?php }	elseif ( has_post_format( 'quote' ) ) : { ?>
                                    <article class="quote-post">
                                        <?php  get_template_part( 'lib/post-formats/quote' ); ?>
                                    </article>
                                    <?php }	elseif ( has_post_format( 'aside' ) ) : { ?>
                                    <article class="aside-post">
                                        <?php  get_template_part( 'lib/post-formats/aside' ); ?>
                                    </article>
                                    <?php }	else : { ?>
                                    <article class="standard-post">
                                        <div class="entry-body">
                                            <!-- standard post-->
                                            <div class="entry-content">
                                                <?php  get_template_part( 'lib/post-formats/standard' ); ?>
                                                <!-- home custom excerpt -->
                                                <div class="excerpt">
                                                    <?php if (is_singular()): the_content();
                                                else : ?>
                                                    <?php echo excerpt(55); ?>
                                                    <?php endif;?>
                                                </div>
                                                <!-- home custom excerpt -->
                                            </div>
                                            <!-- standard post-->
                                        </div> <!-- end article section -->
                                    </article>
                                    <?php }	endif; ?>
                                    <!-- end article -->
                                </li>
                            </ul>


							<?php endwhile; ?>

                            <!-- begin #pagination -->
                                <div class="three-fourth">
                                    <div class="space"></div>
                                    <div class="title-border" style="float: left;"></div>
                                    <!-- begin #pagination -->
                                    <?php if (function_exists("wpthemess_paginate")) { wpthemess_paginate(); } ?>
                                    <!-- end #pagination -->
                                </div>
                            <!-- end #pagination -->

						<?php else : ?>

						<article id="post-not-found">
						    <header>
						    	<h1><?php _e("No Posts Yet", "site5framework"); ?></h1>
						    </header>
						    <section class="post_content">
						    	<p><?php _e("Sorry, What you were looking for is not here.", "site5framework"); ?></p>
						    </section>
						</article>

						<?php endif; ?>
				
					</div> <!-- end #main -->

				</div><!-- three-fourth -->

                <div class="one-fourth-block last" style="margin-top: 20px;">
					<?php get_template_part( 'blog', 'sidebar' ); ?>
				</div>

			</div> <!-- end #content -->
        </div><!-- end #white-background -->
<?php get_footer(); ?>