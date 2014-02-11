<div id="filter_wrapper" class="hdp_popup_filter" data-bind="elastic-facets: {}, if: elastic_ready">

  <a class="btn btn_show_filter clearfix" href="#"><span>Filter</span></a>

  <div id="df_sidebar_filters_wrap" style="visibility:hidden;">

    <div id="df_sidebar_filters">

      <form data-bind="submit: submit_facets, template: { name: facet_template, afterRender: facet_after_render, foreach: facets }">


      </form>

    </div>

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

  <!-- Temp condition. Don't need this after re-index of ES (theoreticaly) -->
  <!-- ko if: $data.id == 'terms.artist' || $data.id == 'venue.location.state' || $data.id == 'terms.city' || $data.id == 'terms.venue' || $data.id == 'terms.type' || $data.id == 'terms.promoter' -->
  <div data-bind="attr: {'class':$data.css_class()+' df_filter_inputs_list_wrapper'}" data-template="select">

    <span class="df_filter_label" data-bind="text: $data.label"></span>

    <select multiple="true" data-bind="options: $data.options, optionsValue: 'id', optionsText: 'text', selectedOptions: $data.select_multiple, select: { }" style="width: 97%"></select>

  </div>
  <!-- /ko -->

</script>
