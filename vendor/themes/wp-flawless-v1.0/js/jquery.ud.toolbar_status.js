/* =========================================================
 * jquery-toolbar-status.js v1.0.0
 * http://usabilitydynamics.com
 * =========================================================
 * Copyright 2011 Usability Dynamics, Inc.
 *
 * Version 0.0.5
 *
 * Copyright (c) 2011 Usability Dynamics, Inc. (usabilitydynamics.com)
 * ========================================================= */

(function( jQuery ){


  /**
   * Displays a message in the toolbar.
   *
   * {missing detailed description}
   *
   * @author potanin@UD
   * @version 1.0.0
   */
  jQuery.fn.toolbar_message = function( message, settings ) {

    /* Set Settings */
    var settings = jQuery.extend({
      css_class: false,
      type: 'default',
      debug: false,
      color: false,
      hide: 10000,
      dim: 1000,
      fade_speed: 'slow',
      style: ' padding-left: 10px; '
    }, settings);

    /* Internal logging function */
    log = function(something, type) {

      if(!settings.debug) {
        return;
      }

      if(window.console && console.debug) {

        if (type == 'error') {
          console.error(something);
        } else {
          console.log(something);
        }

      }

    };

    /* Detect toolbar */
    if(jQuery("#wpadminbar").length) {
      settings.insert_after = '#wp-admin-bar-root-default';
      settings.element_type = 'span';
    } else if (jQuery("#wp-admin-bar").length) {
      settings.insert_after = '#flawless_buddypress_edit_layout';
      settings.element_type = 'li';
    }

    /* Make sure the element we're looking for exists */
    if(!jQuery(settings.insert_after).length) {
      return;
    }

    settings.html = '<' + settings.element_type + ' class="flawless_toolbar_status" type="' + settings.type + '" style="' + settings.style + ' " ><span class="flawless_toolbar_status_text">' + message + '</span></' + settings.element_type + '>';


    /* Remove current message if exists */
    if(jQuery('.flawless_toolbar_status').length) {
      jQuery('.flawless_toolbar_status').remove();
    }

    var element = jQuery(settings.html).insertAfter(settings.insert_after);

    /* Schedule removal */
    if(settings.hide) {
      setTimeout(function() {
        jQuery(element).fadeOut(settings.fade_speed, function() {
          jQuery(this).remove();
        });
      }, settings.hide);
    }
    
    if(settings.dim) {
      setTimeout(function() {
        jQuery(element).fadeTo(settings.fade_speed, 0.3);
      }, settings.dim);
    }

    /* Change font-color */
    if(settings.color) {
      jQuery('.flawless_toolbar_status_text', element).css('color', settings.color);
    }

    /* Add custom class  */
    if(settings.css_class) {
      jQuery(element).addClass(settings.css_class);
    }

    return element;

  };

})( jQuery );
