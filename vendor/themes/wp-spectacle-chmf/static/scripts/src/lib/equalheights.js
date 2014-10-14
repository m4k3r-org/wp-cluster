/**
 * Compare the heights of specified elements and give the biggest height to each.
 *
 * @class equalize
 */
define( ['jquery'], function( $ ){

  return {

    /**
     * Give the max height to all the elements from the specified elements.
     *
     * @method equalize
     * @param {Object} elements jQuery object array of elements to compare the heights
     * @param {Integer} minimumWindowWidth Conditional to equalize heights if the view-port hits the minimum width
     * @return void
     */
    equalize: function( elements, minimumWindowWidth ){

      $( window ).on( 'resizeEnd', function( e ){

        var maxHeight = 0;

        if( (minimumWindowWidth != undefined) && (document.documentElement.clientWidth <= parseInt( minimumWindowWidth )) ){
          if( elements.data( 'equalized' ) ){
            elements.css( {
              'min-height': 'inherit'
            } );
          }

          return false;
        }

        elements.each( function(){

          var height = $( this ).outerHeight( true );

          if( height > maxHeight ){
            maxHeight = height;
          }

        } );

        elements.css( {
          'min-height': maxHeight + 'px'
        } );

        elements.data( 'equalized', true );
      } );

      $( window ).trigger( 'resize' );

    }
  }

} );
