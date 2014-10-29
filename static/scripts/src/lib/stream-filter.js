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

        var is_selected = false;

        if ( t.hasClass( t.data( 'sel' ) ) )
        {
          is_selected = true;
        }

        var parent = t.parents( that.container );

        // Remove the classes from the other elements
        $( '.filter', parent ).each( function(){
          $( this ).removeClass( $( this ).data( 'sel' ) );
        } );


        if ( is_selected)
        {

          // Remove class from the clicked element
          t.removeClass( t.data( 'sel' ) );

          $( '.twitter-streams' ).show();
          $( '.instagram-streams' ).show();
          $( '.facebook-streams' ).show();
          $( '.youtube-streams' ).show();
        }
        else
        {

          // Add class to the clicked element
          t.addClass( t.data( 'sel' ) );

          switch( t.data( 'sel' ) ){
            case 'sel-twitter':
              $( '.twitter-streams' ).show();
              $( '.instagram-streams' ).hide();
              $( '.facebook-streams' ).hide();
              $( '.youtube-streams' ).hide();
              break;
            case 'sel-instagram':
              $( '.twitter-streams' ).hide();
              $( '.instagram-streams' ).show();
              $( '.facebook-streams' ).hide();
              $( '.youtube-streams' ).hide();
              break;
            case 'sel-facebook':
              $( '.twitter-streams' ).hide();
              $( '.instagram-streams' ).hide();
              $( '.facebook-streams' ).show();
              $( '.youtube-streams' ).hide();
              break;
            case 'sel-youtube':
              $( '.twitter-streams' ).hide();
              $( '.instagram-streams' ).hide();
              $( '.facebook-streams' ).hide();
              $( '.youtube-streams' ).show();
              break;

          }
        }



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