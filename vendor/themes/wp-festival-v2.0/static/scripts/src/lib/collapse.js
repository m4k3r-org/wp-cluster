/**
 * Handle collapse module
 *
 */
define( ['jquery'], function( $ ){

  var collapseMod = {

    handle: function(){

      $( 'body' ).on( 'click', '.panel-heading', function(){

        id = $( this ).attr( 'href' );

        if( $( this ).hasClass( 'collapsed' ) ){
          $( this ).removeClass( 'collapsed' );

          $( id ).show();

          $( this ).find( 'a' ).addClass( 'up' );
          $( this ).find( 'a' ).removeClass( 'down' );

        } else{
          $( this ).addClass( 'collapsed' );

          $( id ).hide();

          $( this ).find( 'a' ).addClass( 'down' );
          $( this ).find( 'a' ).removeClass( 'up' );
        }

      } );
    }

  };

  return {

    init: function(){

      collapseMod.handle();

    }
  }

} );