/**
 * Contact Form
 *
 * @class contact
 */
define(['jquery'], function( $ ){

  var contact = {

    /**
     * Add placeholders for contact form
     *
     * @method eventOpen
     */
    addPlaceholders: function(){

      $('.ginput_container input' ).each( function(){

        placeholder =  $(this ).parent().siblings('label' ).text();

        $(this).attr('placeholder', placeholder);
      });

      $('.ginput_container textarea' ).each( function(){

        placeholder =  $(this ).parent().siblings('label' ).text();

        $(this).attr('placeholder', placeholder);
      });

    }

  };

  return {

    /**
     * Bootstrap the contact form
     *
     * @method init
     */
    init : function()
    {
      contact.addPlaceholders();
    }

  }

});