* * *
[![Issues - Bug](https://badge.waffle.io/DiscoDonniePresents/wp-disco.png?label=bug&title=Bugs)](http://waffle.io/discodonniepresents/wp-disco)
[![Issues - Backlog](https://badge.waffle.io/DiscoDonniePresents/wp-disco.png?label=backlog&title=Backlog)](http://waffle.io/discodonniepresents/wp-disco/)
[![Issues - Active](https://badge.waffle.io/DiscoDonniePresents/wp-disco.png?label=in progress&title=Active)](http://waffle.io/discodonniepresents/wp-disco/)
* * *
[![Dependency Status](https://gemnasium.com/f56b4e969b951926d6f5cb1a1e85d579.svg)](https://gemnasium.com/DiscoDonniePresents/wp-disco)
[![Scrutinizer Quality](http://img.shields.io/scrutinizer/g/discodonniepresents/wp-disco.svg)](https://scrutinizer-ci.com/g/discodonniepresents/wp-disco)
[![Scrutinizer Coverage](http://img.shields.io/scrutinizer/coverage/g/discodonniepresents/wp-disco.svg)](https://scrutinizer-ci.com/g/discodonniepresents/wp-disco)
[![CircleCI](https://circleci.com/gh/DiscoDonniePresents/wp-disco.png?circle-token=dc5268ed8b79870f45b64fad741e68418a847bba)](https://circleci.com/gh/DiscoDonniePresents/wp-disco)
* * *

### To Do
* Add 404 page handler that uses a custom WP page with a specified page template.

### Methods
* wp_disco()->aside()
* wp_disco()->nav()
* wp_disco()->get_events_count()
* wp_disco()->widget_area()
* wp_disco()->breadcrumbs()
* wp_disco()->page_title()
* wp_disco()->thumbnail() - Used to be flawless_thumbnail();
* wp_disco()->module_class() - Used to be flawless_module_class();
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

