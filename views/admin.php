<?php
global $wpdb;

?>

<div class="wrap">
<h2 class="title">Veneer Admin</h2>

  <?php

  if( $_GET[ 'test' ] ) {

    include_once( dirname( __DIR__ ) . '/jobs/repair-encoding.php' );

    $encoding_job = new \UsabilityDynamics\Job( array(
      "type"     => 'repair_encoding',
      "worker"   => array( "repairEncoding", "worker" )
    ));

    // Add Batches.
    $encoding_job->add_batch( $wpdb->get_col( "SELECT ID FROM wp_12_posts WHERE post_status = 'publish' LIMIT 0, 10;" ) );
    $encoding_job->add_batch( $wpdb->get_col( "SELECT ID FROM wp_12_posts WHERE post_status = 'publish' LIMIT 10, 10;" ) );

    // Delete job. (@debug)
    $encoding_job->delete();

    die( '<pre>|' . print_r( $encoding_job, true ) . '|</pre>' );

  }
  ?>

</div>
