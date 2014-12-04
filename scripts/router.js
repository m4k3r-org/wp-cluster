/**
 * This file contains all of our routes throughout the application, and handled our loading process
 */
define(
  [
    'require',
    'global',
    'backbone',
    'lodash',
    'jquery',
    'knockout'
  ],
  function( require, _ddp, Backbone, _, $, ko ){
    return Backbone.Router.extend( {
      routes: {
        "worldwide/events": function(){
          this.defaultWithController.call( this, 'worldwide/events', arguments );
        },
        "worldwide/event/:id": function(){
          this.defaultWithController.call( this, 'worldwide/event', arguments );
        },
        "worldwide/news": function(){
          this.defaultWithController.call( this, 'worldwide/news', [ 'featured' ] );
        },
        "worldwide/news/all": function(){
          this.defaultWithController.call( this, 'worldwide/news', [ 'all' ] );
        },
        "worldwide/news/:id": function(){
          this.defaultWithController.call( this, 'worldwide/newsSingle', arguments );
        },
        "worldwide/festivals": function(){
          this.defaultWithController.call( this, 'worldwide/festivals', arguments );
        },
        "festival/tickets": function(){
          this.default( 'festival/tickets' );
        },
        "festival/:id": function(){
          this.defaultWithController.call( this, 'festival/main', arguments );
        },
        "festival/:id/news": function(){
          this.defaultWithController.call( this, 'festival/news', [ arguments[ 0 ], 'featured' ] );
        },
        "festival/:id/news/all": function(){
          this.defaultWithController.call( this, 'festival/news', [ arguments[ 0 ], 'all' ] );
        },
        "festival/:festival_id/news/:_id": function(){
          this.defaultWithController.call( this, 'festival/newsSingle', arguments );
        },
        "festival/:id/menu": function(){
          this.default( 'festival/menu' );
        },
        "*default": "default"
      },
      /**
       * This is our default path, which simply tries to load a module from within the 'pages' directory
       */
      default: function( path ){
        this.currentPath = path;
        /** If the page is null, we're loading splash */
        if( _.isNull( path ) ){
          path = 'splash';
        }
        /** Load the template and insert it into the DOM (this is the only time we'll do this */
        require( [ 'text!templates/' + path + '.html', 'viewModels/' + path ], function( template, viewModel ){
          /** Build the new div */
          var $page = $( '<div>' ).html( template );
          /** Setup the observable */
          ko.applyBindings( _.isFunction( viewModel ) ? new viewModel() : viewModel, $page[ 0 ] );
          /** Now, slide it in */
          _ddp.slidePage( $page );
        } );
      },
      /**
       * This is our default function that calls controller based routes, it typically just inits the controller
       * object, passing it the arguments that come into the function 1:1
       *
       * @param string path The path to the controller we're going to use (i.e. 'worldwide/event')
       * @param object args The arguments to pass to the controller, based on the path routes array
       */
      defaultWithController: function( path, args ){
        this.currentPath = path;
        require( [ 'controller/' + path ], function( Controller ){
          /** We need to call the passed arguments with 'new', so we use this hack to do so */
          this.createControllerWithArgs( Controller, args );
        }.bind( this ) );
      },
      /**
       * This function is used to create a 'new' instance of an object, without having to use the 'new' keyword,
       * this is a hack so we can use 1 route for all of our Controller based pages
       *
       * @param object ControllerObject An instance of the controller we want to create
       * @param object|null args The arguments we want to pass to the constructor
       */
      createControllerWithArgs: function( ControllerObject, args ){
        var F = function( localArgs ){
          return ControllerObject.apply( this, localArgs );
        };
        F.prototype = ControllerObject.prototype;
        /** Return the object */
        return new F( args );
      }
    } );
  }
);