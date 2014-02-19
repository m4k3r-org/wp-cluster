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

  <h3><?php _e('Current Mapping', DOMAIN_CURRENT_SITE); ?></h3>

  <form action="" method="post">

    <table class="form-table">
      <tbody>
        <tr>
          <td>
            <textarea name="mapping" class="widefat" style="height: 400px;font-family: monospace;" id="mapping_area"></textarea>
          </td>
        </tr>
      </tbody>
    </table>

    <?php submit_button(__( 'Put Mapping', DOMAIN_CURRENT_SITE )); ?>
  </form>

  <script type="text/javascript">
    jQuery(document).ready(function(){
      jQuery('#mapping_area').val( JSON.stringify(<?php echo $mapping; ?>, null, 4) );
    });

    jQuery(document).ready(function(){
      jQuery.ajax(ajaxurl, {
        data: {
          action: 'index_documents',
          type: 'event'
        }
      });
    });
  </script>

  <?php endif; ?>
</div>