<script type="text/html" id="wp_social_stream_social_item_single_twitter">
  <div class="inner">
    <img data-bind="attr: { 'src': data.user.profile_image_url_https }" alt="Profile" class="avatar">

    <div class="meta">
      <h4 data-bind="text: data.user.screen_name"></h4>
      <time data-bind="text: from_now"></time>
    </div>
    <div class="clearfix"></div>

    <p data-bind="visible: data.text, html: data.text"></p>
  </div>
  
  <img data-bind="visible: data.thumb_url, attr: { 'src': data.thumb_url, 'alt': data.text }" class="main-pic" />
</script>
