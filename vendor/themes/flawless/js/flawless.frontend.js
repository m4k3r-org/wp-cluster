/**
 * Flawless - Global Frontend JavaScript
 *
 * @version 0.5.0
 * @since Flawless 0.2.3
 * @author Usability Dynamics, Inc.
 *
 */

/* Load Defaults */
var flawless = jQuery.extend( true, {
  fancybox_options : {
    'transitionIn' : 'elastic',
    'transitionOut' : 'elastic',
    'speedIn' : 600,
    'speedOut' : 200,
    'cyclic' : true,
    'overlayShow' : false
  }
}, typeof flawless == 'object' ? flawless : {} );

/**
 * Perform actions that require images to be fully loaded
 *
 * @since Flawless 0.3.2
 * @author Usability Dynamics, Inc.
 */
jQuery( window ).load( function() {
  flawless_resize_dom_elements();
  flawless.masonry();

} );

/**
 * Primary $.ready() function.
 *
 * @since Flawless 0.0.1
 * @author Usability Dynamics, Inc.
 *
 */
jQuery( document ).ready( function() {
  jQuery( document ).trigger( 'flawless::ready::initialize' );

  flawless.log( 'Flawless Global JS Loaded.' );

  jQuery( document ).trigger( 'flawless::ready::complete' );

  jQuery( '.flawless_placeholder' ).each( function() {

    var placeholder = this;

    placeholder.update_dimensions = function() {
      jQuery( this ).html( jQuery( this ).width() + 'px X ' + jQuery( this ).height() + 'px' );
    }

    placeholder.update_dimensions();

    jQuery( window ).resize( function() {
      placeholder.update_dimensions();
    } );

    jQuery( this ).click( function() {
      flawless.annoy_me( jQuery( this ) );
    } );

  } );

  jQuery( '.imr-learn-more' ).addClass( 'btn btn-danger' );

  flawless.comment_form();
  flawless_resize_dom_elements();

} );

/**
 * Ran on flawless::ready - giving child themes and other scripts the ability to modify global flawless object
 *
 * @since Flawless 0.3.5
 * @author Usability Dynamics, Inc.
 *
 */
jQuery( document ).bind( 'flawless::ready::complete', function() {

  jQuery( '.no-ajax' ).removeClass( 'no-ajax' );

  if( typeof _gaq === 'object' && typeof _gaq.push === 'function' ) {
    flawless.google_analytics = true;
    flawless.log( 'Google Analytics active - event tracking enabled.' );
  }

  /* Fix hidden ad_hoc menus */
  if( jQuery( '.flawless_ad_hoc_menu_parent' ).length ) {

    jQuery( '.flawless_ad_hoc_menu_parent' ).each( function() {

      /* Display this item, and it's parents, if it is hidden, but the parents are not */
      if( !jQuery( this ).is( ':visible' ) && jQuery( this ).parents().is( ':visible' ).length ) {
        jQuery( this ).parents().show();
      }

    } );
  }

  jQuery( 'form.search_format' ).submit( function() {

    if( typeof flawless.header !== 'object' ) {
      return true;
    }

    if( flawless.header.must_enter_search_term == 'true' && jQuery( '.search_input_field', this ).val() == '' ) {
      jQuery( '.search_input_field', this ).focus();
      return false;
    }

  } );

  jQuery( '.search_input_field.flawless_input_autogrow' ).focus( function() {

    if( !flawless.search_input_field_width ) {
      flawless.search_input_field_width = parseInt( jQuery( this ).width() )
    }

    if( typeof jQuery.prototype.animate !== 'function' ) {
      return;
    }

    var args = {
      wrapper_width : jQuery( this ).closest( '.search_inner_wrapper' ).width(),
      search_button_width : jQuery( this ).siblings( '.search_button ' ).outerWidth(),
      expanded_width : flawless.search_input_field_width + 100
    }

    /* Prevent expanded input width + search button from being wider than the wrapper */
    if( args.expanded_width > ( args.wrapper_width - args.search_button_width ) ) {
      args.expanded_width = args.wrapper_width - args.search_button_width - 25;
    }

    /* Increase width */
    if( jQuery( this ).width() == flawless.search_input_field_width && args.expanded_width > flawless.search_input_field_width ) {
      jQuery( this ).animate( { width : ( args.expanded_width ) + 'px' }, 500 );
    }

  } );

  jQuery( '.toggle_visual_debug' ).click( function() {
    flawless.toggle_visual_debug();
  } );

  jQuery( '.search_input_field' ).blur( function() {

    if( jQuery( this ).val() == "" ) {
      jQuery( this ).delay( 500 ).animate( { width : ( flawless.search_input_field_width ) + 'px' }, 500 );
    }

  } );

  /* Handle header dropdown menus */
  flawless_header_dropdown_menus();

  /* Enable Layout Editor */
  jQuery( '.flawless_edit_layout' ).click( function( e ) {

    e.preventDefault();

    if( typeof flawless_layout_editor == 'function' ) {
      flawless_layout_editor( this, e );
    }

  } );

  jQuery( document ).trigger( 'flawless::ui_refresh' );

  jQuery( 'a[data-toggle="tab"]' ).on( 'shown', function( e ) {
    jQuery( document ).trigger( 'flawless::ui_refresh' );
  } );

  if( typeof prettyPrint == 'function' ) {
    prettyPrint();
  }

} );

