<?php
/*
Template Name: Home
*/
?>

<?php get_header(); ?>

            <!-- begin #full-width-slider -->
            <div id="full-width-slider">
                <div class="container clearfix">
                <?php if(of_get_option('sc_displayslider') == '1') { ?>
                <?php if(of_get_option('sc_slidertype') == 'rev') { ?>
                    <?php putRevSlider( "homepage" ) ?>
                    <?php } ?>
                <?php if(of_get_option('sc_slidertype') == 'flex') { ?>
                    <?php get_template_part( 'homepage', 'slider' ); ?>
                    <?php } ?>
                <?php } ?>
                </div>
            </div>

            <!-- end #full-width-slider -->
            <!-- begin #white-bg -->
            <div id="white-background" >
                <div id="content" class="clearfix" style="padding-bottom: 0;">
                    <?php if(of_get_option('sc_homecontent') == '1') { ?>

                    <!--begin cols content -->
                    <div id="home-content">
                        <div class="container clearfix">
                            <div class="one-third">
                                <div class="home-content-icon">
                                    <div class="icon-center">
                                        <img src="<?php echo of_get_option('sc_homecontent1img') ?>" class="img-align-left" alt="<?php echo of_get_option('sc_homecontent1title') ?>" />
                                    </div>
                                </div>
                                <h4><?php echo of_get_option('sc_homecontent1title') ?></h4>
                                <p><?php echo of_get_option('sc_homecontent1') ?></p>
                                <?php if (of_get_option('sc_homecontent1url')!='') { ?>
                                <p class="readmore">
                                    <a href="<?php echo of_get_option('sc_homecontent1url') ?>"><?php _e('Read More', 'site5framework') ?></a>
                                </p>
                                <?php } ?>
                            </div>

                            <div class="one-third">
                                <div class="home-content-icon">
                                    <div class="icon-center">
                                        <img src="<?php echo of_get_option('sc_homecontent2img') ?>" class="img-align-left" alt="<?php echo of_get_option('sc_homecontent2title') ?>" />
                                    </div>
                                </div>
                                <h4><?php echo of_get_option('sc_homecontent2title') ?></h4>
                                <p><?php echo of_get_option('sc_homecontent2') ?></p>
                                <?php if (of_get_option('sc_homecontent2url')!='') { ?>
                                <p class="readmore">
                                    <a href="<?php echo of_get_option('sc_homecontent2url') ?>"><?php _e('Read More', 'site5framework') ?></a>
                                </p>
                                <?php } ?>
                            </div>

                            <div class="one-third last">
                                <div class="home-content-icon">
                                    <div class="icon-center">
                                        <img src="<?php echo of_get_option('sc_homecontent3img') ?>" class="img-align-left" alt="<?php echo of_get_option('sc_homecontent3title') ?>" />
                                    </div>
                                </div>
                                <h4><?php echo of_get_option('sc_homecontent3title') ?></h4>
                                <p><?php echo of_get_option('sc_homecontent3') ?></p>
                                <?php if (of_get_option('sc_homecontent3url')!='') { ?>
                                <p class="readmore">
                                    <a href="<?php echo of_get_option('sc_homecontent3url') ?>"><?php _e('Read More', 'site5framework') ?></a>
                                </p>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <!-- end cols content -->

                    <?php } ?>

                    <div class="two-third" style="margin-bottom: 0;">
                    <?php if(of_get_option('sc_portfoliohome') == '1') : ?>
                        <div class="one-third">
                            <h3 class="title-margin">
                                <?php echo of_get_option('sc_portfoliohometitle') ?>
                            </h3>

                            <div class="title-border">
                                <div class="title-block"></div>
                            </div>

                            <div class="one-third-block">
                                <?php
                                    $args=array('post_type'=> 'audio', 'post_status'=> 'publish','orderby'=> 'menu_order','posts_per_page'=>8,'showposts'=>8,'caller_get_posts'=>1,'paged'=>$paged,); query_posts($args);
                                    if ( have_posts() ) :
                                        ?>
                                        <ul id="projects-carousel" class="loading">
                                            <?php
                                            while (have_posts()): the_post();
                                                $categories = wp_get_object_terms( get_the_ID(), 'albums');
                                                ?>
                                                <!-- PROJECT ITEM STARTS -->
                                                <li>
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
                                                <?php
                                            endwhile;
                                            wp_reset_query();
                                            ?>
                                        </ul>
                                        <?php
                                    else :
                                        ?>

                                        <?php
                                    endif;
                                    ?>
                            </div>
                        </div>
                        <!-- LATEST ALBUMS CAROUSEL ENDS HERE -->


                        <!-- HOMEPAGE PLAYER BEGIN HERE -->
                        <div class="one-third last">
                            <h3 class="title-margin">
                                <?php echo of_get_option('sc_playerhometitle') ?>
                            </h3>

                            <div class="title-border">
                                <div class="title-block"></div>
                            </div>

                            <div class="one-third-block">
                                <div id="componentWrapper">

                                    <div class="playerHolder">

                                        <div class="player_controls">
                                            <!-- pause/play -->
                                            <div class="controls_toggle"><img src='<?php echo get_template_directory_uri(); ?>/library/images/html5-audio-player/set1/play.png' width='30' height='31' alt='controls_toggle'/></div>
                                        </div>

                                        <!-- progress -->
                                        <div class="player_progress">
                                            <div class="progress_bg"></div>
                                            <div class="load_progress"></div>
                                            <div class="play_progress"></div>
                                        </div>

                                        <!-- song time -->
                                        <div class="player_mediaTime">
                                            <!-- current time and total time are separated so you can change the design if needed. -->
                                            <div class="player_mediaTime_current"></div><div class="player_mediaTime_total"></div>
                                        </div>

                                        <!-- volume tooltip -->
                                        <div class="player_volume_tooltip"><p></p></div>

                                        <!-- progress tooltip -->
                                        <div class="player_progress_tooltip"><p></p></div>

                                    </div>

                                    <div class="playlistHolder">
                                        <div class="componentPlaylist">
                                            <div class="playlist_inner">
                                                <!-- playlist items are appended here! -->
                                            </div>
                                        </div>
                                        <!-- preloader -->
                                        <div class="preloader"></div>
                                    </div>
                                </div>

                                <!-- List of playlists -->
                                <div id="playlist_list">
                                    <?php
                                    global $post;
                                    $term = get_query_var('term');
                                    $tax = get_query_var('taxonomy');
                                    $args=array('post_type'=> 'audio', 'post_status'=> 'publish', 'orderby'=> 'menu_order', 'name'=> of_get_option('sc_audio_post_id'), 'posts_per_page'=>1,);
                                    $taxargs = array($tax=>$term);
                                    if($term!='' && $tax!='') { $args  = array_merge($args, $taxargs); }

                                    query_posts($args);

                                    while ( have_posts()):the_post();
                                        $category = wp_get_object_terms( get_the_ID(), 'albums');
                                        ?>

                                        <!-- local playlist -->
                                    <ul id='playlist1'>
                                        <?php
                                        $player = null;
                                        $playlist = null;
                                        $args        = array(
                                            'post_type' => 'attachment',
                                            'numberposts' => -1,
                                            'post_status' => null,
                                            'post_parent' => $post->ID
                                        );
                                        $attachments = get_posts($args);
                                        $arrImages =& get_children('post_type=attachment&orderby=menu_order&order=DESC&post_mime_type=audio/mpeg&post_parent=' . get_the_ID());

                                        if ($arrImages) {
                                            foreach ($arrImages as $attachment) {
                                        ?>
                                        <li class= "playlistItem" data-type='local'
                                            data-mp3Path="<?php echo wp_get_attachment_url($attachment->ID) ?>"
                                            data-oggPath="">

                                            <a class="playlistNonSelected" href='#'><?php echo $attachment->post_title ?></a>
                                        </li>

                                        <?php
                                            }
                                        }
                                        ?>
                                    </ul>

                                    <?php
                                    endwhile;
                                    wp_reset_query();
                                    ?>

                                </div>
                            </div>
                        </div>
                        <?php endif ?>
                        <!-- HOMEPAGE PLAYER END HERE -->


                        <!-- BLOG POST SECTION BEGIN HERE -->
                        <?php if(of_get_option('sc_bloghome') == '1') : ?>
                        <div class="two-third" style="margin-bottom: 0;">
                            <h3 class="title-margin">
                                <?php echo of_get_option('sc_bloghometitle') ?>
                            </h3>

                            <div class="title-border">
                                <div class="title-block"></div>
                            </div>

                            <div class="two-third" style="margin-bottom: 0;">
                                <ul id="home-blog-items"  class="two-third" style="margin: 0;">
                                    <?php
                                    global $post;
                                    $term = get_query_var('term');
                                    $tax = get_query_var('taxonomy');
                                    $args= array('post_type'=> 'post','post_status'=> 'publish', 'caller_get_posts'=>1, 'paged'=>$paged, 'posts_per_page'=>of_get_option('sc_blogpostsperpage'));
                                    $taxargs = array($tax=>$term);
                                    if($term!='' && $tax!='') { $args  = array_merge($args, $taxargs); }

                                    query_posts($args);

                                    while ( have_posts()):the_post();
                                        $categories = wp_get_object_terms( get_the_ID(), 'categories');
                                        ?>


                                        <!-- PROJECT ITEM STARTS -->
                                        <li class="two-third-block <?php foreach ($categories as $category) { echo $category->slug. ' '; } ?>" data-id="id-<?php the_ID(); ?>" data-type="<?php foreach ($categories as $category) { echo $category->slug. ' '; } ?>">
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
                                                            <?php echo excerpt(15); ?>
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
                                                            <?php echo excerpt(15); ?>
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
                                                            <?php echo excerpt(15); ?>
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
                                        <!-- EVENTS ITEM ENDS -->
                                        <?php endwhile;
                                    wp_reset_query();
                                    ?>
                                </ul>
                            </div>
                        </div>
                        <?php endif ?>
                    </div>
                    <!-- BLOG POST SECTION END HERE -->

                    <div class="one-third last" style="margin-bottom: 5px;">
                    <?php if(of_get_option('sc_eventshome') == '1') : ?>
                    <!-- UPCOMING EVENTS SECTION BEGIN HERE -->
                    <div class="one-third">
                        <h3 class="title-margin">
                            <?php echo of_get_option('sc_eventshometitle') ?>
                        </h3>

                        <div class="title-border">
                            <div class="title-block"></div>
                        </div>

                        <div class="one-third-block">
                            <ul id="home-event-items"  class="one-third" style="margin: 0;">
                                <?php
                                global $post;
                                $term = get_query_var('term');
                                $tax = get_query_var('taxonomy');
                                $args=array('post_type'=> 'event','post_status'=> 'publish', 'orderby'=> 'menu_order', 'caller_get_posts'=>1, 'paged'=>$paged, 'posts_per_page'=>of_get_option('sc_eventhomeitemsperpage'));
                                $taxargs = array($tax=>$term);
                                if($term!='' && $tax!='') { $args  = array_merge($args, $taxargs); }

                                query_posts($args);

                                while ( have_posts()):the_post();
                                    $categories = wp_get_object_terms( get_the_ID(), 'event_types');
                                    ?>

                                <!-- PROJECT ITEM STARTS -->
                                <li class="event-home-item <?php foreach ($categories as $category) { echo $category->slug. ' '; } ?>" data-id="id-<?php the_ID(); ?>" data-type="<?php foreach ($categories as $category) { echo $category->slug. ' '; } ?>">
                                    <div class="event-date">
                                        <?php
                                        global $post;
                                        $date = get_post_meta( $post->ID, 'snbp_event_date', true );
                                        echo $date
                                        ?>
                                    </div>

                                    <div class="event-title">
                                        <h5>
                                            <a href="<?php the_permalink() ?>" title="<?php the_title(); ?>" title="<?php the_title(); ?>"> <?php the_title(); ?> </a>
                                        </h5>
                                    </div>

                                    <div class="event-venue">
                                        <?php
                                        global $post;
                                        $venue = get_post_meta( $post->ID, 'snbp_event_venue', true );
                                        echo $venue;
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
                                <!-- EVENTS ITEM ENDS -->
                                <?php endwhile;
                                wp_reset_query();
                                ?>
                            </ul>
                        </div>
                    </div>
                    <?php endif ?>
                    <!-- UPCOMING EVENTS SECTION END HERE -->

                    <!-- LATEST VIDEO SECTION BEGIN HERE -->
                    <?php if(of_get_option('sc_videohome') == '1') : ?>
                        <h3 class="title-margin">
                            <?php echo of_get_option('sc_videohometitle') ?>
                        </h3>

                        <div class="title-border">
                            <div class="title-block"></div>
                        </div>

                        <div class="one-third">
                            <?php
                            $args=array('post_type'=> 'video', 'post_status'=> 'publish', 'orderby'=> 'menu_order', 'name'=> of_get_option('sc_video_post_id'), 'posts_per_page'=>of_get_option('sc_videopostsperhomepage')); query_posts($args);
                            if ( have_posts() ) :
                                ?>
                                <ul id="video-wrapper" class="homepage-video">
                                    <?php
                                    while (have_posts()): the_post();
                                        $categories = wp_get_object_terms( get_the_ID(), 'collections');
                                        ?>
                                        <!-- PROJECT ITEM STARTS -->
                                        <li>
                                            <div class="one-third-block">
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
                                            </div>
                                        </li>
                                        <!-- PROJECT ITEM ENDS -->
                                        <?php
                                    endwhile;
                                    wp_reset_query();
                                    ?>
                                </ul>
                                <?php
                            else :
                                ?>

                                <?php
                            endif;
                            ?>
                        </div>
                    <?php endif ?>
                    <!-- LATEST VIDEO SECTION END HERE -->

                    <?php if(of_get_option('sc_soundcloudhome') == '1') : ?>
                        <h3 class="title-margin">
                            <?php echo of_get_option('sc_soundcloudhometitle') ?>
                        </h3>

                        <div class="title-border">
                            <div class="title-block"></div>
                        </div>

                        <div class="one-third-block">
                            <?php echo of_get_option('sc_soundcloud') ?>
                        </div>
                    <?php endif; ?>

                    </div>

                    <!--<div class="sound-cloud-widget">
                        <?php /*above_footer_widget(); //Action hook */?>
                    </div>-->

                </div><!-- end #content -->
            </div>
            <!-- end #white-background -->
<?php get_footer(); ?>