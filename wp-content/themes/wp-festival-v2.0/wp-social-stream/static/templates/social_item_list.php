<script type="text/html" id="wp_social_stream_social_item_list">
  <section class="stream-filters clearfix">
    <section class="col-xs-12 col-md-7" data-bind="template: { name: 'wp_social_stream_social_item_meta', data: $data.meta}"></section>
    <section class="col-xs-12 col-md-5" data-bind="template: { name: 'wp_social_stream_social_item_filters', data: $data.filters}"></section>
  </section>
  <section class="info-stream clearfix" data-bind="template: { name: 'wp_social_stream_social_item_single', foreach: $data.results(), as: 'result', afterRender: window.scmfStream.checkState }">
  </section>
</script>