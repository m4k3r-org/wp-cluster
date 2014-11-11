<script type="text/html" id="wp_social_stream_social_item_list_home">
  <section data-bind="template: { name: 'wp_social_stream_social_item_meta_home', data: $data.meta}"></section>

  <section class="stream-data">
    <section class="info-stream clearfix" data-bind="template: { name: 'wp_social_stream_social_item_single_home', foreach: $data.results(), as: 'result', afterRender: window.scmfStream.checkState }">
    </section>

    <div class="clearfix"></div>
    <div class="indicator-container">
      <div class="indicator-parent">
        <div class="indicator">
          <span class="icon-indicator"></span>
        </div>
      </div>
    </div>
  </section>
</script>
