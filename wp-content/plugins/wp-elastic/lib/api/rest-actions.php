<?php
/**
 * REST API Method
 *
 * @param $data
 */
function wpElasticSettingsAPI( $data ) {

}

/**
 * REST API Method
 *
 * @param $data
 */
function wpElasticActionsAPI( $data ) {

}

/**
 * REST API Method
 *
 * @param $data
 */
function wpElasticSearchAPI( $data ) {}

/**
 * REST API Method
 *
 * @param $data
 */
function wpElasticDocumentAPI( $data ) {}

/**
 * REST API Method
 *
 * @param $data
 */
function wpElasticServiceAPI( $data ) {

  if( !current_user_can( 'manage_options' ) ) {
    return;
  }

  return wp_send_json( array(
    'ok' => true,
    'data' => 'wip'
  ));

}