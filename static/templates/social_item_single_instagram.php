<script type="text/html" id="wp_social_stream_social_item_single_instagram">
  <div class="inner">
    <span class="section-thumb">
      <a data-bind="attr: { 'href': url, 'title': data.caption.text }">
        <img data-bind="attr: { 'src': data.images.low_resolution.url, 'alt': data.caption.text }" />
      </a>
    <span class="section-title">
      <a data-bind="text: data.caption.text, attr: { 'href': url, 'title': data.caption.text }"></a>
    </span>
    <span class="clear"></span>
  </div>
  <div class="section-intro">
    <a data-bind="text: from_now, attr: { 'href': url, 'title': data.caption.text }"></a>
  </div>
  <a class="network-label" data-bind="attr: { 'href': url, 'title': data.caption.text }">
    <img src="<?php echo plugins_url( 'static/images/instagram.png', dirname( __DIR__ ) ); ?>" alt="Network Logo" class="icon">
  </a>
</script>
