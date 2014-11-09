/**
 * Admin Organizers page
 *
 */
( function( $, i ) {

  /**
   *
   */
  function usersFormatResult( data ) {
      var markup = "<table class='data-result'><tr>";
      markup += "<td class='data-info'>";
      markup += "<span class='data-title'>" + data.title + "</span>";
      markup += " <span class='data-login'>( " + data.login + " )</span>";
      markup += "</td></tr></table>";
      return markup;
  }

  /**
   *
   *
   */
  function userFormatSelection( data ) {
      return data.title;
  }
  
  /**
   *
   */
  function add_select( e ) {
    var wrapper = e.parents( 'ul' ),
        html = '';
    html += '<li class="related-user-item">';
    html += '<input type="hidden" class="select2" name="organizers[' + e.data( 'organizer_id' ) + '][related_users][]" data-title="" data-id="" data-login="" value=""/>';
    html += '<a href="javascript:;" class="action remove-select2"><span class="eb-icon eb-minus-icon"></span></a>';
    html += '</li>';
    
    wrapper.append( html );
    init_select( $( wrapper.find( 'li:last-child .select2' ) ) );
  }
  
  /**
   *
   */
  function remove_select( e ) {
    e.parents( 'li' ).remove();
  }
  
  /**
   *
   */
  function init_select( e ) {
  
    var settings = {
      placeholder: i.l10n.select_user,
      minimumInputLength: 3,
      ajax: {
        url: i.ajax_url,
        dataType: 'json',
        quietMillis: 100,
        data: function ( term, page ) { // page is the one-based page number tracked by Select2
          return {
            q: term, //search term
            page_limit: 10, // page size
            page: page, // page number
            action: "eventbrite_user"
          };
        },
        results: function ( data, page ) {
          var more = (page * 10) < data.total; // whether or not there are more results available
          // notice we return the value of more so Select2 knows if more results can be loaded
          return {results: data.users, more: more};
        }
      },
      formatResult: usersFormatResult, // omitted for brevity, see the source of this page
      formatSelection: userFormatSelection, // omitted for brevity, see the source of this page
      dropdownCssClass: "bigdrop", // apply css that makes the dropdown taller
      escapeMarkup: function (m) { return m; } // we do not want to escape markup since we are displaying html in results
    };
    
    if( e.data( 'id' ) !== '' && e.data( 'title' ) !== '' && e.data( 'login' ) !== '' ) {
      $.extend( settings, {
        initSelection : function ( element, callback ) {
          var data = { 
            id: $( element ).data( 'id' ), 
            title: $( element ).data( 'title' ), 
            login: $( element ).data( 'login' ) 
          };
          callback( data );
        }
      } );
    }
  
    $( e ).select2( settings );
    
    //** Add action hooks */
    var action = $( e ).parents( 'li' ).find( ".action" );
    if ( action.hasClass( 'add-select2' ) ) {
      action.click( function() { add_select( $( this ) ); return false; } );
    } else if ( action.hasClass( 'remove-select2' ) ) {
      action.click( function() { remove_select( $( this ) ); return false; } );
    }
    action.show();
    
  }
  
  /**
   *
   */
  $( document ).ready( function(){
  
    //** */
    $( ".select2" ).each( function( i, e ) {
      init_select( $( e ) );
    } );
  
  } );
  
} )( jQuery, _wp_eventbrite );


