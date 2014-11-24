jQuery( function( $ ){

  $( 'body' ).on( 'change', '.organizer-item-widget-image', function(){

    var image = $( this ).parent().siblings().find( '#organizer-item-widget-selected-image' );

    image.attr( 'src', $( this ).val() );

  } );
} );