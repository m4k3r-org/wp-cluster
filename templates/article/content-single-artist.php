<?php
/**
 * Artist Content template
 */

global $wp_query;

if( !isset( $wp_query->data[ 'artist-hero' ] ) || !is_array( $wp_query->data[ 'artist-hero' ] ) ) {
  $wp_query->data[ 'artist-hero' ] = array();
}

$wp_query->data[ 'artist-hero' ] = wp_parse_args( $wp_query->data[ 'artist-hero' ], array_filter( array(
  'box_height' => get_post_meta( get_the_ID(), 'viewHeroHeight', true ),
  'image_alignment' => get_post_meta( get_the_ID(), 'viewHeroAlignment', true ),
) ) );
 
$artist = wp_festival()->get_post_data( get_the_ID() );
 
?>

<section class="article-content" data-type="content">
  <div class="container">
  
    <div class="section">
      <?php get_template_part('templates/aside/artist-hero'); ?>
    </div>

    <!-- Social Stream Module -->
    <div class="section">
      <?php echo do_shortcode( '[social_stream 
        ' . ( !empty( $artist[ 'usernameYoutube' ] ) ? "youtube_search_for=\"{$artist[ 'usernameYoutube' ]}\"" : '' ) . '
        ' . ( !empty( $artist[ 'usernameFacebook' ] ) ? "facebook_search_for=\"{$artist[ 'usernameFacebook' ]}\"" : '' ) . '
        ' . ( !empty( $artist[ 'usernameInstagram' ] ) ? "instagram_search_for=\"!{$artist[ 'usernameInstagram' ]}\"" : '' ) . '
        ' . ( !empty( $artist[ 'usernameTwitter' ] ) ? "twitter_search_for=\"{$artist[ 'usernameTwitter' ]}\"" : '' ) . '
      ]' ); ?>
    </div>
    <!-- #Social Stream Module -->

  </div>
</section>
