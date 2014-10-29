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
        ' . ( !empty( $artist[ 'socialStreams' ][ 'youtube' ] ) ? "youtube_search_for=\"{$artist[ 'socialStreams' ][ 'youtube' ]}\"" : '' ) . '
        ' . ( !empty( $artist[ 'socialStreams' ][ 'facebook' ] ) ? "facebook_search_for=\"{$artist[ 'socialStreams' ][ 'facebook' ]}\"" : '' ) . '
        ' . ( !empty( $artist[ 'socialStreams' ][ 'instagram' ] ) ? "instagram_search_for=\"!{$artist[ 'socialStreams' ][ 'instagram' ]}\"" : '' ) . '
        ' . ( !empty( $artist[ 'socialStreams' ][ 'twitter' ] ) ? "twitter_search_for=\"{$artist[ 'socialStreams' ][ 'twitter' ]}\"" : '' ) . '
      ]' ); ?>
    </div>
    <!-- #Social Stream Module -->

  </div>
</section>
