<?php get_header(); ?>

        <div id="white-background">
            <!-- content -->
            <div id="content" class="container clearfix" style="padding-bottom: 0;">

                <div class="page-title-heading">
                    <h2><?php _e("OUR BLOG", "site5framework"); ?>
                        <?php
                        $singledescpage = of_get_option('sc_singledesc');
                        $singledesc = get_post_meta($singledescpage, 'snbpd_pagedesc');
                        ?>

                        <?php if (!empty($singledesc)) {
                            echo ' / <span>';
                            echo $singledesc[0].'</span>';
                        }?>
                    </h2>
                </div>
                <div class="title-border"></div>

                <div class="three-fourth">
                    <div id="main" role="main" class="sidebar-line-left">

					<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

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

                    </div><!-- end #main -->

                    <?php if(of_get_option('sc_authorbox') == '1') : ?>
					<div class="author clearfix">
				        <div class="author-gravatar">
				            <?php echo get_avatar( $post->post_author, 64 ); ?>       
				        </div>
				        <div class="author-about">
				        	<h4>
				        	<?php if(get_the_author_meta( 'first_name') != ''  &&  get_the_author_meta( 'last_name') != '' ) : ?>
				            	<?php the_author_meta( 'first_name'); ?> <?php the_author_meta( 'last_name'); ?> 
				       		<?php else: ?>
				           		<?php the_author_meta( 'user_nicename'); ?> 
				       		<?php endif; ?>
				       		</h4>
				            <p class="author-description"><?php the_author_meta( 'description' ); ?></p>
				        </div>
				    </div>
					<?php endif ?>

                        <div class="space"></div>
                        <div class="title-border" style="float: left;"></div>
						
						<?php comments_template(); ?>
						
						<?php endwhile; ?>			
						
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
						
					
				</div><!-- three-fourth -->

                <div class="one-fourth-block last" style="margin-top: 20px;">
                    <?php get_template_part( 'blog', 'sidebar' ); ?>
                </div>
			</div> <!-- end #content -->

        </div><!-- end #white-background -->
<?php get_footer(); ?>