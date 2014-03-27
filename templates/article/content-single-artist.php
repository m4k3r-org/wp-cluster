<?php
/**
 * Artist Content template
 */
?>

<?php get_template_part('templates/aside/artist-hero'); ?>

<!-- Social Stream Module -->
<?php echo do_shortcode( '[social_stream 
  youtube_search_for="" 
  facebook_search_for="" 
  instagram_search_for=""
  twitter_search_for=""
]' ); ?>
<!-- #Social Stream Module -->
