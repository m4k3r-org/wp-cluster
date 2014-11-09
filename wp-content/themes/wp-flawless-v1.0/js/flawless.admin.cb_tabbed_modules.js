/**
 * Carrington Build - Tabbed Modules
 *
 * @since Flawless 0.3.4
 * @author Usability Dynamics, Inc.
 *
 * jslint devel: true, undef: true, browser: true, sloppy: true, vars: true, white: true, plusplus: true, maxerr: 50, indent: 2
 *
 */

jQuery( document ).ready( function() {

  if( typeof cfct_builder !== 'object' ) {
    return;
  }

  if( typeof jQuery.fn.tabs !== 'function' ) {
    return;
  }

  /**
   * If a configuration variable exists, we prepare it.
   *
   * {missing description}
   *
   * @author potanin@UD
   */
  if( typeof cb_ud_tabbed_blocks === 'object' ) {
    jQuery.each( cb_ud_tabbed_blocks, function( index, block ) {

      /* Delete reference from array */
      delete cb_ud_tabbed_blocks[ index ];

      var the_block = jQuery( '#' + block.id );

      if( !the_block.length || typeof block.tabbed_sections != 'object' ) {
        return;
      }

      cb_ud_tabbed_blocks[ block.id ] = {};

      /* Cycle through saved settings and transform into a more useful array */
      jQuery.each( block.tabbed_sections , function( section_index, single_section ) {

        jQuery.each( single_section.tabs , function( tab_index, tab_label ) {

          cb_ud_tabbed_blocks[ block.id ][ tab_index ] = {
            tab_label: tab_label
          }

          if( typeof single_section.modules != 'undefined' && typeof single_section.modules[ tab_index ] != 'undefined' ) {
            cb_ud_tabbed_blocks[ block.id ][ tab_index ].modules = single_section.modules[ tab_index ];
          }

        });

      });

    });

  }

  jQuery( cfct_builder ).bind( 'add-module submit-module-form-response edit-module-response reorder-modules-response do-remove-row-response tabs_changed',  function( e ) {
    save_tabbed_blocks();
  })

  jQuery( cfct_builder ).bind( 'new-row-inserted',  function(e) {
    cb_ud_prepare_blocks();
  });

  jQuery( cfct_builder ).bind( 'cb_ud_tabbed_blocks::destroy_tab_section',  function( e, args ) {

    args.modules = jQuery( '.cfct-module', args.tabs_element );

    /* Move each elemenet out of the tabs */
    jQuery( args.modules ).each( function() {
      jQuery( this ).appendTo( args.block_modules );
    });

    args.tabs_element.remove();

    jQuery( args.block_modules ).removeAttr( 'has_active_tabbed_blocks' );

    cfct_messenger.setMessage( 'Tabbed module removed.', 'confirm' );
    jQuery( cfct_builder ).trigger("tabs_changed");
  });

  /* It saves CB data by click on Publish, Save Daraft & Preview buttons
   * @author odokienko@UD
   **/
  jQuery( '#submitdiv :input[type=submit][name=save]:visible,#submitdiv :input[type=submit][name=publish]:visible,a#post-preview' ).click(function () {
    if (typeof cfct_builder == 'object' ){
      jQuery( cfct_builder ).trigger("tabs_changed");
    }
  });

  /**
   * {missing summary}
   *
   * Can't use jQuery Tabs because of some conflict that occurs after the CB popup window closes. Seems to be related to: http://core.trac.wordpress.org/ticket/19189
   *
   * @author potanin@UD
   */
  jQuery( cfct_builder ).bind( 'cb_ud_tabbed_blocks::add_new_tab',  function( e, args ) {

    if( typeof args.tab_label === 'undefined' ) {
      args.tab_label = '';
    }

    /* Diffirentiates between user clicking Add Tab, or saved tabs loading automatically */
    args.auto_load = false;

    args.tab_container = jQuery( '.cb_ud_tab_container', args.tabs_element );
    args.panel_container = jQuery( '.cb_ud_panel_container', args.tabs_element );

    /* Hide All! */
    jQuery( '.cb_ud_tab', args.tab_container ).removeClass( 'selected_tab' );
    jQuery( '.cb_ud_tab_panel', args.panel_container ).hide();

    args.this_tab = jQuery( '<li class="cb_ud_tab selected_tab"><input type="text" class="cb_ud_tab_label" value="' + args.tab_label + '" /> <span class="cb_ud_tab_action" cb_ud_tab_action="delete_tab" alt="Delete Tab">&times;</span></li>' );
    args.this_panel = jQuery( '<div class="cb_ud_tab_panel"></div>' );

    jQuery( args.tab_container ).append( args.this_tab );
    jQuery( args.panel_container ).append( args.this_panel );

    jQuery( '.cb_ud_tab_label', args.tab_container ).change(function () {
      jQuery( cfct_builder ).trigger("tabs_changed");
    });

    /* Associate panel with tab */
    args.this_tab.data( 'panel' , args.this_panel );

    /* Load any passed modules - move them from wherever they are into the panel of this tab */
    if( typeof args.modules === 'object' ) {

      jQuery.each( args.modules, function( module_index, module_id ) {
        jQuery( '#' + module_id + '.cfct-module' ).appendTo( args.this_panel );
      });

      args.auto_load = true;

    }

    jQuery( args.this_tab ).click( function() {

      jQuery( '.cb_ud_tab', args.tab_container ).removeClass( 'selected_tab' );
      jQuery( '.cb_ud_tab_panel', args.panel_container ).hide();

      jQuery( this ).addClass( 'selected_tab' );
      jQuery( this ).data( 'panel' ).show();

    });

    jQuery( '.cb_ud_tab_action[cb_ud_tab_action="delete_tab"]', args.this_tab ).click( function() {

      args.this_tab = jQuery( this ).closest( '.cb_ud_tab' );
      args.this_panel =  jQuery( args.this_tab ).data( 'panel' );

      /* Save the modules */
      jQuery( '.cfct-module', args.this_panel ).appendTo( args.block_modules );

      args.this_tab.remove();
      args.this_panel.remove();

      jQuery( '.cb_ud_tab:first', args.tab_container ).addClass( 'selected_tab' );
      jQuery( '.cb_ud_tab_panel:first', args.panel_container ).show();

      jQuery( cfct_builder ).trigger("tabs_changed");

    });

    /* Conect the new droppable tabs with modules. */
    jQuery( '.cb_ud_tab_panel' ).sortable({
      connectWith: jQuery( '.cfct-block-modules.ui-sortable' ),
      stop: cfct_builder.updateModuleOrderEnd
    });

    /* Connect us with all the modules out there. */
    jQuery( '.cfct-block-modules.ui-sortable' ).sortable( 'option', 'connectWith', jQuery( '.cb_ud_tab_panel' ) );

    /* Focus on input of new tab */
    if( !args.auto_load ) {
      jQuery( 'input.cb_ud_tab_label', args.this_tab ).focus();
    }

    /* console.dir( args ); */

  });

  cb_ud_prepare_blocks();

});

