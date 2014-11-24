<script type="text/html" id="wp_social_stream_social_item_filters">

    <div class="col-xs-12 col-md-5 clearfix social-stream-filters" data-bind="foreach:$data" >
      <a href="#" data-bind="attr: {'class': 'filter ' + $data, 'data-sel': 'sel-' + $data}">
        <span data-bind="attr: {'class': 'icon-' + $data}">
        </span>
      </a>
    </div>

</script>
