<?php

  //** Ensure cfct_build_module class is loaded, BuddyPress is active and the Group Component is available  */
  if ( !class_exists( 'cfct_build_module' ) || !BP_VERSION || !function_exists( 'bp_has_groups' ) ) {
    return;
  }

  /**
   * Flawless Group Loop Module
   *
   * Used to query BuddyPress Groups when BuddyPress is active.
   *
   * @author potanin@UD
   */
	class f_cb_bp_forum_module extends cfct_build_module {

    public function __construct( ) {
    	$opts = array(
        'description' => __( 'Display a BuddyPress forum.', 'flawless' ),
        'icon' => 'bp-forum/bp-forum.png'
    	 );

    	parent::__construct( 'cfct-module-loop', __( 'BuddyPress Forum', 'flawless' ), $opts );

      $this->init( );

    }

    protected function init( ) {
    	// do this at init 'cause we can't do intl in member declarations
    	$this->content_display_options = array(
        'title' => __( 'Titles Only', 'carrington-build' ),
        'excerpt' => __( 'Excerpts', 'carrington-build' ),
        'content' => __( 'Post Content', 'carrington-build' ),
        'advanced' => __( 'Advanced ( Custom )', 'carrington-build' )
    	 );

    }

    public function display( $data ) {
      global $groups_template, $bp;
 
      ob_start();

      ?>

      <script type="text/javascript">
        jQuery( document ).ready(function() {

          if( typeof jQuery.fn.dynamic_filter == 'function' ) {

            jQuery( '.bp-forum' ).dynamic_filter({
              settings: {
                server_driven: true,
                debug: true,
                per_page: 20,
                dom_limit: 200,
                unique_tag: 'topic_id',
                messages: {
                  server_error: false
                }
              },
              ajax: {
                url: '<?php echo admin_url('admin-ajax.php'); ?>?action=bp_get_topics',
                forum_id: '<?php echo $data[$this->get_field_id( 'forum_id' )][0]; ?>'
              },
              classes: {
                inputs_list_wrapper: 'inputs_list_wrapper cfct-module'
              },
              ux: {
                filter: jQuery('<div class="filter cfct-block block-25"></div>'),
                results_wrapper: jQuery('<div class="results_wrapper cfct-block block-75"></div>'),
                results: jQuery('<div class="results cfct-module"></div>'),
                status: jQuery('<div class="alert alert-success"></div>')
              },
              attributes: {
                search_terms: {
                  label: 'Search',
                  sort_order: 1,
                  filter_type: 'input',
                  filter: true
                },
                topic_title: {
                  label: 'Topic Title',
                  sort_order: 10,
                  display: true,
                  render_callback: function( default_value, args ) {
                    return '<a href="' + args.result_row.topic_link + '">' + default_value + '</a>'
                  }
                },
                topic_tags: {
                  label: 'Tags',
                  sort_order: 55,
                  display: true,
                  render_callback: function( default_value, args ) {
                    return '<span class="product_name">' + args.result_row.object_name + '</span>' + ( default_value != '' ? ': ' + default_value : '' );
                  }
                },
                freshness: {
                  label: 'Freshness',
                  sort_order: 90,
                  display: true,
                  render_callback: function( default_value, args ) {
                    return 'Freshness ' + default_value;
                  }
                },
                topic_type: {
                  label: 'Topic Types',
                  display: false,
                  filter: true,
                  values: {
                    regular_event: {
                      label: 'Premium Request'
                    },
                    major_event: {
                      label: 'Regular Topic'
                    }
                  },
                },
                topic_time: {
                  label: 'Time',
                  display: false,
                  filter: false
                }
              }
            });

          } else {
            //flawless.add_notice('Dynamic Filter disabled.');
          }

        });
      </script>

      <div class="dynamic_filter bp-forum"></div>

      <?php

      $html = ob_get_contents();
      ob_end_clean();


      return $html;

    }


    /**
     * Output the Admin Form
     *
     * @author potanin@UD
     * @return string HTML
     */
    public function admin_form( $data ) {
      global $wpdb, $bp;

      $active_groups = new BP_Groups_Template( 0, 'active', 1, false, false, false, '', true, false, false, false );

      $attribute_fields = apply_filters( 'bb_groups_module_attribute_fields', array(
        'avatar' => array(
          'label' => __( 'Avatar', 'flawless' )
        ),
        'title' => array(
          'label' => __( 'Title', 'flawless' )
        ),
        'description' => array(
          'label' => __( 'Description', 'flawless' )
        )
      ) );

      $html[] = '<div id="'.$this->id_base.'-admin-form-wrapper" class="'.$this->id_base.'-admin-form-wrapper">';
    	ob_start( ); ?>

      <style type="text/css">
        .cfct-module-loop-admin-form-wrapper {
          overflow: hidden;
        }

        .cfct-module-loop-admin-form-wrapper .ui_panel {
          width: 50%;
          float: left;
        }

        .cfct-module-loop-admin-form-wrapper .ui_panel .group_item {
          border-bottom: 1px solid #E3E3E3;
          margin-bottom: 2px;
          padding-bottom: 4px;
        }

        .cfct-module-loop-admin-form-wrapper .ui_panel .group_item .avatar {
          margin: 0 3px;
          position: relative;
          top: 6px;
        }

        .cfct-module-loop-admin-form-wrapper input.short {
          width: 30px;
        }

        }

      </style>

      <div class="ui_panel left">

        <ul class="options-list">

        </ul>

        <ul class="options-list">
          <?php foreach( $active_groups->groups as $group ) {  ?>
          <li class="group_item">
            <label>
              <?php echo bp_core_fetch_avatar( array( 'item_id' => $group->id, 'object' => 'group', 'type' => 'thumb', 'avatar_dir' => 'group-avatars',  'width' => 20, 'height' => 20 ) ); ?>
              <input type="radio" name="<?php echo $this->get_field_name(  'forum_id' ); ?>[]" <?php echo in_array( $group->id, ( array ) $data[$this->get_field_name( 'forum_id' )] ) ? 'checked="checked"' : '' ?> value="<?php echo $group->id; ?>" />
              <input type="hidden" name="<?php echo $this->get_field_name(  'group_names' ); ?>[<?php echo $group->id; ?>]"  value="<?php echo $group->name; ?>" />
              <?php echo $group->name; ?>
            </label>
          </li>
          <?php } ?>
        </ul>

      </div>

      <div class="ui_panel right">

        <div class="list-description"><?php _e( 'Attributes to Display', 'flawless' ); ?></div>
        <ul class="options-list wp-tab-panel">
          <?php foreach( $attribute_fields as $key => $this_data ) {  ?>

          <li>
            <label>
              <input type="checkbox" name="<?php echo $this->get_field_name( 'attributes' ); ?>[]" <?php echo in_array( $key, ( array ) $data[$this->get_field_name( 'attributes' )] ) ? 'checked="checked"' : '' ?> value="<?php echo $key; ?>" />
              <?php echo $this_data['label']; ?>
            </label>
          </li>
          <?php } ?>

        </ul>
      </div>

      <?php $html[] = ob_get_contents( );
      ob_end_clean( );

      $html[] = '</div>';

      $html = apply_filters( 'f_cb_bp_forum_module::admin_form', $html, $this );

      //** Unset Global variable so as not to screw up something inadvertently */
      unset( $groups_template );

      return implode( '', ( array ) $html );

    }


    /**
     * Display custom text for a saved module in the builder
     *
     * @author potanin@UD
     * @return string HTML
     */
     public function text( $data ) {

      $group_names = $data[$this->get_field_id( 'group_names' )];
      $groups = $data[$this->get_field_id( 'forum_id' )];

      $print = array( );

      foreach( ( array ) $groups as $group_id ) {
        $group_name = $group_names[ $group_id ];

        if( !empty( $group_name  ) ) {
          $print[] = $group_name;
        }
      }

      if( !empty( $print ) ) {
        //** If we have labels, print them */
        return implode( ', ', ( array ) $print );

      } elseif( !empty( $groups ) ) {
        //** If no labels but have a count */
        return sprintf( __( ' Selected %1s groups.' ,'flawless' ), count( $groups ) );

      } else {
        //** Got nothing.. */
        return __( 'No forum selected.', 'flawless' );
      }

    }




	}

  //** Register the module */
	cfct_build_register_module( 'f_cb_bp_forum_module' );

