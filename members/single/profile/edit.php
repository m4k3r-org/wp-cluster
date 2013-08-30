<?php do_action( 'bp_before_profile_edit_content' );

if ( bp_has_profile( 'profile_group_id=' . bp_get_current_profile_group_id() ) ) :
  while ( bp_profile_groups() ) : bp_the_profile_group(); ?>

<ul class="nav nav-pills button-nav">
  <?php bp_profile_group_tabs(); ?>
</ul>
<div class="clearfix"></div>

<form action="<?php bp_the_profile_group_edit_form_action(); ?>" method="post" id="profile-edit-form" class="form-horizontal standard-form <?php bp_the_profile_group_slug(); ?>">
  <fieldset>
    <?php do_action( 'bp_before_profile_field_content' ); ?>
    <?php /* <legend><?php printf( __( "Editing '%s' Profile Group", "buddypress" ), bp_get_the_profile_group_name() ); ?></legend> */ ?>
    <div class="clearfix"></div>

    <?php while ( bp_profile_fields() ) : bp_the_profile_field(); ?>

      <div<?php bp_field_css_class( 'control-group' ) ?>>

        <?php if ( 'textbox' == bp_get_the_profile_field_type() ) : ?>
          <label class="control-label" for="<?php bp_the_profile_field_input_name(); ?>"><?php bp_the_profile_field_name(); ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'buddypress' ); ?><?php endif; ?></label>
          <div class="controls input">
            <input type="text" name="<?php bp_the_profile_field_input_name(); ?>" id="<?php bp_the_profile_field_input_name(); ?>" value="<?php bp_the_profile_field_edit_value(); ?>" <?php if ( bp_get_the_profile_field_is_required() ) : ?>aria-required="true"<?php endif; ?>/>
        <?php endif; ?>

        <?php if ( 'textarea' == bp_get_the_profile_field_type() ) : ?>
          <label class="control-label" for="<?php bp_the_profile_field_input_name() ?>"><?php bp_the_profile_field_name(); ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'buddypress' ); ?><?php endif; ?></label>
          <div class="controls">
            <textarea rows="5" cols="40" name="<?php bp_the_profile_field_input_name(); ?>" id="<?php bp_the_profile_field_input_name(); ?>" <?php if ( bp_get_the_profile_field_is_required() ) : ?>aria-required="true"<?php endif; ?>><?php bp_the_profile_field_edit_value(); ?></textarea>
        <?php endif; ?>

        <?php if ( 'selectbox' == bp_get_the_profile_field_type() ) : ?>
          <label class="control-label" for="<?php bp_the_profile_field_input_name(); ?>"><?php bp_the_profile_field_name(); ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'buddypress' ); ?><?php endif; ?></label>
          <div class="controls">
            <select name="<?php bp_the_profile_field_input_name(); ?>" id="<?php bp_the_profile_field_input_name(); ?>" <?php if ( bp_get_the_profile_field_is_required() ) : ?>aria-required="true"<?php endif; ?>>
              <?php bp_the_profile_field_options() ?>
            </select>
        <?php endif; ?>

        <?php if ( 'multiselectbox' == bp_get_the_profile_field_type() ) : ?>
          <label class="control-label" for="<?php bp_the_profile_field_input_name() ?>"><?php bp_the_profile_field_name() ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'buddypress' ); ?><?php endif; ?></label>
          <div class="controls">
            <select name="<?php bp_the_profile_field_input_name() ?>" id="<?php bp_the_profile_field_input_name() ?>" multiple="multiple" <?php if ( bp_get_the_profile_field_is_required() ) : ?>aria-required="true"<?php endif; ?>>
              <?php bp_the_profile_field_options(); ?>
            </select>
            <?php if ( !bp_get_the_profile_field_is_required() ) : ?>
              <a class="clear-value" href="javascript:bp_clear_profile_field( '<?php bp_the_profile_field_input_name(); ?>' );"><?php _e( 'Clear', 'buddypress' ); ?></a>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ( 'radio' == bp_get_the_profile_field_type() ) : ?>
          <label class="control-label"><?php bp_the_profile_field_name(); ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'buddypress' ); ?><?php endif; ?></label>
          <div class="controls radio">
            <?php bp_the_profile_field_options(); ?>
            <?php if ( !bp_get_the_profile_field_is_required() ) : ?>
              <a class="clear-value" href="javascript:bp_clear_profile_field( '<?php bp_the_profile_field_input_name(); ?>' );"><?php _e( 'Clear', 'buddypress' ); ?></a>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ( 'checkbox' == bp_get_the_profile_field_type() ) : ?>
          <label class="control-label"><?php bp_the_profile_field_name(); ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'buddypress' ); ?><?php endif; ?></label>
          <div class="controls checkbox">
            <?php bp_the_profile_field_options(); ?>
        <?php endif; ?>

        <?php if ( 'datebox' == bp_get_the_profile_field_type() ) : ?>
          <label class="control-label" for="<?php bp_the_profile_field_input_name(); ?>_day"><?php bp_the_profile_field_name(); ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'buddypress' ); ?><?php endif; ?></label>
          <div class="controls datebox">
            <select name="<?php bp_the_profile_field_input_name(); ?>_day" id="<?php bp_the_profile_field_input_name(); ?>_day" <?php if ( bp_get_the_profile_field_is_required() ) : ?>aria-required="true"<?php endif; ?>>
              <?php bp_the_profile_field_options( 'type=day' ); ?>
            </select>
            <select name="<?php bp_the_profile_field_input_name() ?>_month" id="<?php bp_the_profile_field_input_name(); ?>_month" <?php if ( bp_get_the_profile_field_is_required() ) : ?>aria-required="true"<?php endif; ?>>
              <?php bp_the_profile_field_options( 'type=month' ); ?>
            </select>
            <select name="<?php bp_the_profile_field_input_name() ?>_year" id="<?php bp_the_profile_field_input_name(); ?>_year" <?php if ( bp_get_the_profile_field_is_required() ) : ?>aria-required="true"<?php endif; ?>>
              <?php bp_the_profile_field_options( 'type=year' ); ?>
            </select>
        <?php endif; ?>

        <?php do_action( 'bp_custom_profile_edit_fields' ); ?>

        <p class="help-block"><?php bp_the_profile_field_description(); ?></p>
        </div>
      </div>

    <?php endwhile; ?>

  <?php do_action( 'bp_after_profile_field_content' ); ?>

  <div class="submit">
    <input type="submit" class="btn btn-primary" name="profile-group-edit-submit" id="profile-group-edit-submit" value="<?php _e( 'Save Changes', 'buddypress' ); ?> " />
  </div>

  <input type="hidden" name="field_ids" id="field_ids" value="<?php bp_the_profile_group_field_ids(); ?>" />

  <?php wp_nonce_field( 'bp_xprofile_edit' ); ?>
</fieldset>
</form>

<?php endwhile; endif; ?>

<?php do_action( 'bp_after_profile_edit_content' ); ?>
