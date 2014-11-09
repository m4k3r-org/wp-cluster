<script type="text/html" id="wp_social_stream_social_item_single_twitter">
  <div class="inner">
    <span data-bind="visible: data.thumb_url" class="section-thumb">
      <a data-bind="attr: { 'href': url, 'title': data.text }">
        <img data-bind="attr: { 'src': data.thumb_url, 'alt': data.text }" />
      </a>
    </span>
    <span class="section-title">
      <a data-bind="text: data.text, attr: { 'href': url, 'title': data.text }"></a>
    </span>
    <span class="clear"></span>
  </div>
  <div class="section-intro">
    <a data-bind="text: from_now, attr: { 'href': url, 'title': data.text }"></a>
  </div>
  <a class="network-label" data-bind="attr: { 'href': url, 'title': data.text }">
    <img src="<?php echo plugins_url( 'static/images/twitter.png', dirname( __DIR__ ) ); ?>" alt="Network Logo" class="icon">
  </a>
</script>
