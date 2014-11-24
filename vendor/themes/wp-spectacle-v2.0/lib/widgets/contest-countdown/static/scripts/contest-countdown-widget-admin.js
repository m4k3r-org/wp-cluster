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

JS.namespace( 'countdown' );
JS.countdown = {

  init: function(){
    this.initMultiDates();
  },

  initMultiDates: function(){
    this.datesHideShowAdd();
    this.datesHideShowRemove();

    this.eventDatesAddRow();
    this.eventDatesRemoveRow();

  },

  eventDatesAddRow: function(){
    var that = this;

    jQuery( 'body' ).on( 'click', '.dates-add', function( e ){

      var tr = jQuery( this ).parent().closest( 'table' ).siblings( '.dates-clone' ).find( 'tr' ).clone();

      jQuery( this ).parent().closest( '.dates-multi' ).append( tr );

      jQuery( this ).parent().closest( '.dates-multi' ).find('tr:last').find( '.datepicker' ).attr('id', Math.floor((Math.random() * 100000000000) + 1) );

      jQuery( this ).parent().closest( '.dates-multi' ).find('tr:last').find( '.datepicker' ).removeClass( 'hasDatepicker' ).removeData( 'datepicker' ).unbind().datepicker({
        dateFormat : 'yy-mm-dd'
      });

      that.datesHideShowAdd();
      that.datesHideShowRemove();

      e.preventDefault();

    } );
  },

  eventDatesRemoveRow: function(){
    var that = this;

    jQuery( 'body' ).on( 'click', '.dates-remove', function( e ){

      jQuery( this ).parents( 'tr' ).remove();

      that.datesHideShowAdd();
      that.datesHideShowRemove();

      e.preventDefault();
    } );
  },

  datesHideShowAdd: function(){
    //   jQuery('#dates-multi .td-add a').hide();
    jQuery( "#dates-multi .data-row:last .td-add a" ).show();

    //   console.log(jQuery("#dates-multi .data-row:last .td-add a"));
  },

  datesHideShowRemove: function(){
    if( jQuery( '#dates-multi .data-row' ).length > 1 ){
      jQuery( '#dates-multi .td-remove a' ).show();
    } else{
      jQuery( '#dates-multi .td-remove a' ).hide();
    }
  }
}

jQuery( function(){

  JS.countdown.init();

} );