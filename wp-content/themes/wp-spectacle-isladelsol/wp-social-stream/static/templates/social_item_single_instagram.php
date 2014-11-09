<script type="text/html" id="wp_social_stream_social_item_single_instagram">
  <div class="inner">
    <img data-bind="attr: { 'src': data.user.profile_picture }" alt="Profile" class="avatar">

    <div class="meta">
      <h4 data-bind="text: data.user.full_name"></h4>
      <time data-bind="text: from_now"></time>
    </div>
    <div class="clearfix"></div>

    <p data-bind="visible: data.caption.text, html: data.caption.text"></p>
  </div>
  
  <img data-bind="visible: data.images.low_resolution.url, attr: { 'src': data.images.low_resolution.url, 'alt': data.caption.text }" class="main-pic" />
</script>
