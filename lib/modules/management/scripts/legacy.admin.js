/**
 * WP-Property Global Admin Scripts
 *
 * Include on all back-end pages, extends UD global.
 *
 * @author team@UD
 * @version 1.0
 */

/* Extend WPP Global by Merging in WPP, which in turn contains UD and UD Global */
var wpp = jQuery.extend( true, wpp, {

  /* Central location for timers */
  timers: {},

  /* Placeholder */
  strings: {},

  /**
   * Enable "Feedback" tab.
   *
   * Enabled when SaaS connection exists - allows users to submit suggestions and bug reports from admin UI.
   * Requires SaaS connection.
   *
   * @since 2.0
   * @author potanin@UD
   */
  enable_feedback: function () {
    wpp.log( 'wpp.admin.enable_feedback()', arguments );

  },

  /**
   * Add WP Pointer
   *
   * Actions: toggle, close, open, destroy, sendToTop
   *
   * @since 2.0
   * @author potanin@UD
   */
  pointer: function ( target, args, action ) {
    wpp.log( 'wpp.admin.pointer()', arguments );

    args = jQuery.extend( true, {}, {
      pointerClass: 'wp-pointer ud-pointer',
      pointerWidth: 400,
      title: '',
      content: '',
      position: { 'edge': 'top', 'align': 'left' }
    }, args );

    if ( typeof jQuery.prototype.pointer !== 'function' ) {
      return false;
    }

    args.content = '<h3>' + args.title + '</h3>' + args.content;

    var this_pointer = jQuery( target ).pointer( args );

    this_pointer.pointer( action ? action : 'open' );

    wpp.admin[ '_pointers' ] = wpp.admin[ '_pointers' ] ? wpp.admin[ '_pointers' ] : [];

    wpp.admin[ '_pointers' ].push( this_pointer );

    return this_pointer;

  },

  /**
   * Display a full-width notice.
   *
   */
  core_notice: function ( message, type, fadeout ) {
    wpp.log( 'wpp.admin.core_notice()', arguments );

    /* If there is no area for message yet - place it. */
    if ( jQuery( '.wpp_core_notice' ).length === 0 ) {
      jQuery( '<div class="wpp_core_notice"></div>' ).insertBefore( 'div.wrap' );
    }

    /* Hide message area if message is empty */
    if ( typeof message === 'undefined' || message === '' ) {
      jQuery( '.wpp_core_notice' ).fadeOut( 3000, function () {
        jQuery( this ).hide( 500, function () {
          jQuery( this ).html( '' ).removeClass( type )
        } );
      } );
      return;
    }

    if ( typeof type === 'undefined' || type === false ) type = 'wpp_updated';

    /* Show message */
    jQuery( '.wpp_core_notice' ).addClass( type ).empty().show().html( message );

    /* FadeOut if needed */
    if ( typeof fadeout !== 'undefined' && fadeout ) {
      setTimeout( function () {
        jQuery( '.wpp_core_notice' ).fadeOut( 5000, function () {
          jQuery( this ).hide( 500, function () {
            jQuery( this ).html( '' ).removeClass( type )
          } );
        } );
      }, 5000 );
    }
  }

} );

/**
 * (!!WIP) Open / Get Contextual Help Section
 *
 * wpp.toggle_contextual( 'wpp::single_listing' )
 *
 * @todo Need to enable the help-tab-content when there is one.
 * @author potanin@UD
 * @param element
 */
wpp.toggle_contextual = function ( id, args ) {
  wpp.log( 'wpp.toggle_contextual()', arguments );

  args = jQuery.extend( true, {
    'target': jQuery( '[data-contextual-id="' + id + '"]' ),
    'event': null
  }, args );

  if ( args.target.length < 1 ) {
    return false;
  }

  var screen_meta = jQuery( "#screen-meta" );
  var panel = jQuery( "#contextual-help-wrap" );
  var help_link = jQuery( "#contextual-help-link" );

  /* If Already Open - we close Help */
  if ( help_link.hasClass( 'screen-meta-active' ) ) {

    help_link.removeClass( 'screen-meta-active' );

    panel.slideUp( 'fast', function () {
      panel.hide();
      screen_meta.hide();
      jQuery( '.screen-meta-toggle' ).css( 'visibility', '' );
    } );

    if ( args.target ) {
      args.target.removeClass( 'wpp_contextual_highlight' );
    }

    jQuery( document ).trigger( 'contextual-help-link::toggle' );

    return args.target.text();
  }

  /* If not open - we open help and maybe scroll to something */
  if ( !help_link.hasClass( 'screen-meta-active' ) ) {

    help_link.addClass( 'screen-meta-active' );

    if ( args.target ) {
      args.target.addClass( 'wpp_contextual_highlight' );
    }

    panel.slideDown( 'fast', function () {
      panel.show();
      screen_meta.show();

      if ( args.target ) {
        jQuery( 'html, body' ).animate( { scrollTop: args.target.offset().top }, 1000 );
      }

    } );

  }

  return args.target.text();

}