/**
 * Add Button to all blocks to create Tab Menu
 *
 * Cycles through all elements containing the "Add Module" link and inserts our links after it.
 *
 * @author potanin@UD
 */
function save_tabbed_blocks() {
  var blocks = [];
  console.log( 'submit-module-form-response()' );

  /* Cycle through the blocks.  The ones with tabs will have the attribute */
  jQuery( 'td.cfct-block[has_active_tabbed_blocks = "true"]' ).each( function() {

    var block = {
      id: jQuery( this ).attr( 'id' ),
      tabbed_sections: {}
    };

    jQuery( '.cb_ud_tab_container_wrapper', this ).each( function( section_index ) {

      block.tabbed_sections[ section_index ] = {
        tabs: {},
        modules: {}
      };

      jQuery( 'input.cb_ud_tab_label', this ).each( function( tab_index ) {
        block.tabbed_sections[ section_index ].tabs[ tab_index ] = jQuery( this ).val();
      });

      jQuery( 'div.cb_ud_tab_panel', this ).each( function( tab_index ) {

        block.tabbed_sections[ section_index ].modules[ tab_index ] = [];

        jQuery( 'div.cfct-module', this ).each( function( module_count ) {
          block.tabbed_sections[ section_index ].modules[ tab_index ][ module_count ] = jQuery( this ).attr( 'id' );
        });

      });

    });

    blocks.push( block );

  });

  jQuery( '.cfct-block-modules.ui-sortable' ).sortable( 'option', 'connectWith', jQuery( '.cb_ud_tab_panel' ) );

  jQuery.ajax({
    url: ajaxurl,
    data: {
      action: 'flawless_cb_save_tabbed_blocks',
      args: {
        post_id: jQuery( '#post_ID' ).val(),
        blocks: blocks
      }
    },
    success: function( result ) {

      if( result.success === 'true' ) {
        cfct_messenger.setMessage('Tab settngs saved.','confirm');
      } else {
        cfct_messenger.setMessage('There was a problem saving tab settings.','confirm');
      }

    },
    dataType: 'json'

  });


}


