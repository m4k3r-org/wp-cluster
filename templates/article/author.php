<?php
/**
 * The default template for displaying content. Used for both single and index/archive/search.
 *
 * @author Usability Dynamics
 * @module wp-festival
 * @since wp-festival 2.0.0
 */

$userdata = get_userdata( get_the_author_meta( 'ID' ) );

?>
<article class="author <?php get_post_type(); ?>" data-type="<?php get_post_type(); ?>">

  <div class="author-wrapper">
    <section class="author-avatar clearfix">
      <?php echo get_avatar( $userdata->ID, 96 ); ?>
    </section>
    <section class="author-data">
      <span class="author-name"><span class="display-name"><?php echo $userdata->display_name; ?></span> <?php _e( 'Author', wp_festival2( 'domain' ) ); ?></span>
      <p><?php echo $userdata->description; ?></p>
    </section>
  </div>

</article>