/**
 * Functions to be executed when DOM is ready or updated
 *
 * @since Flawless 0.3.2
 * @author Usability Dynamics, Inc.
 */
jQuery( document ).bind( 'flawless::ui_refresh', function() {
  flawless.log( 'Bound Function: flawless::ui_refresh' );

  /* Setup Helper Scripts (if they are disabled, they are not loaded */
  if( typeof jQuery.fn.lazyload == 'function' ) {
    jQuery( 'img.lazy' ).lazyload();
  }

  if( typeof jQuery.fn.form_helper == 'function' ) {
    jQuery( 'form' ).form_helper( {
      debug : flawless.developer_mode
    } );
  }

  if( typeof jQuery.fn.tooltip == 'function' ) {
    jQuery( '.webster' ).tooltip()
  }

  if( typeof jQuery.fn.placeholder == 'function' ) {
    jQuery( 'input[type=text],input[type=email],input[type=password],input[type=tel],textarea' ).placeholder();
  }

  /* Enable Fancybox, if function exists, for all links with fancybox_image class and gallery times */
  if( typeof jQuery.fn.fancybox == 'function' ) {
    jQuery( 'a.fancybox_image, a[href$="jpg"], a[href$="png"]' ).fancybox( flawless.fancybox_options );
  }

  /* Enable Popover Plugin */
  if( typeof jQuery.fn.popover == 'function' ) {
    jQuery( '[rel=popover]' ).popover();
  }

  /* Debug Expander */
  jQuery( '.flawless_toggable_debug' ).dblclick( function() {
    jQuery( this ).hasClass( 'flawless_debug_visible' ) ? jQuery( this ).removeClass( 'flawless_debug_visible' ) : jQuery( this ).addClass( 'flawless_debug_visible' );
  } );

  flawless.masonry();

} );

/**
 * Google Analytics Event Tracking
 *
 * @author potanin@UD
 */
jQuery( document ).bind( 'flawless::track_event', function( event, data ) {

  data = jQuery.extend( true, { category : '', action : '', label : '', value : 0, non_interfaction : false }, data );

  if( !flawless.google_analytics ) {
    flawless.log( 'Unable to track event, Google Analytics not found.' );
    return;
  }

  _gaq.push( ['_trackEvent', data.category, data.action, data.label, data.value, data.non_interfaction ] );

} )

/**
 * Apply Masonry Layout to Elements, if library exists
 *
 * @since Flawless 0.2.3
 * @author Usability Dynamics, Inc.
 */
flawless.masonry = function() {

  if( typeof jQuery.fn.masonry != 'function' ) {
    return false;
  }

  jQuery( '.listing-masonry' ).masonry( {
    itemSelector : '.cfct-block'
  } );

  jQuery( 'div.gallery' ).masonry( {
    itemSelector : '.gallery-item',
    isAnimated : true
  } );

}

/**
 * Progress Bar.
 *
 * @since Flawless 0.5.0
 * @author Usability Dynamics, Inc.
 */
flawless.progress_bar = function( args, onComplete ) {
  args = jQuery.extend( true, {
    wrapper : jQuery( '<div class="progress progress-striped active"></div>' ),
    bar : jQuery( '<div class="bar"></div>' ),
    interval_event : {},
    type : 'info',
    start : 0,
    current : 0,
    end : 100,
    increment : 5,
    frequency : 100
  }, args );

  args.wrapper.addClass( 'progress-' + args.type );
  args.wrapper.append( args.bar );

  args.interval_event = setInterval( function() {

    jQuery( args.bar ).width( ( args.current = args.current + args.increment ) + '%' );

    if( args.current >= 100 ) {

      clearInterval( args.interval_event )

      if( typeof jQuery.prototype.fadeTo === 'function' ) {
        args.wrapper.fadeTo( 1500, 0, function() { typeof onComplete === 'function' ? onComplete() : false; } );
      } else {
        args.wrapper.css( 'opacity', 0 );
        typeof onComplete === 'function' ? onComplete() : false;
      }
      args.wrapper.removeClass( 'active' );
    }

  }, args.frequency );

  return args.wrapper;

}

