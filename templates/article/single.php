<article class="<?php wp_disco()->module_class(); ?>" data-object-id="<?php the_ID(); ?>">

    <header class="entry-title-wrapper">
      <?php wp_disco()->page_title(); ?>
    </header>

    <aside class="breadcrumbs">
      <?php wp_disco()->breadcrumbs(); ?>
    </aside>

  <?php get_template_part( 'templates/article/entry-meta', 'header' ); ?>

  <div class="entry-content clearfix">
      <?php the_content( 'More Info' ); ?>
    </div>

  <?php get_template_part( 'templates/article/comments', get_post_type() ); ?>
  <?php get_template_part( 'templates/article/entry-meta', 'footer' ); ?>

</article>
