<?php
/**
 * Server Configuration template
 * @author korotkov@ud
 */
?>
<div class="wrap">

  <h2><?php _e('Mapping Options', DOMAIN_CURRENT_SITE) ?></h2>

  <p class="description"><?php _e('Manage types and their mappings', DOMAIN_CURRENT_SITE) ?></p>

  <?php wp_disco()->search->action_messages(); ?>

  <h3><?php _e('Index Types', DOMAIN_CURRENT_SITE); ?></h3>

  <form action="" method="post">

    <table class="form-table">
      <tbody>
        <tr valign="top">
          <td>
            <?php if ( !empty( $post_types ) && is_array( $post_types ) ): ?>
            <?php foreach( $post_types as $key => $type ): ?>
            <label>
              <input <?php echo is_array($active_types)&&in_array($key, $active_types)?'checked="checked"':''; ?> type="checkbox" value="<?php echo $key; ?>" name="index_types[]" />
              <?php echo $type->labels->menu_name; ?>
            </label>
            <?php endforeach; ?>
            <?php else: ?>
            <p><?php _e('No post types found.', DOMAIN_CURRENT_SITE); ?></p>
            <?php endif; ?>
          </td>
        </tr>
      </tbody>
    </table>

    <?php submit_button(); ?>
  </form>

  <?php if ( !empty($mapping) ): ?>

  <script type="text/javascript">
    jQuery(function() {
      jQuery( "#tabs" ).tabs();
    });
  </script>

  <h3><?php _e('Current Mapping', DOMAIN_CURRENT_SITE); ?></h3>

  <form action="" method="post">

    <div id="tabs">
      <ul>
        <?php foreach( $mapping as $type_key => $type_mapping ): ?>
        <li><a href="#<?php echo $type_key; ?>"><?php echo $type_key; ?></a></li>
        <?php endforeach; ?>
      </ul>

      <?php foreach( $mapping as $type_key => $type_mapping ): ?>
      <div id="<?php echo $type_key; ?>">
        <textarea name="mapping[<?php echo $type_key; ?>]" class="widefat" style="height: 400px;font-family: monospace;" id="mapping_area"><?php echo $type_mapping; ?></textarea>
      </div>
      <?php endforeach; ?>
    </div>

    <?php submit_button(__( 'Put Mapping', DOMAIN_CURRENT_SITE )); ?>
  </form>

  <?php endif; ?>
</div>