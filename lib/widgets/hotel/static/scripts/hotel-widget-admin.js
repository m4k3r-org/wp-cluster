jQuery( function( $ ){

  $( 'body' ).on( 'change', '.hotel-widget-image', function(){

    var image = $( this ).parent().siblings().find( '#hotel-widget-selected-image' );

    image.attr( 'src', $( this ).val() );

  } );
} );