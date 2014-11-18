<?php get_header(); ?>

<header>
  <section class="presenter-logos">
    <img src="<?php echo get_stylesheet_directory_uri(); ?>/static/images/presenter-logos.png" alt="Disco Donnie Presents ultimo">
  </section>

  <h1 class="main-logo">
    <a href="/">
      <img src="<?php echo get_stylesheet_directory_uri(); ?>/static/images/logo.png" alt="Winter Fantasy">
    </a>
  </h1>

</header>

<div class="page-content">
  <div class="container">
    <?php
    the_post();
    the_content();
    ?>
  </div>
</div>

<?php  get_footer(); ?>

