<?php
/**
 * The default template for displaying content. Used for both single and index/archive/search.
 *
 * @author Usability Dynamics
 * @module festival  
 * @since festival 0.1.0
 */
?>
<article <?php post_class(); ?> data-type="<?php get_post_type(); ?>">

  <?php $img = wp_festival()->get_image_link_by_post_id( get_the_ID(), array( 'width' => '1140', 'height' => '350' ) ); ?>
  <?php if( !empty( $img ) ) : ?>
    <section class="article-image">
      <img class="img-responsive" src="<?php echo $img; ?>" alt="" />
    </section>
  <?php endif; ?>

  <header class="article-header">
		<h1 class="article-title" data-type="post_title"><?php the_title(); ?></h1>
	</header>

  <section class="article-content" data-type="content">
		<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', wp_festival( 'domain' ) ) ); ?>
  </section>

</article>