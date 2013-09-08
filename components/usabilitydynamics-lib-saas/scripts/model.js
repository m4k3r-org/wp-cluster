/**
 * Knockout View Model
 *
 * Flexible View Model generator for interfaces:
 * creates View Model object and initializes (apply bindings) knockout functionality.
 * Contains basic view_model functionality such as add_data, remove_data ( the same as depriciated add and remove row functionality ), etc.
 * Also, it's used as Gateway to SaaS: initializes socket connection.
 *
 * @version 1.0
 * @description ViewModel class for all knockout/saas instances ( view models )
 * @package Knockout
 * @subpackage UD
 * @dependencies jquery, knockout, knockout-mapping, ud.socket, ud.saas
 * @author team@UD
 * @param args. mixed. Settings.
 */
if ( typeof ko !== 'undefined' ) {

  ko = jQuery.extend( true, {
    'log': ( typeof wpp !== 'undefined' && wpp.log === 'function' ) ? wpp.log : function () {
      return false;
    },
    'ajaxurl': function ( action ) {
      return typeof ajaxurl !== 'undefined' ? ajaxurl + '?action=wpp_' + action : false;
    },
    'strings': ( typeof wpp !== 'undefined' && wpp.strings !== 'undefined' ) ? wpp.strings : {}
  }, ko );

  ko.view_model = function ( args ) {
    'use strict';
    ko.log( 'ko.view_model()', arguments );

    /* Try to determine if model's name is set or model's object already exists */
    args = jQuery.extend( true, {
      'model': {}, // model can be string.
      'args': {}, // specific arguments for model getting. i.e. unique ID.
      'instance': {}, // Object which extends the current model. All additional object's data can be set here
      'bind': true, // If true, it allows do ko.applyBindings()
      'timeout': 3000, //15000, // Interface load timeout.
      'container': false, // Required!
      // All SaaS settings stored here
      'saas': {
        'scope': jQuery.extend( ( typeof wpp.saas === 'object' ? wpp.saas : {} ), { scope: 'wpp.saas' } ), // SaaS object scope ( ud.saas )
        'model': false,
        'screen': false,
        'show_updates': false, // Shows messages in navbar
        'debug_mode': false, // Prints logs to console
        'force_new_connection': false, // If true, we set new socket connection in any case.
        'secure': true, // SSL or not
        'path': '_saas', // Link to object which should be emited on subscribe,
        'instance': typeof wpp._instance === 'object' ? wpp._instance : {} // Contains client's neccessary data such as site_url, api_key, etc
      },
      // Set of actions listeners which are fired on view model 'triggers'
      'actions': {
        'pre_apply': function () {
          return;
        }, // Called before ko.applyBindings() function
        'init': function () {
          return;
        }, // Called after ko.applyBindings() function
        'add_data': function () {
          return;
        }, // Called after data adding
        'remove_data': function () {
          return;
        }, // Called after data removing
        'get_model': function () {
          return;
        }, // Called before we do ajax request for get model
        'timeout': false, // Called on timeout
        'callback': false, // Additional
        'saas_connect': function () {
          return;
        }, // Called on ud::saas::connect event
        'saas_disconnect': function () {
          return;
        }, // Called on ud::saas::disconnect event
        'saas_set_screen': function () {
          return;
        }, // Called on self.saas._set_screen
        'saas_update_on': function () {
          return;
        }, // Called on saas data updating by SaaS
        'saas_update_emit': function () {
          return;
        }, // Called on data emit to SaaS
        'saas_ignored': function () {
          return;
        } // Called if Saas connection is not established and we ignore it.
      },
      // Return / Callback handling. Outputs Error objects to console, returns second argument
      // Should not be overwritten! If you want to add your callback use actions.callback
      'callback': function ( error, data ) {
        var self = this;
        if ( typeof self.actions === 'object' && typeof self.actions.callback === 'function' ) {
          return self.actions.callback( error, data );
        } else {
          if ( error instanceof Error ) {
            ko.log( error, data );
          }
          return data;
        }
      }
    }, typeof args === 'object' ? args : ( typeof args === 'string' ? { 'model': args } : {} ) );

    /* Check container argument */
    var container = ( args.container && typeof args.container !== 'object' ) ? jQuery( args.container ) : args.container;

    if ( !container || typeof container.length === 'undefined' || !container.length > 0 ) {
      return args.callback( new Error( 'ko.view_model. Container is missing, or incorrect.' ), false );
    }

    /* Set Timeout Event */
    if ( !args.actions.timeout || typeof args.actions.timeout !== 'function' ) {
      args.actions.timeout = function ( self ) {
        /**
         * Determine if Saas connection is not established.
         * If yes, - so it can be the reason of timeout.
         * Try to ignore Saas connection in this case and apply bindings without Saas. peshkov@UD
         */
        if ( typeof self.saas.model == 'string' && self.saas.connected == false && self.saas.ignore == false ) {
          try {
            if ( typeof self.saas.scope == 'object' ) self.saas.scope.disconnect();
          } catch ( e ) {
            console.log( e );
          }
          ;
          /* Set bind val passed to view_model */
          self._bind = self._args.bind;
          /* Saas initialization is ignored now. */
          self.saas.ignore = true;
          /* We should set saas variable to observable in any case. */
          if ( typeof self[ self.saas.path ] === 'object' ) {
            self[ self.saas.path ] = ko.mapping.fromJS( self[ self.saas.path ] );
          }
          /* Now, we try to call apply. */
          self.apply();
          /* Updates navbar notice */
          if ( self.saas.show_updates ) {
            self.navbar_notice( 'SaaS offline' );
          }
          /* Special Handlers can be added here */
          try {
            self._args.actions.saas_ignored( self );
          } catch ( e ) {
            self._args.callback( e, self );
          }
          return;
        }

        self._is_timeout = true;
        return args.callback( new Error( 'Timeout. View Model load error.' ) );
      };
    }

    var html = container.html();

    container.html( '' ).addClass( 'ud_view_model ud_ui_loading' ).append( '<div class="ud_ui_spinner"></div>' ).append( '<div class="ud_ui_prepared_interface"></div>' ).find( '.ud_ui_prepared_interface' ).html( html );

    /**
     * Creates View_Model
     *
     * Any view_model methods or static variables shoud be added inside of this function.
     */
    var vm = function ( args, container ) {

      var self = this;

      /* Determines if view_model already applied Bindings ( ko.applyBinding )  */
      self._applied = false;

      /* Determines if model's data is successfully loaded */
      self._loaded = false;

      /* SaaS connection */
      self.saas = args.saas;

      /* Arguments */
      self._args = args;

      /* DOM */
      self.container = container;

      /* Action Hooks */
      self._actions = {};

      /* Bind. boolean */
      self._bind = args.bind;

      /* Is timeout? */
      self._is_timeout = false;

      /* setTimeout instances */
      self.timers = {
        'load_timeout': false,
        'socket_status': false
      };

      /**
       * Pushes new element to array.
       *
       * Example of usage:
       * data-bind="click: $root.add_data.bind( $data, $root.some_array, $root.vhandler )"
       * where $root.vhandler is a function, which creates data.
       *
       * $root.vhandler example:
       * self.handler = function() {
       *   var self = this;
       *   self.arg1 = ko.observable( 'value1' );
       *   self.arg2 = 'value2';
       * }
       *
       * @param observableArray item. Where we want to add new data
       * @param mixed vhandler. Name of function or function which inits new data
       * @param object view_model. The current view_model object
       * @param object event.
       * @author peshkov@UD
       */
      self.add_data = function ( item, vhanlder, view_model, event ) {
        if ( typeof vhanlder == 'function' ) {
          item.push( new vhanlder );
        } else if ( typeof view_model[ vhanlder ] === 'function' ) {
          item.push( new view_model[ vhanlder ]() );
        }
        try {
          self._args.actions.add_data( self, event, item, vhanlder )
        } catch ( e ) {
          self._args.callback( e, view_model );
        }
      };

      /**
       * Removes data from array.
       *
       * Example of usage:
       * data-bind="click: $root.remove_data.bind( $data, $root.some_array )"
       *
       * @param observableArray item. Where we want to remove data
       * @param mixed data. Data which should be removed from array.
       * @param object event.
       * @author peshkov@UD
       */
      self.remove_data = function ( item, data, event ) {
        var c = typeof ko.strings !== 'undefined' && typeof ko.strings.remove_confirmation !== 'undefined' ? ko.strings.remove_confirmation : 'Are you sure you want to remove it?';
        if ( confirm( c ) ) {
          item.remove( data );
        }
        try {
          self._args.actions.remove_data( self, event, item, data )
        } catch ( e ) {
          self._args.callback( e, self );
        }
      };

      /**
       * Checks item in array
       * Template Helper Function
       *
       * @author potanin@UD
       */
      self.in_array = function ( item, data ) {
        // console.info( 'ko.view_model._show()', arguments );
        return ( data instanceof Array && data.indexOf( item ) != -1 );
      };

      /**
       * Renders Message in Navbar sent by SaaS.
       *
       * @author potanin@UD
       */
      self.navbar_notice = function ( data ) {
        var self = this;
        /* */
        data = ( typeof data === 'string' ? { message: data } : ( typeof data === 'object' ? data : {} ) );
        /* If message property is not defined at all, we do not proceed */
        if ( typeof data.message === 'undefined' ) return;

        var socket_status = jQuery( 'li#ud_saas_message' ), socket_object = jQuery( 'li#ud_saas_object' );

        /* Add Navbar Status Container */
        if ( !socket_status.length > 0 ) {
          jQuery( 'ul#wp-admin-bar-root-default.ab-top-menu' ).append( socket_status = jQuery( '<li id="ud_saas_message" class="ud_saas_message"></li>' ) );
        }

        if ( data.object ) {
          if ( !socket_object.length > 0 ) {
            jQuery( 'ul#wp-admin-bar-root-default.ab-top-menu' ).append( socket_object = jQuery( '<li id="ud_saas_object" class="ud_saas_object">[object]</li>' ) );
          }
          ;
          jQuery( socket_object ).unbind( 'click' );
          jQuery( socket_object ).bind( 'click', function () {
            ud.admin.pointer( socket_object, {
              'pointerClass': 'wp-pointer ud-pointer ud-saas-debug',
              'title': data.message,
              'pointerWidth': 600,
              'content': '<pre>' + JSON.stringify( data.object, undefined, 2 ) + '</pre>'
            } );
          } );
        }

        socket_status.fadeTo( 500, 1, function () {
          window.clearTimeout( self.timers.socket_status );
          socket_status.html( data.message ? data.message : '' );
          self.timers.socket_status = window.setTimeout( function () {
            socket_status.fadeTo( 3000, 0.5, function () {
              window.setTimeout( function () {
                socket_status.fadeTo( 3000, 0.1 );
              }, 5000 );
            } );
          }, data.fade_out ? data.fade_out : 10000 );
        } );

        return data.message;
      };

      /**
       * Wrapper for ko.applyBindings()
       *
       * Calls before ko.applyBindings() - self.pre_apply()
       * Calls after ko.applyBindings()  - init()
       *
       * @author peshkov@UD
       */
      self.apply = function () {
        // console.info( 'ko.view_model.vm().apply()' );

        var self = this;

        if ( self._is_timeout ) {

          // console.info( 'ko.view_model.vm().apply() - Timeout.' );
          return false;

        } else if ( !self._bind ) {

          // console.info( 'ko.view_model.vm().apply() - Halted.' );
          return self._args.callback( null, self );

        } else if ( this._applied ) {

          // console.info( 'ko.view_model.vm().apply() - Already applied.' );
          return self._args.callback( null, self );

        } else if ( !self._loaded ) {

          // console.info( 'ko.view_model.vm().apply() - Model was not loaded' );
          var error = typeof ko.strings.ko_model_failed ? ko.strings.ko_model_failed : 'Knockout Model was not loaded.';
          return self._args.callback( new Error( error ), self );

        } else {

          try {

            /* Special Handlers can be added here */
            try {
              self._args.actions.pre_apply( self )
            } catch ( e ) {
              self._args.callback( e, self );
            }

            ko.applyBindings( self, self.container.get( 0 ) );

            self._applied = true;

            self.container.removeClass( 'ud_ui_loading' ).addClass( 'ud_ui_applied' );

            /** Prevent Load Timeout Event */
            clearTimeout( self.timers.load_timeout );

            // console.info( 'ko.view_model.vm().apply() - Applied.' );

            /* Special Handlers can be added here */
            try {
              self._args.actions.init( self );
            } catch ( e ) {
              console.log( e );
              self._args.callback( e, self );
            }

            return self._args.callback( null, self );

          } catch ( error ) {

            return self._args.callback( error, self );

          }

        }

      }

      /**
       * Recursively parses model data and prepares it for MVVM view_model.
       * Internal function which called automatically on view_model initialization.
       * Should not be used manually!
       *
       * @author peshkov@UD
       */
      self._prepare_model_data = function ( r, d ) {
        // console.info( '_prepare_model_data', arguments );

        var self = this;
        var data = {};

        r = typeof r !== 'object' ? {} : r;
        d = typeof d !== 'object' ? self : d;

        for ( var i in r ) {

          switch ( i ) {

            case '_static':
              // console.info( '_static', r[i] );

              for ( var e in r[i] ) {
                /* Handler for incoming data. You need to add handler to 'model' argument */
                if ( d && typeof d[e] === 'function' ) {
                  data[e] = d[e]( r[i][e] );
                } else {
                  data[e] = r[i][e];
                }
              }
              break;

            case '_observable':
              // console.info( '_observable', r[i] );

              for ( var e in r[i] ) {
                /* Handler for incoming data. You need to add handler to 'model' argument */
                if ( d && typeof d[e] === 'function' ) {
                  data[e] = d[e]( r[i][e] );
                } else {
                  if ( !r[i][e] || typeof r[i][e].length === 'undefined' ) {
                    data[e] = ko.observable( r[i][e] );
                  } else {
                    if ( typeof r[i][e] === 'object' ) {

                      data[e] = ko.observableArray( r[i][e] );

                    } else {

                      data[e] = ko.observable( r[i][e] );

                    }
                  }
                }
              }
              break;

          /**
           * Contains additional interfaces added by hooks
           *
           * It's used instead of do_action.
           * You should add all additional data to the current interface by apply_filter,
           * which has to be called in model.
           *
           * To add 'some_action' hook to interface you should do the following steps:
           * View:
           * <div data-action_hook="some_action"></div>
           * Model:
           * r._action = { some_hook: [ '<div>HEllo</div>', '<span>WoRlD!</span>' ], some_other_hook: [ '' ], ... }
           * Result:
           * <div data-action_hook="some_action"><div>HEllo</div><span>WoRlD!</span></div>
           *
           * @author peshkov@UD
           */
            case '_action':
              self._actions = jQuery.extend( true, self._actions, r[i] );
              break;

          /**
           * SAAS interface
           * Used by SaaS. All data for SaaS emit should be added here.
           *
           * @author peshkov@UD
           */
            case '_saas':
              if ( typeof self.saas.path == 'string' ) {
                data[ self.saas.path ] = {};
                for ( var e in r[i] ) {
                  switch ( e ) {
                    case 'conditionals':
                      data[ self.saas.path ] = jQuery.extend( true, {}, data[ self.saas.path ], r[i][e] );
                      break;
                    case 'interface':
                    default:
                      data[ self.saas.path ] = jQuery.extend( true, {}, r[i][e], data[ self.saas.path ] );
                      break;
                  }
                }
                /* Convert values screwed up by PHP/MySQL/whatever */
                if ( typeof ud.utils === 'object' && typeof ud.utils.type_fix === 'function' ) {
                  data[ self.saas.path ] = ud.utils.type_fix( data[ self.saas.path ], { 'nullify': true } );
                }
                /* Interface Handler. */
                if ( d && typeof d[ self.saas.path ] === 'function' ) {
                  data[ self.saas.path ] = d[ self.saas.path ]( data[ self.saas.path ] );
                }
              }
              break;

          /**
           * Any other data.
           * Check it recursively.
           */
            default:
              /* Handler for incoming data. You need to add handler to 'model' argument */
              if ( d && typeof d[i] === 'function' ) {
                data[i] = d[i]( r[i] );
              } else {
                /**
                 * Determine if we need to check data recursively or just set value.
                 */
                if ( typeof r[i] === 'object' && typeof r[i].length === 'undefined' ) {
                  data[i] = self._prepare_model_data( r[i], ( typeof d[i] === 'object' ? d[i] : false ) );
                } else {
                  data[i] = r[i];
                }
              }
              break;
          }
        }

        return data;
      }

      /* Init callback on timeout */
      self.timers.load_timeout = setTimeout( function () {
        args.actions.timeout( self )
      }, args.timeout );

      /* All additional methods and elements for the current model can be added here */
      self = jQuery.extend( true, self, ( typeof self._args.instance === 'object' ? self._args.instance : {} ) );

      /**
       * Determine if we already have model or we need to get it from server.
       */
      if ( typeof self._args.model === 'string' ) {
        self._args.model_name = self._args.model;

        /** Get model data from server */
        jQuery.ajax( {
          url: ko.ajaxurl( 'get_model' ),
          data: {
            'args': jQuery.extend( true, {}, { 'model': self._args.model_name }, self._args.args )
          },
          dataType: 'json',
          type: 'POST',
          async: false,
          beforeSend: function ( xhr ) {
            try {
              self._args.actions.get_model( self )
            } catch ( e ) {
              self._args.callback( e, self );
            }
            xhr.overrideMimeType( 'application/json; charset=utf-8' );
          },
          complete: function ( jqXHR ) {
            var r = {};
            try {
              r = jQuery.parseJSON( jqXHR.responseText );
              if ( typeof r !== 'object' || jqXHR.responseText === '' ) {
                throw new Error( 'Ajax response is empty' );
              } else if ( jqXHR.status === 500 ) {
                throw new Error( 'Internal Server Error' );
              } else if ( jqXHR.statusText === 'timeout' ) {
                throw new Error( 'Server Timeout' );
              } else if ( typeof r.success == true ) {
                throw new Error( 'Could not get \'' + self._args.model + '\' model' );
              }
            } catch ( error ) {
              ko.log( 'AJAX Error: ' + ( error.message ? error.message : 'Unknown.' ) );
              self._args.model = r = false;
            }
            if ( r ) {
              self._args.model = r.model;
              self._args.model._name = self._args.model_name;
            }
          },
          error: function ( jqXHR, textStatus, errorThrown ) {
          }
        } );
      } else {
        if ( typeof self._args.model._name === 'undefined' ) {
          self._args.model._name = '';
        }
      }

      /** Determine if global model is _loaded. If not, we stop process. */
      if ( !self._args.model ) {
        return;
      } else {
        self._loaded = true;
      }

      /* Prepare data for MVVM view_model by parsing model's object */
      var prepared_data = self._prepare_model_data( self._args.model );

      /* Merge model Localization data with global Localization strings */
      prepared_data.strings = jQuery.extend( true, ko.strings, ( typeof prepared_data.strings != 'undefined' ) ? prepared_data.strings : {} );

      /* Merge default and prepared data */
      self = jQuery.extend( true, self, prepared_data );

      /**
       * Go through all hooks and add interfaces if exist.
       * See: case _action in self._prepare_model_data()
       */
      for ( var i in self._actions ) {
        if ( jQuery( '[data-action_hook="' + i + '"]', container ).length > 0 ) {
          for ( var a in self._actions[i] ) {
            if ( typeof self._actions[i][a] === 'string' ) {
              jQuery( '[data-action_hook="' + i + '"]', container ).append( self._actions[i][a] );
            } else {
              // Ignore it for now.
            }
          }
        }
      }

      /** SAAS LOGIC STARTS HERE NOW */

      /**
       * Ran after SaaS has trigger Screen Set Event, meaning it is ready
       *
       * @author potanin@UD
       */
      self.saas._set_screen = function ( e, args ) {
        ko.log( 'ko.view_model.vm()._saas_screen_set()', arguments );

        /**
         * If ignore is true we stop SaaS initialization.
         * It happens, if timeout is called and Saas is still not established.
         */
        if ( self.saas.ignore ) {
          return;
        }

        self.saas.connected = true;

        /**
         * Determine if view_model has saas data before add listeners
         */
        if ( typeof self[ self.saas.path ] === 'object' ) {
          /* Set _active flag to true. So it says that saas is ready */
          self.saas._active( true );
          /* Set update trigger as we already know session ID */
          self.saas.update_trigger = self.saas.id + '::update::' + self.saas.screen;

          /**
           * Wait for Interface Request
           */
          self.saas.scope.on( self.saas.id + '::request::_interface', function () {
            ko.log( 'ko.view_model.vm()._saas_screen_set(); Interface request received, responding.' );
            self.saas._merging( true );
            self.saas.scope.emit( self.saas.id + '::update::' + self.saas.screen, {
              'key': self.saas.path,
              'value': ko.toJS( self[ self.saas.path ] )
            } );
            self.saas._merging( false );
          } );

          /**
           * Monitor Update Triggers, and set values
           */
          self.saas.scope.on( self.saas.update_trigger, function ( data ) {
            // console.info( '_prepare_interface() -> ' + self.saas.update_trigger + ' (' + update.path + '.' + update.key + ')', arguments );

            var data = jQuery.extend( {
              'path': false,
              'key': false,
              'value': false,
              'update': true,
              'message': false
            }, data );

            var full_path = ( data.path && data.key ? ( data.path + '.' + data.key ) : false );

            /* Disable subscribe event */
            self.saas._merging( true );

            /* Update value */
            if ( data.update && full_path !== false ) {
              try {
                self.saas._observable_caller[ full_path ][ 'value' ]( data.value );
              } catch ( e ) {
                self._args.callback( new Error( 'Interface Path Not Found' ), self );
              }
            }

            /* Updates navbar notice if message exists */
            if ( typeof data.message === 'string' && data.message.length > 0 && self.saas.show_updates ) {
              self.navbar_notice( data.message );
            }

            /* Special Handlers can be added here */
            try {
              self._args.actions.saas_update_on( self, data )
            } catch ( e ) {
              self._args.callback( e, self );
            }

            /* Enable subscribe event */
            self.saas._merging( false );
          } );

          jQuery( document ).bind( self.saas.id + '::disconnected', function ( event, data ) {
            self.saas._active( false );
          } );

          self[ self.saas.path ] = self.saas._set_emit( self[ self.saas.path ] );
        }

        /** Call SaaS callback if needed when data is set and prepared */
        try {
          self._args.actions.saas_set_screen( self )
        } catch ( e ) {
          self._args.callback( e, self );
        }

        /* Set bind val passed to view_model */
        self._bind = self._args.bind;

        /* Now, when SaaS is ready we try to call apply again */
        self.apply();
      };

      /**
       * Observable data, pares and add Subscribers to all observable items that emit updates
       *
       * @param data
       * @param path
       * @author potanin@UD
       */
      self.saas._set_emit = function ( data, path ) {
        // console.info( '_prepare_interface()', arguments );

        /* Add emit on subscribe event */
        var subscribe = function ( object, key, path ) {
          self.saas._observable_caller[ path + '.' + key ] = { 'path': path, 'key': key, 'value': object };
          object.subscribe( function ( value ) {
            if ( !self.saas._merging() ) {
              try {
                var data = { 'key': key, 'value': value, 'path': path };
                /* Special Handlers can be added here */
                self._args.actions.saas_update_emit( self, data );
                self.saas.scope.emit( self.saas.update_trigger, data );
              } catch ( e ) {
                self._args.callback( e, self );
              }
            }
          }, self );
        };

        /* Recursively check data and set subscribe events */
        var subscribe_deep = function ( object, path ) {
          for ( var key in object ) {
            /* Ignore extra data */
            if ( key === '__ko_mapping__' ) {
              continue;
              /* Determine if element is observable */
            } else if ( ko.isObservable( object[key] ) ) {
              subscribe( object[key], key, path );
              /* Recursively check and subscribe object's data */
            } else if ( typeof object[key] === 'object' ) {
              subscribe_deep( object[key], path + '.' + key );
            }
          }
        }

        data = ko.mapping.fromJS( data );

        subscribe_deep( data, typeof path === 'string' ? path : self.saas.path );

        return data;
      };

      /* Determine if still have saas data as function and fix it */
      if ( typeof self[ self.saas.path ] === 'function' ) {
        self[ self.saas.path ] = self[ self.saas.path ]( {} );
      }

      /**
       * Now we try to connect to SaaS ( if saas.model is set )
       * If SaaS Screen is specified, we wait for screen to be ready, and then load Interface, if needed
       */
      if ( typeof self.saas.model === 'string' ) {
        // console.info( 'ko.view_model() - Binding SaaS MVVM Handlers' );

        /** Return Error if SaaS scope object is incorrect some reason */
        if ( typeof self.saas.scope.connect !== 'function' ) {
          self._bind = false;
          return self._args.callback( new Error( typeof ko.strings.saas_connection_fail !== 'undefined' ? ko.strings.saas_connection_fail : 'Application could not connect to SaaS.' ), self );
        }

        /* Enable/Disable Navbar SaaS Status and console Updates */
        self.saas.scope.show_updates = self.saas.show_updates;
        if ( typeof self.saas.scope.settings.log !== 'undefined' ) {
          if ( self.saas.debug_mode ) jQuery.extend( self.saas.scope.settings.log, { 'events': true, 'all_data': true } ); else jQuery.extend( self.saas.scope.settings.log, { 'events': false, 'all_data': false } );
        }

        /* Update|Fix SaaS instance */
        self.saas.scope.instance = jQuery.extend( {
          'api_key': false,
          'site_uid': false,
          'customer_key': false,
          'customer_name': false,
          'site_url': false,
          'home': false,
          'ajax': false,
          'ip': false,
          'site': false,
          'screen': false
        }, self.saas.scope.instance, typeof self.saas.instance === 'object' ? self.saas.instance : {} );

        /* Set additional specific SaaS data */
        jQuery.extend( self.saas, {
          // Socket Session ID
          'id': false,
          // Name of event which called on self[ self.saas.path ] element subscribe
          'update_trigger': false,
          // Used as a flag to disable subscribe while updating
          '_merging': ko.observable( false ),
          // Screen not set until SaaS says it is set
          '_active': ko.observable( null ),
          // The list of interface elements which should do saas.emit on subscribe
          '_observable_caller': {},
          // Check if we connected to SaaS
          'connected': false,
          //
          'ignore': false
        } );

        if ( typeof self.saas.screen === 'string' ) {

          /* We should do SaaS connection before bindings will be applied. */
          self._bind = false;

          /* Call self._saas_screen_set when socket is connected. */
          if ( self.saas.scope.id && !self.saas.force_new_connection ) {
            /* Looks like connection is already established, so just add bind */
            self.saas.id = self.saas.scope.id;
            jQuery( document ).one( self.saas.scope.id + '::update::screen_set::' + self.saas.screen, self.saas._set_screen );
          } else {
            /* Connection is not established yet ( or we force new connection ),
             * so we add event to ud::saas::connect, because we need to know connection ID. */
            jQuery( document ).one( 'ud::saas::connect', function ( event, socket ) {
              self.saas.id = socket.id;
              /* */
              jQuery( document ).one( socket.id + '::update::screen_set::' + self.saas.screen, self.saas._set_screen );
              /* */
              try {
                self._args.actions.saas_connect( self )
              } catch ( e ) {
                self._args.callback( e, self );
              }
              /* Prints all updates in navbar */
              jQuery( document ).bind( socket.id + '::update', function ( event, data ) {
                if ( self.saas.show_updates ) {
                  self.navbar_notice( data );
                }
              } );
              /* Prints 'SaaS Online' on SaaS ready */
              jQuery( document ).one( socket.id + '::init', function ( event, socket ) {
                if ( self.saas.show_updates ) {
                  self.navbar_notice( { 'message': ko.strings.connection_established ? ko.strings.connection_established : 'SaaS Online.' } );
                }
              } );
              /* Call saas_disconnect action and print 'SaaS Offline' on connection lost */
              jQuery( document ).one( socket.id + '::disconnected', function ( event, socket ) {
                if ( self.saas.show_updates ) {
                  self.navbar_notice( { 'message': ko.strings.connection_lost ? ko.strings.connection_lost : 'SaaS Offline.' } );
                }
                /* Do additional actions ( callback ) on disconnect */
                try {
                  self._args.actions.saas_disconnect( self )
                } catch ( e ) {
                  self._args.callback( e, self );
                }
              } );
            } );
          }

          /* Switch active screen ( SaaS instance screen ) */
          self.saas.scope.instance.screen = self.saas.screen;

        }

        /* Do connection to socket */
        self.saas.scope.connect( self.saas.model, { 'force new connection': self.saas.force_new_connection, 'secure': self.saas.secure } );

      } else {

        /* We should set saas variable to observable in any case. */
        if ( typeof self[ self.saas.path ] === 'object' ) {
          self[ self.saas.path ] = ko.mapping.fromJS( self[ self.saas.path ] );
        }

      }

    }

    /* Bind Knockout */
    vm = new vm( args, container );

    ko.log( vm );

    return vm.apply();

  }

} else {
  ko = { view_model: function () {
    return false;
  } }
}
