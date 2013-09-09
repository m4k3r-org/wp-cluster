<div class="wrap">
    <?php screen_icon(); ?>
  <h2>Amazon S3 Uploads</h2>
  <?php if ( !empty( $message ) ): ?>
    <div id="asssu-settings_updated" class="updated settings-error"> 
        <p><strong><?= $message ?></strong></p>
    </div>
  <?php endif; ?>
  <?php foreach ( $form->getNonFieldErrors() as $error ): ?>
    <div id="asssu-settings_updated" class="updated settings-error"> 
        <p><strong><?= $error ?></strong></p>
    </div>
  <?php endforeach; ?>
  <?php if ( count( $form->getNonFieldErrors() ) > 0 || count( $form->getErrors() ) > 0 ): ?>
    <div id="asssu-settings_updated" class="updated settings-error"> 
        <p><strong>Your changes have not been saved.</strong></p>
    </div>
  <?php endif; ?>
  <form method="post" action="">
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><label>Current status</label></th>
                <td><?= isset( $this->asssu->config[ 'is_active' ] ) && $this->asssu->config[ 'is_active' ] ? 'Active' : 'Not active' ?></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?= $form[ 'is_active' ]->getLabelTag() ?></th>
                <td>
                    <?= $form[ 'is_active' ]->asWidget() ?>
                    <?php if ( $form[ 'is_active' ]->hasErrors() ): ?>
                      <?= $form[ 'is_active' ]->getErrors() ?>
                    <?php endif; ?>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?= $form[ 'access_key' ]->getLabelTag() ?></th>
                <td>
                    <?= $form[ 'access_key' ]->asWidget() ?>
                  <?php if ( $form[ 'access_key' ]->hasErrors() ): ?>
                    <span class="description" style="color:red;"><?= $form[ 'access_key' ]->getErrors()->getFirst() ?></span>
                  <?php endif; ?>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?= $form[ 'secret_key' ]->getLabelTag() ?></th>
                <td>
                    <?= $form[ 'secret_key' ]->asWidget() ?>
                  <?php if ( $form[ 'secret_key' ]->hasErrors() ): ?>
                    <span class="description" style="color:red;"><?= $form[ 'secret_key' ]->getErrors()->getFirst() ?></span>
                  <?php endif; ?>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?= $form[ 'bucket_name' ]->getLabelTag() ?></th>
                <td>
                    <?= $form[ 'bucket_name' ]->asWidget() ?>
                  <?php if ( $form[ 'bucket_name' ]->hasErrors() ): ?>
                    <span class="description" style="color:red;"><?= $form[ 'bucket_name' ]->getErrors()->getFirst() ?></span>
                  <?php endif; ?>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?= $form[ 'bucket_subdir' ]->getLabelTag() ?></th>
                <td>
                    <?= $form[ 'bucket_subdir' ]->asWidget() ?>
                  <?php if ( $form[ 'bucket_subdir' ]->hasErrors() ): ?>
                    <span class="description" style="color:red;"><?= $form[ 'bucket_subdir' ]->getErrors()->getFirst() ?></span>
                  <?php endif; ?>
                  <span class="description"><?php _e( 'If you want to store all images in a bucket\'s subdirectory, like \'media/blog\'.' ); ?></span>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?= $form[ 'terms_of_use' ]->getLabelTag() ?></th>
                <td>
                    <?= $form[ 'terms_of_use' ]->asWidget() ?>
                  <?php if ( $form[ 'terms_of_use' ]->hasErrors() ): ?>
                    <span class="description" style="color:red;"><?= $form[ 'terms_of_use' ]->getErrors()->getFirst() ?></span>
                  <?php endif; ?>
                </td>
            </tr>
        </table>
        <p class="submit">
            <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes' ); ?>"/>
        </p>
    </form>
    <span class="description">If you find this plugin usefull, please <a href="#" onclick="jQuery('#asssu-donate').show();">donate</a>.</span>
    <div id="asssu-donate" style="display:none;">
        <span class="description">
        No minimum donation amount, it's totally up to you.<br/>
        If you prefer to send me a handicraft, then <a href="mailto:atvdev@gmail.com">ask for my address</a>.<br/>
        </span>
        <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
        <input type="hidden" name="cmd" value="_s-xclick">
        <input type="hidden" name="hosted_button_id" value="7T88Q3EHGD9RS">
        <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
        <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
        </form>
    </div>
</div>
