<?php
/**
 * Service Status template
 * @author korotkov@ud
 */
?>
<div class="wrap">
  <h2><?php echo !empty( $cluster_info['name'] )?$cluster_info['name']:__('ElasticSearch Status', DOMAIN_CURRENT_SITE); ?> [<?php echo !empty( $cluster_info['version']['number'] )?$cluster_info['version']['number']:__('Unknown', DOMAIN_CURRENT_SITE); ?>]</h2>
  <p class="description"><?php echo !empty($cluster_info['tagline'])?$cluster_info['tagline']:__('Unconfigured environment', DOMAIN_CURRENT_SITE); ?></p>

  <?php if ( empty( $search_settings ) ) : ?>
  <div class="error settings-error" id="setting-error-settings_updated">
    <p><strong><?php echo sprintf(__('Currently your ElasticSearch configuration is empty. Visit <a href="%s">Server</a> section to configure.', DOMAIN_CURRENT_SITE), admin_url('admin.php?page=wp-disco-manage-search-server')); ?></strong></p>
  </div>
  <?php else: ?>

  <table id="cluster-health">
    <tr>
      <td><?php _e('Cluster name', DOMAIN_CURRENT_SITE); ?></td>
      <td><b style="color:<?php echo $cluster_health['status'] ?>;background:#999;padding:0 5px;"><?php echo $cluster_health['cluster_name']; ?></b></td>
    </tr>
    <tr>
      <td><?php _e('Time out', DOMAIN_CURRENT_SITE) ?></td>
      <td><?php echo $cluster_health['timed_out']?__('Yes', DOMAIN_CURRENT_SITE):__('No', DOMAIN_CURRENT_SITE) ?></td>
    </tr>
    <tr>
      <td><?php _e('Number of nodes', DOMAIN_CURRENT_SITE) ?></td>
      <td><?php echo $cluster_health['number_of_nodes']; ?></td>
    </tr>
    <tr>
      <td><?php _e('Number of data nodes', DOMAIN_CURRENT_SITE) ?></td>
      <td><?php echo $cluster_health['number_of_data_nodes']; ?></td>
    </tr>
    <tr>
      <td><?php _e('Active Primary Shards', DOMAIN_CURRENT_SITE) ?></td>
      <td><?php echo $cluster_health['active_primary_shards']; ?></td>
    </tr>
    <tr>
      <td><?php _e('Active Shards', DOMAIN_CURRENT_SITE) ?></td>
      <td><?php echo $cluster_health['active_shards']; ?></td>
    </tr>
    <tr>
      <td><?php _e('Relocating Shards', DOMAIN_CURRENT_SITE) ?></td>
      <td><?php echo $cluster_health['relocating_shards']; ?></td>
    </tr>
    <tr>
      <td><?php _e('Initializing Shards', DOMAIN_CURRENT_SITE) ?></td>
      <td><?php echo $cluster_health['initializing_shards']; ?></td>
    </tr>
    <tr>
      <td><?php _e('Unassigned Shards', DOMAIN_CURRENT_SITE) ?></td>
      <td><?php echo $cluster_health['unassigned_shards']; ?></td>
    </tr>
    <tr>
      <td><?php _e('Number of indices', DOMAIN_CURRENT_SITE) ?></td>
      <td><?php echo count($cluster_health['indices']); ?></td>
    </tr>
  </table>

  <h2><?php _e('Cluster Indices Status', DOMAIN_CURRENT_SITE); ?></h2>

  <table id="cluster-indices-status">
    <tr>
      <?php foreach( $cluster_health['indices'] as $key => $index ): ?>
      <td>
        <h3 style="color:<?php echo $index['status']; ?>;<?php echo $key==wp_disco()->get('search.index')?'text-decoration:underline;':''; ?>"><?php echo $key; ?></h3>
      </td>
      <?php endforeach; ?>
    </tr>
  </table>

  <h2><?php _e('Current Index Information', DOMAIN_CURRENT_SITE); ?></h2>

  <table id="current-index-information">
    <tr>
      <th><?php _e('Documents'); ?></th>
      <th><?php _e('Size'); ?></th>
      <th><?php _e('Indexing'); ?></th>
      <th><?php _e('Get'); ?></th>
      <th><?php _e('Search'); ?></th>
    </tr>
    <tr>
      <td><?php echo $current_index['_all']['total']['docs']['count'] ?></td>
      <td><?php echo $current_index['_all']['total']['store']['size'] ?></td>
      <td><?php echo $current_index['_all']['total']['indexing']['index_total'] ?></td>
      <td><?php echo $current_index['_all']['total']['get']['total'] ?></td>
      <td><?php echo $current_index['_all']['total']['search']['query_total'] ?></td>
    </tr>
  </table>

  <?php endif; ?>

</div>