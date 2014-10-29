jQuery( function( $ ){

  $( 'body' ).on( 'change', '.feature-item-widget-image', function(){

    var image = $( this ).parent().siblings().find( '#feature-item-widget-selected-image' );

    image.attr( 'src', $( this ).val() );

  } );
} );