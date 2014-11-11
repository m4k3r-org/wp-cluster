<?php

  //** Ensure cfct_build_module class is loaded, BuddyPress is active and the Group Component is available  */
  if ( !class_exists('cfct_build_module') || !BP_VERSION || !function_exists( 'bp_has_groups' ) ) {
    return;
  }

  /**
   * Flawless Group Loop Module
   *
   * Used to query BuddyPress Groups when BuddyPress is active.
   *
   * @author potanin@UD
   */
	class f_cb_bp_group_module extends cfct_build_module {

    public function __construct() {
    	$opts = array(
        'description' => __('Choose and display a set of BuddyPress groups.', 'flawless'),
        'icon' => 'bp-groups/bp-groups.png'
    	);

    	parent::__construct('cfct-module-loop', __('BuddyPress Groups', 'flawless'), $opts);

      $this->init();

    }

    protected function init() {
    	// do this at init 'cause we can't do intl in member declarations
    	$this->content_display_options = array(
        'title' => __('Titles Only', 'carrington-build'),
        'excerpt' => __('Excerpts', 'carrington-build'),
        'content' => __('Post Content', 'carrington-build'),
        'advanced' => __('Advanced (Custom)', 'carrington-build')
    	);

    }

    public function display($data) {
      global $groups_template, $bp;

      $_groups_template = $groups_template;

       if ( !bp_has_groups( 'user_id=0&type=active&include=' . implode( ',', (array) $data[$this->get_field_id('groups')] ) ) ) {
        return;
      }

      $atts = array(
        'item_class' => ''
      );

      $html = array();
      
      $prepare_text = create_function( '$content', '  return empty( $content ) ? false : stripslashes( $content ); ');

      $html[] = '<div class="groups dir-list shortcode clearfix">';
      $html[] = '<ul class="groups-list item-list clearfix">';

      while ( bp_groups() ) {

        bp_the_group();
        
        $html[] = '<li class="list-item  ' . $atts['item_class'] . ' clearfix">';
        $html[] = '<div class="cfct-module clearfix">';
        
        foreach( (array) $data[$this->get_field_id('attributes')] as $meta_key ) {

          $this_value = false;

          switch( $meta_key ) {

            case 'avatar':
              $this_value = '<a href="' . bp_get_group_permalink() . '" title="' . bp_get_group_name() . '">' . bp_get_group_avatar() . '</a>';
            break;

            case 'title':
              $this_value = '<a href="' . bp_get_group_permalink() . '" class="bp_group_link" title="' . bp_get_group_name() . '">' . bp_get_group_name() . '</a>';
            break;

            case 'description':
              $this_value = bp_get_group_description();
            break;

            default:            
              $this_value = $prepare_text( groups_get_groupmeta( $groups_template->groups[ $groups_template->current_group ]->id, $meta_key ) ) ;
            break;

          }
          
          $this_value = do_shortcode( $this_value );
          
          if( $this_value ) {
            $html[] = '<div class="item-' . $meta_key . '">' . $this_value . '</div>';
          }          

        }

        $html[] = '</div>';
        $html[] = '</li>';

      }

      $html[] = '</ul>';
      $html[] = '</div>';

      /* Execute shortcodes before we unset the $group_template variable back */
      $html = implode( '', (array) $html );
      
      $groups_template = $_groups_template;
      
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
    	ob_start(); ?>

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
        
        <?php /* 
        <ul class="options-list">
          <li>
            <label>
              <?php _e( 'Title', 'flawless' ); ?>
              <input type="text" id="<?php echo $this->get_field_id('content'); ?>" name="<?php echo $this->get_field_name('content'); ?>" value="<?php echo $data[$this->get_field_name('content')]; ?>" />
            </label>
          </li>
        </ul>
        */ ?>

        <ul class="options-list">
        <?php /*
          <li>
            <label>
              <input type="checkbox" id="<?php echo $this->get_field_id('group_query'); ?>" name="<?php echo $this->get_field_name('group_query'); ?>" <?php checked( 'all', $data[$this->get_field_name('group_query')] ); ?> value="all" />
              <?php _e( 'All active groups.', 'flawless' ); ?>
            </label>
          </li>

          <li>
            <label>
              <input type="checkbox" id="<?php echo $this->get_field_id('show_all_groups'); ?>" name="<?php echo $this->get_field_name('show_all_groups'); ?>" <?php checked( 'all', $data[$this->get_field_name('show_all_groups')] ); ?> value="all" />
              <?php _e( 'All active groups.', 'flawless' ); ?>
            </label>
          </li>
          <li>
            <label>
              <?php _e( 'Maximum Groups: ', 'flawless' ); ?>
              <input type="text" class="short" id="<?php echo $this->get_field_id('max_groups'); ?>" name="<?php echo $this->get_field_name('max_groups'); ?>" value="<?php $data[$this->get_field_name('group_query')]; ?>" />
            </label>
          </li>
        */ ?>

        </ul>

        <ul class="options-list">
          <?php foreach( $active_groups->groups as $group ) {  ?>
          <li class="group_item">
            <label>
              <?php echo bp_core_fetch_avatar( array( 'item_id' => $group->id, 'object' => 'group', 'type' => 'thumb', 'avatar_dir' => 'group-avatars',  'width' => 20, 'height' => 20 ) ); ?>
              <input type="checkbox" name="<?php echo $this->get_field_name(  'groups' ); ?>[]" <?php echo in_array( $group->id, (array) $data[$this->get_field_name('groups')] ) ? 'checked="checked"' : '' ?> value="<?php echo $group->id; ?>" />
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
              <input type="checkbox" name="<?php echo $this->get_field_name( 'attributes' ); ?>[]" <?php echo in_array( $key, (array) $data[$this->get_field_name('attributes')] ) ? 'checked="checked"' : '' ?> value="<?php echo $key; ?>" />
              <?php echo $this_data['label']; ?>
            </label>
          </li>
          <?php } ?>

        </ul>
      </div>

      <?php $html[] = ob_get_contents();
      ob_end_clean();

      $html[] = '</div>';

      $html = apply_filters( 'f_cb_bp_group_module::admin_form', $html, $this);

      //** Unset Global variable so as not to screw up something inadvertently */
      unset($groups_template);

      return implode( '', (array) $html );

    }

    
    /**
     * Display custom text for a saved module in the builder
     *
     * @author potanin@UD
     * @return string HTML
     */
     public function text( $data ) {
        
      $group_names = $data[$this->get_field_id('group_names')];      
      $groups = $data[$this->get_field_id('groups')];
      
      $print = array();
      
      foreach( (array) $groups as $group_id ) {
        $group_name = $group_names[ $group_id ];
        
        if( !empty( $group_name  ) ) {
          $print[] = $group_name;
        }
      }
      
      if( !empty( $print ) ) {
        //** If we have labels, print them */
        return implode( ', ', (array) $print );
        
      } elseif( !empty( $groups ) ) {
        //** If no labels but have a count */
        return sprintf( __( ' Selected %1s groups.' ,'flawless'), count( $groups ) );
        
      } else {
        //** Got nothing.. */
        return __( 'No selected groups.', 'flawless' );      
      }
      
    }




	}

  //** Register the module */
	cfct_build_register_module('f_cb_bp_group_module');

