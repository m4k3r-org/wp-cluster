<?php
/**
 * Social Twitter Meta customization
 *
 */
namespace UsabilityDynamics\SEO {

  if( !class_exists( '\UsabilityDynamics\SEO\Twitter' ) ) {

    class Twitter {
    
      /**
       * Add specific hooks
       */
      public function __construct() {
        add_action( 'template_redirect', array( $this, 'maybe_set_twitter_image' ) );
        add_filter( 'wpseo_twitter_image', array( $this, 'maybe_fix_relative_link' ), 99 );
      }
      
      /**
       * Try to set twitter image
       */
      public function maybe_set_twitter_image() {
        global $post;
        
        $twitter_img = \WPSEO_Meta::get_value( 'twitter-image' );
        if( !empty( $twitter_img ) ) {
          return null;
        }
        
        //** Maybe use opengraph image of current post */
        $twitter_img = \WPSEO_Meta::get_value( 'opengraph-image' );
        
        //** Maybe use featured image of current post */
        if( empty( $twitter_img ) && function_exists( 'has_post_thumbnail' ) && has_post_thumbnail( $post->ID ) ) {
					/**
					 * Filter: 'wpseo_twitter_image_size' - Allow changing the Twitter Card image size
					 * @api string $featured_img Image size string
					 */
					$featured_img = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), apply_filters( 'wpseo_twitter_image_size', 'full' ) );
					if ( $featured_img ) {
						$twitter_img = $featured_img[0];
					}
				}
        
        //** Maybe use default opengraph image */
        if( empty( $twitter_img ) ) {
          $seo_options = \WPSEO_Options::get_all();
          if( !empty( $seo_options[ 'og_default_image' ] ) ) {
            $twitter_img = $seo_options[ 'og_default_image' ];
          }
        }
        
        //** If we found image, set it as twitter image */
        if( !empty( $twitter_img ) ) {
          \WPSEO_Meta::set_value( 'twitter-image', $twitter_img, $post->ID );
          add_action( 'wpseo_head', array( $this, 'reset_twitter_image' ), 999 );
        }
      
      }
      
      /**
       * Reset twitter image
       */
      public function reset_twitter_image() {
        global $post;
        \WPSEO_Meta::set_value( 'twitter-image', '', $post->ID );
      }
      
      /**
       * Fixes relative url to absolute.
       */
      function maybe_fix_relative_link( $rel ) {
        if( empty( $rel ) ) {
          return $rel;
        }
      
        //* return if already absolute URL */
        if ( parse_url( $rel, PHP_URL_SCHEME) != '' || substr($rel, 0, 2) == '//') { 
          return $rel;
        }

        $base = get_home_url();
        
        //* queries and anchors */
        if ( $rel[0]=='#' || $rel[0]=='?' ) { 
          return $base.$rel;
        }

        //* parse base URL and convert to local variables: $scheme, $host, $path */
        extract( parse_url( $base ) );

        //* remove non-directory element from path */
        $path = preg_replace( '#/[^/]*$#', '', $path );

        //* destroy path if relative url points to root */
        if ($rel[0] == '/') $path = '';

        //* dirty absolute URL */
        $abs = "$host$path/$rel";

        //* replace '//' or '/./' or '/foo/../' with '/' */
        $re = array('#(/\.?/)#', '#/(?!\.\.)[^/]+/\.\./#');
        for( $n=1; $n>0; $abs = preg_replace( $re, '/', $abs, -1, $n ) ) {}

        //* absolute URL is ready! */
        return $scheme . '://' . $abs;
      }

    }

  }

}
