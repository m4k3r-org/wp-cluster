/**
 * Global Frontend JavaScript
 *
 * @version 1.0.0
 * @author Insidedesign
 *
 * jshint bitwise:true, curly:true, eqeqeq:true,  browser:true, jquery:true, indent: 2, global  $:false, jQuery:false, moment:false
 */
require( [ '/assets/models/locale', '/assets/models/settings' ], function( locale, settings ) {
  console.log( 'app', 'loaded' );

  return;

  /* Declare global var if it not setup already */
  var hddp = jQuery.extend( true, {
    'debug': false,
    'loaded': false,
    'coords': {},

    /**
     * Apply Time Formatting
     *
     * Old Format: 'mmm d, yyyy'
     *
     * @source http://momentjs.com/docs/
     * @since 1.0.0
     * @author potanin@UD
     */
    'time': function( time, format ) {

      try {

        if( typeof format == 'undefined' ) format = 'MMMM D, YYYY';
        time = moment( time ).format( format );

      } catch( error ) {
      }

      return time;

    },

    /**
     * Executed on Result Row Click. Collapses all rows, expands clicked one.
     *
     * @since 1.0.0
     * @author potanin@UD
     */
    'toggle_row': function( data, event ) {

      if( !event.shiftKey ) {
        jQuery( '.hdp_event_expanded' ).hide();
        jQuery( '.hdp_event_collapsed' ).show();
      }

      jQuery( '.hdp_event_collapsed', event.currentTarget ).toggle();
      jQuery( '.hdp_event_expanded', event.currentTarget ).toggle();

      //** Make links inside clickable */
      return true;

    },

    'elastic_filter_init': function() {

      if( typeof window.ef_app === 'undefined' ) {
        return new Error( 'Elastic Filter not found.' );
      }

      //** Init elastic filter */
      jQuery.prototype.elastic_filter = window.elastic_filter = ef_app.init();

      /**
       * Add settings for elastic filter on ready callback
       *
       * Removed some conditional logic below that was referencing old Post Types
       */
      window.elastic_filter.ready = function( ef, error, report ) {

        try {

          hddp.log( 'elastic_filter.ready' );

          switch( this.view_model.settings.index() ) {

            case 'hdp_event':
            {

              var sort_option_exists = false;
              for( var i in this.view_model.settings.defaults.sort ) {
                if( typeof this.view_model.settings.defaults.sort[i]._geo_distance != 'undefined' ) {
                  sort_option_exists = true;
                }
              }

              if( this.view_model.settings.defaults.sort && !sort_option_exists ) {

                /*this.view_model.settings.defaults.sort['_geo_distance'] = {
                 'order' : 'asc', 'unit' : 'miles',
                 'venue.location.coordinates' : { 'lat' : hddp.coords.latitude, 'lon' : hddp.coords.longitude }
                 };*/

              }

              // Baseline Filter
              //this.view_model.query.range({time: {from: hddp.time(new Date(), 'YYYY/MM/DD HH:mm:ss')}});
              this.view_model.query.range( {time: {from: hddp.time( 0, 'YYYY/MM/DD HH:mm:ss' )}} );

              break;
            }

            case 'hdp_photo_gallery':
            {

              break;
            }

            default:
              break;

          }

        } catch( error ) {
          hddp.log( error );
        }
      };

    },

    /**
     * Custom search query function for select2 of EF
     */
    'fulltext_search_query': function( query ) {

      if( typeof window.elastic_filter == 'undefined' ) return new Error( 'Elastic Filter not found' );

      window.elastic_filter.view_model.query.full_text( query.term );

      // Use elastic filter post for full_text search
      window.elastic_filter.custom_search( function( error, response ) {

        // catch error
        if( error ) {
          hddp.log( error );
          return;
        }

        // return if nothing found
        if( typeof response.documents == 'undefined' || response.documents.length == 0 ) {
          query.callback( {results: []} );
          return;
        }

        var results = [];

        // collect results into right array
        for( var i in response.documents ) {
          results.push( {id: response.documents[i].body.id, text: response.documents[i].body.title, raw: response.documents[i]} );
        }

        var data = {results: results};

        // display processed results
        query.callback( data );

      } );

      return true;
    },

    /**
     * Result item selection callback. Just go to URL of the Event.
     */
    'selection_callback': function( event ) {
      if( event && typeof event.raw.body.url != 'undefined' ) {
        window.location = event.raw.body.url;
      }
    }

  }, typeof hddp === 'object' ? hddp : {} );

  /**
   * Primary Initialization
   *
   * @author {unknown}
   */
  jQuery( document ).bind( 'hddp::initialize', function() {
    hddp.log( 'initialize' );

    ud.load.css( '//cdn.usabilitydynamics.com/js/ud.select/3.2/assets/select2.css' );

    /* Do our tabs */
    if( typeof jQuery.prototype.tabs === 'function' ) {
      jQuery( '.dd_fixed_panel_wrapper, .tabbed-content' ).tabs();
    }

    /* Load any information stored in cookies */
    if( typeof jQuery.cookie === 'function' ) {
      hddp.log( 'Using cookies.' );
      hddp.use_cookies = true;

      if( jQuery.cookie( 'latitude' ) != null && jQuery.cookie( 'longitude' ) != null ) {
        hddp.coords = {
          latitude: jQuery.cookie( 'latitude' ),
          longitude: jQuery.cookie( 'longitude' )
        };
      }

      /* If browser supports geolocation, and we have not already tried asking, get it from user */
      if( jQuery.cookie( 'geolocation_attempted' ) == null && typeof navigator === 'object' && ( typeof hddp.coords != 'object' || ( typeof hddp.coords.latitude == 'undefined' && typeof hddp.coords.longitude == 'undefined' ) ) ) {
        jQuery.cookie( 'geolocation_attempted', true );
        navigator.geolocation.getCurrentPosition( hddp.geolocate, function( error ) {
          hddp.log( 'Could not geolocate.', 'error' );
        } );
      }

      if( typeof hddp.coords === 'object' ) {
        hddp.log( 'Have location, hddp.coords: ', hddp.coords );
      }
    }

    if( typeof hdp_current_event === 'object' && hdp_current_event.geo_located === 'true' && typeof google === 'object' && typeof google.maps === 'object' ) {

      jQuery( document ).ready( function() {

        hdp_current_event.mappos = new google.maps.LatLng( hdp_current_event.latitude, hdp_current_event.longitude );
        hdp_current_event.map = new google.maps.Map( document.getElementById( 'event_location' ), {
          scaleControl: true,
          center: hdp_current_event.mappos,
          zoom: 10,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        } );

        hdp_current_event.marker = new google.maps.Marker( { map: hdp_current_event.map, position: hdp_current_event.map.getCenter() } );
        jQuery( document ).on( 'tabsactivate', function( event, ui ) {
          if( ui.newPanel[0].id == 'section_map' ) {
            google.maps.event.trigger( hdp_current_event.map, 'resize' );
            hdp_current_event.map.setCenter( hdp_current_event.mappos );
          }
        } );

      } );

    }

    /* Attach to our filter button */
    jQuery( '#filter_wrapper .btn_show_filter' ).live( 'click', function( e ) {

      e.preventDefault();

      if( jQuery( '#filter_wrapper #df_sidebar_filters_wrap' ).css( 'visibility' ) == 'hidden' ) {
        jQuery( '#filter_wrapper #df_sidebar_filters_wrap' ).css( 'visibility', 'visible' );
      } else {
        jQuery( '#filter_wrapper #df_sidebar_filters_wrap' ).toggle();
      }

    } );

    /*jQuery( '#filter_wrapper #df_sidebar_filters_wrap' ).live('blur', function() {

     jQuery( this ).toggle();

     } );*/

  } );

  /**
   * Primary DOM Initialization
   *
   * Will be called multiple times, needs to have provisions to avoid duplicate event binding.
   *
   * @author {unknown}
   */
  jQuery( document ).bind( 'hddp::dom_update', function() {
    hddp.log( 'dom_update' );

    /* Remove event handlers to avoid double-triggers */
    jQuery( '.hdp_event_collapsed' ).unbind( 'click ' );
    jQuery( '.hdp_event_expanded' ).unbind( 'click ' );

    /* Toggle effect on Event Results */
    jQuery( '.hdp_event_collapsed, .hdp_event_expanded' ).click( function( event ) {
      if( jQuery( '#dynamic_filter' ).hasClass( 'df_ajax_loading' ) || jQuery( '#dynamic_filter' ).hasClass( 'df_filter_pending' ) ) {
        event.preventDefault();
        return false;
      }
      jQuery( '.hdp_event_expanded:visible' ).hide();
      jQuery( '.hdp_event_collapsed' ).not( ':visible' ).show();
      jQuery( this ).toggle().siblings( '.hdp_event_collapsed, .hdp_event_expanded' ).toggle();
    } );

    /* Prevent element toggle when clicking on link within Event Result */
    jQuery( '.hdp_event_collapsed a' ).unbind( 'click' );
    jQuery( '.hdp_event_collapsed a' ).click( function( event ) {
      event.stopPropagation();
      jQuery( this ).parent().parent().click();
      return false;
    } );

    /** If we're on the events page, we need to listen for our upcoming/past events click */
    jQuery( '#hdp_filter_events .df_element' ).unbind( 'click' );
    jQuery( '#hdp_filter_events .df_element' ).click( function( event ) {
      event.preventDefault();
      window.dynamic_filter.s.ajax.args.filter_events = jQuery( this ).attr( '_filter' );
      jQuery( this ).addClass( 'df_sortable_active' ).siblings().removeClass( 'df_sortable_active' );
      jQuery( document ).trigger( 'dynamic_filter::execute_filters' );
      return false;
    } );

  } );

  /**
   * Event Triggered after DF Render Complete
   *
   * @author potanin@UD
   */
  jQuery( document ).bind( 'dynamic_filter::render_data::complete', function() {
    hddp.log( 'dynamic_filter::render_data::complete' );

    jQuery( document ).trigger( 'hddp::dom_update' );

  } );
  jQuery( document ).bind( 'dynamic_filter::render_filter_ui::initiate', function() {
    jQuery( 'body' ).append( '<style>.df_overlay_back{z-index:99999;background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyBpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNSBXaW5kb3dzIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjE5RDU5NzY3RjJENzExRTJCMUJBRTdCMDBDNjdDOUFGIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjE5RDU5NzY4RjJENzExRTJCMUJBRTdCMDBDNjdDOUFGIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6MTlENTk3NjVGMkQ3MTFFMkIxQkFFN0IwMEM2N0M5QUYiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6MTlENTk3NjZGMkQ3MTFFMkIxQkFFN0IwMEM2N0M5QUYiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz4ztIVWAAAAD0lEQVR42mJgYGBoAAgwAACFAIHr1UyZAAAAAElFTkSuQmCC);}.df_overlay{z-index:999999;background-image: url(data:image/gif;base64,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);background-position:50% 50%;background-repeat:no-repeat;}.df_overlay,.df_overlay_back{display:none;position:fixed;top:0;bottom:0;left:0;right:0;}</style><div class="df_overlay_back"></div><div class="df_overlay"></div>' );
  } );
  jQuery( document ).bind( 'dynamic_filter::execute_filters dynamic_filter::render_filter_ui::initiate', function() {
    jQuery( '.df_overlay_back,.df_overlay' ).show();
  } );
  jQuery( document ).bind( 'dynamic_filter::render_data::complete', function() {
    jQuery( '.df_overlay_back,.df_overlay' ).hide();
  } );

  /**
   *
   */
  jQuery( document ).bind( 'ud::elasticsearch::render::complete', function() {
    jQuery( document ).trigger( 'hddp::dom_update' );
  } );
  /**
   * Happens after filters are rendered, updates right sidebar filters
   *
   * @author williams@ud
   */
  jQuery( document ).bind( 'dynamic_filter::update_filters::complete', function() {
    hddp.log( 'dynamic_filter::update_filters::complete' );

    /** Transform the elements */
    if( !hddp.loaded ) {
      jQuery( '#df_sidebar_filters' ).jqTransform();
      jQuery( '.hdp_sort .df_sorter div[attribute_key="hdp_event_date"]' ).addClass( 'df_sortable_active' );
      jQuery( '[attribute_key="hdp_date_range"] .df_filter_trigger' ).datepicker();
    } else {

      /** Lets build our custom changer here */
      jQuery( '.jqTransformSelectWrapper' ).each( function( e ) {

        /** Save the element, we'll need it later */
        var ul = jQuery( 'ul', this );
        var c = 0;

        /** We need to clear out all the items in this child's UL */
        jQuery( 'li', ul ).remove();

        /** Now go through our select, and add the elements back to the UL */
        jQuery( 'select option', this ).each( function( e ) {

          /** Setup the new li */
          var li = jQuery( '<li></li>' );
          var a = jQuery( '<a href="#"></a>' );

          /** Add the text */
          a.text( jQuery( this ).text() ).attr( 'index', c );

          /** If we're selected, add the class */
          if( jQuery( this ).is( 'selected' ) ) {
            a.addClass( 'selected' );
          }

          /** Manually add the click event (copied from jQtransform) */
          a.click( function( e ) {
            e.preventDefault();
            var wrapper = jQuery( this ).parent().parent().parent();
            var sel = jQuery( 'select', wrapper ).first();
            var ul = jQuery( this ).parent().parent();
            var si = sel[0].selectedIndex;
            var new_si = jQuery( this ).attr( 'index' );
            /* Removed selected */
            jQuery( 'a.selected', wrapper ).removeClass( 'selected' );
            jQuery( this ).addClass( 'selected' );

            /* Fire the onchange event, add new selected */
            if( si != new_si ) {
              sel[0].selectedIndex = new_si;
              sel.trigger( 'change' );
            }
            sel.attr( 'selectedIndex', new_si );
            jQuery( 'span:eq(0)', wrapper ).html( jQuery( this ).html() );
            ul.hide();
          } );

          /** Add the value, to the li, and add the li to the ul */
          ul.append( li.append( a ) );

          /** Update the count */
          c = c + 1;

        } );

        /** If none of them are selected, make the first one selected */
        if( jQuery( 'a.selected', ul ).length == 0 ) {

          jQuery( 'a', ul ).first().addClass( 'selected' );

        }

      } );

    }

    /** Update our loaded */
    hddp.loaded = true;

  } );

  /**
   * Console Logging Function
   *
   * @author potanin@UD
   */
  hddp.log = function() {

    switch( true ) {

      case ( arguments[0] instanceof Error ):
        console.error( arguments[0].message );
        break;

      case ( hddp.debug ):
        console.log.apply( console, [ 'hddp' ].concat( arguments ) );
        break;

    }

    return arguments[0] ? arguments[0] : null;

  };

  /**
   * Gather Location Information
   *
   * @author potanin@UD
   */
  hddp.geolocate = function( position ) {
    hddp.log( 'hddp.geolocate()' );

    if( typeof position.coords != 'object' ) {
      return;
    }

    hddp.coords = {
      latitude: position.coords.latitude,
      longitude: position.coords.longitude
    }

    /* Save to Cookie */
    if( hddp.use_cookies ) {
      jQuery.cookie( 'latitude', hddp.coords.latitude );
      jQuery.cookie( 'longitude', hddp.coords.longitude );
      hddp.log( 'Location saved.' );
    }

    hddp.log( hddp.coords );

  }

  /**
   * Bind for elastic_filter init
   */
  jQuery( document ).bind( 'elastic_filter::initialize', hddp.elastic_filter_init );

  /**
   * Call DOM triggers
   *
   * @author potanin@UD
   */
  jQuery( document ).ready( function() {
    jQuery( document ).trigger( 'hddp::initialize' );
    jQuery( document ).trigger( 'hddp::dom_update' );

    /** If we're on the homepage, on resize, we need to resize the home page slider */
    /** Original Dimensions: 890x500 */
    if( jQuery( 'body' ).hasClass( 'home' ) ) {
      var resizeCarousel, resizerTimer, resizerTimeout, $ul = jQuery( '.cfct-module-carousel ul' ), height, liHeight, imgHeight, newHeight, $imgs, initialized = false;
      resizeCarousel = function() {
        newHeight = 0;
        $imgs = jQuery( 'li:visible', $ul );
        for( var x = 0; x < $imgs.length; x++ ) {
          liHeight = jQuery( $imgs[ x ] ).height();
          imgHeight = jQuery( 'img', $imgs[ x ] ).height();
          /** Take the lesser of the 2 */
          height = liHeight < imgHeight ? liHeight : imgHeight;
          if( height > newHeight ) {
            newHeight = height;
          }
        }
        $ul.height( newHeight );
      };
      resizerTimer = function() {
        clearTimeout( resizerTimeout );
        resizerTimeout = setTimeout( resizeCarousel, 400 );
      };
      /** Hook into our Window resize event */
      jQuery( window ).on( 'resize', function() {
        resizerTimer();
      } );
      /** Now, every second lets continue */
      jQuery( window ).on( 'load', resizeCarousel );
    }
  } );

  jQuery( window ).on( 'load', function() {
    jQuery( ".entry-content" ).fitVids();
  } );

} );
