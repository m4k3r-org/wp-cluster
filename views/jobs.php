<?php
global $wpdb;
require_once( dirname( __DIR__ ) . '/lib/jobs/repair-encoding.php' );
require_once( dirname( __DIR__ ) . '/lib/jobs/site-screenshot.php' );

// Register Job Workers.
add_filter( 'job::repair-encoding', array( 'repairEncoding', 'worker' ), 10, 3 );
add_filter( 'job::site-screenshot', array( 'siteScreenshot', 'worker' ), 10, 3 );

/**
 * Test Basic XML-RPC Call
 *
 */
if( $_GET[ 'test' ] == 'basic-rpc' ) {

  // @todo Make request to http://raas.usabilitydynamics.com to "snapshot.Generate" passing in "url", "viewport" and "timeout"

}

/**
 * Use RaaS Job to task RaaS Server
 *
 */
if( $_GET[ 'test' ] == 'create-screenshots' ) {

  // Register Job Type. (Uses UsabilityDynamics\RaaS\Job -> UsabilityDynamics\Job)
  $_job = new \UsabilityDynamics\RaaS\Job( array(
    "type"   => 'site-screenshot'
  ));

  // Push screenshot request for drop.veneer.io
  $_job->push( array(
    "url" => "drop.veneer.io",
    "viewport" => "1280x1024"
  ));

  // Push screenshot request for discodonniepresents.com
  $_job->push( array(
    "url" => "discodonniepresents.com",
    "viewport" => "1280x1024"
  ));

  // Force job execution right now
  \UsabilityDynamics\Veneer\Job::process_jobs( $_job->id );

  // Delete Job.
  $_job->delete();

  die( '<pre>' . print_r( $_job, true ) . '</pre>' );

}

/**
 * Create Local Job
 *
 */
if( $_GET[ 'test' ] == 'create-job' ) {

  $encoding_job = new \UsabilityDynamics\Job( array(
    "type"   => 'repair-encoding'
  ));

  // Multisite Batches.
  if( is_multisite() ) {

    foreach( $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs};" ) as $_blog_id ) {

      $encoding_job->push( array(
        "table" => $wpdb->base_prefix . $_blog_id . '_posts'
      ));

    }

  }

  // Single Site.
  if( !is_multisite() ) {
    $encoding_job->push( array( "table" => $wpdb->posts ));
  }

  die( '<pre>|' . print_r( $encoding_job, true ) . '|</pre>' );

}

?>
<div class="wrap">

  <h2><?php _e( 'Jobs', $_locale ); ?></h2>

  <?php /* $wp_list_table->views(); */ ?>

  <form id="jobs-filter" action="<?php echo admin_url( 'tools.php?page=veneer-jobs' ); ?>" method="get">
    <?php /* $wp_list_table->search_box( $post_type_object->labels->search_items, 'post' ); */ ?>
    <?php /* $wp_list_table->display(); */ ?>
  </form>

  <table class="widefat">
    <thead>
    <tr>
      <th><?php _e( 'Type', $_locale ); ?></th>
      <th><?php _e( 'Batches', $_locale ); ?></th>
      <th><?php _e( 'Status', $_locale ); ?></th>
    </tr>
    </thead>

    <tbody>

    <?php foreach( (array) $_jobs as $job ) { ?>
      <tr>
        <td><?php echo $job->post_title; ?></td>
        <td><?php echo $job->post_status; ?></td>
      </tr>
    <?php } ?>

    </tbody>
  </table>

  <div class="side-actions">
    <ul>
      <li>Process All</li>
      <li>Stop All</li>
      <li>Configure RaaS</li>
    </ul>
  </div>

  <div id="ajax-response"></div>
  <br class="clear"/>

  <?php global $wpi_xml_server; $wpi_xml_server->ui->render_api_fields(); ?>

</div>