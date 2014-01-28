### Methods
* wp_disco()->get_events_count()


### Legacy Code
// add_action( 'wp_ajax_nopriv_ud_df_post_query', create_function( '', ' die( json_encode( hddp::df_post_query( $_REQUEST )));' ));
// add_action( 'wp_ajax_ud_df_post_query', create_function( '', ' die( json_encode( hddp::df_post_query( $_REQUEST )));' ));
// add_action( 'wp_ajax_elasticsearch_query', array( 'UsabilityDynamics\Disco', 'elasticsearch_query' ) );
// add_action( 'wp_ajax_nopriv_elasticsearch_query', array( 'UsabilityDynamics\Disco', 'elasticsearch_query' ) );