/**
 * Apply Masonry Layout to Elements, if library exists
 *
 * @since Flawless 0.2.3
 * @author Usability Dynamics, Inc.
 *
 */
flawless.toggle_visual_debug = function() {
  jQuery( 'body' ).toggleClass( 'flawless_visual_debug' );
}

/**
 * Apply Masonry Layout to Elements, if library exists
 *
 * @since Flawless 0.2.3
 * @author Usability Dynamics, Inc.
 *
 */
flawless.annoy_me = function( container, args ) {

  args = jQuery.extend( true, {
    element : jQuery( '<span style="border-radius:50%;width: 75px;height:75px;background-color:rgba(249, 250, 255, 0.28);position:absolute;" class="something"></span>' ),
    container : container ? container : jQuery( '.flawless_placeholder' )
  }, args );

  jQuery( args.container ).append( args.element );
  jQuery( args.container ).css( 'position', 'relative' );

  var makeNewPosition = function() {
    var nh = Math.floor( Math.random() * ( jQuery( args.container ).height() - 75 ) );
    var nw = Math.floor( Math.random() * ( jQuery( args.container ).width() - 75 ) );
    return [nh, nw];
  }

  var animateDiv = function() {
    var newq = makeNewPosition();
    jQuery( args.element ).animate( { top : newq[0], left : newq[1] }, function() { animateDiv(); } );
  };

  animateDiv();

};

/**
 * Binds AJAX functionality to all instances of form.flawless_comment_form
 *
 * @author potanin@UD
 */
if( typeof flawless.comment_form == 'undefined' ) {
  flawless.comment_form = function() {

    jQuery( 'div.comments_wrapper' ).each( function() {

      var args = {
        comments_wrapper : this,
        response : jQuery( '.wp_comment_form_container' ),
        comment_list : jQuery( 'ol.commentlist' ),
        comment_form : jQuery( 'form.flawless_comment_form' )
      }

      if( jQuery( '.response_container', args.comments_wrapper ).length ) {
        args.response_container = jQuery( '.response_container', args.comments_wrapper );
      } else {
        args.response_container = jQuery( '<p class="response_container"></p>' );
        jQuery( args.response_container ).insertBefore( 'p.form-submit', args.comments_wrapper );
      }

      args._append_comments = function( comment_html ) {
        jQuery( args.comment_list ).html( comment_html );
      }

      jQuery( '#cancel-comment-reply-link' ).live( 'click', function( e ) {
        e.preventDefault();
        jQuery( 'input#comment_parent ', args.response ).val( '' );
        jQuery( '#cancel-comment-reply-link' ).hide();
        jQuery( args.comments_wrapper ).append( args.response );

      } );

      jQuery( 'a.comment-reply-link' ).live( 'click', function( e ) {
        e.preventDefault();

        if( typeof addComment === 'object' && typeof addComment.moveForm === 'function' ) {
        }

        args.comment = jQuery( this ).closest( 'li.comment' );
        args.cancel_reply = jQuery( '#cancel-comment-reply-link', args.response );

        jQuery( 'input#comment_parent ', args.response ).val( jQuery( args.comment ).attr( 'data-comment_id' ) );

        jQuery( args.comment ).append( args.response );
        jQuery( args.cancel_reply ).show();

      } );

      jQuery( args.comment_form ).submit( function( e ) {
        e.preventDefault();

        if( jQuery( args.comment_form ).hasClass( 'form_processing' ) ) {
          return;
        }

        jQuery( args.comment_form ).addClass( 'form_processing' );
        args.button = jQuery( 'input[type=submit]', this );
        args.comment_textarea = jQuery( 'textarea[name=comment]', this );
        args.form_data = jQuery( args.comment_form ).serialize();

        jQuery.ajax( {
          url : flawless.ajax_url,
          type : 'post',
          dataType : 'json',
          data : {
            action : 'frontend_ajax_handler',
            the_action : 'comment_submit',
            form_data : args.form_data
          },
          success : function( data, textStatus, jqXHR ) {

            jQuery( args.comment_form ).removeClass( 'form_processing' );

            if( typeof data === 'object' && data.success ) {

              /* Move Comment Form to its position in case we are replying */
              jQuery( args.comments_wrapper ).append( args.response );

              jQuery( args.comment_textarea ).val( '' );

              jQuery( args.response_container ).html( '<p class="alert alert-success">' + data.message ? data.message : 'Could not add comment.</p>' );

              if( data.comment_count ) {
                jQuery( '.comment_count' ).text( data.comment_count );
              }

              args._append_comments( data.comment_html );

            } else {

              if( typeof data === 'object' && data.message != '' ) {
                var response = data.message;
              } else if( data != '' ) {
                var response = data;
              } else {
                var response = 'Could not add comment.';
              }

              jQuery( args.response_container ).html( '<p class="alert alert-error">' + response + '</p>' );

            }

          },
          error : function( jqXHR, textStatus, errorThrown ) {
            jQuery( args.comment_form ).removeClass( 'form_processing' );

            jQuery( args.response_container ).html( '<p class="alert alert-error">' + ( jqXHR.responseText ? jqXHR.responseText : 'An error occured.' ) + '</p>' );

          }
        } );

      } );

    } );

  }
}