/**
 * (WIP) Render a Modal Message on Fatal Frontend (JS/SaaS/View Model) Errors
 *
 * Hopefully never happens, but must catch potential errors and call this function so user has some idea of what went wrong.
 * Will try to submit error report via socket.
 *
 * @todo Should display notice as a modal window. - potanin@UD 10/15/12
 * @since 2.0
 * @author potanin@UD
 */
wpp.fatal_notice = function ( args, callback ) {
  wpp.log( 'wpp.fatal_notice()', arguments );

  args = jQuery.extend( {
    error: '',
    submit: false,
    target: '.wrap',
    callback: false
  }, ( args instanceof Error || typeof args !== 'object' ) ? { error: args } : args );

  var target = typeof args.target === 'object' ? args.target : jQuery( args.target ), error = ( args.error instanceof Error ? args.error : new Error( 'Fatal UI Error Occured: ' + args.error ) ), notice = jQuery( '<div class="wpxi_contextual wpp_large_guide wpp_fatal"><p><strong>Sorry!</strong></p><p>' + ( error.message ) + '</p></div>' );

  if ( target.length > 0 ) target.html( notice ).show();

  if ( wpp.saas && wpp.saas.callback ) {
    wpp.saas.callback( 'fatal_error', args.submit, function ( error, response ) {
      if ( response.success ) {
        jQuery( notice ).append( '<p>' + response.message + '</p>' );
      } else {
        jQuery( notice ).append( '<p>An error report could not be submitted. Please submit a help request at UsabilityDynamics.com.</p>' );
      }
      if ( typeof args.callback === 'function' ) {
        args.callback( error, response );
      }
    } );
  } else {
    jQuery( notice ).append( '<p>An error report could not be submitted. Please submit a help request at UsabilityDynamics.com.</p>' );
  }

};

/**
 * Enable tabbed UI on a DOM element
 *
 */
wpp.tabbed_ui = function ( element, _args ) {
  wpp.log( 'wpp.tabbed_ui()', arguments );

  /* Ensure all required jQuery libraries are loaded */
  if ( typeof ko !== 'object' || typeof jQuery.prototype.tabs !== 'function' || typeof jQuery.prototype.sortable !== 'function' || typeof jQuery.prototype.draggable !== 'function' || typeof jQuery.prototype.droppable !== 'function' ) {
    return;
  }

  var el = jQuery( element );

  /* Load args from DOM attributes */
  var args = jQuery.extend( true, {
    'settings': el.attr( 'data-settings' )
  }, typeof _args === 'object' ? _args : {} );

  /* Load settings dynamically from variable */
  var settings = ( typeof window[ args.settings ] === 'object' ) ? window[ args.settings ] : {};

  var property_types = [];

  jQuery( element ).tabs( {
    //panelTemplate: "<li></li>",
    //tabTemplate: ""
  } );

};

/**
 * Adds Standard Attribute and Classification auto completion to a row.
 *
 * When a value is selected, it is added to the closest hidden input field.
 *
 * @author potanin@UD
 */
wpp.attribute_autocompletion = function ( element ) {
  wpp.log( 'wpp.settings.add_attribute_autocompletion', arguments );

  if ( typeof wpp.settings._attribute_feed !== 'object' ) {
    return false;
  }

  if ( !jQuery( element ).length ) {
    return false;
  }

  var _parent = jQuery( element ).closest( 'li' );
  var _row = jQuery( element ).closest( 'tr.wpp_dynamic_table_row' );
  var _label_field = jQuery( 'input.wpp_attribute_label', _row );

  jQuery( element ).autocomplete( {
    minLength: 0,
    appendTo: _parent,
    source: wpp.settings._attribute_feed,
    focus: function ( event, ui ) {
      return false;
    }, select: function ( event, ui ) {
    }
  } ).data( 'autocomplete' )._renderItem = function ( ul, item ) {
    return jQuery( '<li class="wpp_result_item"></li>' ).data( "item.autocomplete", item ).append( '<a><span class="wpp_label">' + item.label + '</span>' + ( item.description ? '<span class="wpp_description">' + item.description + "</span>" : '' ) + '</a>' ).appendTo( ul );
  };

  /* On value change, add the actual slug to the hidden input field for saving */
  jQuery( element ).bind( 'autocompleteselect', function ( event, ui ) {
    jQuery( 'input.attribute_classification_slug', _parent ).val( ui.item.slug );
  } );

  /* Render list of all items when clicked. */
  jQuery( element ).click( function () {
    jQuery( this ).autocomplete( "search", "" );
  } );

  /* Monitor changes to Label field */
  jQuery( _label_field ).change( function () {
    wpp.log( 'Label updated...' );
  } );

};

/**
 * Create slug from title
 * @param slug
 * @return
 */
