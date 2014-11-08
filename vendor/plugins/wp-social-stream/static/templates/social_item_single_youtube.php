<script type="text/html" id="wp_social_stream_social_item_single_youtube">
  <div class="inner">
    <span class="section-thumb">
      <a data-bind="attr: { 'href': url, 'title': data.title }">
        <img data-bind="attr: { 'src': data.thumb_url, 'alt': data.title }" />
      </a>
    </span>
    <span class="section-title">
      <a data-bind="text: data.title, attr: { 'href': url, 'title': data.title }"></a>
    </span>
    <span class="clear"></span>
  </div>
  <div class="section-intro">
    <a data-bind="text: from_now, attr: { 'href': url, 'title': data.title }"></a>
  </div>
  <a class="network-label" data-bind="attr: { 'href': url, 'title': data.title }">
    <img src="<?php echo plugins_url( 'static/images/youtube.png', dirname( __DIR__ ) ); ?>" alt="Network Logo" class="icon">
  </a>
</script>