/**
 * Add a message to DOM.
 *
 * Only first argument must be passed containing the text of message.
 * To set the type of message, pass secon argument as object with following 'type' options: warning, error, success, info (default is info)
 * The message will be inserted into .global_notice_wrapper, unless other specified.  If the specified container is not found, a container will be created automtaiclaly after <header>
 *
 * This function will add a "close" trigger if the Twitter Bootstrap Alert function exists.
 *
 * @author potanin@UD
 */
if( typeof flawless.add_notice == 'undefined' ) {
  flawless.add_notice = function( message, s ) {

    s = jQuery.extend( true, {
      type : 'info',
      heading : false,
      allow_dismissal : true,
      classes : {
        alert : 'alert'
      },
      hide : false,
      fade : false,
      wrapper : jQuery( '.primary_notice_container' ),
      remove_others : true
    }, s );

    /* If wrapper does not exist in DOM, we insert a new one */
    if( !jQuery( s.wrapper ).is( ':visible' ) && jQuery( '.content_container' ).length ) {
      s.wrapper = jQuery( '<div class="primary_notice_container container"></div>' ),
        jQuery( '.content_container' ).prepend( s.wrapper );
    }

    if( s.remove_others ) {
      jQuery( '.alert', s.wrapper ).remove();
    }

    /* If no message, we leave after we remove previous messages */
    if( message == '' ) {
      return;
    }

    /* Identify our message container. Add close option if alert function exists  */
    var element = jQuery( '<div class="' + s.classes.alert + ' ' + s.type + '" alert_type="' + s.type + '">' + message + '</div>' )

    if( typeof jQuery.fn.alert == 'function' && s.allow_dismissal ) {
      element.prepend( '<a href="#" class="close">&times;</a>' );
      element.attr( 'data-dismiss', 'alert' );
    }

    jQuery( s.wrapper ).append( element );

  }
}

/**
 * Enables Editor, called from WP Toolbar, or BuddyPress toolbar if it exists
 *
 */
function flawless_layout_editor( edit_button, e ) {
  var edit_button = edit_button;
  var flawless_modules = [];

  if( jQuery( edit_button ).data( 'disable_click' ) ) {
    return;
  }

  if( typeof jQuery.fn.frontend_editor != 'function' ) {
    return;
  }

  if( typeof jQuery.fn.toolbar_message == 'function' ) {
    jQuery.fn.toolbar_message( 'Layout editor enabled.' );
  } else {
    flawless.add_notice( 'Layout editor enabled.' );
  }

  if( !flawless.frontend_editor ) {

    /* Associate Frontend Editor with every element with .flawless_dynamic_area class */
    flawless.frontend_editor = jQuery( 'div.flawless_dynamic_area' ).frontend_editor( {
      settings : {
        max_width : flawless.max_width ? flawless.max_width : false,
        debug : flawless.developer_mode
      }
    } );

    /* Save Original Attribute if it has not been saved yet */
    if( !jQuery( edit_button ).attr( 'original_label' ) ) {
      jQuery( edit_button ).attr( 'original_label', jQuery( edit_button ).text() );
    }

    jQuery( edit_button ).text( 'Save Layout' );

  } else {

    jQuery( edit_button ).data( 'disable_click', true );
    jQuery( edit_button ).text( 'Saving...' );

    jQuery.ajax( {
      url : flawless.ajax_url,
      data : {
        action : 'flawless_action',
        styles : flawless.frontend_editor.styles,
        _wpnonce : flawless.nonce,
        the_action : 'save_front_end_layout'
      },
      success : function( data, textStatus, jqXHR ) {

        if( data.success ) {

          flawless.frontend_editor.disable();

          if( typeof jQuery.fn.toolbar_message == 'function' ) {
            jQuery.fn.toolbar_message( 'Layout saved.', { type : 'success' } );
          } else {
            flawless.add_notice( 'Layout editor enabled.', { type : 'success' } );
          }

          jQuery( edit_button ).text( jQuery( edit_button ).attr( 'original_label' ) );

          flawless.frontend_editor = false;

        } else {

          if( typeof jQuery.fn.toolbar_message == 'function' ) {
            jQuery.fn.toolbar_message( 'Error saving layout, no response from server.', { type : 'error', dim : false } );
          } else {
            flawless.add_notice( 'Error saving layout, no response from server.', { type : 'error' } );
          }

        }

        jQuery( edit_button ).data( 'disable_click', false );

      },
      dataType : "json"
    } );

  }
}

