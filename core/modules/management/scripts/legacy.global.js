/**
 * WP-Property Global
 *
 * Extends ud object.
 *
 * @author team@UD
 * @version 1.0
 */

//** odokienko@UD */
if ( top === self ) {
  //not in a frame
} else {
  //in a frame
  top.location.href = document.location.href;
}

var wpp = ( typeof wpp === 'object' ) ? wpp : {};

/* Merge UD Global Functions into WPP Global, and Add New Functions */
jQuery.extend( true, wpp, ud, {

  /* Localization data */
  strings: {},

  /* Slug for Scope of this Object */
  scope: 'wpp',

  flags: {},

  /**
   * Progress Bar.
   * Usage: jQuery( '.some_element' ).append( wpp.progress_bar( { type: 'success', frequency : 200 }, function() {  ..do something when done.. } ) );
   *
   * @author Usability Dynamics, Inc.
   */
  progress_bar: function ( args, onComplete ) {
    args = jQuery.extend( true, {
      wrapper: jQuery( '<div class="wpp_progress wpp_progress-striped active"></div>' ),
      bar: jQuery( '<div class="wpp_bar"></div>' ),
      interval_event: {},
      type: 'info',
      start: 0,
      current: 0,
      end: 100,
      increment: 5,
      frequency: 100
    }, args );

    args.wrapper.addClass( 'progress-' + args.type );
    args.wrapper.append( args.bar );
    args.wrapper.current = args.current;

    args.wrapper.step_up = function () {
      this.current = this.current + args.increment;
      jQuery( args.bar ).width( this.current + '%' );
    };

    args.wrapper.set = function ( current ) {
      this.current = current;
      jQuery( args.bar ).width( current + '%' );
    };

    if ( args.frequency ) {
      args.interval_event = setInterval( function () {

        args.wrapper.step_up();

        if ( args.current >= 100 ) {

          clearInterval( args.interval_event );

          if ( typeof jQuery.prototype.fadeTo === 'function' ) {
            args.wrapper.fadeTo( 1500, 0, function () {
              typeof onComplete === 'function' ? onComplete() : false;
            } );
          } else {
            args.wrapper.css( 'opacity', 0 );
            typeof onComplete === 'function' ? onComplete() : false;
          }
          args.wrapper.removeClass( 'active' );
        }

      }, args.frequency );
    }

    return args.wrapper;

  },

  /**
   * Create a dynamic Google Map for a listing.
   *
   * @author potanin@UD
   */
  render_map: function ( selector, args ) {

    if ( !jQuery( selector ).length ) {
      return wpp.log( wpp.strings.maps_failure, 'error' );
    }

    var map = {
      element: jQuery( selector )
    };

    args = jQuery.extend( true, {
      dom_id: selector.attr( 'id' ),
      height: '500px',
      width: '100%',
      latitude: map.element.attr( 'data-latitude' ),
      longitude: map.element.attr( 'data-longitude' ),
      zoom: map.element.attr( 'data-zoom' ) ? map.element.attr( 'data-zoom' ) : 10,
      infowindow: {
        title: wpp.strings.infobox_title,
        content: wpp.strings.infobox_content,
        icon_url: 'https://maps.google.com/mapfiles/marker.png'
      }
    }, args );

    if ( !args.dom_id ) {
      map.element.attr( 'id', args.dom_id = 'wpp_google_map' );
    }

    //** Hide map element until loaded */
    map.element.css( 'opacity', 0 );

    if ( typeof google === 'object' && typeof google.maps != 'undefined' ) {

      //** Once we are this far, set the dimensions */
      map.element.height( args.height );
      map.element.width( args.width );

      map.google_object = new google.maps.Map( document.getElementById( args.dom_id ), {
        zoom: args.zoom,
        center: new google.maps.LatLng( args.latitude, args.longitude ),
        mapTypeId: google.maps.MapTypeId.ROADMAP
      } );

      map.marker = new google.maps.Marker( {
        position: new google.maps.LatLng( args.latitude, args.longitude ),
        map: map.google_object,
        title: args.infowindow.title,
        icon: args.icon_url
      } );

      if ( args.infowindow ) {
        map.infowindow = new google.maps.InfoWindow( {
          content: args.infowindow.content
        } );
      }

      google.maps.event.addListenerOnce( map.google_object, 'idle', function () {

        typeof jQuery.prototype.fadeTo === 'function' ? map.element.fadeTo( 2000, 1 ) : map.element.css( 'opacity', 1 );

        map.infowindow.open( map.google_object, map.marker );

        google.maps.event.addListener( map.infowindow, 'domready', function () {

          document.getElementById( 'infowindow' ).parentNode.style.overflow = 'hidden';
          document.getElementById( 'infowindow' ).parentNode.parentNode.style.overflow = 'hidden';
        } );

        google.maps.event.addListener( map.marker, 'click', function () {
          map.infowindow.open( map.google_object, map.marker );
        } );

      } );

    }

  },

  /**
   * Add Fancybox support, if library available.
   *
   * @author potanin@UD
   */
  enable_fancybox: function ( selector, args ) {

    args = {
      'transitionIn': 'elastic',
      'transitionOut': 'elastic',
      'speedIn': 600,
      'speedOut': 200,
      'overlayShow': false
    };

    if ( typeof jQuery.fn.fancybox === 'function' ) {
      jQuery( selector ? selector : 'a.fancybox_image, .gallery-item a' ).fancybox( args );
    }

  },

  /**
   * Add Remove Notification support.
   *
   * @author peshkov@UD
   */
  enable_remove_notification: function ( selector, notice ) {
    var s = selector ? selector : 'a.wpp_remove';
    jQuery( s ).live( 'click', function () {
      var link = jQuery( this ).attr( 'href' ), n = '';
      if ( typeof link === 'undefined' || link === '' ) {
        link = ( typeof jQuery( this ).attr( 'link' ) != 'undefined' ) ? jQuery( this ).attr( 'link' ) : false;
      }
      if ( link ) {
        if ( typeof notice === 'undefined' || notice === '' ) {
          n = jQuery( this ).attr( 'data-notice' );
          if ( typeof n === 'undefined' || n === '' ) {
            n = wpp.strings.remove_confirmation;
          }
        } else {
          n = notice;
        }
        if ( confirm( n ) ) {
          window.location = link;
        }
        return false;
      }
    } );

  },

  /**
   * Apply currency formatting to a passed number, or add change event listener to a DOM object.
   *
   * @author potanin@UD
   */
  format_currency: function ( selector ) {

    var _format = function ( value ) {
      if ( typeof jQuery.prototype.number_format === 'function' ) {
        value = jQuery().number_format( value.replace( /[^\d|\.]/g, '' ), {
          numberOfDecimals: 0,
          decimalSeparator: '.',
          thousandSeparator: ','
        } );
      }

      return value === 'NaN' ? '' : value;

    };

    selector = ( jQuery( selector ).length > 0 ) ? jQuery( selector ) : selector;

    if ( typeof selector === 'number' || typeof selector === 'string' ) {
      return _format( selector );
    }

    if ( typeof selector === 'object' ) {
      jQuery( selector ).change( function () {
        jQuery( this ).val( _format( jQuery( this ).val() ) );
      } );
    }

  },

  /**
   * Format a passed variable, or attach
   *
   *
   */
  format_number: function ( selector ) {

    var _format = function ( value ) {
      if ( typeof jQuery.prototype.number_format === 'function' ) {
        value = jQuery().number_format( value.replace( /[^\d|\.]/g, '' ) );
        return value === 'NaN' ? '' : value;
      }
    };

    selector = ( jQuery( selector ).length > 0 ) ? jQuery( selector ) : selector;

    if ( typeof selector === 'number' || typeof selector === 'string' ) {
      return _format( selector );
    }

    if ( typeof selector === 'object' ) {
      jQuery( selector ).change( function () {
        jQuery( this ).val( _format( jQuery( this ).val() ) );
      } );
    }

  },

  /**
   *
   *
   *
   */
  add_commas: function ( string ) {

    string += '';
    x = string.split( '.' );
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while ( rgx.test( x1 ) ) {
      x1 = x1.replace( rgx, '$1' + ',' + '$2' );
    }

    return x1 + x2;

  },

  /**
   * Dynamic table
   */
  table: {

    flags: {
      table_sortable: false
    },

    /**
     * Add Sort functionality to Table
     */
    sortable: function () {

      if ( typeof jQuery.fn.sortable === 'function' ) {

        jQuery( 'table.wpp_sortable tbody' ).sortable();

        //** The event should be added only at once. So we check flag. */
        if ( !wpp.table.flags.table_sortable ) {
          wpp.table.flags.table_sortable = true;
          jQuery( 'table.wpp_sortable tbody tr' ).live( "mouseover mouseout", function ( event ) {
            if ( event.type === "mouseover" ) {
              jQuery( this ).addClass( "wpp_draggable_handle_show" );
            } else {
              jQuery( this ).removeClass( "wpp_draggable_handle_show" );
            }
          } );
        }

      }

    },

    /**
     * Adds a row to a Dynamic Table
     *
     * @author potanin@UD
     */
    add_row: function ( element ) {
      'use strict';
      wpp.log( 'wpp.add_row()', arguments );
      var auto_increment = false;
      var table = jQuery( element ).closest( '.ud_ui_dynamic_table' );
      var table_id = jQuery( table ).attr( "id" );
      var unique = Math.floor( Math.random() * 1000000 );
      var cloned = jQuery( ".wpp_dynamic_table_row:last", table ).clone();
      var callback_function = jQuery( element ).attr( 'data-callback-function' );

      /**
       * Set unique IDs and FORs of DOM elements recursivly
       *
       * @param object el. jQuery DOM object
       * @param integer unique. Unique suffix which will be added to all IDs and FORs
       * @author Maxim Peshkov
       */
      var wpp_set_unique_ids = function ( el, unique ) {
        if ( typeof el === "undefined" || el.size() === 0 ) {
          return;
        }

        el.each( function () {
          var child = jQuery( this );

          if ( child.children().size() > 0 ) {
            wpp_set_unique_ids( child.children(), unique );
          }

          var id = child.attr( 'id' );
          if ( typeof id != 'undefined' ) {
            child.attr( 'id', id + '_' + unique );
          }

          var efor = child.attr( 'for' );
          if ( typeof efor != 'undefined' ) {
            child.attr( 'for', efor + '_' + unique );
          }
        } );
      };

      //* Determine if table rows are numeric */
      if ( jQuery( table ).attr( 'auto_increment' ) === 'true' || jQuery( table ).attr( 'data-auto_increment' ) === 'true' ) {
        var auto_increment = true;
      } else if ( jQuery( table ).attr( 'use_random_row_id' ) === 'true' ) {
        var use_random_row_id = true;
      } else if ( jQuery( table ).attr( 'allow_random_slug' ) === 'true' ) {
        var allow_random_slug = true;
      }

      wpp_set_unique_ids( cloned, unique );

      //* Increment name value automatically */
      if ( auto_increment ) {

        //* Cycle through all child elements and fix names */
        jQuery( 'input, select, textarea', cloned ).each( function ( element ) {
          var old_name = jQuery( this ).attr( 'name' );
          var matches = old_name.match( /\[(\d{1,2})\]/ );
          var old_count = parseInt( matches[1] ) ? parseInt( matches[1] ) : 100;
          var new_count = old_count + 1;
          var tries = 0;

          //** Increase incrementally until we get a unique name */
          while ( jQuery( '[name="' + old_name.replace( '[' + old_count + ']', '[' + new_count + ']' ) + '"]' ).length ) {
            new_count = new_count + 1;
            tries = tries + 1;

            if ( tries === 10 ) {
              break;
            }

          }

          jQuery( this ).attr( 'name', old_name.replace( '[' + old_count + ']', '[' + new_count + ']' ) );

        } );

      } else if ( use_random_row_id ) {

        //* Get the current random id of row */
        var random_row_id = jQuery( cloned ).attr( 'random_row_id' );
        var new_random_row_id = Math.floor( Math.random() * 1000000 );

        //* Cycle through all child elements and fix names */
        jQuery( 'input,select,textarea', cloned ).each( function ( element ) {
          var old_name = jQuery( this ).attr( 'name' );
          if ( typeof old_name != 'undefined' ) {
            var new_name = old_name.replace( '[' + random_row_id + ']', '[' + new_random_row_id + ']' );
            jQuery( this ).attr( 'name', new_name );
          }
        } );

        jQuery( cloned ).attr( 'random_row_id', new_random_row_id );

      } else if ( allow_random_slug ) {

        //* Update Row names */
        var slug_setter = jQuery( "input.slug_setter", cloned );
        if ( slug_setter.length > 0 ) {
          updateRowNames( slug_setter.get( 0 ), true );
        }

      }

      //* Insert new row after last one */
      jQuery( cloned ).appendTo( table );

      //* Get Last row to update names to match slug */
      var added_row = jQuery( ".wpp_dynamic_table_row:last", table );

      //* Bind ( Set ) ColorPicker with new fields '.wpp_input_colorpicker' */
      bindColorPicker( added_row );
      // Display row just in case
      jQuery( added_row ).show();

      //* Blank out all values */
      jQuery( "textarea", added_row ).val( '' );
      jQuery( "select", added_row ).val( '' );
      jQuery( "input[type=text]", added_row ).val( '' );
      jQuery( "input[type=checkbox]", added_row ).attr( 'checked', false );
      jQuery( ".wpp_remove_row .wpp_link", added_row ).show();

      //* Unset 'new_row' attribute */
      jQuery( added_row ).attr( 'new_row', 'true' );

      //* Focus on new element */
      jQuery( 'input.slug_setter', added_row ).focus();

      //* Fire Event after Row added to the Table */
      added_row.trigger( 'added' );

      if ( callback_function ) {
        wpp_call_function( callback_function, window, added_row );
      }

      return added_row;
    },

    /**
     * Delete row from a Dynamic Table
     *
     * @author peshkov@UD
     */
    remove_row: function ( element ) {
      var row, table, row_count;

      if ( typeof element === 'undefined' ) return false;
      row = jQuery( element ).closest( 'tr' );
      if ( !row.length > 0 ) return false;
      table = row.closest( 'table' );
      if ( !table.length > 0 ) return false;
      row_count = table.find( 'tbody tr' ).length;

      //* Confirm */
      if ( !confirm( wpp.strings.remove_confirmation ) ) {
        return false;
      }
      //* Blank out all values */
      jQuery( "input[type=text]", row ).val( '' );
      jQuery( "input[type=checkbox]", row ).attr( 'checked', false );
      //* Don't hide last row */
      if ( row_count > 1 ) {
        row.hide();
        row.remove();
      }
      table.trigger( 'row_removed', [row] );

    }

  }

}, wpp );

/**
 * Backwards Compatibility.
 *
 */
function wpp_format_currency ( selector ) {
  wpp.format_currency( selector );
}

/**
 * Backwards Compatibility.
 *
 */
function wpp_format_number ( selector ) {
  wpp.format_number( selector );
}

/**
 * Backwards Compatibility.
 *
 */
function wpp_add_commas ( string ) {
  wpp.add_commas( string );
}


