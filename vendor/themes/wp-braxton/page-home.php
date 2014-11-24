<?php

	/* Template Name: Home */

?>

<?php get_header(); ?>
	<?php $mvp_featured = get_option('mvp_featured_posts'); if ($mvp_featured == "true") { ?>
		<div id="top-story-wrapper">
			<?php if(get_option('mvp_feat_post') == 'Featured Posts 1') { ?>
				<div id="top-story-contain">
					<div id="top-story-middle">
						<div id="middle-img">
							<?php $mvp_slider = get_option('mvp_slider'); if ($mvp_slider == "true") { ?>
								<?php $recent = new WP_Query(array( 'tag' => get_option('mvp_slider_tags'), 'posts_per_page' => get_option('mvp_slider_num')  )); while($recent->have_posts()) : $recent->the_post(); $do_not_duplicate[] = $post->ID; if (isset($do_not_duplicate)) { ?>
								<?php } endwhile; ?>
								<?php if (isset($do_not_duplicate)) { $recent = new WP_Query(array( 'post__not_in' => $do_not_duplicate, 'posts_per_page' => '1'  )); while($recent->have_posts()) : $recent->the_post(); $do_not_duplicate[] = $post->ID; if (isset($do_not_duplicate)) { ?>
									<a href="<?php the_permalink(); ?>" rel="bookmark">
									<?php if (  (function_exists('has_post_thumbnail')) && (has_post_thumbnail())  ) { ?>
										<div class="top-middle-image">
											<?php the_post_thumbnail('post-thumb'); ?>
											<?php if(get_post_meta($post->ID, "mvp_video_embed", true)): ?>
												<div class="video-button">
													<img src="<?php echo get_template_directory_uri(); ?>/images/video-but.png" alt="<?php the_title(); ?>" />
												</div><!--video-button-->
											<?php endif; ?>
										</div><!--top-middle-image-->
										<div id="middle-text">
											<h3><?php $category = get_the_category(); echo $category[0]->cat_name; ?></h3>
											<h2><?php the_title(); ?></h2>
											<p><?php echo excerpt(22); ?></p>
										</div><!--middle-text-->
									<?php } ?>
									</a>
								<?php } endwhile; } ?>
							<?php } else { ?>
								<?php $recent = new WP_Query('posts_per_page=1'); while($recent->have_posts()) : $recent->the_post(); $do_not_duplicate[] = $post->ID; if (isset($do_not_duplicate)) { ?>
									<a href="<?php the_permalink(); ?>" rel="bookmark">
									<?php if (  (function_exists('has_post_thumbnail')) && (has_post_thumbnail())  ) { ?>
										<div class="top-middle-image">
											<?php the_post_thumbnail('post-thumb'); ?>
											<?php if(get_post_meta($post->ID, "mvp_video_embed", true)): ?>
												<div class="video-button">
													<img src="<?php echo get_template_directory_uri(); ?>/images/video-but.png" alt="<?php the_title(); ?>" />
												</div><!--video-button-->
											<?php endif; ?>
										</div><!--top-middle-image-->
										<div id="middle-text">
											<h3><?php $category = get_the_category(); echo $category[0]->cat_name; ?></h3>
											<h2><?php the_title(); ?></h2>
											<p><?php echo excerpt(22); ?></p>
										</div><!--middle-text-->
									<?php } ?>
									</a>
								<?php } endwhile; ?>
							<?php } ?>
						</div><!--middle-img-->
					</div><!--top-story-middle-->
					<div id="top-story-left">
						<?php if(get_option('mvp_featured_left') == 'Select a category:') { ?>
						<?php } else { ?>
							<span class="top-header-contain"><h3><?php echo get_option('mvp_featured_left'); ?></h3></span>
							<ul class="top-stories">
								<?php if (!empty($do_not_duplicate)) { $current_category = get_option('mvp_featured_left'); $category_id = get_cat_ID($current_category); $recent = new WP_Query(array( 'cat' => $category_id, 'post__not_in' => $do_not_duplicate, 'posts_per_page' => '2'  )); while($recent->have_posts()) : $recent->the_post(); $do_not_duplicate[] = $post->ID; if (isset($do_not_duplicate)) { ?>
								<li>
									<a href="<?php the_permalink(); ?>" rel="bookmark">
									<?php if (  (function_exists('has_post_thumbnail')) && (has_post_thumbnail())  ) { ?>
										<div class="top-story-image">
											<?php the_post_thumbnail('small-thumb'); ?>
											<?php if(get_post_meta($post->ID, "mvp_video_embed", true)): ?>
												<div class="video-button">
													<img src="<?php echo get_template_directory_uri(); ?>/images/video-but.png" alt="<?php the_title(); ?>" />
												</div><!--video-button-->
											<?php endif; ?>
										</div><!--top-story-image-->
									<?php } ?>
									<h2><?php the_title(); ?></h2>
									</a>
								</li>
								<?php } endwhile; } ?>
							</ul>
						<?php } ?>
					</div><!--top-story-left-->
				</div><!--top-story-contain-->
				<div id="top-story-right">
					<?php if(get_option('mvp_featured_right') == 'Select a category:') { ?>
					<?php } else { ?>
						<span class="top-header-contain"><h3><?php echo get_option('mvp_featured_right'); ?></h3></span>
						<ul class="top-stories">
							<?php if (!empty($do_not_duplicate)) { $current_category = get_option('mvp_featured_right'); $category_id = get_cat_ID($current_category); $recent = new WP_Query(array( 'cat' => $category_id, 'post__not_in' => $do_not_duplicate, 'posts_per_page' => '2'  )); while($recent->have_posts()) : $recent->the_post(); $do_not_duplicate[] = $post->ID; if (!empty($do_not_duplicate)) { ?>
							<li>
								<a href="<?php the_permalink() ?>">
								<?php if (  (function_exists('has_post_thumbnail')) && (has_post_thumbnail())  ) { ?>
									<div class="top-story-image">
										<?php the_post_thumbnail('small-thumb'); ?>
										<?php if(get_post_meta($post->ID, "mvp_video_embed", true)): ?>
											<div class="video-button">
												<img src="<?php echo get_template_directory_uri(); ?>/images/video-but.png" alt="<?php the_title(); ?>" />
											</div><!--video-button-->
										<?php endif; ?>
									</div><!--top-story-image-->
								<?php } ?>
								<h2><?php the_title(); ?></h2>
								</a>
							</li>
							<?php } endwhile; } ?>
						</ul>
					<?php } ?>
				</div><!--top-story-right-->
			<?php } else if(get_option('mvp_feat_post') == 'Featured Posts 2') { ?>
				<?php $mvp_slider = get_option('mvp_slider'); if ($mvp_slider == "true") { ?>
					<div id="feat1-main-wrapper">
						<?php $recent = new WP_Query(array( 'tag' => get_option('mvp_slider_tags'), 'posts_per_page' => get_option('mvp_slider_num')  )); while($recent->have_posts()) : $recent->the_post(); $do_not_duplicate[] = $post->ID; if (isset($do_not_duplicate)) { ?>
						<?php } endwhile; ?>
						<?php if (isset($do_not_duplicate)) { $recent = new WP_Query(array( 'post__not_in' => $do_not_duplicate, 'posts_per_page' => '1'  )); while($recent->have_posts()) : $recent->the_post(); $do_not_duplicate[] = $post->ID; if (isset($do_not_duplicate)) { ?>
							<a href="<?php the_permalink(); ?>" rel="bookmark">
							<?php if (  (function_exists('has_post_thumbnail')) && (has_post_thumbnail())  ) { ?>
								<div class="feat1-main-img">
									<?php the_post_thumbnail('post-thumb'); ?>
									<?php if(get_post_meta($post->ID, "mvp_video_embed", true)): ?>
										<div class="video-button">
											<img src="<?php echo get_template_directory_uri(); ?>/images/video-but.png" alt="<?php the_title(); ?>" />
										</div><!--video-button-->
									<?php endif; ?>
								</div><!--feat1-main-img-->
							<?php } ?>
							<div class="feat1-main-text">
								<h3><?php $category = get_the_category(); echo $category[0]->cat_name; ?></h3>
								<?php if(get_post_meta($post->ID, "mvp_featured_headline", true)): ?>
									<h2><?php echo get_post_meta($post->ID, "mvp_featured_headline", true); ?></h2>
								<?php else: ?>
									<h2><?php the_title(); ?></h2>
								<?php endif; ?>
							</div><!--feat1-main-text"-->
							</a>
						<?php } endwhile; } ?>
					</div><!--feat1-main-wrapper-->
					<div id="feat1-left-wrapper">
						<?php if (!empty($do_not_duplicate)) { $recent = new WP_Query(array( 'post__not_in' => $do_not_duplicate, 'posts_per_page' => '1', 'offset' => '1'  )); while($recent->have_posts()) : $recent->the_post(); $do_not_duplicate[] = $post->ID; if (isset($do_not_duplicate)) { ?>
							<a href="<?php the_permalink(); ?>" rel="bookmark">
							<?php if (  (function_exists('has_post_thumbnail')) && (has_post_thumbnail())  ) { ?>
								<div class="feat1-left-img">
									<?php the_post_thumbnail('medium-thumb'); ?>
									<?php if(get_post_meta($post->ID, "mvp_video_embed", true)): ?>
										<div class="video-button">
											<img src="<?php echo get_template_directory_uri(); ?>/images/video-but.png" alt="<?php the_title(); ?>" />
										</div><!--video-button-->
									<?php endif; ?>
									<div class="feat1-left-tri">
										<span class="feat1-tri-up"></span>
									</div><!--feat1-left-tri-->
								</div><!--feat1-left-img-->
							<?php } ?>
							<div class="feat1-left-text">
								<h2><?php the_title(); ?></h2>
								<p><?php echo excerpt(18); ?></p>
							</div><!--feat1-left-text-->
							</a>
						<?php } endwhile; } ?>
					</div><!--feat1-left-wrapper-->
					<div id="feat1-right-wrapper">
						<?php if (!empty($do_not_duplicate)) { $recent = new WP_Query(array( 'post__not_in' => $do_not_duplicate, 'posts_per_page' => '1', 'offset' => '2'  )); while($recent->have_posts()) : $recent->the_post(); $do_not_duplicate[] = $post->ID; if (isset($do_not_duplicate)) { ?>
							<a href="<?php the_permalink(); ?>" rel="bookmark">
							<div class="feat1-right-text">
								<h2><?php the_title(); ?></h2>
								<p><?php echo excerpt(18); ?></p>
							</div><!--feat1-right-text-->
							<?php if (  (function_exists('has_post_thumbnail')) && (has_post_thumbnail())  ) { ?>
								<div class="feat1-right-img">
									<?php the_post_thumbnail('medium-thumb'); ?>
									<?php if(get_post_meta($post->ID, "mvp_video_embed", true)): ?>
										<div class="video-button">
											<img src="<?php echo get_template_directory_uri(); ?>/images/video-but.png" alt="<?php the_title(); ?>" />
										</div><!--video-button-->
									<?php endif; ?>
									<div class="feat1-right-tri">
										<span class="feat1-tri-down"></span>
									</div><!--feat1-right-tri-->
								</div><!--feat1-right-img-->
							<?php } ?>
							</a>
						<?php } endwhile; } ?>
					</div><!--feat1-right-wrapper-->
				<?php } else { ?>
					<div id="feat1-main-wrapper">
						<?php $recent = new WP_Query(array( 'posts_per_page' => '1'  )); while($recent->have_posts()) : $recent->the_post(); $do_not_duplicate[] = $post->ID; if (isset($do_not_duplicate)) { ?>
							<a href="<?php the_permalink(); ?>" rel="bookmark">
							<?php if (  (function_exists('has_post_thumbnail')) && (has_post_thumbnail())  ) { ?>
								<div class="feat1-main-img">
									<?php the_post_thumbnail('post-thumb'); ?>
									<?php if(get_post_meta($post->ID, "mvp_video_embed", true)): ?>
										<div class="video-button">
											<img src="<?php echo get_template_directory_uri(); ?>/images/video-but.png" alt="<?php the_title(); ?>" />
										</div><!--video-button-->
									<?php endif; ?>
								</div><!--feat1-main-img-->
							<?php } ?>
							<div class="feat1-main-text">
								<h3><?php $category = get_the_category(); echo $category[0]->cat_name; ?></h3>
								<?php if(get_post_meta($post->ID, "mvp_featured_headline", true)): ?>
									<h2><?php echo get_post_meta($post->ID, "mvp_featured_headline", true); ?></h2>
								<?php else: ?>
									<h2><?php the_title(); ?></h2>
								<?php endif; ?>
							</div><!--feat1-main-text"-->
							</a>
						<?php } endwhile; ?>
					</div><!--feat1-main-wrapper-->
					<div id="feat1-left-wrapper">
						<?php if (!empty($do_not_duplicate)) { $recent = new WP_Query(array( 'post__not_in' => $do_not_duplicate, 'posts_per_page' => '1', 'offset' => '1'  )); while($recent->have_posts()) : $recent->the_post(); $do_not_duplicate[] = $post->ID; if (isset($do_not_duplicate)) { ?>
							<a href="<?php the_permalink(); ?>" rel="bookmark">
							<?php if (  (function_exists('has_post_thumbnail')) && (has_post_thumbnail())  ) { ?>
								<div class="feat1-left-img">
									<?php the_post_thumbnail('medium-thumb'); ?>
									<?php if(get_post_meta($post->ID, "mvp_video_embed", true)): ?>
										<div class="video-button">
											<img src="<?php echo get_template_directory_uri(); ?>/images/video-but.png" alt="<?php the_title(); ?>" />
										</div><!--video-button-->
									<?php endif; ?>
									<div class="feat1-left-tri">
										<span class="feat1-tri-up"></span>
									</div><!--feat1-left-tri-->
								</div><!--feat1-left-img-->
							<?php } ?>
							<div class="feat1-left-text">
								<h2><?php the_title(); ?></h2>
								<p><?php echo excerpt(18); ?></p>
							</div><!--feat1-left-text-->
							</a>
						<?php } endwhile; } ?>
					</div><!--feat1-left-wrapper-->
					<div id="feat1-right-wrapper">
						<?php if (!empty($do_not_duplicate)) { $recent = new WP_Query(array( 'post__not_in' => $do_not_duplicate, 'posts_per_page' => '1', 'offset' => '2'  )); while($recent->have_posts()) : $recent->the_post(); $do_not_duplicate[] = $post->ID; if (isset($do_not_duplicate)) { ?>
							<a href="<?php the_permalink(); ?>" rel="bookmark">
							<div class="feat1-right-text">
								<h2><?php the_title(); ?></h2>
								<p><?php echo excerpt(18); ?></p>
							</div><!--feat1-right-text-->
							<?php if (  (function_exists('has_post_thumbnail')) && (has_post_thumbnail())  ) { ?>
								<div class="feat1-right-img">
									<?php the_post_thumbnail('medium-thumb'); ?>
									<?php if(get_post_meta($post->ID, "mvp_video_embed", true)): ?>
										<div class="video-button">
											<img src="<?php echo get_template_directory_uri(); ?>/images/video-but.png" alt="<?php the_title(); ?>" />
										</div><!--video-button-->
									<?php endif; ?>
									<div class="feat1-right-tri">
										<span class="feat1-tri-down"></span>
									</div><!--feat1-right-tri-->
								</div><!--feat1-right-img-->
							<?php } ?>
							</a>
						<?php } endwhile; } ?>
					</div><!--feat1-right-wrapper-->
				<?php } ?>
			<?php } else if(get_option('mvp_feat_post') == 'Featured Posts 3') { ?>
				<?php $mvp_slider = get_option('mvp_slider'); if ($mvp_slider == "true") { ?>
					<?php $recent = new WP_Query(array( 'tag' => get_option('mvp_slider_tags'), 'posts_per_page' => get_option('mvp_slider_num')  )); while($recent->have_posts()) : $recent->the_post(); $do_not_duplicate[] = $post->ID; if (isset($do_not_duplicate)) { ?>
					<?php } endwhile; ?>
					<?php if (isset($do_not_duplicate)) { $recent = new WP_Query(array( 'post__not_in' => $do_not_duplicate, 'posts_per_page' => '1'  )); while($recent->have_posts()) : $recent->the_post(); $do_not_duplicate[] = $post->ID; if (isset($do_not_duplicate)) { ?>
						<a href="<?php the_permalink(); ?>" rel="bookmark">
						<?php if (  (function_exists('has_post_thumbnail')) && (has_post_thumbnail())  ) { ?>
							<div id="feat2-main-img">
								<?php the_post_thumbnail('post-thumb'); ?>
								<?php if(get_post_meta($post->ID, "mvp_video_embed", true)): ?>
									<div class="video-button">
										<img src="<?php echo get_template_directory_uri(); ?>/images/video-but.png" alt="<?php the_title(); ?>" />
									</div><!--video-button-->
								<?php endif; ?>
								<div class="feat2-main-tri">
									<span class="feat2-tri-left"></span>
								</div><!--feat2-main-tri-->
							</div><!--feat2-main-img-->
						<?php } ?>
						<div id="feat2-main-text">
							<h3><?php $category = get_the_category(); echo $category[0]->cat_name; ?></h3>
							<h2><?php the_title(); ?></h2>
							<p><?php echo excerpt(26); ?></p>
						</div><!--feat2-main-text-->
						</a>
					<?php } endwhile; } ?>
				<?php } else { ?>
					<?php $recent = new WP_Query(array( 'posts_per_page' => '1'  )); while($recent->have_posts()) : $recent->the_post(); $do_not_duplicate[] = $post->ID; if (isset($do_not_duplicate)) { ?>
						<a href="<?php the_permalink(); ?>" rel="bookmark">
						<?php if (  (function_exists('has_post_thumbnail')) && (has_post_thumbnail())  ) { ?>
							<div id="feat2-main-img">
								<?php the_post_thumbnail('post-thumb'); ?>
								<?php if(get_post_meta($post->ID, "mvp_video_embed", true)): ?>
									<div class="video-button">
										<img src="<?php echo get_template_directory_uri(); ?>/images/video-but.png" alt="<?php the_title(); ?>" />
									</div><!--video-button-->
								<?php endif; ?>
								<div class="feat2-main-tri">
									<span class="feat2-tri-left"></span>
								</div><!--feat2-main-tri-->
							</div><!--feat2-main-img-->
						<?php } ?>
						<div id="feat2-main-text">
							<h2><?php the_title(); ?></h2>
							<p><?php echo excerpt(26); ?></p>
						</div><!--feat2-main-text-->
						</a>
					<?php } endwhile; ?>
				<?php } ?>
			<?php } else { ?>
				<div id="top-story-contain">
					<div id="top-story-middle">
						<div id="middle-img">
							<?php $mvp_slider = get_option('mvp_slider'); if ($mvp_slider == "true") { ?>
								<?php $recent = new WP_Query(array( 'tag' => get_option('mvp_slider_tags'), 'posts_per_page' => get_option('mvp_slider_num')  )); while($recent->have_posts()) : $recent->the_post(); $do_not_duplicate[] = $post->ID; if (isset($do_not_duplicate)) { ?>
								<?php } endwhile; ?>
								<?php if (isset($do_not_duplicate)) { $recent = new WP_Query(array( 'post__not_in' => $do_not_duplicate, 'posts_per_page' => '1'  )); while($recent->have_posts()) : $recent->the_post(); $do_not_duplicate[] = $post->ID; if (isset($do_not_duplicate)) { ?>
									<a href="<?php the_permalink(); ?>" rel="bookmark">
									<?php if (  (function_exists('has_post_thumbnail')) && (has_post_thumbnail())  ) { ?>
										<div class="top-middle-image">
											<?php the_post_thumbnail('post-thumb'); ?>
											<?php if(get_post_meta($post->ID, "mvp_video_embed", true)): ?>
												<div class="video-button">
													<img src="<?php echo get_template_directory_uri(); ?>/images/video-but.png" alt="<?php the_title(); ?>" />
												</div><!--video-button-->
											<?php endif; ?>
										</div><!--top-middle-image-->
										<div id="middle-text">
											<h3><?php $category = get_the_category(); echo $category[0]->cat_name; ?></h3>
											<h2><?php the_title(); ?></h2>
											<p><?php echo excerpt(22); ?></p>
										</div><!--middle-text-->
									<?php } ?>
									</a>
								<?php } endwhile; } ?>
							<?php } else { ?>
								<?php $recent = new WP_Query('posts_per_page=1'); while($recent->have_posts()) : $recent->the_post(); $do_not_duplicate[] = $post->ID; if (isset($do_not_duplicate)) { ?>
									<a href="<?php the_permalink(); ?>" rel="bookmark">
									<?php if (  (function_exists('has_post_thumbnail')) && (has_post_thumbnail())  ) { ?>
										<div class="top-middle-image">
											<?php the_post_thumbnail('post-thumb'); ?>
											<?php if(get_post_meta($post->ID, "mvp_video_embed", true)): ?>
												<div class="video-button">
													<img src="<?php echo get_template_directory_uri(); ?>/images/video-but.png" alt="<?php the_title(); ?>" />
												</div><!--video-button-->
											<?php endif; ?>
										</div><!--top-middle-image-->
										<div id="middle-text">
											<h3><?php $category = get_the_category(); echo $category[0]->cat_name; ?></h3>
											<h2><?php the_title(); ?></h2>
											<p><?php echo excerpt(22); ?></p>
										</div><!--middle-text-->
									<?php } ?>
									</a>
								<?php } endwhile; ?>
							<?php } ?>
						</div><!--middle-img-->
					</div><!--top-story-middle-->
					<div id="top-story-left">
						<?php if(get_option('mvp_featured_left') == 'Select a category:') { ?>
						<?php } else { ?>
							<span class="top-header-contain"><h3><?php echo get_option('mvp_featured_left'); ?></h3></span>
							<ul class="top-stories">
								<?php if (!empty($do_not_duplicate)) { $current_category = get_option('mvp_featured_left'); $category_id = get_cat_ID($current_category); $recent = new WP_Query(array( 'cat' => $category_id, 'post__not_in' => $do_not_duplicate, 'posts_per_page' => '2'  )); while($recent->have_posts()) : $recent->the_post(); $do_not_duplicate[] = $post->ID; if (isset($do_not_duplicate)) { ?>
								<li>
									<a href="<?php the_permalink(); ?>" rel="bookmark">
									<?php if (  (function_exists('has_post_thumbnail')) && (has_post_thumbnail())  ) { ?>
										<div class="top-story-image">
											<?php the_post_thumbnail('small-thumb'); ?>
											<?php if(get_post_meta($post->ID, "mvp_video_embed", true)): ?>
												<div class="video-button">
													<img src="<?php echo get_template_directory_uri(); ?>/images/video-but.png" alt="<?php the_title(); ?>" />
												</div><!--video-button-->
											<?php endif; ?>
										</div><!--top-story-image-->
									<?php } ?>
									<h2><?php the_title(); ?></h2>
									</a>
								</li>
								<?php } endwhile; } ?>
							</ul>
						<?php } ?>
					</div><!--top-story-left-->
				</div><!--top-story-contain-->
				<div id="top-story-right">
					<?php if(get_option('mvp_featured_right') == 'Select a category:') { ?>
					<?php } else { ?>
						<span class="top-header-contain"><h3><?php echo get_option('mvp_featured_right'); ?></h3></span>
						<ul class="top-stories">
							<?php if (!empty($do_not_duplicate)) { $current_category = get_option('mvp_featured_right'); $category_id = get_cat_ID($current_category); $recent = new WP_Query(array( 'cat' => $category_id, 'post__not_in' => $do_not_duplicate, 'posts_per_page' => '2'  )); while($recent->have_posts()) : $recent->the_post(); $do_not_duplicate[] = $post->ID; if (!empty($do_not_duplicate)) { ?>
							<li>
								<a href="<?php the_permalink() ?>">
								<?php if (  (function_exists('has_post_thumbnail')) && (has_post_thumbnail())  ) { ?>
									<div class="top-story-image">
										<?php the_post_thumbnail('small-thumb'); ?>
										<?php if(get_post_meta($post->ID, "mvp_video_embed", true)): ?>
											<div class="video-button">
												<img src="<?php echo get_template_directory_uri(); ?>/images/video-but.png" alt="<?php the_title(); ?>" />
											</div><!--video-button-->
										<?php endif; ?>
									</div><!--top-story-image-->
								<?php } ?>
								<h2><?php the_title(); ?></h2>
								</a>
							</li>
							<?php } endwhile; } ?>
						</ul>
					<?php } ?>
				</div><!--top-story-right-->
			<?php } ?>
		</div><!--top-story-wrapper-->
	<?php } ?>
	<div id="content-wrapper">
		<div id="content-main">
			<div id="home-main">
				<?php if(get_option('mvp_home_layout') == 'Blog') { ?>
				<h3 class="home-widget-header"><?php echo get_option('mvp_blog_header'); ?></h3>
				<?php if(get_option('mvp_blog_layout') == 'large') { ?>
				<div class="home-widget">
					<ul class="wide-widget infinite-content">
						<?php $mvp_slider = get_option('mvp_slider'); $mvp_posts = get_option('mvp_featured_posts'); if (($mvp_slider == "true") || ($mvp_posts == "true")) { ?>
							<?php $recent = new WP_Query(array( 'tag' => get_option('mvp_slider_tags'), 'posts_per_page' => get_option('mvp_slider_num')  )); while($recent->have_posts()) : $recent->the_post(); $do_not_duplicate[] = $post->ID; if (isset($do_not_duplicate)) { ?>
							<?php } endwhile; ?>
							<?php if (isset($do_not_duplicate)) { $mvp_posts_num = get_option('mvp_posts_num'); $paged = (get_query_var('page')) ? get_query_var('page') : 1; query_posts(array( 'posts_per_page' => $mvp_posts_num, 'post__not_in'=>$do_not_duplicate, 'paged' =>$paged )); if (have_posts()) : while (have_posts()) : the_post(); ?>
							<li class="infinite-post">
								<a href="<?php the_permalink(); ?>" rel="bookmark">
								<div class="wide-img">
									<?php if (  (function_exists('has_post_thumbnail')) && (has_post_thumbnail())  ) { ?>
									<?php $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'post-thumb' ); ?>
									<span class="wide-shade">
										<img class="lazy" src="<?php echo get_template_directory_uri(); ?>/images/trans.gif" data-original="<?php echo $thumb['0']; ?>" />
									</span>
									<?php } ?>
									<noscript><span class="wide-shade"><img class="wide-shade" src="<?php echo $thumb['0']; ?>" /></span></noscript>
									<?php if(get_post_meta($post->ID, "mvp_video_embed", true)): ?>
										<div class="video-button">
											<img src="<?php echo get_template_directory_uri(); ?>/images/video-but.png" alt="<?php the_title(); ?>" />
										</div><!--video-button-->
									<?php endif; ?>
									<span class="widget-cat-contain"><h3 class="widget-cat"><?php $category = get_the_category(); echo $category[0]->cat_name; ?></h3></span>
								</div><!--wide-img-->
								<div class="wide-text">
									<h2><?php the_title(); ?></h2>
									<span class="widget-info"><span class="widget-author"><?php the_author(); ?></span> | <?php the_time(get_option('date_format')); ?></span>
									<p><?php echo excerpt(20); ?></p>
								</div><!--wide-text-->
								</a>
							</li>
							<?php endwhile; endif; } ?>
						<?php } else { ?>
							<?php $mvp_posts_num = get_option('mvp_posts_num'); $paged = (get_query_var('page')) ? get_query_var('page') : 1; query_posts(array( 'posts_per_page' => $mvp_posts_num, 'paged' =>$paged )); if (have_posts()) : while (have_posts()) : the_post(); ?>
							<li class="infinite-post">
								<a href="<?php the_permalink(); ?>" rel="bookmark">
								<div class="wide-img">
									<?php if (  (function_exists('has_post_thumbnail')) && (has_post_thumbnail())  ) { ?>
									<?php $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'post-thumb' ); ?>
									<span class="wide-shade">
										<img class="lazy" src="<?php echo get_template_directory_uri(); ?>/images/trans.gif" data-original="<?php echo $thumb['0']; ?>" />
									</span>
									<?php } ?>
									<noscript><span class="wide-shade"><img class="wide-shade" src="<?php echo $thumb['0']; ?>" /></span></noscript>
									<?php if(get_post_meta($post->ID, "mvp_video_embed", true)): ?>
										<div class="video-button">
											<img src="<?php echo get_template_directory_uri(); ?>/images/video-but.png" alt="<?php the_title(); ?>" />
										</div><!--video-button-->
									<?php endif; ?>
									<span class="widget-cat-contain"><h3 class="widget-cat"><?php $category = get_the_category(); echo $category[0]->cat_name; ?></h3></span>
								</div><!--wide-img-->
								<div class="wide-text">
									<h2><?php the_title(); ?></h2>
									<span class="widget-info"><span class="widget-author"><?php the_author(); ?></span> | <?php the_time(get_option('date_format')); ?></span>
									<p><?php echo excerpt(20); ?></p>
								</div><!--wide-text-->
								</a>
							</li>
							<?php endwhile; endif; ?>
						<?php } ?>
					</ul>
				<div class="nav-links">
					<?php if (function_exists("pagination")) { pagination($wp_query->max_num_pages); } ?>
				</div><!--nav-links-->
				</div><!--home-widget-->
				<?php } else if(get_option('mvp_blog_layout') == 'list') { ?>
				<div class="home-widget">
					<ul class="home-list infinite-content">
						<?php $mvp_slider = get_option('mvp_slider'); $mvp_posts = get_option('mvp_featured_posts'); if (($mvp_slider == "true") || ($mvp_posts == "true")) { ?>
							<?php $recent = new WP_Query(array( 'tag' => get_option('mvp_slider_tags'), 'posts_per_page' => get_option('mvp_slider_num')  )); while($recent->have_posts()) : $recent->the_post(); $do_not_duplicate[] = $post->ID; if (isset($do_not_duplicate)) { ?>
							<?php } endwhile; ?>
							<?php if (isset($do_not_duplicate)) { $mvp_posts_num = get_option('mvp_posts_num'); $paged = (get_query_var('page')) ? get_query_var('page') : 1; query_posts(array( 'posts_per_page' => $mvp_posts_num, 'post__not_in'=>$do_not_duplicate, 'paged' =>$paged )); if (have_posts()) : while (have_posts()) : the_post(); ?>
							<li class="infinite-post">
								<a href="<?php the_permalink(); ?>" rel="bookmark">
								<div class="home-list-img">
									<?php if (  (function_exists('has_post_thumbnail')) && (has_post_thumbnail())  ) { ?>
									<?php $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'medium-thumb' ); ?>
									<img class="lazy" src="<?php echo get_template_directory_uri(); ?>/images/trans.gif" data-original="<?php echo $thumb['0']; ?>" />

									<?php } ?>
									<noscript><?php the_post_thumbnail('medium-thumb'); ?></noscript>
									<?php if(get_post_meta($post->ID, "mvp_video_embed", true)): ?>
										<div class="video-button">
											<img src="<?php echo get_template_directory_uri(); ?>/images/video-but.png" alt="<?php the_title(); ?>" />
										</div><!--video-button-->
									<?php endif; ?>
									<span class="widget-cat-contain"><h3 class="widget-cat"><?php $category = get_the_category(); echo $category[0]->cat_name; ?></h3></span>
								</div><!--home-list-img-->
								<div class="home-list-content">
									<span class="widget-info"><span class="widget-author"><?php the_author(); ?></span> | <?php the_time(get_option('date_format')); ?></span>
									<h2><?php the_title(); ?></h2>
									<p><?php echo excerpt(19); ?></p>
								</div><!--home-list-content-->
								</a>
							</li>
							<?php endwhile; endif; } ?>
						<?php } else { ?>
							<?php $mvp_posts_num = get_option('mvp_posts_num'); $paged = (get_query_var('page')) ? get_query_var('page') : 1; query_posts(array( 'posts_per_page' => $mvp_posts_num, 'paged' =>$paged )); if (have_posts()) : while (have_posts()) : the_post(); ?>
							<li class="infinite-post">
								<a href="<?php the_permalink(); ?>" rel="bookmark">
								<div class="home-list-img">
									<?php if (  (function_exists('has_post_thumbnail')) && (has_post_thumbnail())  ) { ?>
									<?php $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'medium-thumb' ); ?>
									<img class="lazy" src="<?php echo get_template_directory_uri(); ?>/images/trans.gif" data-original="<?php echo $thumb['0']; ?>" />

									<?php } ?>
									<noscript><?php the_post_thumbnail('medium-thumb'); ?></noscript>
									<?php if(get_post_meta($post->ID, "mvp_video_embed", true)): ?>
										<div class="video-button">
											<img src="<?php echo get_template_directory_uri(); ?>/images/video-but.png" alt="<?php the_title(); ?>" />
										</div><!--video-button-->
									<?php endif; ?>
									<span class="widget-cat-contain"><h3 class="widget-cat"><?php $category = get_the_category(); echo $category[0]->cat_name; ?></h3></span>
								</div><!--home-list-img-->
								<div class="home-list-content">
									<span class="widget-info"><span class="widget-author"><?php the_author(); ?></span> | <?php the_time(get_option('date_format')); ?></span>
									<h2><?php the_title(); ?></h2>
									<p><?php echo excerpt(19); ?></p>
								</div><!--home-list-content-->
								</a>
							</li>
							<?php endwhile; endif; ?>
						<?php } ?>
					</ul>
				<div class="nav-links">
					<?php if (function_exists("pagination")) { pagination($wp_query->max_num_pages); } ?>
				</div><!--nav-links-->
				</div><!--home-widget-->
				<?php } else if(get_option('mvp_blog_layout') == 'columns') { ?>
				<div class="home-widget">
					<ul class="split-columns infinite-content">
						<?php $mvp_slider = get_option('mvp_slider'); $mvp_posts = get_option('mvp_featured_posts'); if (($mvp_slider == "true") || ($mvp_posts == "true")) { ?>
							<?php $recent = new WP_Query(array( 'tag' => get_option('mvp_slider_tags'), 'posts_per_page' => get_option('mvp_slider_num')  )); while($recent->have_posts()) : $recent->the_post(); $do_not_duplicate[] = $post->ID; if (isset($do_not_duplicate)) { ?>
							<?php } endwhile; ?>
							<?php if (isset($do_not_duplicate)) { $mvp_posts_num = get_option('mvp_posts_num'); $paged = (get_query_var('page')) ? get_query_var('page') : 1; query_posts(array( 'posts_per_page' => $mvp_posts_num, 'post__not_in'=>$do_not_duplicate, 'paged' =>$paged )); if (have_posts()) : while (have_posts()) : the_post(); ?>
							<li class="infinite-post">
								<a href="<?php the_permalink(); ?>" rel="bookmark">
								<div class="split-img">
									<?php if (  (function_exists('has_post_thumbnail')) && (has_post_thumbnail())  ) { ?>
									<?php $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'medium-thumb' );
$url = $thumb['0']; ?>
									<img class="lazy" src="<?php echo get_template_directory_uri(); ?>/images/trans.gif" data-original="<?php echo $url; ?>" />

									<?php } ?>
									<noscript><?php the_post_thumbnail('medium-thumb'); ?></noscript>
									<?php if(get_post_meta($post->ID, "mvp_video_embed", true)): ?>
										<div class="video-button">
											<img src="<?php echo get_template_directory_uri(); ?>/images/video-but.png" alt="<?php the_title(); ?>" />
										</div><!--video-button-->
									<?php endif; ?>
									<span class="widget-cat-contain"><h3 class="widget-cat"><?php $category = get_the_category(); echo $category[0]->cat_name; ?></h3></span>
								</div><!--split-img-->
								<div class="split-text">
									<span class="widget-info"><span class="widget-author"><?php the_author(); ?></span> | <?php the_time(get_option('date_format')); ?></span>
									<h2><?php the_title(); ?></h2>
									<p><?php echo excerpt(19); ?></p>
								</div><!--split-text-->
								</a>
							</li>
							<?php endwhile; endif; } ?>
						<?php } else { ?>
							<?php $mvp_posts_num = get_option('mvp_posts_num'); $paged = (get_query_var('page')) ? get_query_var('page') : 1; query_posts(array( 'posts_per_page' => $mvp_posts_num, 'paged' =>$paged )); if (have_posts()) : while (have_posts()) : the_post(); ?>
							<li class="infinite-post">
								<a href="<?php the_permalink(); ?>" rel="bookmark">
								<div class="split-img">
									<?php if (  (function_exists('has_post_thumbnail')) && (has_post_thumbnail())  ) { ?>
									<?php $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'medium-thumb' );
$url = $thumb['0']; ?>
									<img class="lazy" src="<?php echo get_template_directory_uri(); ?>/images/trans.gif" data-original="<?php echo $url; ?>" />

									<?php } ?>
									<noscript><?php the_post_thumbnail('medium-thumb'); ?></noscript>
									<?php if(get_post_meta($post->ID, "mvp_video_embed", true)): ?>
										<div class="video-button">
											<img src="<?php echo get_template_directory_uri(); ?>/images/video-but.png" alt="<?php the_title(); ?>" />
										</div><!--video-button-->
									<?php endif; ?>
									<span class="widget-cat-contain"><h3 class="widget-cat"><?php $category = get_the_category(); echo $category[0]->cat_name; ?></h3></span>
								</div><!--split-img-->
								<div class="split-text">
									<span class="widget-info"><span class="widget-author"><?php the_author(); ?></span> | <?php the_time(get_option('date_format')); ?></span>
									<h2><?php the_title(); ?></h2>
									<p><?php echo excerpt(19); ?></p>
								</div><!--split-text-->
								</a>
							</li>
							<?php endwhile; endif; ?>
						<?php } ?>
					</ul>
				<div class="nav-links">
					<?php if (function_exists("pagination")) { pagination($wp_query->max_num_pages); } ?>
				</div><!--nav-links-->
				</div><!--home-widget-->
				<?php } ?>
				<?php } else if(get_option('mvp_home_layout') == 'Widgets') { ?>
					<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Homepage Widget Area')): endif; ?>
				<?php } ?>
			</div><!--home-main-->
		</div><!--content-main-->
		<?php get_sidebar('home'); ?>
	</div><!--content-wrapper-->
</div><!--main-wrapper-->
<?php get_footer(); ?>