wpp.create_slug = function ( slug ) {
  slug = slug.replace( /[^a-zA-Z0-9_\s]/g, "" );
  slug = slug.toLowerCase();
  slug = slug.replace( /\s/g, '_' );
  return slug;
};

/**
 * Returns random int ID
 */
wpp.random_id = function () {
  return ( Math.floor( Math.random() * (99999999 - 1000000 + 1) ) + 1000000 );
};

/**
 * Bind ColorPicker with input fields '.wpp_input_colorpicker'
 *
 * @param object instance. jQuery object
 */
var bindColorPicker = function ( instance ) {
  if ( typeof window.jQuery.prototype.ColorPicker === 'function' ) {
    if ( !instance ) {
      instance = jQuery( 'body' );
    }
    jQuery( '.wpp_input_colorpicker', instance ).ColorPicker( {
      onSubmit: function ( hsb, hex, rgb, el ) {
        jQuery( el ).val( '#' + hex );
        jQuery( el ).ColorPickerHide();
        jQuery( el ).trigger( 'change' );
      },
      onBeforeShow: function () {
        jQuery( this ).ColorPickerSetColor( this.value );
      }
    } ).bind( 'keyup', function () {
        jQuery( this ).ColorPickerSetColor( this.value );
      } );
  }
};

/**
 * Updates Row field names
 *
 * @param object instance. DOM element
 * @param boolean allowRandomSlug. Determine if Row can contains random slugs.
 */
var updateRowNames = function ( instance, allowRandomSlug ) {
  if ( typeof instance === 'undefined' ) {
    return false;
  }
  if ( typeof allowRandomSlug === 'undefined' ) {
    var allowRandomSlug = false;
  }

  var this_row = jQuery( instance ).parents( 'tr.wpp_dynamic_table_row' );
  // Slug of row in question
  var old_slug = jQuery( this_row ).attr( 'slug' );
  // Get data from input.slug_setter
  var new_slug = jQuery( instance ).val();
  // Convert into slug
  new_slug = wpp.create_slug( new_slug );

  // Don't allow to blank out slugs
  if ( new_slug === "" ) {
    if ( allowRandomSlug ) {
      new_slug = 'random_' + Math.floor( Math.random() * 1000000 );
    } else {
      return;
    }
  }

  // There is no sense to continue if slugs are the same /
  if ( old_slug === new_slug ) {
    return;
  }

  // Get all slugs of the table
  var slugs = jQuery( this_row ).parents( 'table' ).find( 'input.slug' );
  slugs.each( function ( k, v ) {
    if ( jQuery( v ).val() === new_slug ) {
      new_slug = 'random_' + Math.floor( Math.random() * 1000000 );
      return false;
    }
  } );

  // If slug input.slug exists in row, we modify it
  jQuery( ".slug", this_row ).val( new_slug );
  // Update row slug
  jQuery( this_row ).attr( 'slug', new_slug );

  // Cycle through all child elements and fix names
  jQuery( 'input,select,textarea', this_row ).each( function ( i, e ) {
    var old_name = jQuery( e ).attr( 'name' );
    if ( typeof old_name != 'undefined' && !jQuery( e ).hasClass( 'wpp_no_change_name' ) ) {
      var new_name = old_name.replace( '[' + old_slug + ']', '[' + new_slug + ']' );
      if ( jQuery( e ).attr( 'id' ) ) {
        var old_id = jQuery( e ).attr( 'id' );
        var new_id = old_id.replace( '[' + old_slug + ']', '[' + new_slug + ']' );
      }
      // Update to new name
      jQuery( e ).attr( 'name', new_name );
      jQuery( e ).attr( 'id', new_id );
    }
  } );

  // Cycle through labels too
  jQuery( 'label', this_row ).each( function ( i, e ) {
    if ( jQuery( e ).attr( 'id' ) ) {
      var old_for = jQuery( e ).attr( 'for' );
      var new_for = old_for.replace( old_slug, new_slug );
      // Update to new name
      jQuery( e ).attr( 'for', new_for );
    }
  } );

  jQuery( ".slug", this_row ).trigger( 'change' );
};

/**
 * Assign Property Stat to Group Functionality
 *
 * @param object opt Params
 * @author Maxim Peshkov
 */
