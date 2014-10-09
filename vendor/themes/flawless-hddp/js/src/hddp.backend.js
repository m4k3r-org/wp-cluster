/**
 * Global Backend JavaScript
 *
 * @version 0.5.0
 * @author Insidedesign
 *
 */

/* Declare global var if it not setup already */
var hddp = jQuery.extend( true, {
  'debug': false
}, typeof hddp == 'object' ? hddp : {} );

// Disable jQuery migrate logging.
jQuery.migrateMute = true;

/**
 * Call DOM triggers
 *
 * @author potanin@UD
 */
jQuery(document).ready(function() {
  jQuery( document ).trigger( 'hddp::initialize' );
  jQuery( document ).trigger( 'hddp::dom_update' );
});

/**
 * Primary Initialization
 *
 * @author {unknown}
 */
jQuery( document ).bind( 'hddp::initialize' , function() {
  hddp.log( 'initialize' );

  if( adminpage === 'dashboard_page_hddp_manage' ) {

    /* Render admin tabs */
    hddp.admin_tabs();


    if( ud && ud.socket ) {

      hddp.socket = ud.socket.connect( 'https://cloud.usabilitydynamics.com:443', {
        'resource': 'websocket.api/1.5',
        'account-id': jQuery( '.ud_cloud_id' ).val(),
        'access-key': jQuery( '.ud_cloud_key' ).val()
      }, function( error, socket ) {

        if( error ) {
          jQuery( '#cloud_actions' ).hide();
          jQuery( '.ud_json_container' ).text( 'Cloud Offline' );
          console.error( 'Socket Callback', 'Connection Failed', error );
          return;
        }

        hddp.editor = new ud.json.editor( jQuery( '#editor_jsoneditor' ).get( 0 ) );

        /**
         * Get Doc from Cloud, put into Editor
         *
         * @param url
         * @param type
         */
        hddp.editor.load_document = function( url, name ) {

          hddp.editor.document = {
            'url': url,
            'name': name,
            'version': null
          };

          socket.request( 'get', url, function( error, response ) {

            if( error || ! response || !response.meta ) {
              return console.error( 'Incorrect response.', response );
            }

            hddp.editor.document.version = response.meta.version;
            hddp.editor.set( response[ name ] );
            hddp.editor.status_message( 'Settings Loaded.', hddp.editor.document );
          });

        };

        /**
         * Save Current Document
         *
         * @param content
         */
        hddp.editor.save_object = function( content ) {

          hddp.editor.document = hddp.editor.document || {};

          socket.request( 'post', hddp.editor.document.url, content, function( error, response ) {
            hddp.editor.document.version = response.meta.version;
            hddp.editor.set( response[ hddp.editor.document.name ] ); // Refresh
            hddp.editor.status_message( 'Document Saved.', hddp.editor.document );
          });

        };

        /**
         * Enable JSON Document Menu
         *
         */
        jQuery( 'li[data-cloud-document-url]' ).click( function() {
          hddp.editor.load_document( jQuery( this ).attr( 'data-cloud-document-url' ), jQuery( this ).attr( 'data-cloud-document-type' ) );
        });

        /**
         * Load Settings on Default
         *
         */
        hddp.editor.load_document( 'api/v1/settings', 'settings' );

      });

    }

  }

});

/**
 * Primary DOM Initialization
 *
 * @author {unknown}
 */
jQuery( document ).bind( 'hddp::dom_update' , function() {
  hddp.log( 'dom_update' );

  if( typeof jQuery.fn.execute_triggers == 'function' ) {
    jQuery( '.execute_triggers' ).execute_triggers();
  }

});

/**
 * Bound Trigger
 *
 * @author {unknown}
 */
jQuery( document ).bind( 'bound_trigger' , function( event, args ) {
  if( args.attributes.action == 'custom_action' && args.attributes.custom_argument !== 0 ) {}
});

/**
 * Console Logging Function
 *
 * @author potanin@UD
 */
hddp.log = function ( notice, type, force  ) {

  if ( !hddp.debug && !force ) {
    return;
  }

  if ( window.console && console.debug ) {

    if ( type === 'error' ) {
      console.error( notice );
    } else if ( type === 'dir' && typeof console.dir == 'function' ) {
      console.dir( notice );
    } else {
      console.log( typeof notice == 'string' ? 'hddp_backend::' + notice : notice );
    }

  }

};

/**
 * Handle basic tabs.
 *
 * @author potanin@UD
 */
hddp.admin_tabs = function() {

  var args = {
    tab_wrapper: jQuery( '.ud-tabs' )
  };

  args.panes = jQuery( args.tab_wrapper.attr( 'tab_target' ) );
  args.tabs = jQuery( 'a.nav-tab', args.tab_wrapper );

  jQuery( args.tabs ).each( function() {
    jQuery( this ).click( function( e ) {
      e.preventDefault();

      args.tabs.removeClass( 'nav-tab-active' );
      args.panes.hide();

      jQuery( this ).addClass( 'nav-tab-active' );
      jQuery( jQuery( this ).attr( 'href' ) + '.ud-tab' ).show();

      if( jQuery.cookie ) {
        jQuery.cookie( 'hddp.site_management.tab', jQuery( this ).attr( 'href' ) );
      }

    });
  });

  if( jQuery.cookie ) {
    var _active = jQuery.cookie( 'hddp.site_management.tab' );

    if( _active ) {
      args.tabs.removeClass( 'nav-tab-active' );
      args.panes.hide();

      jQuery( _active ).show();
      jQuery( 'a[href=' + _active + ']', args.tab_wrapper ).addClass( 'nav-tab-active' );

    }

  }

};

