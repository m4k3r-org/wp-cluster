<!DOCTYPE html>
<html>
  <head>
    <title><?php wp_title( '|', true, 'right' ); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
  </head>
  <body <?php body_class(); ?> data-top="background-position:50% 0px;" data-bottom="background-position:50% 140px;">
    <?php the_content(); ?>
    <?php wp_footer(); ?>
  </body>
</html>