jQuery.fn.wppGroups = function ( opt ) {
  var instance = jQuery( this ), //* Default params */
    defaults = {
      groupsBox: '#wpp_attribute_groups',
      groupWrapper: '#wpp_dialog_wrapper_for_groups',
      closeButton: '.wpp_close_dialog',
      assignButton: '.wpp_assign_to_group',
      unassignButton: '.wpp_unassign_from_group',
      removeButton: '.wpp_remove_row',
      sortButton: "#sort_stats_by_groups"
    };

  opt = jQuery.extend( {}, defaults, opt );

  //* Determine if dialog Wrapper exist */
  if ( !jQuery( opt.groupWrapper ).length > 0 ) {
    jQuery( 'body' ).append( '<div id="wpp_dialog_wrapper_for_groups"></div>' );
  }

  var groupsBlock = jQuery( opt.groupsBox ), sortButton = jQuery( opt.sortButton ), statsRow = instance.parents( 'tr.wpp_dynamic_table_row' ), statsTable = instance.parents( '#wpp_inquiry_attribute_fields' ), close = jQuery( opt.closeButton, groupsBlock ), assign = jQuery( opt.assignButton ), unassign = jQuery( opt.unassignButton ), wrapper = jQuery( opt.groupWrapper ), colorpicker = jQuery( 'input.wpp_input_colorpicker', groupsBlock ), groupname = jQuery( 'input.slug_setter', groupsBlock ), remove = jQuery( opt.removeButton, groupsBlock ), sortButton = jQuery( opt.sortButton ),

  //* Open Groups Block */
    showGroupBox = function () {
      groupsBlock.show( 300 );
      wrapper.css( 'display', 'block' );
    },

  //* Close Groups Block */
    closeGroupBox = function () {
      groupsBlock.hide( 300 );
      wrapper.css( 'display', 'none' );

      statsRow.each( function ( i, e ) {
        jQuery( e ).removeClass( 'groups_active' );
      } )
    };

  //* EVENTS */
  instance.live( 'click', function () {
    showGroupBox();
    jQuery( this ).parents( 'tr.wpp_dynamic_table_row' ).addClass( 'groups_active' );
  } );

  instance.live( 'focus', function () {
    jQuery( this ).trigger( 'blur' );
  } );

  //* Close Group Box */
  close.live( 'click', function () {
    closeGroupBox();
  } );

  //* Assign attribute to Group */
  assign.live( 'click', function () {
    var row = jQuery( this ).parent().parent();
    statsRow.each( function ( i, e ) {
      if ( jQuery( e ).hasClass( 'groups_active' ) ) {
        jQuery( 'td:first', e ).css( 'background-color', jQuery( 'input.wpp_input_colorpicker', row ).val() );

        jQuery( e ).attr( 'wpp_attribute_group', row.attr( 'slug' ) );
        jQuery( 'input.wpp_group_slug', e ).val( row.attr( 'slug' ) );

        var groupName = jQuery( 'input.slug_setter', row ).val();
        if ( groupName === '' ) {
          groupName = 'NO NAME';
        }

        jQuery( 'input.wpp_attribute_group', e ).val( groupName );
      }
    } );
    closeGroupBox();
  } );

  //* Unassign attribute from Group */
  unassign.live( 'click', function () {
    statsRow.each( function ( i, e ) {
      if ( jQuery( e ).hasClass( 'groups_active' ) ) {
        jQuery( e ).find( 'td' ).css( 'background-color', '' );
        jQuery( e ).removeAttr( 'wpp_attribute_group' );
        jQuery( 'input.wpp_group_slug', e ).val( '' );
        jQuery( 'input.wpp_attribute_group', e ).val( '' );
      }
    } );
    closeGroupBox();
  } );

  //* Refresh background of all attributes on color change */
  colorpicker.live( 'change', function () {
    var cp = jQuery( this );
    var s = cp.parent().parent().attr( 'slug' );
    instance.each( function ( i, e ) {
      if ( s === jQuery( e ).next().val() ) {
        var _parent_row = jQuery( e ).closest( 'tr.wpp_dynamic_table_row' );
        jQuery( 'td.wpp_draggable_handle', _parent_row ).css( 'background-color', cp.val() );
      }
    } );
  } );

  //* Refresh Group Name field of all assigned attributes on group name change */
  groupname.live( 'change', function () {
    var gn = ( jQuery( this ).val() != '' ) ? jQuery( this ).val() : 'NO NAME';
    var s = jQuery( this ).parent().parent().attr( 'slug' );
    instance.each( function ( i, e ) {
      if ( s === jQuery( e ).next().val() ) {
        jQuery( e ).val( gn );
      }
    } );
  } );

  //* Remove group from the list */
  remove.live( 'click', function () {
    var s = jQuery( this ).parent().parent().attr( 'slug' );
    instance.each( function ( i, e ) {
      if ( s === jQuery( e ).next().val() ) {
        jQuery( e ).parent().parent().css( 'background-color', '' );
        //* HACK FOR IE7 */
        if ( typeof jQuery.browser.msie != 'undefined' && ( parseInt( jQuery.browser.version ) === 7 ) ) {
          jQuery( e ).parent().parent().find( 'td' ).css( 'background-color', '' );
        }
        jQuery( e ).val( '' );
        jQuery( e ).next().val( '' );
      }
    } );
  } );

  //* Close Groups Box on wrapper click */
  wrapper.live( 'click', function () {
    closeGroupBox();
  } );

  //* Sorts all attributes by Groups */
  sortButton.live( 'click', function () {
    jQuery( 'tbody tr', groupsBlock ).each( function ( gi, ge ) {
      statsRow.each( function ( si, se ) {
        if ( typeof jQuery( se ).attr( 'wpp_attribute_group' ) != 'undefined' ) {
          if ( jQuery( se ).attr( 'wpp_attribute_group' ) === jQuery( ge ).attr( 'slug' ) ) {
            jQuery( se ).attr( 'sortpos', ( gi + 1 ) );
          }
        } else {
          jQuery( se ).attr( 'sortpos', '9999' );
        }
      } );
    } );
    var sortlist = jQuery( 'tbody', statsTable );
    var listitems = sortlist.children( 'tr' ).get();
    listitems.sort( function ( a, b ) {
      var compA = parseFloat( jQuery( a ).attr( 'sortpos' ) );
      var compB = parseFloat( jQuery( b ).attr( 'sortpos' ) );
      return ( compA < compB ) ? -1 : ( compA > compB ) ? 1 : 0;
    } );
    jQuery.each( listitems, function ( idx, itm ) {
      sortlist.append( itm );
    } );
  } );

  //* HACK FOR IE7 */
  //* Set background-color for assigned attributes */
  if ( typeof jQuery.browser.msie != 'undefined' && ( parseInt( jQuery.browser.version ) === 7 ) ) {
    var sortlist = jQuery( 'tbody', statsTable );
    var listitems = sortlist.children( 'tr' ).get();
    jQuery.each( listitems, function ( i, e ) {
      jQuery( e ).find( 'td' ).css( 'background-color', jQuery( e ).css( 'background-color' ) );
    } );
  }
};

