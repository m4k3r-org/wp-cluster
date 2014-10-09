<?php  if( !apply_filters( 'skip_header', false ) ) { get_header( 'buddypress' ); } ?>

  <div class="<?php flawless_wrapper_class(); ?>">
    <div class="<?php flawless_block_class( 'main cfct-block' ); ?>">

    <?php do_action( 'bp_before_register_page' ) ?>
    <?php $wp_bp_nonce = md5(NONCE_KEY . time()); ?>
    <div class="page" id="register-page">

      <form action="" name="signup_form" id="signup_form" class="standard-form form-horizontal" method="post" enctype="multipart/form-data">

        <li class="wp_bp_<?php echo $wp_bp_nonce; ?>_first">
          <?php /* Spam Prevention */ ?>           
          <input type="hidden" name="action" value="process_bp_registration" />
          <input type="text" name="wp_wp_nonce" value="<?php echo $wp_bp_nonce; ?>" />
        
          <p>Account Details</p>
          <input type="text" name="username" />
          <input type="text" name="email" />
          <input type="password" name="password" />
          <input type="password" name="password2" />
          <input type="text" name="name" />
          <input type="text" name="occupation" />
          <input type="text" name="website" />
          <input type="hidden" name="wp_bp[success_message]" value="<?php echo esc_attr($success_message); ?>" />
        <?php /* Span Prevention */ ?>
      </li>
      <?php if ( 'registration-disabled' == bp_get_current_signup_step() ) : ?>
  
        <?php do_action( 'bp_before_registration_disabled' ) ?>

          <p><?php _e( 'User registration is currently not allowed.', 'buddypress' ); ?></p>

        <?php do_action( 'bp_after_registration_disabled' ); ?>
          
      <?php endif; // registration-disabled signup setp ?>

      <?php if ( 'request-details' == bp_get_current_signup_step() ) : ?>
          
    <h2><?php _e( 'Create an Account', 'buddypress' ) ?></h2>
        
    <p><?php _e( 'Registering for this site is easy, just fill in the fields below and we\'ll get a new account set up for you in no time.', 'buddypress' ) ?></p>

        <?php do_action( 'bp_before_account_details_fields' ) ?>

        <?php /***** Basic Account Details ******/ ?>
        <div class="register-section" id="basic-details-section">
          <fieldset>
          <legend><?php _e( 'Account Details', 'buddypress' ) ?></legend>
          <?php ob_start(); do_action( 'bp_signup_username_errors' ); $bp_signup_username_errors = ob_get_contents(); ob_end_clean();?>
          <div class="control-group<?php echo (!empty($bp_signup_username_errors))?' error':''; ?>">
            <label for="signup_username" class="control-label"><?php _e( 'Username', 'buddypress' ) ?></label>
            <div class="controls">
              <input type="text" name="signup_username" id="signup_username" value="<?php bp_signup_username_value() ?>" validation_ajax="flawless_signup_field_check"  required="required"  pattern="^[a-z0-9]{1,}$" title="<?php _e( 'Only lowercase letters and numbers allowed', 'buddypress' ) ?>" />
              <?php echo (!empty($bp_signup_username_errors)) ? "<span class='help-inline'>{$bp_signup_username_errors}</span>" : '';  ?>
            </div>
          </div>
          <?php ob_start(); do_action( 'bp_signup_email_errors' ); $bp_signup_email_errors = ob_get_contents(); ob_end_clean(); ?>
          <div class="control-group<?php echo (!empty($bp_signup_email_errors))?' error':''; ?>">
            <label for="signup_email" class="control-label"><?php _e( 'Email Address', 'buddypress' ) ?></label>
            <div class="controls">
              <input type="email" name="signup_email" id="signup_email" value="<?php bp_signup_email_value() ?>" required="required"  validation_ajax="flawless_signup_field_check" />
              <?php echo (!empty($bp_signup_email_errors)) ? "<span class='help-inline'>{$bp_signup_email_errors}</span>" : '';  ?>
            </div>
          </div>
          <?php ob_start(); do_action( 'bp_signup_password_errors' ); $bp_signup_password_errors = ob_get_contents(); ob_end_clean(); ?>
          <div class="control-group<?php echo (!empty($bp_signup_password_errors))?' error':''; ?>">
            <label for="signup_password" class="control-label"><?php _e( 'Choose a Password', 'buddypress' ) ?></label>
            <div class="controls">
              <input type="password" name="signup_password" id="signup_password" value="" required="required" />
              <?php echo (!empty($bp_signup_password_errors)) ? "<span class='help-inline'>{$bp_signup_password_errors}</span>" : '';  ?>
            </div>
          </div>
          <?php ob_start(); do_action( 'bp_signup_password_confirm_errors' ); $bp_signup_password_confirm_errors = ob_get_contents(); ob_end_clean(); ?>
          <div class="control-group<?php echo (!empty($bp_signup_password_confirm_errors))?' error':''; ?>">
            <label for="signup_password_confirm" class="control-label"><?php _e( 'Confirm Password', 'buddypress' ) ?></label>
            <div class="controls">
              <input type="password" name="signup_password_confirm" id="signup_password_confirm" value="" required="required" matches="signup_password" />
              <?php echo (!empty($bp_signup_password_confirm_errors)) ? "<span class='help-inline'>{$bp_signup_password_confirm_errors}</span>" : '';  ?>
            </div>
          </div>
          
        </fieldset><!-- #basic-details-section -->
        </div>
        <?php do_action( 'bp_after_account_details_fields' ) ?>

        <?php /***** Extra Profile Details ******/ ?>

        <?php if ( bp_is_active( 'xprofile' ) ) : ?>

          <?php do_action( 'bp_before_signup_profile_fields' ) ?>

          <div class="register-section" id="profile-details-section">
          <fieldset>
            <legend><?php _e( 'Profile Details', 'buddypress' ) ?></legend>
            

            <?php /* Use the profile field loop to render input fields for the 'base' profile field group */ ?>
            <?php if ( bp_is_active( 'xprofile' ) ) : if ( bp_has_profile( 'profile_group_id=1' ) ) : while ( bp_profile_groups() ) : bp_the_profile_group(); ?>

            <?php while ( bp_profile_fields() ) : bp_the_profile_field(); ?>
              <?php ob_start(); do_action( 'bp_' . bp_get_the_profile_field_input_name() . '_errors' ); $the_profile_field_input_name_errors = ob_get_contents(); ob_end_clean(); ?>
            <?php $bp_the_profile_field_type = bp_get_the_profile_field_type();?>
              <div class="editfield control-group<?php echo (!empty($the_profile_field_input_name_errors))?' error':''; ?>">
                <label for="<?php bp_the_profile_field_input_name() ?>" class="control-label"><?php bp_the_profile_field_name() ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'buddypress' ) ?><?php endif; ?></label>
                <div class="controls <?php echo $bp_the_profile_field_type;?>">

                <?php if ( 'textbox' == bp_get_the_profile_field_type() ) : ?>
                      <input type="text" name="<?php bp_the_profile_field_input_name() ?>" id="<?php bp_the_profile_field_input_name() ?>" value="<?php bp_the_profile_field_edit_value() ?>" <?php if ( bp_get_the_profile_field_is_required() ) : ?> required="required"<?php endif; ?> />
                <?php endif; ?>

                <?php if ( 'textarea' == bp_get_the_profile_field_type() ) : ?>
                      <textarea rows="5" cols="40" name="<?php bp_the_profile_field_input_name() ?>" id="<?php bp_the_profile_field_input_name() ?>" <?php if ( bp_get_the_profile_field_is_required() ) : ?> required="required"<?php endif; ?>><?php bp_the_profile_field_edit_value() ?></textarea>
                <?php endif; ?>

                <?php if ( 'selectbox' == bp_get_the_profile_field_type() ) : ?>
                    <select name="<?php bp_the_profile_field_input_name() ?>" id="<?php bp_the_profile_field_input_name() ?>" <?php if ( bp_get_the_profile_field_is_required() ) : ?> required="required"<?php endif; ?>>
                      <?php bp_the_profile_field_options() ?>
                    </select>
                <?php endif; ?>

                <?php if ( 'multiselectbox' == bp_get_the_profile_field_type() ) : ?>
                      <select name="<?php bp_the_profile_field_input_name() ?>" id="<?php bp_the_profile_field_input_name() ?>" multiple="multiple" <?php if ( bp_get_the_profile_field_is_required() ) : ?> required="required"<?php endif; ?>>
                        <?php bp_the_profile_field_options() ?>
                      </select>
                <?php endif; ?>

                <?php if ( 'radio' == bp_get_the_profile_field_type() ) : ?>
                    <?php bp_the_profile_field_options() ?>
                    <?php if ( !bp_get_the_profile_field_is_required() ) : ?>
                      <a class="clear-value" href="javascript:clear( '<?php bp_the_profile_field_input_name() ?>' );"><?php _e( 'Clear', 'buddypress' ) ?></a>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if ( 'checkbox' == bp_get_the_profile_field_type() ) : ?>
                      <?php bp_the_profile_field_options() ?>
                <?php endif; ?>

                <?php if ( 'datebox' == bp_get_the_profile_field_type() ) : ?>
                      <select name="<?php bp_the_profile_field_input_name() ?>_day" id="<?php bp_the_profile_field_input_name() ?>_day" <?php if ( bp_get_the_profile_field_is_required() ) : ?> required="required"<?php endif; ?>>
                        <?php bp_the_profile_field_options( 'type=day' ) ?>
                      </select>

                      <select name="<?php bp_the_profile_field_input_name() ?>_month" id="<?php bp_the_profile_field_input_name() ?>_month" <?php if ( bp_get_the_profile_field_is_required() ) : ?> required="required"<?php endif; ?>>
                        <?php bp_the_profile_field_options( 'type=month' ) ?>
                      </select>

                      <select name="<?php bp_the_profile_field_input_name() ?>_year" id="<?php bp_the_profile_field_input_name() ?>_year" <?php if ( bp_get_the_profile_field_is_required() ) : ?> required="required"<?php endif; ?>>
                        <?php bp_the_profile_field_options( 'type=year' ) ?>
                      </select>

                <?php endif; ?>

                <?php do_action( 'bp_custom_profile_edit_fields' ) ?>

                <?php echo (!empty($the_profile_field_input_name_errors)) ? "<span class='help-inline'>{$the_profile_field_input_name_errors}".bp_get_the_profile_field_description()."</span>" : ''; ?>
                </div>
              </div>

            <?php endwhile; ?>

            <input type="hidden" name="signup_profile_field_ids" id="signup_profile_field_ids" value="<?php bp_the_profile_group_field_ids() ?>" />

            <?php endwhile; endif; endif; ?>
            </fieldset>
          </div><!-- #profile-details-section -->

          <?php do_action( 'bp_after_signup_profile_fields' ) ?>

        <?php endif; ?>

        <?php if ( bp_get_blog_signup_allowed() ) : ?>

          <?php do_action( 'bp_before_blog_details_fields' ) ?>

          <?php /***** Blog Creation Details ******/ ?>

          <div class="register-section" id="blog-details-section">
          <fieldset>
            <legend><?php _e( 'Blog Details', 'buddypress' ) ?></legend>
              <div class="control-group">
                <div class="controls">
                  <label class="checkbox"><input type="checkbox" name="signup_with_blog" id="signup_with_blog" value="1"<?php if ( (int) bp_get_signup_with_blog_value() ) : ?> checked="checked"<?php endif; ?> /> <?php _e( 'Yes, I\'d like to create a new site', 'buddypress' ) ?></label>
                </div>
              </div>
            
                
            <?php ob_start(); do_action( 'bp_signup_blog_url_errors' ); $bp_signup_blog_url_errors = ob_get_contents(); ob_end_clean();?>
            <div id="blog-details" class="control-group<?php if ( (int) bp_get_signup_with_blog_value() ) : ?> show<?php endif; ?><?php echo (!empty($bp_signup_blog_url_errors))?' error':''; ?>">

              <label for="signup_blog_url" class="control-label"><?php _e( 'Blog URL', 'buddypress' ) ?></label>
              <div class="controls">
              
                <?php if ( is_subdomain_install() ) : ?>
                  http:// <input type="text" name="signup_blog_url" id="signup_blog_url" value="<?php bp_signup_blog_url_value() ?>" /> .<?php echo preg_replace( '|^https?://(?:www\.)|', '', site_url() ) ?>
                <?php else : ?>
                  <?php echo site_url() ?>/ <input type="text" name="signup_blog_url" id="signup_blog_url" value="<?php bp_signup_blog_url_value() ?>" />
                <?php endif; ?>
                <?php echo (!empty($bp_signup_blog_url_errors)) ? "<span class='help-inline'>{$bp_signup_blog_url_errors}</span>" : '';  ?>  
              </div>
            </div> 

            
            <?php ob_start(); do_action( 'bp_signup_blog_title_errors' ); do_action( 'bp_signup_blog_privacy_errors' ); $bp_signup_blog_title_errors = ob_get_contents(); ob_end_clean();?>
            <div class="control-group<?php echo (!empty($bp_signup_blog_title_errors))?' error':''; ?>">
              <label for="signup_blog_title" class="control-label"><?php _e( 'Site Title', 'buddypress' ) ?></label>
              <div class="controls">
                <?php do_action( 'bp_signup_blog_title_errors' ) ?>
                <input type="text" name="signup_blog_title" id="signup_blog_title" value="<?php bp_signup_blog_title_value() ?>" />
              
              <label><?php _e( 'I would like my site to appear in search engines, and in public listings around this network.', 'buddypress' ) ?>:</label>
        
              <label><input type="radio" name="signup_blog_privacy" id="signup_blog_privacy_public" value="public"<?php if ( 'public' == bp_get_signup_blog_privacy_value() || !bp_get_signup_blog_privacy_value() ) : ?> checked="checked"<?php endif; ?> /> <?php _e( 'Yes', 'buddypress' ) ?></label>
              <label><input type="radio" name="signup_blog_privacy" id="signup_blog_privacy_private" value="private"<?php if ( 'private' == bp_get_signup_blog_privacy_value() ) : ?> checked="checked"<?php endif; ?> /> <?php _e( 'No', 'buddypress' ) ?></label>
              <?php echo (!empty($bp_signup_blog_title_errors)) ? "<span class='help-inline'>{$bp_signup_blog_title_errors}</span>" : '';  ?>
              </div>
            </div>
          </fieldset> 
          </div><!-- #blog-details-section -->

          <?php do_action( 'bp_after_blog_details_fields' ) ?>

        <?php endif; ?>

        <?php do_action( 'bp_before_registration_submit_buttons' ) ?>

        <div class="submit controls">
          <input type="submit" class="btn btn-primary" name="signup_submit" id="signup_submit" value="<?php _e( 'Complete Sign Up', 'buddypress' ) ?>" />
        </div>

        <?php do_action( 'bp_after_registration_submit_buttons' ) ?>

        <?php wp_nonce_field( 'bp_new_signup' ) ?>

      <?php endif; // request-details signup step ?>

      <?php if ( 'completed-confirmation' == bp_get_current_signup_step() ) : ?>

        <h2><?php _e( 'Sign Up Complete!', 'buddypress' ) ?></h2>

        <?php do_action( 'bp_before_registration_confirmed' ) ?>

        <?php if ( bp_registration_needs_activation() ) : ?>
          <p><?php _e( 'You have successfully created your account! To begin using this site you will need to activate your account via the email we have just sent to your address.', 'buddypress' ) ?></p>
        <?php else : ?>
          <p><?php _e( 'You have successfully created your account! Please log in using the username and password you have just created.', 'buddypress' ) ?></p>
        <?php endif; ?>

        <?php do_action( 'bp_after_registration_confirmed' ) ?>

      <?php endif; // completed-confirmation signup step ?>

      <?php do_action( 'bp_custom_signup_steps' ) ?>

      </form>

    </div>
    <style type="text/css">.wp_bp_<?php echo $wp_bp_nonce; ?>_first {display:none;}</style>
    <?php do_action( 'bp_after_register_page' ) ?>

    </div><!-- .main  -->
    
    <?php flawless_widget_area( 'buddypress' ) ?>
    
  </div><!-- #content -->




<?php  if( !apply_filters( 'skip_footer', false ) ) { get_footer( 'buddypress' ); } ?>
