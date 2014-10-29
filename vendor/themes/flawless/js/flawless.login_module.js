/* =========================================================
 * flawless-login-module.js
 * http://usabilitydynamics.com
 * =========================================================
 * Copyright 2011 Usability Dynamics, Inc.
 *
 * Version 0.0.1
 *
 * Copyright ( c ) 2011 Usability Dynamics, Inc. ( usabilitydynamics.com )
 * ========================================================= */

  var flawless = flawless ? flawless : {};



  /**
   * Renders notice.
   * @author potanin@UD
   */
  if( typeof flawless.toggle_password_reset != 'function' ) {
    flawless.toggle_password_reset = function( event, el ) {
      var wrap = jQuery( 'div.nav-collapse.pull-right ul' );
      el = jQuery( el );

      el.trigger( 'flawless::nav_forget_password' );

      jQuery( '.navbar_login_form .flawless_ajax_response', wrap ).hide();
      jQuery( '.navbar_login_form', wrap ).toggle();
      jQuery( '.navbar_reset_password_form', wrap ).toggle();

      if( jQuery( '.navbar_login_form', wrap ).is( ':visible' ) ) {
        jQuery( '.navbar_login_form input.user_login' ).focus();
      }

      if( jQuery( '.navbar_reset_password_form', wrap ).is( ':visible' ) ) {
        jQuery( '.navbar_reset_password_form input.user_login' ).focus();
        if( jQuery( '.navbar_reset_password_form input.user_login' ).val() === '' ) {
          jQuery( '.navbar_reset_password_form input.user_login' ).val( jQuery( '.navbar_login_form input.user_login' ).val() );
        }
      }
    }
  }


  /**
   * Renders notice.
   *
   * @todo Finish popover integration. - potanin@UD
   * @param string text. HTML
   * @param object el. Optional DOM element
   * @author peshkov@UD
   */
  if( typeof flawless.navbar_login_notice != 'function' ) {
    flawless.navbar_login_notice = function( text, container, type, doing ) {

      var primary_notice_container = jQuery( '.primary_notice_container' );
      type = typeof type === 'string' ? type : 'success';
      container = container ? container : jQuery( '.flawless_ajax_response' );

      if( !container.length ) {
        return;
      }

      flawless.navbar_login_notice.schedule_hide = function() {
        flawless.navbar_login_notice.hide = setTimeout( function() { container.fadeOut( 2000 ); }, 5000 );
      }

      flawless.navbar_login_notice.cancel_hide = function() {
        container.fadeIn( 100 )
        clearTimeout( flawless.navbar_login_notice.hide );
      }

      container.removeClass( 'label-important' ).removeClass( 'label-success' );
      container.show();
      container.html( text );

      switch ( type ) {

        case 'success':
          container.addClass( 'label-success' );

          /** Pause and then show the sign in form */
          if( doing === 'password_reset' ) {
            container.append( flawless.progress_bar( { type: 'success', frequency : 200 }, function() {
              flawless.toggle_password_reset();
              jQuery( '.flawless_navbar_form input[type="text"]' ).val( '' );
            } ));
          }

        break;

        default:

          container.addClass( 'label-important' );

          if( !jQuery( '.forget_password_link', container ).length ) {
            container.append( container.reset_trigger =  jQuery( '<a href="#" class="forget_password_link">' + lm_l10n.forget_password + '</a>' ) );

            jQuery( container.reset_trigger ).click( function() {
              jQuery( document ).trigger( 'flawless::track_event', { category: 'Navbar User Action', action: 'Forget Password Link', label: 'After Failure' } );
              flawless.toggle_password_reset();
            });

          }

        break;

      }

      jQuery( container ).hover( function() {
        flawless.navbar_login_notice.cancel_hide();
      }, function() {
        flawless.navbar_login_notice.schedule_hide();
      });

      if( !flawless.navbar_login_notice.hide ) {
        flawless.navbar_login_notice.schedule_hide();
      }

    }
  }


  jQuery( document ).ready( function() {

    /**
     * Switch log in / forget password forms in navbar.
     *
     * @author peshkov@UD
     */
    jQuery( 'a#nav_forget_password' ).click( function( event ) {
      event.preventDefault();
      jQuery( document ).trigger( 'flawless::track_event', { category: 'Navbar User Action', action: 'Forget Password Link', label: 'Before Failure' } );
      flawless.toggle_password_reset( this );
    });


    /**
     * 'Forget password' AJAX form submitting
     *
     * @author peshkov@UD
     */
    jQuery( 'form[name="flawless_password_reset_form"]' ).submit( function( e ) {
      e.preventDefault();
      jQuery( this ).trigger( 'flawless::reset_password_submit' );
      var nwrap = jQuery( '.flawless_ajax_response', this );

      /** Validate form data */
      if( jQuery( 'input[name="user_login"]', this ).val() === '' ) {
        flawless.navbar_login_notice( lm_l10n.enter_fields_properly, nwrap, 'error', 'password_reset' );
        return false;
      }

      jQuery.ajax({
        'url': flawless.ajax_url,
        'dataType': 'json',
        'type': 'POST',
        'data' : jQuery( this ).serialize(),
        'success': function( data, textStatus ) {

          if( !!data.success ) {
            jQuery( document ).trigger( 'flawless::track_event', { category: 'Navbar Password Reset', action: 'Fail', label: 'Unknown Failure' } );
            flawless.navbar_login_notice( lm_l10n.something_wrong, nwrap, 'error', 'password_reset' );
          }

          if( data.success === true ) {
            jQuery( document ).trigger( 'flawless::track_event', { category: 'Navbar Password Reset', action: 'Success' } );
            flawless.navbar_login_notice( lm_l10n.email_was_sent, nwrap, 'success' , 'password_reset' );
          } else {
            jQuery( document ).trigger( 'flawless::track_event', { category: 'Navbar Password Reset', action: 'Fail', label: data.error } );
            flawless.navbar_login_notice( data.error, nwrap, 'error' , 'password_reset' );
          }

        }

      });

    });


    /**
     * Logout AJAX functionality
     *
     * @author peshkov@UD
     */
    jQuery( 'a.f_ajax_logout_link' ).click( function( e ) {
      e.preventDefault();
      jQuery( this ).trigger( 'flawless::log_out' );
      /** Now do request */
      jQuery.ajax( {
        'url': flawless.ajax_url,
        'type': 'POST',
        'data' : {'action':'flawless_ajax_logout'},
        'complete': function( r, status ) {
          window.location.reload( true );
        }
      });
      return false;
    });


    /**
     * 'Log in' AJAX form submitting
     *
     * @author peshkov@UD
     */
    jQuery( 'form[name="flawless_login_form"]' ).submit( function( e ) {
      e.preventDefault();

      jQuery( document ).trigger( 'flawless::login_form_submit' );
      var nwrap = jQuery( '.flawless_ajax_response', this );

      /** Validate form data */
      if( jQuery( 'input[name="log"]', this ).val() === '' ) {
        return flawless.navbar_login_notice( lm_l10n.enter_login, nwrap, 'error', 'login' );
      } else if ( jQuery( 'input[name="pwd"]', this ).val() === '' ) {
        return flawless.navbar_login_notice( lm_l10n.enter_password, nwrap, 'error', 'login' );
      }

      /** Now do request */
      jQuery.ajax({
        'url': flawless.ajax_url,
        'dataType': 'json',
        'type': 'POST',
        'data' : jQuery( this ).serialize(),
        'success': function( data, textStatus ) {

          if( typeof data.success === 'undefined' ) {
            jQuery( document ).trigger( 'flawless::track_event', { category: 'Navbar Login', action: 'Failure', label: lm_l10n.something_wrong.error } );
            flawless.navbar_login_notice( lm_l10n.something_wrong, nwrap, 'error', 'login' );
          }

          if( data.success === true ) {
            jQuery( document ).trigger( 'flawless::track_event', { category: 'Navbar Login', action: 'Success' } );

            if( data.redirect_to ) {
              window.location.href = data.redirect_to;

            } else  {
              window.location.reload( true );
            }

          } else {
            jQuery( document ).trigger( 'flawless::track_event', { category: 'Navbar Login', action: 'Failure', label: data.error } );
            flawless.navbar_login_notice( data.error, nwrap, 'error' , 'login' );
          }

        }

      });

    });
  });