/**


 /**
 * Adds another input field to multi-value attribute rows.
 *
 *
 * @since 2.0.0
 * @author potanin@UD
 */
function wpp_add_multi_value_field ( this_button ) {

  /* Get elements */
  var this_entry_list = jQuery( this_button ).closest( "ul.wpp_single_attribute_entry_list" );
  var field_wrapper = jQuery( this_button ).closest( "li.wpp_field_wrapper" );
  var input_field = jQuery( ".text-input", field_wrapper );

  /* Get values that will change */
  var old_row_count = jQuery( input_field ).attr( "row_count" ) ? parseInt( jQuery( input_field ).attr( "row_count" ) ) : 0;
  var new_row_count = old_row_count + 1;
  var old_name = jQuery( input_field ).attr( "name" );
  var new_name = old_name.replace( '[' + old_row_count + ']', '[' + new_row_count + ']' );

  /* Remove the clicked button */
  jQuery( this_button ).remove();

  /* Clone the field */
  var new_field_wrapper = field_wrapper.clone();

  /* Modify cloned field */
  jQuery( ".text-input", new_field_wrapper ).attr( "name", new_name );
  jQuery( ".text-input", new_field_wrapper ).attr( "row_count", new_row_count );
  jQuery( ".text-input", new_field_wrapper ).val( "" );

  /* Insert cloned field */
  var added_field = jQuery( this_entry_list ).append( new_field_wrapper ).find( ":last" );

  var args = {
    'do_not_blank': true
  };

  /* Do whatever needs to be done with dynamic fields */
  wpp_handle_multi_value_field( added_field, args );

}

/**
 * Handle attribute rows that allow multiple fields.
 *
 * Called when a field within a multi-field row is changed
 *
 * @since 2.0.0
 * @author potanin@UD
 */
function wpp_handle_multi_value_field ( this_field, args ) {

  var args = ( typeof( args ) === 'object' ? args : {} );

  if ( !jQuery.isEmptyObject( args ) ) {
    /*console.log( args );*/
  }

  var this_entry_list = jQuery( this_field ).closest( "ul.wpp_single_attribute_entry_list" );
  var field_wrapper = jQuery( this_field ).closest( "li.wpp_field_wrapper" );
  var fields_in_row = jQuery( ".text-input", this_entry_list ).length;
  var value = jQuery( this_field ).val();

  /* If the last row is modified */
  if ( jQuery( ".text-input:last", this_entry_list ).get( 0 ) === this_field ) {
    var last_field = true;
  } else {
    var last_field = false;
  }

  /* If there are more than one fields in row, and this one was blanked, and we didn't specifically disable blanking - remove it. */
  if ( !args.do_not_blank && value === "" && fields_in_row > 1 ) {
    jQuery( this_field ).remove();
  }

  /* Remove any "Add Line" elements */
  jQuery( ".wpp_add_line", this_entry_list ).remove();

  if ( last_field ) {
    var add_field = '<span class="wpp_add_line">Add Field</span>';
    jQuery( this_field ).after( add_field );
  }

}

/**
 * Basic e-mail validation
 *
 * @param address
 * @return boolean. Returns true if email address is successfully validated.
 */
function wpp_validate_email ( address ) {
  var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;

  return reg.test( address ) !== false;
}

