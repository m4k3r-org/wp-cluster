<?php
/**
 * Server Configuration template
 * @author korotkov@ud
 */
?>
<div class="wrap">

  <h2><?php _e('Mapping Options', DOMAIN_CURRENT_SITE) ?></h2>

  <p class="description"><?php _e('Editing later', DOMAIN_CURRENT_SITE) ?></p>

  <?php wp_disco()->search->action_messages(); ?>

  <h3><?php _e('Index Types', DOMAIN_CURRENT_SITE); ?></h3>

  <?php  echo '<pre>';
  //print_r( $post_types );
  echo '</pre>'; ?>

  <form action="" method="post">

    <table class="form-table">
      <tbody>
        <tr valign="top">
          <td>
            <?php foreach( $post_types as $key => $type ): ?>
            <label>
              <input <?php echo in_array($key, $active_types)?'checked="checked"':''; ?> type="checkbox" value="<?php echo $key; ?>" name="index_types[]" />
              <?php echo $type->labels->menu_name; ?>
            </label>
            <?php endforeach; ?>
          </td>
        </tr>
      </tbody>
    </table>
    <?php submit_button(); ?>
  </form>
</div>