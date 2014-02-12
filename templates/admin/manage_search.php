<?php
/**
 * Service Status template
 * @author korotkov@ud
 */

$search_settings = wp_disco()->get('search');
?>
<div class="wrap">
  <h2><?php _e('Service Status', DOMAIN_CURRENT_SITE) ?></h2>
  <p class="description"><?php _e('This area will help you to manage ElasticSearch implementation for current site.', DOMAIN_CURRENT_SITE) ?></p>

  <?php if ( empty( $search_settings ) ) : ?>
  <div class="error settings-error" id="setting-error-settings_updated">
    <p><strong><?php echo sprintf(__('Currently your ElasticSearch configuration is empty. Visit <a href="%s">Server</a> section to configure.', DOMAIN_CURRENT_SITE), admin_url('admin.php?page=wp-disco-manage-search-server')); ?></strong></p>
  </div>
  <?php endif; ?>

</div>