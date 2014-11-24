jQuery(document).ready(function() {
  var events_page = 1;
  jQuery('.load-more-events').on('click', function(){

    var data = jQuery(this).data();
    data._paged = ++events_page;

    jQuery.ajax('/api/nc/load_more_events/', {
      type: 'GET',
      dataType: 'json',
      data: data,
      success: function(msg){
        if ( msg.status == 'error' ) {
          document.location = data.archive_url;
          return;
        }
        jQuery('#upcomingEvents article.listing-event:last').after(msg.html);
      }
    });

  });
});