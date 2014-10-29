jQuery( function( $ ){

  $( 'body' ).on( 'change', '.video-widget-image', function(){

    var image = $( this ).parent().siblings().find( '#video-widget-selected-image' );

    image.attr( 'src', $( this ).val() );

  } );
} );