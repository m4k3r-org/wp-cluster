<?php get_header(); ?>

<header>
  <h1 class="logo"><a href="/">Coming Home Music Festival 2014</a></h1>

  <h2 class="lead-in">
    <span class="icon-logo2"></span>
    <span class="icon-logo1"></span>
  </h2>

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

