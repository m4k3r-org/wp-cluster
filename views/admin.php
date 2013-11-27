<div class="wrap">
<h2 class="title">Veneer Admin</h2>

  <?php

  // is_multisite();

  if( $_GET[ 'test' ] == 'single' ) {
    $content = $wpdb->get_var( "SELECT post_content from {$wpdb->posts} WHERE ID = 37414;" );

    //$content = str_replace( array( "ÃƒÂ«", "ÃƒÂ¶" ), array( "ë", "ö" ), $content );
    echo $content;

  }
  if( $_GET[ 'test' ] == 'create_job' ) {
    global $wpdb;

    include_once( dirname( __DIR__ ) . '/jobs/repair-encoding.php' );

    $encoding_job = new \UsabilityDynamics\Job( array(
      "type"     => 'repair-encoding',
      "worker"   => array( "repairEncoding", "worker" )
    ));

    // Multisite Batches.
    if( is_multisite() ) {

      foreach( $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs};" ) as $_blog_id ) {

        $encoding_job->add_batch( array(
          "table" => $wpdb->base_prefix . $_blog_id . '_posts'
        ));

      }

    }

    // Single Site.
    if( !is_multisite() ) {
      $encoding_job->add_batch( array(
        "table" => $wpdb->posts
      ));
    }

    // Delete job. (@debug)
    //$encoding_job->delete();

    die( '<pre>|' . print_r( $encoding_job, true ) . '|</pre>' );

  }
  ?>

</div>
