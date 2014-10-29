/**
 * Masonry layout for photos
 *
 * @class masonry
 */
define( ['components/masonry/dist/masonry.pkgd.min'], function( Masonry ){

  return {

    /**
     * Initialize the Masonry layout
     *
     * @method init
     * @param {String} grid The object class or ID string which contains the items
     * @returns {Masonry} A Masonry object
     */
    init: function( grid ){

      var instance = new Masonry( grid, {

        columnWidth: '.item',
        itemSelector: '.item',
        gutter: 0,
        isOriginLeft: true

      } );

      return instance;

    }
  }

} );