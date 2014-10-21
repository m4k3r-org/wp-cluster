if( typeof JS == 'undefined' ){
  var JS = {};
}

JS.namespace = function( srcName ){
  var arg = arguments, name = null, obj = null, i;
  name = srcName.split( '.' );
  obj = JS;

  for( i = 0; i < name.length; i++ ){
    obj[name[i]] = obj[name[i]] || {};
    obj = obj[name[i]];
  }

  return obj;
}

JS.namespace( 'winner' );
JS.winner = {

  init: function(){
    this.initMultiUrls();
  },

  initMultiUrls: function(){
    this.urlsHideShowAdd();
    this.urlsHideShowRemove();

    this.eventUrlsAddRow();
    this.eventUrlsRemoveRow();

  },

  eventUrlsAddRow: function(){
    var that = this;

    jQuery( 'body' ).on( 'click', '.urls-add', function( e ){

      var tr = jQuery( this ).parent().closest( 'table' ).siblings( '.urls-clone' ).find( 'tr' ).clone();

      jQuery( this ).parent().closest( '.urls-multi' ).append( tr );

      that.urlsHideShowAdd();
      that.urlsHideShowRemove();

      e.preventDefault();

    } );
  },

  eventUrlsRemoveRow: function(){
    var that = this;

    jQuery( 'body' ).on( 'click', '.urls-remove', function( e ){

      jQuery( this ).parents( 'tr' ).remove();

      that.urlsHideShowAdd();
      that.urlsHideShowRemove();

      e.preventDefault();
    } );
  },

  urlsHideShowAdd: function(){
    jQuery( "#urls-multi .data-row:last .td-add a" ).show();
  },

  urlsHideShowRemove: function(){
    if( jQuery( '#urls-multi .data-row' ).length > 1 ){
      jQuery( '#urls-multi .td-remove a' ).show();
    } else{
      jQuery( '#urls-multi .td-remove a' ).hide();
    }
  }
}

jQuery( function(){

  JS.winner.init();

} );