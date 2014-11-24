<?php
/**
 * Displays Entry Meta on single pages.
 *
 * @package Flawless
 * @since Flawless 3.0 
 *
 */ 
 
  if(is_home() && $flawless['hide_meta_data_on_home_page'] == 'true') {
    return;
  }

  if(is_category() && $flawless['hide_meta_data_on_category_pages'] == 'true') {
    return;
  }

?>

<ul class="entry-meta header">

  <?php if(post_type_supports( get_queried_object()->post_type, 'author' ) ): ?>
  <li class="author">By <a class="author vcard" rel="author" href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>"><?php echo get_the_author(); ?></a></li>
  <?php endif; ?>
  
  <?php if($flawless['post_types'][$post->post_type]['show_post_meta'] == "true"): ?>
  <li class="entry-date"><time datetime="<?php echo get_the_date('c'); ?>" pubdate><?php echo  get_the_date( ); ?></time></li>
  <?php endif; ?>
  
</ul>

