jQuery( function( $ ){

  $( 'body' ).on( 'change', '.artist-lineup-widget-image', function(){

    var image = $( this ).parent().siblings().find( '#artist-lineup-widget-selected-image' );

    image.attr( 'src', $( this ).val() );

  } );
} );