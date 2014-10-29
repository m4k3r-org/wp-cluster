<?php
/**
 * Artist Content template
 */

global $wp_query;

$artist = wp_festival2()->get_post_data( get_the_ID() );

?>

<section class="stream-filters clearfix">
  <div class="col-xs-12 col-md-7">
    <h2 class="artist-profile-description"><?php echo $artist['post_title']; ?> Social Stream</h2>
  </div>
  <div class="col-xs-12 col-md-5 clearfix social-stream-filters">
    <p>Filter stream:</p>

    <a href="#" class="filter twitter" data-sel="sel-twitter"><span class="icon-twitter"></span></a>
    <a href="#" class="filter facebook" data-sel="sel-facebook"><span class="icon-facebook"></span></a>
    <a href="#" class="filter instagram" data-sel="sel-instagram"><span class="icon-instagram"></span></a>
    <a href="#" class="filter youtube" data-sel="sel-youtube"><span class="icon-youtube"></span></a>
  </div>
</section>

<div class="clearfix"></div>
<script src="https://rawgit.com/SteveSanderson/knockout.mapping/2.4.1/build/output/knockout.mapping-latest.js" type="text/javascript"></script>
<?php

echo do_shortcode( '[wp_social_stream limit="4"
        ' . ( !empty( $artist['socialStreams'][ 'youtube' ] ) ? "youtube_search_for=\"{$artist['socialStreams'][ 'youtube' ]}\"" : '' ) . '
        ' . ( !empty( $artist['socialStreams'][ 'facebook' ] ) ? "facebook_search_for=\"{$artist['socialStreams'][ 'facebook' ]}\"" : '' ) . '
        ' . ( !empty( $artist['socialStreams'][ 'instagram' ] ) ? "instagram_search_for=\"!{$artist['socialStreams'][ 'instagram' ]}\"" : '' ) . '
        ' . ( !empty( $artist['socialStreams'][ 'twitter' ] ) ? "twitter_search_for=\"{$artist['socialStreams'][ 'twitter' ]}\"" : '' ) . '
]' ); ?>


<?php /** Why is this committed?
<div class="row">
  <div class="col-xs-12 col-sm-6 schedule" style="padding:0;">
    <?php
    echo do_shortcode( '[widget_callout_item text=" Test From Shortcode" action="Shortcode Works" url="http://google.com" ]'
    );
    ?>
  </div>

  <div class="col-xs-12 col-sm-6 schedule" style="padding:0;">
    <?php
    echo do_shortcode( '[widget_callout_item text=" Test From Shortcode" action="From Shortcode 2" url="http://google.com" ]'
    );
    ?>
  </div>
</div> */ ?>

<link rel="stylesheet" href="http://malihu.github.io/custom-scrollbar/jquery.mCustomScrollbar.min.css" />
<!--
<script>
  (function($){
    $(window).load(function(){
      $(".overlay-artist-post-content").mCustomScrollbar();
    });
  })(jQuery);
</script>
-->
