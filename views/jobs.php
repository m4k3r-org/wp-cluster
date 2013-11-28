<div class="wrap">

<?php
global $wpdb;
require_once( dirname( __DIR__ ) . '/lib/jobs/repair-encoding.php' );

add_filter( 'job::repair-encoding', array( 'repairEncoding', 'worker' ), 10, 3 );

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

if( $_GET[ 'test' ] == 'repair-encoding' ) {

  $_results = \UsabilityDynamics\Veneer\Job::process_jobs( 'repair-encoding' );

  echo implode( '<br />', $_results );
  //die( '<pre>Job::process_jobs:' . print_r( $_results, true ) . '</pre>' );

} ?>


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

</div>