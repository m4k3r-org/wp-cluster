<script type="text/html" id="wp_social_stream_social_item_single_facebook">
  <div class="inner">
    <span data-bind="html: data.content" class="section-text"></span>
    <span class="clear"></span>
  </div>
  <div class="section-intro">
    <a data-bind="text: from_now, attr: { 'href': url, 'title': data.title }"></a>
  </div>
  <a class="network-label" data-bind="attr: { 'href': url, 'title': data.title }">
    <img src="<?php echo plugins_url( 'static/images/facebook.png', dirname( __DIR__ ) ); ?>" alt="Network Logo" class="icon">
  </a>
</script>
