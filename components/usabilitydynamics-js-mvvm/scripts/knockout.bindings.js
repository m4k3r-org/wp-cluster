/**
 * UD Handlers for Knockout JS
 *
 * @version 1.0
 * @description Set of bindingHandlers for Knockout library
 * @package Knockout
 * @subpackage UD
 * @dependencies jquery, knockout-mapping
 * @author team@UD
 */
(function ( factory ) {
  if ( typeof define === "function" && define.amd ) {
    define( [ "knockout", "jquery", "jquery.ui.sortable" ], factory );
  } else {
    factory( window.ko, jQuery );
  }
})

( function ( ko, $, undefined ) {

  /**
   * Binding for Sortable items
   * @author korotkov@ud
   */
  ko.bindingHandlers.sortable = {
    init: function ( element, valueAccessor, allBindingsAccessor, viewModel, bindingContext ) {
    },
    update: function ( element, valueAccessor, allBindingsAccessor, viewModel, bindingContext ) {
      jQuery( element ).sortable( valueAccessor() );
    }
  };

  /**
   * Prevent form submit on enter key
   */
  ko.bindingHandlers.enter_key = {
    init: function ( element, valueAccessor, allBindingsAccessor, viewModel, bindingContext ) {
      jQuery( element ).keypress( function ( event ) {
        var keyCode = (event.which ? event.which : event.keyCode);
        if ( keyCode === 13 ) {
          return false;
        }
        return true;
      } );
    }
  };

  /**
   * Binding for Tabs
   * @author korotkov@ud
   */
  ko.bindingHandlers.tabbed = {
    init: function ( element, valueAccessor, allBindingsAccessor, viewModel, bindingContext ) {

      ko.utils.domNodeDisposal.addDisposeCallback( element, function () {
        jQuery( element ).tabs( "destroy" );
      } );

    },
    update: function ( element, valueAccessor, allBindingsAccessor, viewModel, bindingContext ) {

      jQuery( element ).bind( "tabsselect", function ( event, ui ) {
        wpp.attributes_tab_index = ui.index - 1;
      } );

      setTimeout( function () {

        try {

          var elem = jQuery( element );

          if ( elem.is( ':ui-tabs' ) )
            elem.tabs( "destroy" );

          var $tabs = elem.tabs();

          $tabs.tabs( "option", "selected", (wpp.attributes_tab_index !== 'undefined' ? wpp.attributes_tab_index : 0) );
          if ( typeof allBindingsAccessor().droppable !== 'undefined' ) {

            var defaults = {
              list: '.connectedSortable',
              accept: '.connectedSortable li',
              hoverClass: 'ui-state-hover'
            };

            var data = jQuery.extend( defaults, allBindingsAccessor().droppable );

            data.drop = function ( event, ui ) {
              if ( typeof data.drop_cb == 'function' ) {
                data.drop_cb( event, ui, viewModel );
              }
            }

            jQuery( "ul:first li", $tabs ).droppable( data );
          }

        } catch ( e ) {
          wpp.log( 'ko.bindingHandlers.tabbed', e.message );
        }

      }, 200 );
    }
  };

  /**
   * Generates unique slug on fly
   *
   * Example: <input type="text" data-bind=" unique_slug: { slug: 'slug', text: 'label', instance: 'some_unique_name' } " />
   *
   * @author peshkov@UD
   */
  ko.bindingHandlers.unique_slug = {
    init: function ( element, valueAccessor, allBindingsAccessor, viewModel, bindingContext ) {
      var settings = jQuery.extend( {
        'slug': false, // viewmodel[ settings.slug ]
        'text': false, // viewmodel[ settings.label ]
        'instance': false, // unique class which will be added to label field to determine list of related data ( other label fields )
        'storage': false, // boolean
        'value_to_slug': false // Sets value to slug
      }, valueAccessor() );
      /* All settings args are required */
      if ( !settings.slug || !settings.text || !settings.instance ) {
        return false;
      }
      /* Links to slug and label data must be correct */
      if ( typeof settings.slug === 'undefined' || typeof settings.text === 'undefined' ) {
        return false;
      }
      /* Creates slug from string */
      var create_slug = function ( slug ) {
        slug = slug.replace( /[^a-zA-Z0-9_\s]/g, "" );
        slug = slug.toLowerCase();
        slug = slug.replace( /\s/g, '_' );
        return slug;
      };
      //** If need to be stored in variable */
      if ( settings.storage ) {
        if ( typeof window.__ud_slug_storage == 'undefined' ) window.__ud_slug_storage = {};
        if ( typeof window.__ud_slug_storage[settings.instance] == 'undefined' ) window.__ud_slug_storage[settings.instance] = [];
        if ( window.__ud_slug_storage[settings.instance].indexOf( settings.slug() ) == -1 ) {
          window.__ud_slug_storage[settings.instance].push( settings.slug() );
        }
      }
      /* Adds Bindings to the current element */
      jQuery( element ).addClass( settings.instance ).data( 'slug', settings.slug() ).change( function () {
        var self = this, val = jQuery( this ).val(), slug = create_slug( val ), exist = false;
        /* Be sure that slug is not empty */
        if ( slug === '' ) {
          slug = 'random';
        }
        /* Determine if slug is aready exist */
        if ( settings.storage ) {
          if ( typeof window.__ud_slug_storage[settings.instance] !== 'undefined' ) {
            if ( window.__ud_slug_storage[settings.instance].indexOf( slug ) != -1 ) {
              exist = true;
            }
          }
        } else {
          jQuery( '.' + settings.instance ).each( function ( i, e ) {
            if ( e !== self && slug === jQuery( e ).data( 'slug' ) ) {
              exist = true;
            }
          } );
        }
        /* Set unique slug by unique ID adding. */
        if ( exist ) {
          slug += '_' + ( Math.floor( Math.random() * (99999999 - 1000000 + 1) ) + 1000000 )
        }
        /* Do not set slug again if item is not new */
        if ( typeof viewModel.new_item == 'function' ) {
          if ( !viewModel.new_item() ) {
            return false;
          }
        }
        /* Set slug */
        if ( typeof settings.slug === 'function' ) settings.slug( slug ); else settings.slug = slug;
        /* Re-set label using observable if needed */
        if ( typeof settings.text === 'function' ) settings.text( val ); else settings.text = val;
        /*  */
        jQuery( self ).data( 'slug', slug );
        /* */
        if ( settings.value_to_slug && slug !== 'random' ) {
          jQuery( self ).val( slug );
        }
      } );
      /* Manually fire 'change' event to check slug of the current element on init */
      setTimeout( function () {
        jQuery( element ).trigger( 'change' )
      }, 100 );
    }
  }

  /**
   * Utilizes Select2. Draws multiselect.
   *
   * @since 2.0
   * @author potanin@UD
   */
  ko.bindingHandlers.select = {
    init: function ( element, valueAccessor, allBindingsAccessor ) {
      var obj = valueAccessor(), allBindings = allBindingsAccessor(), lookupKey = allBindings.lookupKey;
      jQuery( element ).select2( obj );
      if ( lookupKey ) {
        var value = ko.utils.unwrapObservable( allBindings.value );
        jQuery( element ).select2( 'data', ko.utils.arrayFirst( obj.data.results, function ( item ) {
          return item[lookupKey] === value;
        } ) );
      }
      ko.utils.domNodeDisposal.addDisposeCallback( element, function () {
        jQuery( element ).select2( 'destroy' );
      } );

    },
    update: function ( element ) {
      //jQuery(element).trigger('change');
    }
  };

  /**
   * UI datepicker.
   *
   * Usage:
   * <input data-bind="datepicker: myDate, datepickerOptions: { minDate: new Date() }" />
   * myDate should be an observable valiable with value of Date() type
   */
  ko.bindingHandlers.datepicker = {
    init: function ( element, valueAccessor, allBindingsAccessor ) {
      //initialize datepicker with some optional options
      var options = allBindingsAccessor().datepickerOptions || {};
      jQuery( element ).datepicker( options );

      //handle the field changing
      ko.utils.registerEventHandler( element, "change", function () {
        var observable = valueAccessor();
        observable( jQuery( element ).datepicker( "getDate" ) );
      } );

      //handle disposal (if KO removes by the template binding)
      ko.utils.domNodeDisposal.addDisposeCallback( element, function () {
        jQuery( element ).datepicker( "destroy" );
      } );

    },
    update: function ( element, valueAccessor ) {
      var value = ko.utils.unwrapObservable( valueAccessor() );

      //handle date data coming via json from Microsoft
      if ( String( value ).indexOf( '/Date(' ) == 0 ) {
        value = new Date( parseInt( value.replace( /\/Date\((.*?)\)\//gi, "$1" ) ) );
      }

      var current = jQuery( element ).datepicker( "getDate" );

      if ( value - current !== 0 ) {
        jQuery( element ).datepicker( "setDate", value );
      }
    }
  };

  /**
   * LazyLoad
   */
  ko.bindingHandlers.lazyload = {
    init: function ( element, valueAccessor, allBindingsAccessor, viewModel, bindingContext ) {
      var options = valueAccessor() || {};
      jQuery( element ).lazyload( options );
      ko.utils.domNodeDisposal.addDisposeCallback( element, function () {
        jQuery( element ).unbind();
      } );
    }
  };

  /**
   * Renders 'help' Tooltip
   * It's just a helpfull wrapper for tooltip
   *
   * @author peshkov@UD
   */
  ko.bindingHandlers.help = {
    init: function ( element, valueAccessor, allBindingsAccessor, viewModel, bindingContext ) {
      jQuery( element ).addClass( 'wpp_help wpp_button' ).append( '<span class="wpp_icon wpp_icon_106"></span>' ).append( '<div class="wpp_description"></div>' ).find( '.wpp_description' ).html( valueAccessor() );
    }
  };

  /**
   * Prints data to console
   * Can be used for troubleshooting
   *
   * @author peshkovUD
   */
  ko.bindingHandlers.console = {
    init: function ( element, valueAccessor, allBindingsAccessor, viewModel, bindingContext ) {
      var d = valueAccessor();
      if ( typeof d === 'function' ) {
        console.log( 'observable', d() );
      } else {
        console.log( 'static', d );
      }
    }
  }

} );



