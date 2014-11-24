/**
 * Stream Filter implementation
 *
 * @class streamfilter
 */
define( ['jquery'], function( $ ){

  var streamFilter = {

    container: null,

    /**
     * Select the filter button and add the data-sel attribute as a class.
     *
     * @method selectFilter
     */
    selectFilter: function(){
      var that = this;

      $( this.container ).on( 'click', '.filter', function( e ){

        e.preventDefault();

        var t = $( this );
        var parent = t.parents( that.container );

        // Remove the classes from the other elements
        $( '.filter', parent ).each( function(){
          $( this ).removeClass( $( this ).data( 'sel' ) );
        } );

        // Add class to the clicked element
        t.addClass( t.data( 'sel' ) );

      } );
    }

  };

  return {

    /**
     * Bootstrap the module.
     * It will look through the specified container for anchor elements and their data attributes.
     *
     * @method init
     * @param {Object} container Parent element where the filter anchors are
     */
    init: function( container ){

      streamFilter.container = container;

      streamFilter.selectFilter();

    }
  }
} );