/**
 * Add Button to all blocks to create Tab Menu
 *
 * Cycles through all elements containing the "Add Module" link and inserts our links after it.
 *
 * @author potanin@UD
 */
function cb_ud_prepare_blocks() {
  /* console.log( 'cb_ud_prepare_blocks()' ); */

  jQuery( 'td.cfct-build-add-module[has_tabbed_blocks_option != "true"]' ).each( function() {

    var add_module_wrapper = this;
    var add_module_button = jQuery( '.cfct-add-new-module', this );
    var the_block = jQuery( jQuery( add_module_button ).attr( 'href' ) );
    var block_id = the_block.attr( 'id' );

    var create_tabs_button = jQuery( '<p><a href="#add_block_tabs" class="cfct-make-tabbed-module">Add Tabbed Section</a></p> ' );

    jQuery( add_module_wrapper ).append( create_tabs_button );

    /* Add our custom attributes for reference */
    jQuery( add_module_wrapper ).attr( 'has_tabbed_blocks_option', 'true' );
    jQuery( the_block ).attr( 'has_tabbed_blocks_option', 'true' );

    /* Check if setings exist already */
    if( typeof cb_ud_tabbed_blocks === 'object' && typeof cb_ud_tabbed_blocks[ block_id ] === 'object' ) {
      add_tab_ui_to_block( the_block,  {
        tabbed_sections: cb_ud_tabbed_blocks[ block_id ]
      });
    }

    /* When our Add Tabs button is clicked, things happen */
    jQuery( create_tabs_button ).click( function( e)  {
      e.preventDefault();
      add_tab_ui_to_block( the_block );
    });


  });

}


/**
 * Adds UI to Block for modules to be dragged into
 *
 * Cycles through all elements containing the "Add Module" link and inserts our links after it.
 *
 * @author potanin@UD
 */
function add_tab_ui_to_block( the_block, args ) {
  /* console.log( 'add_tab_ui_to_block()' ); */

  args = jQuery.extend( true, { }, args );

  var block_modules = jQuery( '.cfct-block-modules', the_block );

  var tabs = jQuery(
  '<div class="cb_ud_tab_container_wrapper">' +
    '<ul class="cb_ud_tab_container"></ul>' +
    '<div class="cb_ud_panel_container"></div>' +
    '<div class="cb_ud_tab_options"><span class="cb_ud_tab_action" cb_ud_tab_action="add_new_tab">Add Another Tab</span><span class="cb_ud_tab_action" cb_ud_tab_action="destroy_tab_section">Remove Tab Module</span></div>' +
  '</div>'
  );

  jQuery( block_modules ).prepend( tabs );

  /* Must be added to a block to be saved */
  jQuery( the_block ).attr( 'has_active_tabbed_blocks', 'true' );

  var trigger_args = {
    tabs_element: tabs,
    block_modules: block_modules,
    the_block: the_block
  };

  if( typeof args.tabbed_sections === 'object' ) {

    /* Load saved configuraiton into blocks */
    jQuery.each( args.tabbed_sections, function( index, tab_data ) {

      jQuery( cfct_builder ).trigger( 'cb_ud_tabbed_blocks::add_new_tab', jQuery.extend( true, {
        tab_label: tab_data.tab_label,
        modules: tab_data.modules
      }, trigger_args ));

    })

    /* Focus on first */
    jQuery( '.cb_ud_tab', trigger_args.tabs_element ).removeClass( 'selected_tab' );
    jQuery( '.cb_ud_tab:first', trigger_args.tabs_element ).addClass( 'selected_tab' );
    jQuery( '.cb_ud_tab_panel:not(:first)', trigger_args.tabs_element ).hide();
    jQuery( '.cb_ud_tab_panel:first', trigger_args.tabs_element ).show();

  } else {
    jQuery( cfct_builder ).trigger( 'cb_ud_tabbed_blocks::add_new_tab', trigger_args );

  }

  /* Monitor for tab actions */
  jQuery( '.cb_ud_tab_options .cb_ud_tab_action', tabs ).click( function() {
    /* console.log( 'Trigger: ' + 'cb_ud_tabbed_blocks::' + jQuery( this ).attr( 'cb_ud_tab_action' ) );*/
    jQuery( cfct_builder ).trigger( 'cb_ud_tabbed_blocks::' + jQuery( this ).attr( 'cb_ud_tab_action' ), jQuery.extend( true, trigger_args, { trigger: this } ) );
  });

  cfct_messenger.setMessage( 'Tab interface added to block.', 'confirm' );

}