/**
 * Toggle advanced options that are somehow related to the clicked trigger
 *
 * If trigger element has an attr of 'show_type_source', then function attempt to find that element and get its value
 * if value is found, that value is used as an additional requirement when finding which elements to toggle
 *
 * Example: <span class="wpp_show_advanced" show_type_source="id_of_input_with_a_string" advanced_option_class="class_of_elements_to_trigger" show_type_element_attribute="attribute_name_to_match">Show Advanced</span>
 * The above, when clicked, will toggle all elements within the same parent tree of cicked element, with class of "advanced_option_class" and with attribute of "show_type_element_attribute" the equals value of "#id_of_input_with_a_string"
 *
 * Clicking the trigger in example when get the value of:
 * <input id="value_from_source_element" value="some_sort_of_identifier" />
 *
 * And then toggle all elements like below:
 * <li class="class_of_elements_to_trigger" attribute_name_to_match="some_sort_of_identifier">Data that will be toggled.</li>
 *
 * Copyright 2011 Usability Dynamics, Inc. <info@usabilitydynamics.com>
 */
function toggle_advanced_options () {

  jQuery( ".wpp_show_advanced" ).live( "click", function () {
    wpp.log( 'wpp_show_advanced():click' );

    var advanced_option_class = false;
    var show_type = false;
    var show_type_element_attribute = false;
    var wrapper = ( jQuery( this ).attr( 'wrapper' ) ? jQuery( this ).closest( '.' + jQuery( this ).attr( 'wrapper' ) ) : jQuery( this ).parents( 'tr.wpp_dynamic_table_row' ) );
    var toggle_target = jQuery( this ).attr( 'toggle_target' ) ? jQuery( '.' + jQuery( this ).attr( 'toggle_target' ) ) : false;

    if ( jQuery( this ).attr( "advanced_option_class" ) !== undefined ) {
      var advanced_option_class = "." + jQuery( this ).attr( "advanced_option_class" );
    }

    if ( jQuery( this ).attr( "show_type_element_attribute" ) !== undefined ) {
      var show_type_element_attribute = jQuery( this ).attr( "show_type_element_attribute" );
    }

    //* If no advanced_option_class is found in attribute, we default to 'wpp_development_advanced_option' */
    if ( !advanced_option_class ) {
      advanced_option_class = "li.wpp_development_advanced_option";
    }

    //* If element does not have a table row wrapper, we look for the closts .wpp_something_advanced_wrapper wrapper. .wpp_wrapper is the new short-hand class.  */
    if ( !wrapper || wrapper.length === 0 ) {

      if ( jQuery( this ).closest( '.wpp_something_advanced_wrapper' ).length ) {
        wrapper = jQuery( this ).closest( '.wpp_something_advanced_wrapper' );
      } else if ( jQuery( this ).closest( '.wpp_wrapper' ).length ) {
        wrapper = jQuery( this ).closest( '.wpp_wrapper' );
      }

    }

    //* get_show_type_value forces the a look up a value of a passed element, ID of which is passed, which is then used as another conditional argument */
    if ( show_type_source = jQuery( this ).attr( "show_type_source" ) ) {
      var source_element = jQuery( "#" + show_type_source );

      if ( source_element ) {
        //* Element found, determine type and get current value */
        if ( jQuery( source_element ).is( "select" ) ) {
          show_type = jQuery( "option:selected", source_element ).val();
        }
      }
    }

    if ( toggle_target ) {
      var element_path = jQuery( toggle_target );
    } else {
      var element_path = false;
    }

    if ( !element_path && !show_type ) {
      element_path = jQuery( advanced_option_class, wrapper );
    }

    //** Look for advanced options with show type */
    if ( !element_path && show_type ) {
      element_path = jQuery( advanced_option_class + "[" + show_type_element_attribute + "='" + show_type + "']", wrapper );
    }

    /* Check if this element is a checkbox, we assume that we always show things when it is checked, and hiding when unchecked */
    if ( jQuery( this ).is( "input[type=checkbox]" ) ) {

      var toggle_logic = jQuery( this ).attr( "toggle_logic" );

      if ( jQuery( this ).is( ":checked" ) ) {
        if ( toggle_logic == 'reverse' ) {
          jQuery( element_path ).hide();
        } else {
          jQuery( element_path ).show();
        }
      } else {
        if ( toggle_logic == 'reverse' ) {
          jQuery( element_path ).show();
        } else {
          jQuery( element_path ).hide();
        }
      }

      return;

    }

    jQuery( element_path ).toggle();

  } );
}

/**
 *
 * @param functionName
 * @param context
 * @param args
 * @return
 */
function wpp_call_function ( functionName, context, args ) {

  var args = Array.prototype.slice.call( arguments ).splice( 2 );
  var namespaces = functionName.split( "." );
  var func = namespaces.pop();
  for ( var i = 0; i < namespaces.length; i++ ) {
    context = context[namespaces[i]];
  }
  return context[func].apply( this, args );
}

