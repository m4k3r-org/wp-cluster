<?php

get_header();
the_post();

get_template_part('single', 'header');

?>

  <div class="page-content">
    <div class="container">
      <h3 class="title"><?php the_title(); ?></h3>
      <hr class="divider">

      <span class="meta clearfix">
        <span class="date">
          <i class="icon-spectacle-time"></i>
          <span><?php echo get_the_time('D, M d, Y'); ?></span>
        </span>

        <span class="comments-count">
          <i class="icon-spectacle-comment"></i>
          <a href="#"><?php comments_number( 'no comments', '1 comment', '% comments' ); ?></a>
        </span>
      </span>

      <div class="single-post-content">
        <?php the_content(); ?>
      </div>

      <hr>

      <?php comments_template(); ?>

    </div>
  </div>

<?php get_footer(); ?>