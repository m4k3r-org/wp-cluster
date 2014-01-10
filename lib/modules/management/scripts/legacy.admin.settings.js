/**
 * WP Property Settings / Upgrade / New Installation
 *
 * = To Do =
 * - Add System Attribute / Geolocation Attribute notice. (wpp.strings.protected_geolocation_slug)
 *
 * @updated 2.0
 */


/* If jQuery exists */
if( typeof jQuery === 'function' ) {

  /* Initial wpp object (new or extended) */
  wpp = jQuery.extend( true, {
    screen: 'settings',
    settings_ui: {
      animation: false,
      view_model: false,
      save_button: jQuery( 'input.wpp_save_settings' ),
      wrapper: jQuery( '.wpp-ajax-container' ),
      sidebar: jQuery( '.wpp_ui_sidebar' ),
      section_links: jQuery( 'ul.wpp_settings > li > a.wpp_link' ).css( 'opacity', 0.3 ),
      loaded_tabs: {}
    }
  }, typeof wpp === 'object' ? wpp : {} );


  /**
   * Initializer
   *
   */
  jQuery( document ).ready( function() {
    'use strict';wpp.log( 'document::ready', arguments );

    /* Show message if needed. */
    if( typeof wpp.request.message !== 'undefined' ) {
      switch( wpp.request.message ) {
        case 'settings_updated':
        case 'backup_restored':
          wpp.settings_ui.core_notice( wpp.strings[ wpp.request.message ] );
          break;
      }
    }

    /**
     * Handles form saving
     */
    jQuery( 'form.wpp_settings' ).submit( function( ) {

      /** Don't allow to submit form before View Model initialization. */
      if( !wpp.settings_ui.view_model ) {
        return false;
      }

    });

    /** Load all sections */
    wpp.settings_ui.load_sections();

  });


  /**
   *  Fired after Knockout applyBingings
   *
   */
  jQuery(document).bind( 'view_model::wpp_settings::init', function( e, view_model ) {

    /**
     * Mass set property type ( Property types section )
     */
    jQuery( "#wpp_ajax_max_set_property_type" ).click( function() {
     if( !confirm( view_model.strings.confirm_type_change ) ) {
       return;
     }
     jQuery.post( ajaxurl, {
       action: 'wpp_ajax_max_set_property_type',
       property_type: jQuery( "#wpp_ajax_max_set_property_type_type" ).val()
     }, function( data ) {
       if( data !== '' ) {
         jQuery( "#wpp_ajax_max_set_property_type_result" ).show();
         jQuery( "#wpp_ajax_max_set_property_type_result" ).html( data );
       }
     });
    });


    /**
     *
     */
    jQuery( '.wpp-backup-button span' ).click( function()  {
      switch( true ) {
        case jQuery( this ).hasClass( 'wpp_restore' ):
          if( confirm( view_model.strings.restore_backup_confirm ) ) {
            window.location = jQuery( this ).data( 'href' );
          }
          break;
        case jQuery( this ).hasClass( 'wpp_del' ):
          if( confirm( wpp.strings.delete_backup_confirm ) ) {
            jQuery.post( ajaxurl, {
              action: 'wpp_delete_option',
              option: jQuery( this ).data( 'backup' )
            });
            jQuery( this ).parent().remove();
          }
          break;
      }
    });


    /**
     * Show settings array
     */
    jQuery( "#wpp_show_settings_array" ).click( function() {
      jQuery( "#wpp_show_settings_array_cancel" ).show();
      jQuery( "#wpp_show_settings_array_result" ).show();
    });

    /**
     * Hide settings array
     */
    jQuery( "#wpp_show_settings_array_cancel" ).click( function() {
      jQuery( "#wpp_show_settings_array_result" ).hide();
      jQuery( this ).hide();
    });

    /**
     * Re-validate all addresses
     */
    jQuery( "#wpp_ajax_revalidate_all_addresses" ).click( function() {

      jQuery( this ).val( 'Processing...' );
      jQuery( this ).attr( 'disabled', true );
      jQuery( '.address_revalidation_status' ).remove();

      jQuery.post( ajaxurl, {
          action: 'wpp_ajax_revalidate_all_addresses'
          }, function( data ) {

          jQuery( "#wpp_ajax_revalidate_all_addresses" ).val( 'Revalidate again' );
          jQuery( "#wpp_ajax_revalidate_all_addresses" ).attr( 'disabled', false );

        var message;
        if( data.success == 'true' ) {
          message = "<div class='address_revalidation_status updated fade'><p>" + data.message + "</p></div>";
        } else {
          message = "<div class='address_revalidation_status error fade'><p>" + data.message + "</p></div>";
        }

          jQuery( message ).insertAfter( "h2" );
        }, 'json' );
    });

    /**
     * Show property query
     */
    jQuery( "#wpp_ajax_property_query" ).click( function() {

      var property_id = jQuery( "#wpp_property_class_id" ).val();

      jQuery( "#wpp_ajax_property_result" ).html( "" );

      jQuery.post( ajaxurl, {
          action: 'wpp_ajax_property_query',
          property_id: property_id
        }, function( data ) {
          jQuery( "#wpp_ajax_property_result" ).show();
          jQuery( "#wpp_ajax_property_result" ).html( data );
          jQuery( "#wpp_ajax_property_query_cancel" ).show();

        });

    });

    /**
     * Show image data
     */
    jQuery( "#wpp_ajax_image_query" ).click( function() {

      var image_id = jQuery( "#wpp_image_id" ).val();

      jQuery( "#wpp_ajax_image_result" ).html( "" );

      jQuery.post( ajaxurl, {
          action: 'wpp_ajax_image_query',
          image_id: image_id
        }, function( data ) {
          jQuery( "#wpp_ajax_image_result" ).show();
          jQuery( "#wpp_ajax_image_result" ).html( data );
          jQuery( "#wpp_ajax_image_query_cancel" ).show();

        });

    });

    /**
     * Hide property query
     */
    jQuery( "#wpp_ajax_property_query_cancel" ).click( function() {
      jQuery( "#wpp_ajax_property_result" ).hide();
      jQuery( this ).hide();
    });

    /**
     * Hide image query
     */
    jQuery( "#wpp_ajax_image_query_cancel" ).click( function() {
      jQuery( "#wpp_ajax_image_result" ).hide();
      jQuery( this ).hide();
    });

    /**
     * Clear Cache
     */
    jQuery( "#wpp_clear_cache" ).click( function() {
      jQuery( '.clear_cache_status' ).remove();
      jQuery.post( ajaxurl, {
          action: 'wpp_ajax_clear_cache'
        }, function( data ) {
        var message = "<div class='clear_cache_status updated fade'><p>" + data + "</p></div>";
          jQuery( message ).insertAfter( "h2" );
        });
    });
  });


  /**
   * Initializes Settings MVVM View Model
   *
   * @author peshkov@UD
   */
  wpp.settings_ui.set_view_model = function() {

    /** view_model should initialized only at once. */
    if( wpp.settings_ui.view_model ) {
      return false;
    }

    /** Set specific model's data which will be extended by ko.view_model */
    var model = {

      wpp_model: function( data ) {
        return jQuery.extend( {
          'developer_mode': wpp.developer_mode,
          'build_mode': wpp.build_mode,
          'show_ud_log': wpp.show_ud_log,
          'disable_automatic_feature_update': wpp.disable_automatic_feature_update,
          'disable_wordpress_postmeta_cache': wpp.disable_wordpress_postmeta_cache,
          'disable_legacy_detailed': wpp.disable_legacy_detailed,
          'do_not_automatically_regenerate_thumbnails': wpp.do_not_automatically_regenerate_thumbnails,
          'load_scripts_everywhere': wpp.load_scripts_everywhere,
          'do_not_load_theme_specific_css': wpp.do_not_load_theme_specific_css,
          'customer_key': wpp._instance.customer_key ? wpp._instance.customer_key : null,
          'api_key': wpp._instance.api_key ? wpp._instance.api_key : null,
          'site_uid': wpp._instance.site_uid ? wpp._instance.site_uid : null,
          'public_key': wpp._instance.public_key ? wpp._instance.public_key : null,
          'customer_name': null,
          'customer_name_error': wpp.strings.customer_name_is_undefined,
          'message': null, /* Latest message set by SaaS server */
          'splash': null
        }, typeof data === 'object' ? data : {} );
      },

      global: {

        attribute_classification: function( data ) {
          return ko.mapping.fromJS( jQuery.map( data, function(value, data) {
            value.admin = jQuery.map( value.admin, function( value, data ){
              return {slug: data, label: value};
            });
            value.search = jQuery.map( value.search, function( value, data ){
              return {slug: data, label: value};
            });
            value.slug = data;
            value.description = typeof value.description !== 'undefined' ? value.description : '';
            return value;
          } ) );
        },

        property_types: function( data ) {
          var self = this;
          data = data ? data : [];
          data = ko.utils.arrayMap( data, function( d ){
            d.new_item = false;
            return new self._property_type( d );
          });
          return ko.mapping.fromJS(data);
        },

        _property_type: function ( args ) {
          var self = this;

          var args = jQuery.extend( true, {
            label: wpp.strings.new_property_type,
            slug: 'new_listing_type',
            new_item: true,
            show_advanced_settings : false,
            meta: {
              'for_sale' : false,
              'for_rent' : false,
              'by_owner' : false
            },
            settings: {
              'geolocatable' : false,
              'searchable' : false,
              'hierarchical' : false
            },
            hidden_attributes: [],
            property_inheritance: [],
            toggle_settings: function( self, event ) {
              self.show_advanced_settings( !self.show_advanced_settings() );
            }

          } , typeof args === 'object' ? args : {} );

          args = ud.apply_filter( 'view_model::wpp_settings::_property_type', args );

          jQuery.each( args, function( i, e ) {
            if( typeof e === 'function' ) self[i] = e;
            else if( typeof e === 'object' && typeof e.length !== 'undefined' ) self[i] = ko.observableArray( e );
            else self[i] = ko.observable( e );
          } );
        },

        image_sizes: function ( args ) {
          var self = this;
          args = jQuery.map( args, function( data, slug ){
            data.slug = slug;
            return jQuery.extend( { width: '0', height: '0' }, data );
          } );
          return ko.mapping.fromJS ( args.sort( function(a, b){ return !a.built_in-!b.built_in; } ) );
        },

        _image_size: function( args ) {
          var self = this;
          args = jQuery.extend( true, {
            built_in: false,
            slug:'',
            width:'0',
            height:'0'
          }, typeof args === 'object' ? args : {} );
          for( var i in args ) self[i] = ko.observable( args[i] );
        }
      },

      /**
       * Generates Developer keys.
       * Used on Advanced tab
       *
       * @see core/ui/templates/advanced.php
       */
      generate: {
        api_key: function( self ) {
          if( self.saas.id ) {
            wpp.saas.emit( self.saas.update_trigger, {'key': 'api_key', 'path': 'wpp_model'} );
          }
        },
        site_uid: function( self ) {
          wpp.saas.emit( self.saas.update_trigger, {'key': 'site_uid', 'path': 'wpp_model'} );
        },
        public_key: function( self ) {
          wpp.saas.emit( self.saas.update_trigger, {'key': 'public_key', 'path': 'wpp_model'} );
        }
      },

      /**
       * Clear all activity logs.
       * Used on Logs tab.
       *
       * @see core/ui/interfaces/log.interface
       */
      clear_logs: function( self, item ) {
        if( typeof self !== 'object' || typeof item === 'undefined' || !confirm( self.strings.are_you_sure ) ) {
          return;
        }
        /** Set arguments */
        var args = { 'action': 'wpp_clear_logs' };
        if ( typeof item === 'object' && typeof item.id !== 'undefined' ) {
          args.id = item.id;
        }
        jQuery.post( ajaxurl, args, function( data ) {
          if( typeof data[0] !== 'undefined' && parseInt( data[0] ) > 0 ) {
            if( typeof args.id !== 'undefined' ) {
              self.global.activity_logs.remove( item );
            } else {
              self.global.activity_logs.removeAll();
            }
          } else {
            wpp.settings_ui.core_notice( self.strings.error_on_log_removing, 'wpp_error' );
          }
        }, 'json' );
      }

    };

    /** Adds ability to handle model's additional data. Can be used by Premium Features */
    model = ud.apply_filter( 'view_model::wpp_settings::create', model );

    wpp.settings_ui.view_model = ko.view_model( {
      'model': ( typeof wpp._models !== 'undefined' && typeof wpp._models.wpp_settings === 'object' ) ? wpp._models.wpp_settings : 'wpp_settings',
      'saas': {
        'model': 'wpp_settings',
        'screen': wpp.screen,
        'show_updates': true,
        'debug_mode': wpp.developer_mode,
        'path': 'wpp_model'
      },
      'container': wpp.settings_ui.wrapper,
      'actions': {

        'callback': function( error, data ) {
          /* Fatal Errors resulting in no UI */
          if( typeof Error === 'function' && error instanceof Error ) {
            /** Prevent Load Timeout Event. Becuase error already exists */
            if( typeof data !== 'undefined' && typeof data.timers === 'object' ) {
              clearTimeout( data.timers.load_timeout );
            }
            if( !jQuery( '#wpp_fatal_notice' ).length > 0 ) wpp.settings_ui.wrapper.before( '<div id="wpp_fatal_notice"></div>' );
            wpp.settings_ui.wrapper.hide().html('');
            wpp.settings_ui.sidebar.css( 'opacity', 0.3 );
            wpp.fatal_notice( {error: error, submit: this, target: jQuery( '#wpp_fatal_notice' )} );
            wpp.log( error );
            return false;
          }
          return data;
        },

        /* Automatically called on view_model applying */
        'init': function( self ) {
          /* Always do emit first time before subscribe! Because subscribe could be never fired! */
          if( typeof self.saas._observable_caller === 'object' ) {
            jQuery.each( self.saas._observable_caller, function( key, obj ) {
              switch( obj.key ) {
                case 'customer_key':
                  if( obj.value() !== null ) {
                    wpp.saas.emit( self.saas.update_trigger, {'key': obj.key, 'value': obj.value(), 'path': obj.path} );
                  }
                  break;
              }
            } );
          }
          /* We run Sammy when everything ( interfaces ) is ready */
          wpp.settings_ui.router.run();
          /* Adds ability to add hooks here. Also Can be used by Premium Features */
          jQuery( document ).trigger( 'view_model::wpp_settings::init', self );
        },

        /* Called when data is updated by SaaS */
        'saas_update_on': function ( self, data ) {
          switch( data.key ) {
            /* Check customer key */
            case 'customer_key':
              if( data.success ) {
                self.wpp_model.customer_name( data.customer_name );
                self.wpp_model.customer_name_error( null );
              } else {
                self.wpp_model.customer_name( null );
                self.wpp_model.customer_name_error( data.value === '' ? wpp.strings.customer_name_is_undefined : data.message );
              }
              break;

          }
        },

        /* Called if SaaS connection is not established */
        'saas_ignored': function() {
          wpp.core_notice( 'WP-Property SaaS connection is not established. Some functionality is not available.', 'wpp_error' );
        }

      },
      'instance': model
    } );

    /* Show sidebar */
    wpp.settings_ui.sidebar.show();

    if( wpp.settings_ui.view_model ) {
      /** Init pagination between tabs */
      wpp.settings_ui.set_sticky_sidebar();
    } else {
      wpp.settings_ui.wrapper.html( 'Error occurred on page loading' );
    }

  };


  /**
   * Pagination.
   * History implementation.
   *
   * @author peshkov@UD
   */
  wpp.settings_ui.router = new Sammy( function() {

    this.home = location.href;

    this.get( '#:ui_section', function() {
      var self = this;

      var breadcrumb = jQuery( '.wpp_section_title', '.wpp_section_title_wrap' );
      if ( breadcrumb.length > 0 ) breadcrumb.empty().parent().hide();

      /** Set current section in form data to have ability to load the current section after save setting */
      if( jQuery( 'input[name="current_section"]' ).length > 0 ) {
        jQuery( 'input[name="current_section"]' ).val( self.params.ui_section );
      }
      /** Open called section */
      if ( !wpp.settings_ui.animation ) {
        wpp.settings_ui.animation = true;
        jQuery( '.wpp_settings li a.wpp_link' ).parent().removeClass('wpp_current');
        jQuery.each( wpp.settings_ui.loaded_tabs, function( i, e ) {
          e = jQuery( '.wpp_section_' + i, wpp.settings_ui.wrapper );
          jQuery( e[0] ).fadeOut( 500, function() {
            jQuery( this ).hide();
            if( i === self.params.ui_section ) {
              wpp.settings_ui.animation = true;
              jQuery( this ).fadeIn( 500, function() {
                jQuery( '.wpp_settings li a.wpp_link[href="#'+self.params.ui_section+'"]' ).parent().addClass('wpp_current');
                jQuery( this ).show(500, function(){
                  wpp.settings_ui.animation = false;
                  if( jQuery( '.wpp_section_title', this ).length > 0 && breadcrumb.length > 0 ) {
                    breadcrumb.html( jQuery( '.wpp_section_title', this ).html() ).parent().show();
                  }
                  jQuery( document ).trigger(self.params.ui_section+'_show', [this]);
                });
              } );
            }
          } );
        } );
      }
    });

    this.get( '', function( self ) {
      if( self.app.home !== location.href ) {
        window.location = location.href;
      } else {
        this.app.runRoute( 'get', '#main' );
      }
    } );

  });


  /**
   * Load Sections by way of Ajax
   *
   * @version 2.0
   * @author potanin@UD
   */
  wpp.settings_ui.load_sections = function() {

    wpp.settings_ui.wrapper.addClass( 'wpp_ui_loading' );

    /**
     * For each settings section run GET UI in background
     */
    var items = [];
    wpp.settings_ui.section_links.each( function( k, element ) {
      items[k] = jQuery( element ).data( 'ui' );
    });

    wpp.ajax( 'get_uis', items, function( response ) {

      for ( k in response.uis ){

        /** IE fix */
        if( typeof response.uis[k] !== 'object' ) continue;

        var item  =  response.uis[k],
            element = wpp.settings_ui.section_links[k],
            section = jQuery( element ).attr( 'href' ).replace( '#', '' );

        if( wpp.settings_ui.loaded_tabs[ section ] ) {return;}

        var html = '<div class="wpp_settings_section wpp_section_' + section + '">' + item.ui + '</div>';

        wpp.settings_ui.loaded_tabs[ section ] = jQuery( html ).hide();

        /* Show Link as loaded */
        jQuery( element ).css( 'opacity', '1' );

        wpp.settings_ui.wrapper.append( wpp.settings_ui.loaded_tabs[ section ] );

        jQuery( document ).trigger( 'wpp::'+section+'::interface::loaded' );

      }

      if( jQuery( '.wpp_settings_section' ).length === wpp.settings_ui.section_links.length ) {

        /** Init MVVM View Model and do applyBindings */
        wpp.settings_ui.set_view_model();
        /** Add trigger for ability to add addional actions on this event */
        jQuery( document ).trigger( 'wpp::settings_ui::loaded' );

      }

    });
  };


  /**
   * Inits sticky sidebar
   *
   * @author peshkov@UD
   */
  wpp.settings_ui.set_sticky_sidebar = function( options ) {
    'use strict';

    if( typeof jQuery.prototype.stickySidebar === 'function' ) {

      /* Set options for stickySidebar */
      var options = jQuery.extend( {
        padding: 28,
        speed: 0,
        on: {
          'update' : function( event, data, sTop, origTop ) {
            var e = jQuery( event.currentTarget ),
                f = false;
            if( !e.hasClass( 'sticky-updated' ) ) {
              e.addClass( 'sticky-updated' );
              f = true;
            }
            e.css( {'left' : 0, 'right' : 0, 'width' : 'auto'} );
            if( origTop < sTop ) {
              jQuery( '.wpp_screen_icon', e ).hide();
              jQuery( '.wpp_title_wrap', e ).css( { 'opacity' : 0.5 } );
              jQuery( '.wpp-title', e ).css( { 'font-size' : '18px' } );
              e.css( {'paddingTop' : '4px', 'paddingBottom' : '4px'} );
            } else if ( !f ) {
              jQuery( '.wpp_screen_icon', e ).show();
              jQuery( '.wpp_title_wrap', e ).css( { 'opacity' : 1 } );
              jQuery( '.wpp-title', e ).css( { 'font-size' : '23px' } );
              e.css( {'paddingTop' : '20px', 'paddingBottom' : '20px'} );
            }
          },
          'remove' : function( event ) {
            var e = jQuery( event.currentTarget );
            e.removeClass( 'sticky-updated' );
          }
        }
      }, typeof options === 'object' ? options : {} );

      jQuery( '.wpp-ui-header' ).stickySidebar( options );

      /* Remove stickySidebar functionality if Contextual help is opened to avoid bugs. peshkov@UD */
      jQuery( document ).bind( 'contextual-help-link::toggle', function( event, isHidden ) {
        jQuery( '.wpp-ui-header' ).stickySidebar( 'remove' );
        setTimeout( function() {
          if( jQuery( '#screen-meta' ).is( ':hidden' ) ) {
            jQuery( '.wpp-ui-header' ).stickySidebar( options );
          }
        }, 500 );
      } );

    }
  };


  /**
   * Display a full-width notice.
   *
   */
  wpp.settings_ui.core_notice = function( message, type ) {
    'use strict';wpp.log( 'wpp.settings_ui.core_notice()', arguments );
    wpp.core_notice( message, ( typeof type !== 'undefined' ? type : 'wpp_updated' ), true );
  };

}

