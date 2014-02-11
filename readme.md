### Methods
* wp_disco()->aside()
* wp_disco()->nav()
* wp_disco()->get_events_count()
* wp_disco()->widget_area()
* wp_disco()->breadcrumbs()
* wp_disco()->page_title()
* wp_disco()->module_class()
* wp_disco()->wrapper_class() - Used to be flawless_wrapper_class();
* wp_disco()->block_class() - Used to be flawless_block_class();
* wp_disco()->get_template_part()
* wp_disco()->get_current_sidebars()
* wp_disco()->widget_area_tabs()

### Legacy Code
// add_action( 'wp_ajax_nopriv_ud_df_post_query', create_function( '', ' die( json_encode( hddp::df_post_query( $_REQUEST )));' ));
// add_action( 'wp_ajax_ud_df_post_query', create_function( '', ' die( json_encode( hddp::df_post_query( $_REQUEST )));' ));
// add_action( 'wp_ajax_elasticsearch_query', array( 'UsabilityDynamics\Disco', 'elasticsearch_query' ) );
// add_action( 'wp_ajax_nopriv_elasticsearch_query', array( 'UsabilityDynamics\Disco', 'elasticsearch_query' ) );

