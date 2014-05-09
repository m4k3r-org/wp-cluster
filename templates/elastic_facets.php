<div class="elastic_filter" data-bind="elastic-facets: {}, if: elastic_ready">

  <div id="df_sidebar_filters" class="df_sidebar_filters" data-bind="if: $data.facets">

    <div class="df_filter_inputs_list_wrapper">

    <form data-bind="submit: submit_facets">

      <div class="df_filter_inputs_list_wrapper">
        <span class="df_filter_label" data-bind="">Search</span>
        <input data-bind="value: query.full_text"/>
      </div>

      <div data-bind="template: { name: facet_template, afterRender: facet_after_render, foreach: facets }"></div>

    </form>

  </div>
</div>

<!-- Default Facet -->
<script type="text/html" id="template-default-facet">

  <div class="df_filter_inputs_list_wrapper" data-template="default">

    <span class="df_filter_label" data-bind="text: $data.label"></span>

    <select data-bind="options: $data.options, optionsCaption: $data.caption, optionsText: 'text', optionsValue: 'id', value: value"></select>

  </div>

</script>


<!-- Select / Dropdown Facet -->
<script type="text/html" id="template-facet-terms">

  <div data-bind="attr: {'class':$data.css_class()+' df_filter_inputs_list_wrapper'}" data-template="select">

    <span class="df_filter_label" data-bind="text: $data.label"></span>

    <select multiple="true" data-bind="options: $data.options, optionsValue: 'id', optionsText: 'text', selectedOptions: $data.select_multiple, select: { }" style="width: 97%"></select>

  </div>

</script>
