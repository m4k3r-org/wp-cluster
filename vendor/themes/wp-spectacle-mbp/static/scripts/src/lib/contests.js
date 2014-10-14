define(['jquery'], function( $) {

  return {

    init : function() {

      $('.contest .icon-downarrow, .contest .icon-uparrow' ).click(function(e) {

        e.preventDefault();

        var t = $(this);

        if ( t.hasClass('icon-downarrow'))
        {
          t.removeClass('icon-downarrow');
          t.addClass('icon-uparrow');

          $('.contest .mobile-invisible' ).show();

          $('html, body' ).animate({
            scrollTop: $('.contest .mobile-invisible:first' ).offset().top
          }, 1000);
        }
        else
        {
          t.removeClass('icon-uparrow');
          t.addClass('icon-downarrow');

          $('.contest .mobile-invisible' ).hide();
        }

      });


    }
  }

});