<?php
/**
 * Server Configuration template
 * @author korotkov@ud
 */
?>
<div class="wrap">

  <h2><?php _e('Indexing Documents', DOMAIN_CURRENT_SITE) ?></h2>

  <p class="description"><?php _e('Indexing process management.', DOMAIN_CURRENT_SITE) ?></p>

  <?php wp_disco()->search->action_messages(); ?>

  <h3><?php _e('Console', DOMAIN_CURRENT_SITE); ?></h3>

  <div id="console"></div>

  <h3><?php _e('Bulk Actions', DOMAIN_CURRENT_SITE); ?></h3>

  <form action="clear_all_docs" class="ajax-form-action" id="clear">
    <?php submit_button( __('Clear All'), 'primary', 'clear', false ); ?>
  </form>
  <form action="reindex_all_docs" class="ajax-form-action" id="reindex">
    <?php submit_button( __('Re-index All'), 'secondary', 'clear', false ); ?>
  </form>

  <script type="text/javascript">
    jQuery(document).ready(function(){

      jQuery('form.ajax-form-action').on('submit', function(){
        var that = jQuery(this);
        jQuery.ajax(ajaxurl, {
          data: {
            action: that.attr('action')
          }
        });
        return false;
      });

    });
  </script>
</div>