/**
 * DOCUMENT READY EVENTS AND ACTIONS
 */
jQuery( document ).ready( function () {

  /**
   * Inserts Arbitrary Content into DOM. Gets Data from DataCloud.
   *
   * This is more or less an example/test.
   * For this to be realized Data API would need to start indexing product-specific materials for content.
   *
   * @since 2.0
   * @author potanin@UD
   */
  wpp.insert_content = function ( element, args, event ) {
    wpp.log( 'wpp.insert_content', arguments );

    var args = jQuery.extend( true, {
      'ui_class': 'wpp_dynamic_content'
    }, typeof _args === 'object' ? _args : {} );

    var _temp = [
      '<a href="#">Configuring advanced query parameters in URL.</a>', '<a href="#">Best ways of working around low  import limits.</a>', '<a href="#">Some other support topics.</a>'
    ];

    jQuery( '<ul class="' + ( args.ui_class ) + '"><li>' + _temp.join( '</li><li>' ) + '</li>' ).insertAfter( jQuery( element ).closest( 'p' ) );

    jQuery( element ).removeAttr( 'data-wpp_action' ).hide();
  };

  /**
   * Backend Action Handler
   *
   * @updated 2.0
   * @author potanin@UD
   */
  jQuery( '*[data-wpp_action]' ).live( 'click', function ( event ) {
    'use strict';
    wpp.log( 'data-wpp_action::click', arguments );

    var action = jQuery( this ).attr( 'data-wpp_action' ), args = {};

    if ( typeof window.wpp[ action ] != 'function' ) {
      return false;
    }

    try {
      args = jQuery.parseJSON( jQuery( this ).attr( 'data-args' ) );
    } catch ( error ) { /* We don't actually care. */
    }

    window.wpp[ action ]( this, args, event );

  } );

  /**
   * {unknown}
   *
   *
   */
  jQuery( '.wpp_tabbed_ui' ).each( function () {
    wpp.tabbed_ui( this );
  } );

  /**
   * Handle JS Events for Dropdowns
   * @props: https://github.com/star2dev
   */
  jQuery( '.wpp_dropdown .wpp_button' ).live( 'click', function ( e ) {
    e.preventDefault();

    if ( !jQuery( this ).find( 'span.wpp_toggle' ).hasClass( 'wpp_active' ) ) {
      jQuery( '.wpp_dropdown_slider' ).slideUp( 10 );
      jQuery( 'span.wpp_toggle' ).removeClass( 'wpp_active' );
    }

    jQuery( this ).parent().find( '.wpp_dropdown_slider' ).slideToggle( 10 );
    jQuery( this ).find( 'span.wpp_toggle' ).toggleClass( 'wpp_active' );

    jQuery( document ).bind( 'click', function ( e ) {
      if ( e.target.id != jQuery( '.wpp_dropdown' ).attr( 'class' ) ) {
        jQuery( '.wpp_dropdown_slider' ).slideUp( 10 );
        jQuery( 'span.wpp_toggle' ).removeClass( 'wpp_active' );
      }
    } );

    return false;
  } );

  /* Monitor changes to fields that can have multiple values */
  jQuery( "tr.wpp_attribute_row.wpp_allow_multiple .text-input" ).live( "change", function () {
    wpp_handle_multi_value_field( this );
  } );

  /* Cycle through all multi-value fields on ready  */
  jQuery( "tr.wpp_attribute_row.wpp_allow_multiple .text-input" ).each( function () {
    wpp_handle_multi_value_field( this );
  } );

  /* Add new input field row */
  jQuery( "tr.wpp_attribute_row.wpp_allow_multiple .wpp_add_line" ).live( "click", function () {
    wpp_add_multi_value_field( this );
  } );

  /* Remove any highlight classes */
  jQuery( "#contextual-help-link" ).click( function () {
    jQuery( "#contextual-help-wrap h3" ).removeClass( "wpp_contextual_highlight" );
    jQuery( document ).trigger( 'contextual-help-link::toggle' );
  } );

  toggle_advanced_options();

  // Toggle wpp_wpp_settings_configuration_do_not_override_search_result_page_
  jQuery( "#wpp_wpp_settings_configuration_automatically_insert_overview_" ).change( function () {
    if ( jQuery( this ).is( ":checked" ) ) {
      jQuery( "li.wpp_wpp_settings_configuration_do_not_override_search_result_page_row" ).hide();
    } else {
      jQuery( "li.wpp_wpp_settings_configuration_do_not_override_search_result_page_row" ).show();
    }
  } );

  // Bind ( Set ) ColorPicker
  bindColorPicker();

  // Add row to UD UI Dynamic Table
  jQuery( ".wpp_add_row" ).live( "click", function () {
    wpp.table.add_row( this );
  } );

  // Remove row from UD UI Dynamic Table
  jQuery( ".wpp_remove_row" ).live( "click", function () {
    wpp.table.remove_row( this );
  } );

  // When the .slug_setter input field is modified, we update names of other elements in row
  jQuery( ".wpp_dynamic_table_row[new_row=true] input.slug_setter" ).live( "keyup", function () {
    updateRowNames( this, true );
  } );
  jQuery( ".wpp_dynamic_table_row[new_row=true] select.slug_setter" ).live( "change", function () {
    updateRowNames( this, true );
  } );

  // Delete dynamic row
  //* @todo: depreciated. wpp.table.remove_row should be used insted of this one. peshkov@UD */
  jQuery( ".wpp_delete_row" ).live( "click", function () {
    var parent = jQuery( this ).parents( 'tr.wpp_dynamic_table_row' );
    var table = jQuery( jQuery( this ).parents( 'table' ).get( 0 ) );
    var row_count = table.find( ".wpp_delete_row" ).length;
    if ( jQuery( this ).attr( 'verify_action' ) === 'true' ) {
      if ( !confirm( 'Are you sure?' ) )
        return false;
    }
    // Blank out all values
    jQuery( "input[type=text]", parent ).val( '' );
    jQuery( "input[type=checkbox]", parent ).attr( 'checked', false );
    // Don't hide last row
    if ( row_count > 1 ) {
      jQuery( parent ).hide();
      jQuery( parent ).remove();
    }

    table.trigger( 'row_removed', [parent] );
  } );

  jQuery( '.wpp_attach_to_agent' ).live( 'click', function () {
    var agent_image_id = jQuery( this ).attr( 'id' );
    if ( agent_image_id != '' )
      jQuery( '#library-form' ).append( '<input name="wpp_agent_post_id" type="text" value="' + agent_image_id + '" />' ).submit();
  } );

  wpp.table.sortable();

  /**
   * Handles form saving
   * Do any validation/data work before the settings page form is submitted
   * @author odokienko@UD
   */
  jQuery( ".wpp_settings_page form" ).submit( function ( form ) {
    var error_field = {object: false, tab_index: false};

    /* The next block make validation for required fields    */
    jQuery( "form .wpp_tabs :input[validation_required=true],form .wpp_tabs .wpp_required_field :input,form .wpp_tabs :input[required],form .wpp_tabs :input.slug_setter" ).each( function () {

      /* we allow empty value if dynamic_table has only one row */
      var dynamic_table_row_count = jQuery( this ).closest( '.wpp_dynamic_table_row' ).parent().children( 'tr.wpp_dynamic_table_row' ).length;

      if ( !jQuery( this ).val() && dynamic_table_row_count != 1 ) {
        error_field.object = this;
        error_field.tab_index = jQuery( '.wpp_tabs a[href="#' + jQuery( error_field.object ).closest( ".ui-tabs-panel" ).attr( 'id' ) + '"]' ).parent().index();
        return false;
      }
    } );

    /* if error_field object is not empty then we've error found */
    if ( error_field.object != false ) {
      /* do focus on tab with error field */
      if ( typeof error_field.tab_index != 'undefined' ) {
        jQuery( '.wpp_tabs' ).tabs( 'select', error_field.tab_index );
      }
      /* mark error field and remove mark on keyup */
      jQuery( error_field.object ).addClass( 'ui-state-error' ).one( 'keyup', function () {
        jQuery( this ).removeClass( 'ui-state-error' );
      } );
      jQuery( error_field.object ).focus();
      return false;
    }
  } );

  if ( typeof jQuery.fn.datepicker == 'function' ) {
    jQuery( 'input.wpp_date_picker' ).datepicker( {
      changeMonth: true,
      changeYear: true
    } );
  }

  //** Default Property Image Uploader */
  jQuery( '.wpp_default_property_image_uploader' ).live( 'click', function () {
    tb_show( '', 'media-upload.php?type=image&amp;TB_iframe=true' );

    window.send_to_editor = function ( html ) {
      var imgurl = jQuery( 'img', html ).attr( 'src' );
      var classes = jQuery( 'img', html ).attr( 'class' );
      var attach_id = classes.replace( /(.*?)wp-image-/, '' );
      jQuery( '.wpp_default_property_image_field' ).val( imgurl );
      jQuery( '.wpp_default_property_image_id' ).val( attach_id );
      jQuery( '.wpp_default_property_image_preview' ).attr( 'src', imgurl );
      jQuery( '.wpp_default_property_image_data' ).show();
      jQuery( '.wpp_default_property_image_message' ).hide();
      tb_remove();
    };

    return false;
  } );

  //** Remove Default Property Image */
  jQuery( '.wpp_remove_default_property_image' ).live( 'click', function () {
    jQuery( '.wpp_default_property_image_field' ).val( '' );
    jQuery( '.wpp_default_property_image_id' ).val( '' );
    jQuery( '.wpp_default_property_image_message' ).show();
    jQuery( '.wpp_default_property_image_data' ).hide();
  } );

} );
