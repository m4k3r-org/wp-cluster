<?php
/**
 * The default template for displaying content. Used for both single and index/archive/search.
 *
 * @author Usability Dynamics
 * @module festival
 * @since festival 0.1.0
 */
?>

<header class="article-header">
  <h1 class="article-title" data-type="post_title"><?php the_title(); ?></h1>
</header>

<section class="meta">
  <span class="date"><i class="icon-calendar"></i> <?php the_time('d M, Y'); ?></span>
  <?php /* Disable category meta on single post for now.
  <span class="category"><label><?php _e( 'Category', wp_festival( 'domain' ) ); ?>:</label><?php the_category(', '); ?></span>
  //*/ ?>
  <?php /*
  <span class="share">
    <ul>
      <li><a target="_blank" href="http://www.facebook.com/sharer.php?u=<?php the_permalink();?>" class="facebook_share"><img alt="Share on Facebook" class="ssba" title="Facebook" src="http://edm.cluster.veneer.io/modules/simple-share-buttons-adder/buttons/somacro/facebook.png"></a></li>
      <li><a target="_blank" href="http://twitter.com/home/?status=<?php the_title();?>%20<?php the_permalink();?>" class="twitter_share"><img alt="Tweet about this on Twitter" class="ssba" title="Twitter" src="http://edm.cluster.veneer.io/modules/simple-share-buttons-adder/buttons/somacro/twitter.png"></a></li>
      <li><a target="_blank" href="https://plusone.google.com/_/+1/confirm?hl=en&url=<?php the_permalink();?>" class="google_share"><img alt="Share on Google+" class="ssba" title="Google+" src="http://edm.cluster.veneer.io/modules/simple-share-buttons-adder/buttons/somacro/google.png"></a></li>
    </ul>
  </span>
  */ ?>
</section>

<?php if( $img = wp_festival()->get_image_link_by_post_id( get_the_ID(), array( 'width' => '854', 'height' => '480', 'default' => false, 'crop' => true ) ) ): ?>
  <section class="article-image">
    <img class="img-responsive" src="<?php echo $img; ?>" alt="" />
  </section>
<?php endif; ?>

<section class="article-content" data-type="content">
  <div class="container">
  <?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', wp_festival( 'domain' ) ) ); ?>
  </div>
</section>

<?php //get_template_part( 'templates/article/author', wp_festival()->get_query_template() ); ?>
<?php comments_template(); ?>

