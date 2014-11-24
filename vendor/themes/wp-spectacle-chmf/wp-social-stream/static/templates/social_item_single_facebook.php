<script type="text/html" id="wp_social_stream_social_item_single_facebook">
  <div class="inner">
    <img data-bind="attr: { 'src': '//graph.facebook.com/' + data.author() + '/picture' }" alt="Profile" class="avatar">

    <div class="meta">
      <h4 data-bind="text: data.author"></h4>
      <time data-bind="text: from_now"></time>
    </div>
    <div class="clearfix"></div>

    <p data-bind="html: data.content"></p>
  </div>
</script>
