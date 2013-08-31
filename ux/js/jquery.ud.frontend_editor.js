/* =========================================================
 * jquery.ud.frontend_editor.js v1.5.0
 * http://usabilitydynamics.com
 * =========================================================
 * Copyright 2011 Usability Dynamics, Inc.
 *
 *
 * Version 0.0.1
 * Validation: http://www.jslint.com/
 *
 * Copyright (c) 2012 Usability Dynamics, Inc. (usabilitydynamics.com)
 * ========================================================= */

/*jslint indent: 2 */
/*global window */
/*global console */
/*global clearTimeout */
/*global setTimeout */
/*global jQuery */
( function( jQuery ){
  jQuery.fn.frontend_editor = function( s ) {

    /* Set Settings */
    s = jQuery.extend( true, {
      settings: {
        movable_elements: '.flawless_module',
        max_width: false,
        change_delay: 10,
        handles: {
          header: 's',
          footer: 's'
        },
        timers: {
          abandonment: 2000
        },
        debug: false
      },
      styles: {
        containers: {},
        modules: {}
      },
      wrapper: jQuery( '.container:first' ),
      ui: {
        containers: this,
        panel_container: jQuery( '<div class="ffe_panel_container"></div>' ).hide(),
        panel: jQuery( '<div class="container ffe_panel"></div>' ),
        controls: {
          layout_width: {
            element: jQuery( '<div class="ffe_layout_width"></div>' ).slider({ value: jQuery( 'div.container' ).width(), min: 800, max: 1650, step: 10, slide: function( event, ui ) { jQuery( 'body div.container:not(.ffe_panel)' ).css( 'max-width', ui.value ); } }),
            label: 'Maximum Layout Width:'
          },
          gutter_width: {
            element: jQuery( '<div class="ffe_gutter_width"></div>' ).slider({ value: 10, min: 0, max: 100, step: 1, slide: function( event, ui ) { set_gutter( ui.value ); } }),
            label: 'Gutter Width:'
          },
          visual_debug_toggle: {
            element: jQuery( '<span class="ffe_visual_debug_toggle btn btn-inverse">Toggle Layout Helpers</span>' ).click( function() { flawless.toggle_visual_debug(); } )
          }
        }
      }
    }, s );


    /* Get Modules */
    if( typeof s.ui.modules != 'object' ) {
      s.ui.modules = jQuery( s.settings.movable_elements, s.ui.containers );
    }

   /**
    * Internal logging function
    *
    * @author potanin@UD
    */
    var log = function( something, type ) {

      if( !s.settings.debug ) {
        return;
      }

      if( window.console && console.debug ) {

        if ( type == 'error' ) {
          console.error( typeof something !== 'object' ? 'frontend_editor:: ' + something : something );
        } else {
          console.log( typeof something !== 'object' ? 'frontend_editor:: ' + something : something  );
        }

      }

    };

    var set_gutter = s.set_gutter = function( width ) {

      jQuery( 'div.formatted-row.row-fluid.row-fluid > div[class*="span"] > .cfct-module' ).css( 'margin-right', Math.round( ( width / 2 ) )  );
      jQuery( 'div.content_wrapper.row-fluid > div[class*="span"] > .cfct-module' ).css( 'margin-right', Math.round( ( width / 2 ) )  );
      jQuery( 'div.row-fluid > div[class*="span"] > .cfct-module' ).css( 'margin-right', Math.round( ( width / 2 ) )  );

      jQuery( 'div.formatted-row.row-fluid.row-fluid > div[class*="span"] > .cfct-module' ).css( 'margin-left', Math.round( ( width / 2 ) )  );
      jQuery( 'div.content_wrapper.row-fluid > div[class*="span"] > .cfct-module' ).css( 'margin-left', Math.round( ( width / 2 ) )  );
      jQuery( 'div.row-fluid > div[class*="span"] > .cfct-module' ).css( 'margin-left', Math.round( ( width / 2 ) )  );

      //jQuery( '.row-fluid > div.first[class*="span"] > .cfct-module' ).css( 'margin-left', Math.round( ( width / 2 ) ) );

    }

    var _create_panel = s._create_panel = function() {

      if( jQuery( 'body' ).hasClass( 'ffe_panel_active' ) ) {
        s.ui.panel_container.slideUp();
        jQuery( 'body' ).removeClass( 'ffe_panel_active' );
        return true;
      }

      //** Show Dropdown */
      jQuery( 'body' ).addClass( 'ffe_panel_active' );

      jQuery( 'body' ).prepend( s.ui.panel_container );

      s.ui.panel_container.append( s.ui.panel );

      s.ui.panel_container.slideDown( 'fast', function() {

        jQuery.each( s.ui.controls, function( control_name, data ) {

          s.ui.panel.append( data.element );

          jQuery( data.element ).wrap( '<div class="ffe_control" />' );

          if( data.label ) {
            jQuery( '<label class="ffe_label">' + data.label + '</label>' ).insertBefore( data.element );
          }

        });

      });

    }


    /**
     * Ran on Initialization, after the UI references have been created.
     *
     * {missing description}
     *
     * @author potanin@UD
     */
    this.enable = function() {
      log( 'enable()' );

      /* Setup Containers */
      jQuery.each( s.ui.containers, function() {

        var container = this;
        var container_type = jQuery( container ).attr( 'container_type' );

        jQuery( container ).css( 'position', 'relative' );
        jQuery( container ).css( 'height', jQuery( container ).height() );

        /* Create data storage point */
        s.styles[ 'containers' ][ container_type ] = '';

        jQuery( document ).trigger( 'frontend_editor::update_styles', {
          type: 'container',
          key: container_type,
          action: 'initialization',
          element: container
        });

        jQuery( container ).resizable( {
          delay: s.settings.change_delay,
          handles: s.settings.handles[ container_type ],
          stop: function( event, this_ui ) {

            jQuery( document ).trigger( 'frontend_editor::update_styles', {
              type: 'container',
              key: container_type,
              action: 'resize',
              element: this_ui.helper,
              ui: this_ui
            });

            /* Unset CSS styles that should not be applied to containers */
            jQuery( container ).css( 'top', '' ).css( 'width', '' ).css( 'left', '' ).css( 'right', '' ).css( 'bottom', '' );

          }
        });

        jQuery( container ).addClass( 'editable_container' );

      });

      /* Setup individual modules */
      jQuery.each( s.ui.modules, function() {

        var module = this;
        var abandonment_timer;
        var element_hash = jQuery( module ).attr( 'element_hash' );
        var position = jQuery( module ).position();
        var selection_shield = jQuery( '<div class="selection_shield" style="position: absolute;top: 0;left: 0;width: 100%;height: 100%;"></div>' );

        s.styles[ 'modules' ][ element_hash ] = '';

        /* Fix current position by making it absolute (make have issues with older settings which were relative for some reason) */
        jQuery( module ).css( 'position', 'absolute' );
        jQuery( module ).css( 'left', position.left + 'px' );
        jQuery( module ).css( 'top', position.top + 'px' );
        jQuery( module ).css( 'height', jQuery( module ).height()  + 'px' );
        jQuery( module ).css( 'width', jQuery( module ).width()  + 'px' );

        jQuery( document ).trigger( 'frontend_editor::update_styles', {
          type: 'module',
          key: element_hash,
          action: 'initialization',
          element: module
        });

        jQuery( module ).addClass( 'movable' );

        /* Make it draggable */
        jQuery( module ).draggable( {
          containment: 'parent',
          delay: s.settings.change_delay,
          grid: [5, 5],
          stop: function( event, this_ui ) {

            jQuery( document ).trigger( 'frontend_editor::update_styles', {
              type: 'module',
              key: element_hash,
              action: 'drag',
              element: this_ui.helper,
              ui: this_ui
            });

          }
        });

        /* Make it resizable */
        jQuery( module ).resizable( {
          containment: 'parent',
          snap: true,
          delay: s.settings.change_delay,
          handles: 'all',
          stop: function( event, this_ui ) {

            jQuery( document ).trigger( 'frontend_editor::update_styles', {
              type: 'module',
              key: element_hash,
              action: 'resize',
              element: this_ui.helper,
              ui: this_ui
            });

          }
        });

        /* Track user intefaction by way of mouse */
        jQuery( module ).mouseenter( function() {
          jQuery( module ).addClass( 'active_now' );

          /* Add overlay element to cover up entire module to avoid interaction */
          if( !jQuery( '.selection_shield', module ).length ) {
            jQuery( module ).append( selection_shield );
          }

          clearTimeout( abandonment_timer );

        }).mouseleave( function() {
          abandonment_timer = setTimeout( function() {
            jQuery( module ).removeClass( 'active_now' );

            jQuery( selection_shield ).remove();

          }, s.settings.timers.abandonment );
        });

      });


    }


    /**
     * Disable the ditor, removing all classes and stuff
     *
     */
    this.disable = function() {
      log( 'disable()' );
      jQuery('.selection_shield').remove();
      jQuery.each( s.ui.modules, function() {
        jQuery( this ).removeClass( 'movable' ).removeClass( 'active_now' ).draggable( 'destroy' ).resizable( 'destroy' ).unbind( 'mouseenter mouseleave' );
      });

      jQuery.each( s.ui.containers, function() {
        jQuery( this ).resizable( 'destroy' ).removeClass( 'editable_container' );
      });

    }


    /**
     * Bound to every Module and Container change.
     *
     * Styles are updated here, most notably converted from pixels to percentages
     *
     * @todo Add some logic to determine if positioning should be left or right based to handle better in responsive layouts. - potanin@UD
     * @author potanin@UD
     */
    jQuery( document ).bind( 'frontend_editor::update_styles', function( event, settings ) {

      /* Get the parent and dimensions */
      var args = {
        type: settings.type,
        key: settings.key,
        parent: jQuery( settings.element ).parent(),
        styles: {
          height: jQuery( settings.element ).height()  + 'px'
        },
        print_styles: []
      };

      /* For Modules only, get the position in relation to the parent */
      if( settings.type == 'module' ) {

        args.element = jQuery( settings.element );
        args.container_width = args.parent.width();

        /* Determine of module is left or right bound */
        args.left_distance = args.element.position().left;
        args.right_distance = args.parent.width() - args.element.width() - args.left_distance;

        if( args.right_distance < args.left_distance ) {
          args.styles.right = parseInt( Math.round( ( args.right_distance / args.container_width ) * 10000 ) / 100 );
          args.styles.right = ( args.styles.right > 0 && args.styles.right < 100 ? args.styles.right : 0 ) + '%';
        } else {
          args.styles.left = parseInt( Math.round( ( args.left_distance / args.container_width ) * 10000 ) / 100 );
          args.styles.left = ( args.styles.left > 0 && args.styles.left < 100 ? args.styles.left : 0 ) + '%';
        }

        args.styles.top =  parseInt( Math.round( ( jQuery( settings.element ).position().top / args.parent.height() ) * 10000 ) / 100 );
        args.styles.width = jQuery( settings.element ).width() + 'px';
        args.styles.position = 'absolute';

        /** Double-check that dimensions are within range */
        args.styles.top = ( args.styles.top > 0 && args.styles.top < 100 ? args.styles.top : 0 ) + '%';

      }

      if( settings.type == 'container' ) {
        args.styles.position = 'relative';
      }

      jQuery.each( args.styles, function( key, value ) {
        args.print_styles.push( key + ':' + value );
      });

      args.print_styles = args.print_styles.join( ';' );

      if( args.type == 'module' ) {
        s.styles.modules[ args.key ] = args.print_styles;
      }

      if( args.type == 'container' ) {
        s.styles.containers[ args.key ] = args.print_styles;
      }

      /* console.log( args.print_styles ); */

    });

    /* Automatically Enable */
    this.enable();

    /* Make available for short access */
    this.styles = s.styles;

    return s;

  };

}) ( jQuery );
