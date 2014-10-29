<script type="text/html" id="wp_social_stream_social_item_single_twitter">
  <div data-bind="ifnot: data.thumb_url" target="_blank">
    <a class="item twitter" data-bind="attr: { 'href': url}">
      <span class="icon-twitter"></span>

      <div class="content"  data-bind="visible: data.text, html: data.text">
      </div>
      <time data-bind="text: from_now"></time>
    </a>
  </div>

  <div data-bind="if: data.thumb_url">
    <a class="item twitter" data-bind="style: {'backgroundImage' : 'url(' + data.thumb_url() + ')'}, attr: { 'href': url }">

      <span class="twitter-image-icon icon-twitter"></span>
    </a>
  </div>
</script>
