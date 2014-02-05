<?php
/**
 * Template Name: Sample page
 *
 * The template displays sample. It's hardcoded
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @author Usability Dynamics
 * @module festival  
 * @since festival 0.1.0
 */
get_template_part( 'templates/page/header', get_post_type() ); 
?>
<section class="container inner-wrapper">
  <div class="row">

    <div class="col-md-12 col-sm-12">
      <div class="content-container">
        <!-- SAMPLE -->
        SAMPLE
        <!-- /SAMPLE -->
      </div>
    </div>

  </div>
</section>
<?php get_template_part( 'templates/page/footer', get_post_type() ); ?>