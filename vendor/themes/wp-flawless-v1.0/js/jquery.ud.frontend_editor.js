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
        containers: this
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

    return this;

  };

}) ( jQuery );
