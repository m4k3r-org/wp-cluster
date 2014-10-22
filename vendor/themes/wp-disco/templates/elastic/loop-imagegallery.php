<div data-template="/elastic/loop-imagegallery" id="dynamic_filter" class="dynamic_filter df_element df_top_wrapper df_element df_top_wrapper clearfix" data-dynamic-filter="hdp_photo_gallery">
  <div class="df_element hdp_results clearfix">
    <!-- ko if: !photos.documents().length -->
    <ul class="df_element hdp_results_items">
      <li class="hdp_results_item">
        <ul class="clearfix">
          <li>No Photos found</li>
        </ul>
      </li>
    </ul>
    <!-- /ko -->
    <!-- ko if: photos.documents().length -->
    <ul data-bind="foreach: photos.documents" class="df_element hdp_results_items clearfix">

      <li class="hdp_results_item" data-bind="attr: {df_id_: _id}">
        <ul class="df_result_data">
          <li data-attribute-key="raw_html">
            <ul>
              <li>
                <ul class="hdp_photo clearfix">
                  <li class="hdp_photo_thumbnail">
                    <a data-bind="href:fields['url'],attr:{title:'Photos from '+fields['summary']}">
                      <div class="overlay"></div>
                      <a data-bind="attr:{'href':fields['image.poster'],title:'Photos from '+fields['summary']}" href="#" rel="imagegallery">
                        <img data-bind="attr:{src:fields['image.small'],'data-large-src':fields['image.poster']}" src="<?php echo includes_url( '/theme/img/placeholder.png' ); ?>" />
                      </a>
                    </a>
                  </li>
                  <li class="hdp_photo_title"><a data-bind="html:fields['summary'],attr:{href:fields['url'],title:'Photos from '+fields['summary']}"></a></li>
                  <li class="hdp_photo_date" data-bind="visible:fields.event_date[0],html:moment(fields.event_date[0]).format('LLLL')"></li>
                  <li class="hdp_photo_location" data-bind="visible:fields['venue.address.city'] && fields['venue.address.state'],html:(fields['venue.address.city']+', '+fields['venue.address.state'])"></li>
                </ul>
              </li>
            </ul>
          </li>
        </ul>
      </li>

    </ul>
    <!-- /ko -->

    <div data-bind="visible:photos.documents().length" class="hdp_results_message clearfix" style="display: block;">
      <div class="df_load_status left">
        Displaying <span class="df_current_count" data-bind="html:photos.count">0</span> of <span data-bind="html:photos.total"></span> galleries
      </div>
      <a class="btn" data-scope="photos" data-bind="visible:photos.has_more_documents,filterShowMoreControl:{count:6}">
        <span>Show More</span>
      </a>
    </div>

  </div>
</div>