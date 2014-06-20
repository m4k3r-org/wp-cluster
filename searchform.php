<?php
/**
 * Search form template.
 *
 * @version 0.5.0
 * @author Usability Dynamics, Inc. <info@usabilitydynamics.com>
 * @package Flawless
 */
global $flawless;

?>

<form id="autocompletion">
  <div class="search_inner_wrapper">
    <label class="screen-reader-text" for="df_q"><?php _e( 'Search for:' ); ?></label>
    <input data-suggest="desktop" class="search_input_field" data-bind="elasticSuggest:{
      size:50,
      document_type:{
        event:'Events',
        imagegallery:'Galleries',
        videoobject:'Videos',
        artist:'Artists',
        promoter:'Promoters',
        tour:'Tours',
        venue:'Venues',
        city:'City',
        'event-type':'Type',
        state:'State'
      },
      search_fields:['summary'],
      return_fields:['summary','url'],
      //** Makes return only upcoming events */
      custom_query: {
        filter: {
          or: [
            {
              range: {
                start_date: {
                  gte: 'now-1d'
                }
              }
            },
            {
              not: {
                filter: {
                  exists: {
                    field: 'start_date'
                  }
                }
              }
            }
          ]
        }
      }
    }" placeholder="<?php echo $flawless[ 'header' ][ 'search_input_placeholder' ] ? $flawless[ 'header' ][ 'search_input_placeholder' ] : sprintf( __( 'Search %1s', 'flawless' ), get_bloginfo( 'name' ) ); ?>"/>
    <img class="loader" data-bind="visible:desktop.loading" src="<?php echo get_template_directory_uri() ?>/img/ajax-loader.gif"/>
  </div>

  <ul data-bind="enable:label=true,visible:desktop.visible,foreach:desktop.documents">
    <!-- ko if: _type != label -->
    <li data-bind="attr:{class:'ac_label '+_type+'_icon'}" class="ac_label"><i class="icon"></i><h5 data-bind="visible:label=_type,html: $root.desktop.types()[_type]"></h5></li>
    <!-- /ko -->
    <li class="ac_item">
      <a data-bind="attr:{href:fields.url},html: fields.summary"></a>
    </li>
  </ul>

</form>