/**
 * Global Frontend JavaScript
 *
 * @version 1.0.0
 * @author Insidedesign
 *
 * jshint bitwise:true, curly:true, eqeqeq:true,  browser:true, jquery:true, indent: 2, global  $:false, jQuery:false, moment:false
 */

var hddp = jQuery.extend( true, {
  'debug': false,
  'loaded': false
}, typeof hddp === 'object' ? hddp : {} );

//jQuery( 'div.header.container' ).headroom();



/**
 * Primary Initialization
 *
 * @author {unknown}
 */
jQuery( document ).bind( 'hddp::initialize', function() {
  hddp.log( 'initialize' );

  /* Do our tabs */
  if( typeof jQuery.prototype.tabs === 'function' ) {
    jQuery( '.dd_fixed_panel_wrapper, .tabbed-content' ).tabs();
  }

  if( typeof hdp_current_event === 'object' && hdp_current_event.geo_located === 'true' && typeof google === 'object' && typeof google.maps === 'object' ) {

    /**
     * Single Event location map
     */
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
 * Call DOM triggers
 *
 * @author potanin@UD
 */
jQuery( document ).ready( function() {

  new Headroom(document.querySelector( 'div.general_header_wrapper' ), {
    tolerance: 5,
    offset : 300,
    classes: {
      "initial": "animated",
      "pinned": "slideInDown",
      "unpinned": "slideOutUp",
      "top": "headroom--top",
      "notTop": "headroom--not-top"
    }
  }).init();

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

/**
 * For jQuery elasticSearch
 */
jQuery( document ).on( 'elasticFilter.submit.success', function() {
  console.debug( 'elasticFilter.submit.success' );
  jQuery( '.hdp_event_collapsed, .hdp_event_expanded' ).unbind( 'click' );
  jQuery( '.hdp_event_collapsed, .hdp_event_expanded' ).on( 'click', function() {
    jQuery( '.hdp_event_expanded:visible' ).hide();
    jQuery( '.hdp_event_collapsed' ).not( ':visible' ).show();
    jQuery( this ).toggle().siblings( '.hdp_event_collapsed, .hdp_event_expanded' ).toggle();
  } );
} );

jQuery( document ).on( 'elasticFilter.facets.render', function( e, form ) {
  console.debug( 'elasticFilter.facets.render' );
  form.removeClass( 'jqtransformdone' ).jqTransform();
} );