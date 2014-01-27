;(function($) {

  $.fn.new_ud_elasticsearch = function() {

    var form = $(this);

    var settings = $.extend({
      period: 'upcoming',
      sort_by: 'hdp_event_date',
      sort_dir: 'ASC',
      from: 0
    }, form.data());

    var state = {
      is_load_more: false,
      current_filters: []
    };

    var doc = $(document);
    var results_container = $('#elasticsearch-results-'+settings.id);
    var facets_container = $('.facets-list', form);

    var time_filter = $('#hdp_filter_events.'+settings.id);
    var time_filter_buttons = $('.df_element', time_filter);

    var sorter = $('#hdp_results_sorter.'+settings.id);
    var sorter_buttons = $('.df_element', sorter);

    var more = {
      self: $('#dynamic_filter.'+settings.id+' .df_load_more'),
      button: $('#dynamic_filter.'+settings.id+' .df_load_more a.btn'),
      current_count: function( value ) {
        if ( state.is_load_more ) {
          $('#dynamic_filter.'+settings.id+' .df_load_more .df_current_count').text(
            parseInt($('#dynamic_filter.'+settings.id+' .df_load_more .df_current_count').text())+parseInt(value)
          );
          more.currently_showing += value;
        } else {
          $('#dynamic_filter.'+settings.id+' .df_load_more .df_current_count').text( value );
          more.currently_showing = value;
        }
        if ( value == 0 ) {
          more.button.hide();
        } else {
          more.button.show();
        }
      },
      total_count: function( value ){
        $('#dynamic_filter.'+settings.id+' .df_load_more .df_total_count').text( value );
        if ( value == 0 ) {
          more.self.hide();
        } else {
          more.self.show();
        }
      },
      more_count: function( value ) {
        $('#dynamic_filter.'+settings.id+' .df_load_more .df_more_count').text( value );
      },
      currently_showing:0
    };

    var init = function(){
      $('body').append('<style>.df_overlay_back{z-index:99999;background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyBpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNSBXaW5kb3dzIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjE5RDU5NzY3RjJENzExRTJCMUJBRTdCMDBDNjdDOUFGIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjE5RDU5NzY4RjJENzExRTJCMUJBRTdCMDBDNjdDOUFGIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6MTlENTk3NjVGMkQ3MTFFMkIxQkFFN0IwMEM2N0M5QUYiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6MTlENTk3NjZGMkQ3MTFFMkIxQkFFN0IwMEM2N0M5QUYiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz4ztIVWAAAAD0lEQVR42mJgYGBoAAgwAACFAIHr1UyZAAAAAElFTkSuQmCC);}.df_overlay{z-index:999999;background-image: url(data:image/gif;base64,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);background-position:50% 50%;background-repeat:no-repeat;}.df_overlay,.df_overlay_back{display:none;position:fixed;top:0;bottom:0;left:0;right:0;}</style><div class="df_overlay_back"></div><div class="df_overlay"></div>');
      form.find('input,select').live('change', submit);
      form.on('data_received', results);
      form.on('data_received', facets);
      form.on('bad_response', bad_response);
      form.on('data_error', error);
      more.button.on('click', load_more);

      doc.bind('ud::elasticsearch::render::complete', function(){

        time_filter_buttons.unbind('click');
        time_filter_buttons.on('click', function(){
          time_filter_buttons.removeClass('df_sortable_active');
          $(this).addClass('df_sortable_active');
          settings.period = $(this).attr('_filter');
          submit();
        });

        sorter_buttons.unbind('click');
        sorter_buttons.on('click', function(){
          sorter_buttons.removeClass('df_sortable_active');
          $(this).addClass('df_sortable_active');
          $(this).attr('sort_direction', settings.sort_dir=='ASC'?'DESC':'ASC');
          settings.sort_by = $(this).attr('attribute_key');
          settings.sort_dir = $(this).attr('sort_direction');
          submit();
        });
      });

      submit();
    };

    var submit = function() {
      jQuery('.df_overlay_back,.df_overlay').show();
      doc.trigger( 'ud::elasticsearch::submit::start' );
      state.current_filters = form.serializeArray();
      $.ajax(ajaxurl, {
        type: 'POST',
        dataType: 'json',
        data: {
          action: settings.action,
          type: settings.type,
          size: settings.size,
          from: settings.from,
          period: settings.period,
          sort_by: settings.sort_by,
          sort_dir: settings.sort_dir,
          query: form.serialize()
        },
        success: function( response ) {
          if ( typeof response.success == 'undefined' ) {
            form.trigger('data_received', arguments);
          } else {
            if ( !response.success ) {
              form.trigger('bad_response', arguments);
            }
          }
        },
        error: function() {
          form.trigger('data_error', arguments);
        },
        complete: function() {
          jQuery('.df_overlay_back,.df_overlay').hide();
          doc.trigger( 'ud::elasticsearch::submit::end' );
        }
      });
    };

    var results = function() {
      if ( !state.is_load_more ) {
        results_container.html(arguments[1].results);
      } else {
        results_container.append(arguments[1].results);
      }
      more.total_count( arguments[1].raw.hits.total );
      more.current_count( arguments[1].raw.hits.hits.length );
      more.more_count( settings.size );
      state.is_load_more = false;
      settings.from = 0;
      doc.trigger( 'ud::elasticsearch::render::complete' );
    };

    var facets = function() {
      facets_container.html(arguments[1].facets);
      $.each( state.current_filters, function(key, value){
        $( '[name="'+value.name+'"]', form ).val( value.value );
      });
      facets_container.parents('form').removeClass('jqtransformdone').jqTransform();
      doc.trigger( 'ud::elasticsearch::render::complete' );
    };

    var bad_response = function() {
      results_container.html('<li class="df_not_found">Could not filter due to error: <b>'+arguments[1].error+'</b></li>');
      more.total_count( 0 );
    };

    var error = function() {
      doc.trigger( 'ud::elasticsearch::error', [arguments[3].message] );
    };

    var load_more = function() {
      settings.from = more.currently_showing;
      state.is_load_more = true;
      submit();
    };

    init();

  };
}(jQuery));