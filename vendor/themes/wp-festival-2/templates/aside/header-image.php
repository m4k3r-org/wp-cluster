<?php
  /** Ok, we need to get the image URL for the background image */
  if( isset( $post ) && isset( $post->ID ) ){
    /** We're only going to do posts and pages for right now */
    if( in_array( $post->post_type, array( 'page', 'post' ) ) ){
      $image = get_post_meta( $post->ID, 'headerImage', true );
      if( !$image ){
        $image = wp_festival2()->get_image_link_by_post_id( $post->ID );
      }
      $title = get_post_meta( $post->ID, 'headerTitle', true );
      if( !$title ){
        $title = get_the_title( $post->ID );
      }
      $subtitle = get_post_meta( $post->ID, 'headerSubtitle', true );
      if( !$subtitle ){
        $subtitle = $post->post_excerpt;
      }
    }
  }
  
  /** If we are on blog archive */
  if ( is_home() ) {
    $image = get_post_meta( get_option( 'page_for_posts' ), 'headerImage', true );
    $title = get_post_meta( get_option( 'page_for_posts' ), 'headerTitle', true );
    $subtitle = get_post_meta( get_option( 'page_for_posts' ), 'headerSubtitle', true );
  }
  
  /** IF we are on category */
  if ( is_category() ) {
    $category = get_the_category();
    $title = $category[0]->name;
    $subtitle = $category[0]->description;
  }
  
  /** Make sure we have some values */
  if( !isset( $image ) ){
    $image = false;
  }
  if( !isset( $title ) ){
    $title = false;
  }
  if( !isset( $subtitle ) ){
    $subtitle = false;
  }

  if( $post->post_type == 'artist')
  {
    $artist = wp_festival2()->get_post_data( get_the_ID() );

    $image = wp_festival2()->get_artist_image_link( get_the_ID(), array(
      'type' => 'landscapeImage'
    ) );
  }
?>
<header <?php if( $image ): ?>style="background-image:url( '<?php echo $image; ?>' )"<?php endif; ?>>
	<div class="darker-bg"></div>

  <div class="container-fluid">
    <div class="row">
      <div class="col-xs-12">
        <?php if( is_front_page() || $post->post_title == 'Home' ): ?>
          <?php wp_festival2()->section( 'front_header_inside' ); ?>
        <?php elseif( $post->post_type == 'artist' ): ?>
          <?php get_template_part('templates/aside/artist-hero'); ?>
        <?php else: ?>
          <?php if( is_singular( 'post' ) ) : ?>
            <div class="category-image">
              <?php $cats = get_the_category(); ?>
                <?php if( function_exists( 'z_taxonomy_image_url' ) && !empty( z_taxonomy_image_url( $cats[ 0 ]->term_id ) ) )  : ?>
                  <img src="<?php echo z_taxonomy_image_url( $cats[ 0 ]->term_id ); ?>" alt="" />
                <?php endif; ?>
            </div>
          <?php endif; ?>
          <?php if( isset( $title ) && $title ): ?>
            <h2><?php echo $title; ?></h2>
          <?php endif; ?>
          <?php if( isset( $subtitle ) && $subtitle ): ?>
            <h4 class="subtitle"><?php echo $subtitle; ?></h4>
          <?php endif; ?>
        <?php endif; ?>

        <?php if( is_singular( 'post' ) ) : ?>
          <h4 class="clearfix">
            <time datetime="2014-05-24">
							<span class="date">
									<span class="icon-date"></span>
								<?php the_date( 'l, F j, Y' ); ?>
							</span>
            </time>
						<span class="comments">
							<span class="icon-comments"></span> <?php comments_number( 'No Comments', '1 Comment' ); ?>
						</span>
          </h4>
				<?php endif; ?>

				<?php if ( is_singular( [ 'post', 'artist' ] ) ): ?>
          <div class="posts-navigation-prev">
            <?php
                next_post_link('%link', '<span class="icon-left">' . _x() . '</span>' );
            ?>
          </div>
          <div class="posts-navigation-next">
            <?php
              previous_post_link('%link', '<span class="icon-right">' . _x() . '</span>' );
            ?>
          </div>
				<?php endif; ?>


        <div class="widget-area">
          <?php if( $post->post_title == 'Contact' ) wp_festival2()->section( 'header-widgets' ); ?>
        </div>
      </div>
    </div>
  </div>

  <?php if( $post->post_title != 'Contact' && $post->post_type != 'artist' ) : ?>
  <a href="#main" class="nav-arrows clearfix">
    <span class="icon-down-arrow arrow-1"></span>
    <span class="icon-down-arrow arrow-2"></span>
    <span class="icon-down-arrow arrow-3"></span>
  </a>
  <?php endif; ?>

  <?php if( $post->post_type == 'artist' ){
    get_template_part( 'templates/aside/artists-carousel' );
  }
  ?>

</header>

<?php 
/** 
 * We need to get and loop the featured posts. Ultimately this should be in a widget, but we're doing this
 * for brevity. Take a look at article/content.php template - it has the same functionality, and should also
 * be moved. -williams@ud
 */ 
if( is_front_page() || $post->post_title == 'Home' ): ?>
  <h2 id="main" class="latest-blog-posts container-fluid">Latest News and Updates</h2>
  <p class="latest-blog-posts container-fluid">Stay informed and be in the know! The most important festival updates are below.</p>

  <?php echo do_shortcode( '[widget_news_block featured=1]' );?>
<?php endif;