/**
 * Applies equalHeights to various elements.
 *
 * Ran twice, once on document.ready and then on windows.load to avoid getting stuck on external assets
 *
 */
function flawless_resize_dom_elements() {
  flawless.log( "Applying equalHeights()" );

  jQuery( '.content_wrapper.row-fluid' ).each( function() {
    //jQuery( ' > .cfct-block', this ).equalHeights();
  } );

  jQuery( '.row-fluid.equalize' ).each( function() {
    jQuery( ' > .cfct-block > .cfct-module ', this ).equalHeights();
  } );

}

/**
 * Handles header dropdown menus.
 *
 */
function flawless_header_dropdown_menus() {

  var all_tabs = jQuery( 'div.disbl div' ).length;
  var dropdown_wrapper = jQuery( ".flawless_header_dropdown_links" );
  var dropdown_section_wrapper = jQuery( ".flawless_header_expandable_sections" );
  var dropdown_sections = jQuery( ".flawless_header_expandable_sections .header_dropdown_div" );

  /* Reset sections after they are loaded to normal hidden settings */
  jQuery( dropdown_sections ).css( 'position', 'static' );
  jQuery( dropdown_sections ).css( 'left', '0' );
  jQuery( dropdown_sections ).hide();

  jQuery( 'ul.log_menu li a' ).click( function( e ) {

    var this_link = this;
    var open_section = jQuery( ".flawless_header_expandable_sections .header_dropdown_div:visible" );
    var open_section_id = jQuery( open_section ).attr( "id" );

    /* Do nothing if a regular link was clicked */
    if( jQuery( this_link ).attr( 'href' ) != '#' ) {
      return;
    } else {
      e.preventDefault();
    }

    var this_tab = jQuery( this_link ).closest( ".flawless_tab_wrapper" );
    var section_id = jQuery( this ).attr( 'section_id' );
    var this_section = jQuery( "#" + section_id, dropdown_section_wrapper );

    if( jQuery( this_section ).is( ":visible" ) ) {
      var this_section_open = true;
      //flawless.log( "this section is open" );
    } else {
      var this_section_open = false;
      //flawless.log( "this section is closed" );
    }

    /* If clicked section is already open, we close it */
    if( this_section_open && ( section_id == open_section_id ) ) {
      jQuery( this_section ).slideUp();
      //flawless.log( "closing this section" );
      return;
    }

    /* If a section is open, and we re switching sections, close open one first */
    if( open_section.length ) {
      jQuery( open_section ).slideUp( "fast", function() {

        /* Open new section */
        jQuery( this_section ).slideDown( "slow", function() {
          flawless_header_section_opened();
        } );

      } );
    } else {

      /* Open new section */
      jQuery( this_section ).slideDown( "slow", function() {
        flawless_header_section_opened();
      } );

    }

  } );

}

/**
 * Executed when a header dropdown section is opened.
 *
 */
function flawless_header_section_opened() {

  /* Render the Google Map is header location dropdown.  */
  if( jQuery( "li.header_contact_section" ).is( ":visible" ) && jQuery( "li.header_contact_section" ).height() > 0 ) {
    jQuery( "li.header_contact_section" ).equalHeights();
  }

  if( jQuery( "li.header_login_section" ).is( ":visible" ) && jQuery( "li.header_login_section" ).height() > 0 ) {
    jQuery( "li.header_login_section" ).equalHeights();
  }

}

/**
 * Validates e-mail address.
 *
 * Source: http://www.white-hat-web-design.co.uk/articles/js-validation.php
 *
 */
function flawless_email_validate( email ) {
  var reg = /^( [A-Za-z0-9_\-\.] )+\@( [A-Za-z0-9_\-\.] )+\.( [A-Za-z]{2,4})$/;
  if( reg.test( email ) == false ) {
    return false;
  }

  return true;
}



