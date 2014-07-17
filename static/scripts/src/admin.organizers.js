/**
 * Admin Organizers page
 *
 */
( function( $, i ) {

  function usersFormatResult( data ) {
      var markup = "<table class='data-result'><tr>";
      markup += "<td class='data-info'>";
      markup += "<span class='data-title'>" + data.title + "</span>";
      markup += " <span class='data-login'>( " + data.login + " )</span>";
      markup += "</td></tr></table>";
      return markup;
  }

  function userFormatSelection( data ) {
      return data.title;
  }

  $( document ).ready( function(){
    
    $( ".select2" ).select2( {
      placeholder: i.l10n.select_user,
      minimumInputLength: 3,
      initSelection : function ( element, callback ) {
        var data = { 
          id: $( element ).data( 'id' ), 
          title: $( element ).data( 'title' ), 
          login: $( element ).data( 'login' ) 
        };
        callback( data );
      },
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
    } );
  
  } );
  
} )( jQuery, _wp_eventbrite );


