/* =========================================================
 * flawless-admin.js
 * http://usabilitydynamics.com
 * =========================================================
 *
 * Version 0.0.5
 *
 * Main handler for Flawless admin functions, loaded only on Flawless pages.
 *
 * Copyright (c) 2012 Usability Dynamics, Inc. (usabilitydynamics.com)
 * ========================================================= */

  var flawless = jQuery.extend( true, {}, typeof flawless === 'object' ? flawless : {} );
  
  /** Support for legacy */
  var flawless_admin = flawless_admin ? flawless_admin : flawless;

  jQuery( document ).ready( function() {

    /* Render available widget areas for Content Types */
    flawless_render_widget_areas_in_dom();

    if( typeof jQuery.prototype.smart_dom_button === 'function' ) {
      jQuery( ".flawless_link[flawless_action]" ).smart_dom_button( {
        debug: true,
        action_attribute: 'flawless_action'
      });

      jQuery( ".flawless_action .execute_action" ).smart_dom_button( {
        debug: true,
        action_attribute: 'flawless_action',
        ajax_action: 'flawless_action',
        wrapper: '.flawless_action'
      });
    }

    if( typeof jQuery.prototype.tabs === 'function' ) {
      jQuery( ".flawless_tabs" ).tabs({});
      jQuery( ".flawless_settings_tabs" ).tabs( { cookie: {expires: 30} } );
    }

    if( typeof jQuery.fn.sortable == 'function' ) {

      jQuery( ".ud_ui_dynamic_table[sortable_table=true] tbody" ).each( function() {

        var this_table = this;

        jQuery( this_table ).sortable( {
          placeholder: 'ui-state-highlight',
          start: function( event, ui ) {
            jQuery( ui.placeholder.html( '<td colspan="'+ jQuery( "td,th", ui.item ).length +'"></td>' ) );
            jQuery( ui.placeholder.height( jQuery( ui.item ).height() ) );

          },
          helper: function( e, tr ) {
            var $originals = tr.children();
            var $helper = tr.clone();
            $helper.children().each( function( index ) {
              jQuery( this ).width( $originals.eq( index ).width() )
            });
            return $helper;
          }
        });

      });

    }

    flawless_toggle_advanced_options();

    // Add row to UD UI Dynamic Table
    jQuery( ".flawless_advanced_content_type_options .slug_setter" ).live( 'focus' , function() {
      var row = jQuery( this ).closest( 'tr' );
      jQuery( ".flawless_advanced_option", row ).show();
      flawless_toggle_the_toggle( jQuery( ".flawless_show_advanced", row ), jQuery( ".flawless_advanced_option", row ) );
    });

    // Add row to UD UI Dynamic Table
    jQuery( ".flawless_add_row" ).live( "click" , function() {
      flawless_add_row( this );
    });

    jQuery( ".flawless_dynamic_table_row[new_row=true] .slug_setter" ).live( 'change', function() {
      flawless_update_row_names( this );
    });

    /* Setup Dynamic Table Stuff */
    flawless_setup_dynamic_tables();

    flawless_adjust_dom_to_conditionals( 'non-instant' );

    jQuery( ".flawless_supported_theme_features .wp-tab-panel :input" ).change( function( e ) {
      flawless_adjust_dom_to_conditionals( 'non-instant', e );
    });

    /* Handle logo deletion */
    jQuery( ".flawless_delete_logo" ).click( function() {
      jQuery.post( ajaxurl, {
        _wpnonce: flawless.actions_nonce ? flawless.actions_nonce : '',
        action: 'flawless_action',
        the_action: 'delete_logo'
      },function ( result ) {

       if( result.success == 'true' ) {
          jQuery( ".current_flawless_logo" ).remove();
        }

      }, 'json' );
    });

    /* Cycle through all checked checkboxes and highlight their parent elements */
    jQuery( 'li.flawless_setup_option_block input[type=checkbox]' ).each( function() {
      var parent_row = jQuery( this ).parents( 'ul.block_options' );
      var parent_holder = jQuery( this ).parents( 'li.flawless_setup_option_block' );

      if( jQuery( this ).is( ":checked" ) ) {
        jQuery( parent_holder ).addClass( 'selected_option' );
      }

    });


    /* When a 'flawless_setup_option_block' element is clicked, the child checkbox is checked, and the element is highlighted */
    jQuery( 'li.flawless_setup_option_block' ).click( function() {

      if( jQuery( this ).hasClass( 'required_option' ) ) {
        return;
      }

      var parent_row = jQuery( this ).parents( 'ul.block_options' );
      var this_option_checkbox = jQuery( 'input[type=checkbox]', this );
      jQuery( 'li.flawless_setup_option_block', parent_row ).removeClass( 'selected_option' );

      jQuery( 'input[type=checkbox]', parent_row ).removeAttr( "checked" )
      jQuery( this_option_checkbox ).attr( 'checked', true );

      jQuery( this ).addClass( 'selected_option' );

    });


    jQuery( ".flawless_force_http_prefix" ).change( function() {

      var value = jQuery( this ).val();
      var found = ( ( value.search( "http" ) ) < 0 ? false : true );

      if( value == "" || found ) {
        return;
      }

      jQuery( this ).val( "http://" + value );

    });

    jQuery( document ).bind( 'flawless::widget_area_change', function() {

      flawless.widget_areas = {};

      jQuery( '.widget_area_sidebar .flawless_widget_item input.flawless_wa[type="text"]' ).each( function( index ) {
        flawless.widget_areas[ jQuery( this ).closest( '.flawless_widget_item' ).attr( 'sidebar_name' ) ] = jQuery( this ).val();
      });

    });

  });


  /**
   * Renders options for widget area dropdowns
   *
   * @todo Need to have the selector list revert to defined position when list items are rearranged within it. - potanin@UD
   * @author potanin@UD
   * @version 0.2.2
   */
  function flawless_render_widget_areas_in_dom() {

    /* Handle addinga new widget area to the selector */
    jQuery( '.flawless_add_new_widget_area' ).click( function() {
      var parent = jQuery( this ).closest( '.flawless_widget_area_list' );

      /* Clone a flawless item, there should always be at least one */
      var new_item = jQuery( '.flawless_widget_item[flawless_widget_area="true"]:last', parent ).clone();

      jQuery( new_item ).attr( 'new_row', 'true' );

      var old_slug = jQuery( new_item ).attr( 'sidebar_name' );
      var new_slug = 'flawless_widget_area_' + Math.floor( Math.random()*1000 );

      // Cycle through all child elements and fix names
      jQuery( 'input, select, textarea', new_item ).each( function( i, e ) {
        var old_name = jQuery( e ).attr( 'name' );
        if ( typeof old_name != 'undefined' && !jQuery( e ).hasClass( 'flawless_no_change_name' ) ) {
          jQuery( e ).attr( 'name', old_name.replace( '['+old_slug+']','['+new_slug+']' ) );
        }

      });

      jQuery( new_item ).attr( 'sidebar_name', new_slug );

      /* Insert new item before the Add New button */
      jQuery( this ).before( new_item );

      jQuery( '.flawless_wa[attribute="name"]', new_item ).val( '' ).focus();
      jQuery( '.flawless_wa[attribute="class"]', new_item ).val( '' ).focus();

    });


    /* Handle widget item removal */
    jQuery( '.flawless_widget_item .flawless_wa[attribute="name"]' ).live( 'change', function() {
      var item = jQuery( this ).closest( '.flawless_widget_item' );
      var new_value = jQuery( this ).val();
      var sidebar_name = jQuery( item ).attr( 'sidebar_name' );

      if( jQuery( this ).attr( 'new_row' ) == 'true' ) {}

      /* Update all others */
      jQuery( '.flawless_widget_item[sidebar_name="' + sidebar_name + '"] .flawless_wa[attribute="name"]' ).val( new_value );

      jQuery( document ).trigger( 'flawless::widget_area_change' );

    });

    jQuery( '.flawless_widget_area_list .flawless_widget_item .delete' ).live( 'click', function() {

      var parent = jQuery( this ).closest( '.flawless_widget_area_list' );
      var list_type = parent.attr( 'type' );

      var element = jQuery( this ).closest( '.flawless_widget_item' );
      var verify_action = jQuery( this ).attr( 'verify_action' ) ? jQuery( this ).attr( 'verify_action' ) : false;
      var new_row = jQuery( element ).attr( 'new_row' ) == 'true' ? true : false;

      var sidebar_name = jQuery( element ).attr( 'sidebar_name' );

      if( verify_action && !new_row ) {
        if( !confirm( verify_action ) ) {
          return false;
        }

      }

      /* Remove all widget areas with this name */
      if( list_type == 'widget_area_selector' ) {

        var count = jQuery( '.flawless_widget_item[flawless_widget_area="true"]', parent ).length;

        if( count > 1 ) {
          jQuery( '.flawless_widget_item[sidebar_name="' + sidebar_name +'"]' ).remove();
        }

      } else {
        jQuery( element ).remove();
      }

    });

    var sortable;

    sortable = jQuery( '.flawless_widget_area_list' ).sortable( {
      connectWith: '.flawless_widget_area_list[type=widget_area_holder]',
      distance: 10,
      forceHelperSize: true,
      forcePlaceholderSize: true,
      helper: 'clone',
      placeholder: 'ui-state-highlight',
      opacity: 0.9,
      scrollSpeed: 60,
      handle: '.handle',
      start: function( event, ui ) {
        ui.item.show();
        sortable.original_index = jQuery( ui.item ).index();
      },
      receive: function( event, ui ) {

        jQuery( 'input[do_not_clone=true]', ui.item ).remove();

        /* Clone so it can be modified */
        var item = jQuery( ui.item ).clone();
        var parent = jQuery( ui.item ).closest( '.flawless_content_type_module' );
        var was_pane = jQuery( ui.item ).closest( '.flawless_was_pane' );

        var args = {
          content_type: jQuery( parent ).attr( 'content_type' ),
          slug: jQuery( parent ).attr( 'slug' ),
          was_slug: jQuery( was_pane ).attr( 'was_slug' ),
          sidebar_name: jQuery( ui.item ).attr( 'sidebar_name' )
        }

        /* Add form elements */
        jQuery( ui.item ).append( '<input type="hidden" do_not_clone="true" name="flawless_settings[' + args.content_type + '][' + args.slug + '][widget_areas][' + args.was_slug + '][]" value="' + args.sidebar_name + '"  />' );
        jQuery( ui.item ).attr( 'do_not_clone', 'true' );

        /* Only return item when dragged out of selector */
        if( jQuery( ui.sender ).attr( 'type' ) != 'widget_area_selector' ) {
          return;
        }

        if( sortable.original_index === 0 ) {
          jQuery( '.flawless_widget_item', ui.sender ).eq( 0 ).before( item );
        } else {
          jQuery( '.flawless_widget_item', ui.sender ).eq( sortable.original_index - 1 ).after( item );
        }

      },
      stop: function( event, ui ) {}

    });

    jQuery( document ).bind( 'added_dynamic_row', function() {
      jQuery( sortable ).sortable( 'enable' );

    });

  }


  function flawless_setup_dynamic_tables() {

    /* Hide the delete button from any locked rows */
    jQuery( ".flawless_dynamic_table_row[lock_row=true] .flawless_delete_row" ).hide();

    jQuery( ".flawless_delete_row" ).live( "click", function() {
      var parent = jQuery( this ).parents( '.flawless_dynamic_table_row' );
      var table = jQuery( jQuery( this ).parents( '.ud_ui_dynamic_table' ).get( 0 ) );
      var row_count = table.find( ".flawless_dynamic_table_row" ).length;
      var can_delete = ( jQuery( parent ).attr( "lock_row" ) == "true" ? false : true );
      var new_row = ( jQuery( parent ).attr( "new_row" ) == "true" ? true : false );

      if( !can_delete ) {
        return;
      }

      if( jQuery( this ).attr( 'verify_action' ) == 'true' && !new_row ) {
        if( !confirm( 'Are you sure?' ) )
          return false;
      }

      // Blank out all values
      jQuery( "input[type=text]", parent ).val( '' );
      jQuery( "input[type=checkbox]", parent ).attr( 'checked', false );

      // Don't hide last row
      if( row_count > 1 ) {
        jQuery( parent ).hide();
        jQuery( parent ).remove();
      }

      table.trigger( 'row_removed' );
    });
  }


  /* Toggle conditional settidngs. Executed on every trigger event, and affects all elements */
  function flawless_adjust_dom_to_conditionals( type, e ) {

    var need_new_tab = false;

    if (typeof e != 'undefined' ){
       if(jQuery( '.conditional_dependency[required_condition="' + jQuery(e.target).attr('affects') + '"]' ).css('display')=='list-item'){
         need_new_tab=true;
       }
    }

    jQuery( '.flawless_supported_theme_features .wp-tab-panel :input' ).each( function() {

      var affects = jQuery( this ).attr( 'affects' );
      var result_element = jQuery( '.conditional_dependency[required_condition="' + affects + '"]' );
      var show_on = jQuery( this ).attr( 'show_on' ) ? jQuery( this ).attr( 'show_on' ) : 'disable';
      var current_tab = jQuery('a[href="' + jQuery("a",result_element ).first().attr('href') + '"]',result_element).closest( '.ui-tabs' );
      var index = jQuery('a[href="' + jQuery("a",result_element[0] ).first().attr('href') + '"]',result_element[0]).parent().index();

      if( show_on == 'disable' && jQuery( this ).is( ':checked' ) ) {
        var action = 'hide';
      }

      if( show_on == 'disable' && !jQuery( this ).is( ':checked' ) ) {
        var action = 'show';
      }

      if( show_on == 'enable' && jQuery( this ).is( ':checked' ) ) {
        var action = 'show';
      }

      if( show_on == 'enable' && !jQuery( this ).is( ':checked' ) ) {
        var action = 'hide';
      }

      if( action == 'hide' ) {

        if( type == "instant" ) {
          result_element.fadeOut(400,function(){
            array_tabs = jQuery('li.ui-state-default.conditional_dependency:visible',this);
            do_select_first_tab(array_tabs);
          });
        }
        else {
          var selected = jQuery(current_tab).tabs('option', 'selected');
          result_element.hide();
          if (selected == index){
            array_tabs = jQuery('li.ui-state-default.conditional_dependency:visible',current_tab);
            if(jQuery(array_tabs).length>0){
              do_select_first_tab(array_tabs);
            }
          }
        }

      } else {
        if( type == "instant" ) {
          result_element.fadeIn();
        } else {
          result_element.show();
          var flawless_tabs = jQuery('tr .flawless_section_specific_tabs.ui-tabs').tabs();

          jQuery(flawless_tabs).each(function(){
              var selected = jQuery(this).tabs('option', 'selected');
              array_tabs = jQuery('li.ui-state-default.conditional_dependency:visible',this);
              if(jQuery(array_tabs).length==1){
                do_select_first_tab(array_tabs);
              }

          })
        }
      }
    });

    /* Check if result element is a selectd UI tab */
    if(typeof need_new_tab !='undefined' && need_new_tab) {
      jQuery('tr .flawless_header_features').each(function(){
        array_tabs = jQuery('li.ui-state-default.conditional_dependency:visible',this);
        do_select_first_tab(array_tabs);
      })
    }
  }

  function do_select_first_tab(array_tabs){
    jQuery(array_tabs).each( function() {
      if(jQuery(this).css('display') == 'list-item'){
        var index = jQuery('a[href="' + jQuery("a",this ).first().attr('href') + '"]',this).parent().index();
        jQuery( this ).closest( '.ui-tabs' ).tabs( 'select', index );
        return false;
      };
    });
  }

  /**
   * Updates Row field names
   *
   */
  var flawless_update_row_names = function( instance, allow_random_slug ) {

    if( typeof instance == 'undefined' ) {
      return false;
    }

    var this_table = jQuery( instance ).closest( ".ud_ui_dynamic_table" );

    if( typeof allow_random_slug == 'undefined' ) {
      var allow_random_slug = ( jQuery( this_table ).attr( "allow_random_slug" ) == "true" ? true : false );
    }

    var this_row = jQuery( instance ).closest( '.flawless_dynamic_table_row' );

    // Slug of row in question
    var old_slug = jQuery( this_row ).attr( 'slug' );

    // Convert into slug
    var new_slug = flawless_create_slug( jQuery( instance ).val() );

    // Don't allow to blank out slugs
    if( new_slug == "" ) {
      if( allow_random_slug ) {
        new_slug = 'random_' + Math.floor( Math.random()*1000 );
      } else {
        jQuery( ":not( input.slug_setter )", this_row ).attr( "disabled", true ).addClass( "temporary_disable" );
        jQuery( "input.slug_setter" , this_row ).val( "" );
        return;
      }
    }

    /* Re-enable fields in case they were disabled due to an empty slug setter */
    jQuery( ":not( input.slug_setter )", this_row ).attr( "disabled", false ).removeClass( "temporary_disable" );

    // If slug input.slug exists in row, we modify it
    jQuery( ".slug" , this_row ).val( new_slug );
    // Update row slug
    jQuery( this_row ).attr( 'slug', new_slug );

    // Cycle through all child elements and fix names
    jQuery( 'input, select, textarea', this_row ).each( function( i,e ) {
      var old_name = jQuery( e ).attr( 'name' );
      if ( typeof old_name != 'undefined' && !jQuery( e ).hasClass( 'flawless_no_change_name' ) ) {
        var new_name =  old_name.replace( '['+old_slug+']','['+new_slug+']' );
        if( jQuery( e ).attr( 'id' ) ) {
          var old_id = jQuery( e ).attr( 'id' );
          var new_id =  old_id.replace( '['+old_slug+']','['+new_slug+']' );
        }
        // Update to new name
        jQuery( e ).attr( 'name', new_name );
        jQuery( e ).attr( 'id', new_id );
      }
    });

    // Cycle through labels too
    jQuery( 'label', this_row ).each( function( i,e ) {
      if( jQuery( e ).attr( 'id' ) ) {
        var old_for = jQuery( e ).attr( 'for' );
        var new_for =  old_for.replace( old_slug,new_slug );
        // Update to new name
        jQuery( e ).attr( 'for', new_for );
      }
    });

    jQuery( ".slug" , this_row ).trigger( 'change' );
  }

  /**
   * Toggle advanced options that are somehow related to the clicked trigger
   *
   * If trigger element has an attr of 'show_type_source', then function attempt to find that element and get its value
   * if value is found, that value is used as an additional requirement when finding which elements to toggle
   *
   * Example: <span class="flawless_show_advanced" show_type_source="id_of_input_with_a_string" advanced_option_class="class_of_elements_to_trigger" show_type_element_attribute="attribute_name_to_match">Show Advanced</span>
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
  function flawless_toggle_advanced_options() {

    jQuery( ".flawless_advanced_option" ).hide();

    jQuery( ".flawless_show_advanced" ).live( "click", function() {

      var advanced_option_class = false;
      var show_type = false;
      var show_type_element_attribute = false;

      //* Try getting arguments automatically */

      if( jQuery( this ).attr( "settings_wrapper" ) !== undefined ) {
        var wrapper = jQuery( this ).closest( '.' + jQuery( this ).attr( "settings_wrapper" ) );
      } else {
        var wrapper = jQuery( this ).closest( 'tr.flawless_dynamic_table_row' );
      }

      if( jQuery( this ).attr( "advanced_option_class" ) !== undefined ) {
        var advanced_option_class = "." + jQuery( this ).attr( "advanced_option_class" );
      }

      if( jQuery( this ).attr( "show_type_element_attribute" ) !== undefined ) {
        var show_type_element_attribute = jQuery( this ).attr( "show_type_element_attribute" );
      }

      //* If no advanced_option_class is found in attribute, we default to 'flawless_advanced_option' */
      if( !advanced_option_class ) {
        advanced_option_class = "li.flawless_advanced_option";
      }

      //* If element does not have a table row wrapper, we look for the closest .flawless_something_advanced_wrapper wrapper */
      if( wrapper.length == 0 ) {
        var wrapper = jQuery( this ).parents( '.flawless_something_advanced_wrapper' );
      }

      //* get_show_type_value forces the a look up a value of a passed element, ID of which is passed, which is then used as another conditional argument */
      if( show_type_source = jQuery( this ).attr( "show_type_source" ) ) {
        var source_element = jQuery( "#" + show_type_source );

        if( source_element ) {
          //* Element found, determine type and get current value */
          if( jQuery( source_element ).is( "select" ) ) {
            show_type = jQuery( "option:selected", source_element ).val();
          }
        }
      }

      if( !show_type ) {
        element_path = jQuery( advanced_option_class, wrapper );
      }

      //** Look for advanced options with show type */
      if( show_type ) {
        element_path = jQuery( advanced_option_class + "[" + show_type_element_attribute + "='"+show_type+"']", wrapper );
      }

      /* Check if this element is a checkbox, we assume that we always show things when it is checked, and hiding when unchecked */
      if( jQuery( this ).is( "input[type=checkbox]" ) ) {

        var toggle_logic = jQuery( this ).attr( "toggle_logic" );

        if( jQuery( this ).is( ":checked" ) ) {
          if( toggle_logic = 'reverse' ) {
            jQuery( element_path ).hide();
          } else {
            jQuery( element_path ).show();
          }
        } else {
          if( toggle_logic = 'reverse' ) {
            jQuery( element_path ).show();
          } else {
            jQuery( element_path ).hide();
          }
        }

        flawless_toggle_the_toggle( this, jQuery( element_path ) );

        return;

      }

      jQuery( element_path ).toggle();

      flawless_toggle_the_toggle( this, jQuery( element_path ) );

    });
  }

  /**
   *
   */
  function flawless_toggle_the_toggle( toggle, toggled ) {

    var text_if_hidden = jQuery( toggle ).attr( "text_if_hidden" );
    var text_if_shown = jQuery( toggle ).attr( "text_if_shown" );

    if( text_if_shown == "" || text_if_hidden  == "" ) {
      return;
    }

    if( jQuery( toggled ).is( ":visible" ) ) {
      jQuery( toggle ).text( text_if_shown );
    } else {
      jQuery( toggle ).text( text_if_hidden );
    }

  }


  /**
   *
   */
  function flawless_create_slug( slug ) {
    slug = slug.replace( /[^a-zA-Z0-9_\s]/g,"" );
    slug = slug.toLowerCase();
    slug = slug.replace( /\s/g,'_' );
    return slug;
  }


  /**
   * Adds new Row to the table
   *
   */
  function flawless_add_row( element ) {

    var args = {};

    var auto_increment = false;
    var table = jQuery( element ).closest( '.ud_ui_dynamic_table' );
    var element_wrapper = jQuery( element ).closest( jQuery( element ).attr( 'element_wrapper' ) );
    var callback_function = jQuery( element ).attr( 'callback_function' ) ? jQuery( element ).attr( 'callback_function' ) : false;

    //* Determine if table rows are numeric */
    if( jQuery( table ).attr( 'auto_increment' ) == 'true' ) {
      var auto_increment = true;

    } else if ( jQuery( table ).attr( 'use_random_row_id' ) == 'true' ) {
      var use_random_row_id = true;

    } else if ( jQuery( table ).attr( 'allow_random_slug' ) == 'true' ) {
      var allow_random_slug = true;

    }

    args.last_row = jQuery( '.flawless_dynamic_table_row:last', table );

    //* Clone last row */
    var added_row = jQuery( args.last_row ).clone();

    //* Set unique 'id's and 'for's for elements of the new row */
    var unique = Math.floor( Math.random()*1000 );
    flawless_set_unique_ids( added_row, unique );

    //* Insert new row after last one */
    if( element_wrapper.length ) {
      jQuery( added_row ).insertBefore( element_wrapper );

    } else {
      jQuery( args.last_row ).after( added_row );

    }

    //* Bind ( Set ) ColorPicker with new fields '.flawless_input_colorpicker' */
    flawless_bind_color_picker( added_row );

    // Display row just in case
    jQuery( added_row ).show();

    //* Blank out all values */
    jQuery( "textarea", added_row ).val( '' );
    jQuery( "select", added_row ).val( '' );
    jQuery( "input[type=text]", added_row ).val( '' );
    jQuery( "input[type=checkbox]", added_row ).attr( 'checked', false );

    //* Increment name value automatically */
    if( auto_increment ) {

      //* Cycle through all child elements and fix names */
      jQuery( 'input,select,textarea', added_row ).each( function( element ) {
        var old_name = jQuery( this ).attr( 'name' );
        var matches = old_name.match( /\[( \d{1,2} )\]/ );
        if ( matches ) {
          old_count = parseInt( matches[1] );
          new_count = ( old_count + 1 );
        }
        var new_name =  old_name.replace( '[' + old_count + ']','[' + new_count + ']' );
        //* Update to new name */
        jQuery( this ).attr( 'name', new_name );
      });

    } else if ( use_random_row_id ) {

      //* Get the current random id of row */
      var random_row_id = jQuery( added_row ).attr( 'random_row_id' );
      var new_random_row_id = Math.floor( Math.random()*1000 );

      //* Cycle through all child elements and fix names */
      jQuery( 'input,select,textarea', added_row ).each( function( element ) {
        var old_name = jQuery( this ).attr( 'name' );
        var new_name =  old_name.replace( '[' + random_row_id + ']','[' + new_random_row_id + ']' );
        //* Update to new name */
        jQuery( this ).attr( 'name', new_name );
      });

      jQuery( added_row ).attr( 'random_row_id', new_random_row_id );

    } else if ( allow_random_slug ){

      //* Update Row names */
      var slug_setter = jQuery( "input.slug_setter", added_row );

      if( slug_setter.length > 0 ) {
        flawless_update_row_names( slug_setter.get( 0 ), true );
      }

    }

    /* Unset locked status */
    jQuery( added_row ).removeAttr( 'lock_row' );

    /* Display any hidden elements are clone */
    jQuery( '[show_on_clone=true]', added_row ).show();

    /* Remove elements that do not carry over */
    jQuery( "[do_not_clone=true]", added_row ).remove();

    //* Unset 'new_row' attribute */
    jQuery( added_row ).attr( 'new_row', 'true' );

    //* Focus on new element */
    jQuery( 'input.slug_setter', added_row ).focus();

    //* Fire Event after Row added to the Table */
    jQuery( document ).trigger( 'added_dynamic_row' );

    /* Legacy: */
    added_row.trigger( 'added' );

    if ( callback_function ) {

      callback_function = window[callback_function];

      if( typeof callback_function === 'function' ) {
        callback_function( added_row );
      }

    }

    return added_row;
  }


  /**
   * Set unique IDs and FORs of DOM elements recursivly
   *
   */
  function flawless_set_unique_ids( el, unique ) {

    if ( typeof el == "undefined" || el.size() === 0 ) {
      return;
    }

    el.each( function(){
      var child = jQuery( this );

      if ( child.children().size() > 0 ) {
        flawless_set_unique_ids( child.children(), unique );
      }

      var id = child.attr( 'id' );
      if( typeof id != 'undefined' ) {
        child.attr( 'id', id + '_' + unique );
      }

      var efor = child.attr( 'for' );
      if( typeof efor != 'undefined' ) {
        child.attr( 'for', efor + '_' + unique );
      }
    });
  }


  /**
   * Bind ColorPicker with input fields '.wpp_input_colorpicker'
   *
   */
  var flawless_bind_color_picker = function( instance ){
    if( typeof window.jQuery.prototype.ColorPicker == 'function' ) {
      if( !instance ) {
        instance = jQuery( 'body' );
      }
      jQuery( '.wpp_input_colorpicker', instance ).ColorPicker( {
        onSubmit: function( hsb, hex, rgb, el ) {
          jQuery( el ).val( '#' + hex );
          jQuery( el ).ColorPickerHide();
          jQuery( el ).trigger( 'change' );
        },
        onBeforeShow: function () {
          jQuery( this ).ColorPickerSetColor( this.value );
        }
      } )
      .bind( 'keyup', function(){
        jQuery( this ).ColorPickerSetColor( this.value );
      });
    }
  }


  /**
   * { Missing Description}
   *
   */
  function flawless_call_function( functionName, context, args ) {
    var args = Array.prototype.slice.call( arguments ).splice( 2 );
    var namespaces = functionName.split( "." );
    var func = namespaces.pop();
    for( var i = 0; i < namespaces.length; i++ ) {
      context = context[namespaces[i]];
    }
    return context[func].apply( this, args );
  }


  /**
   * { Missing Description}
   *
   */
  function flawless_added_custom_post_type( row ) {
    jQuery( '.flawless_added_post_type', row ).val( 'true' );
  }


  /**
   * { Missing Description}
   *
   */
  function flawless_added_custom_taxonomy( row ) {
    jQuery( '.flawless_added_taxonomy', row ).val( 'true' );
  }

