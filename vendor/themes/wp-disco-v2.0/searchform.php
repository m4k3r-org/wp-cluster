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

<form id="autocompletion" class="autocompletion">
  <div class="search_inner_wrapper">
    <label class="screen-reader-text" for="df_q"><?php _e( 'Search for:' ); ?></label>
    <input data-suggest="desktop" class="search_input_field" data-bind="elasticSuggest:{
      selector:'#autocompletion',
      resultList:'.search-autocomplete-list',
      timeout: 1,
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
      return_fields:['summary','url', 'venue.address.city', 'address.city', 'venue.address.state', 'address.state', 'start_date', 'event_date'],
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
    <img class="loader" data-bind="visible:desktop.loading" src="<?php echo get_template_directory_uri() ?>/img/ajax-loader.gif" />
    <img class="cancel" data-bind="visible:!desktop.loading()&&desktop.has_text(),click:desktop.clear" src="<?php echo get_stylesheet_directory_uri() ?>/img/cancel.png" />
  </div>

  <ul data-bind="enable:label=true,visible:desktop.visible" data-state="loading" class="search-autocomplete-list">
    <!-- ko if:desktop.documents().length -->
      <!-- ko foreach:desktop.documents -->
        <!-- ko if: _type != label -->
          <li data-bind="attr:{class:'ac_label '+_type+'_icon'}" class="ac_label"><i class="icon"></i><h5 data-bind="visible:label=_type,html: $root.desktop.types()[_type]"></h5></li>
        <!-- /ko -->
        <li class="ac_item">
          <a data-bind="attr:{href:fields.url}">
            <!-- ko html:fields.summary --><!-- /ko -->

            <!-- ko if:fields.event_date -->
            <br /><i data-bind="if:(fields['venue.address.city']&&fields['venue.address.state'])">
              <!-- ko html:fields['venue.address.city'] --><!-- /ko -->, <!-- ko html:fields['venue.address.state'] --><!-- /ko --> on <!-- ko html:moment(fields.event_date[0]).format('MMM Do, YYYY') --><!-- /ko -->
            </i>
            <!-- /ko -->

            <!-- ko if:fields.start_date -->
            <br /><i data-bind="if:(fields['venue.address.city']&&fields['venue.address.state'])">
              <!-- ko html:fields['venue.address.city'] --><!-- /ko -->, <!-- ko html:fields['venue.address.state'] --><!-- /ko --> on <!-- ko html:moment(fields.start_date[0]).format('MMM Do, YYYY') --><!-- /ko -->
            </i>
            <!-- /ko -->

            <!-- ko if:fields['address.city'] -->
            <br /><i data-bind="if:(fields['address.city']&&fields['address.state'])">
              <!-- ko html:fields['address.city'] --><!-- /ko -->, <!-- ko html:fields['address.state'] --><!-- /ko -->
            </i>
            <!-- /ko -->
          </a>
        </li>
      <!-- /ko -->
    <!-- /ko -->
    <!-- ko if:desktop.documents().length == 0 -->
      <li class="ac_item"><a href="#">Nothing found</a></li>
    <!-- /ko -->
  </ul>

</form>