/**
 * wpElastic API.
 *
 * @example
 *
 *    // Update Settings.
 *    require( 'wp-elastic.api' ).updateSettings({ service: { url: 'localhost:9200', index: 'my-index', key: { public: 'none' } } });
 *
 *    // Delete Settings
 *    require('wp-elastic.api').deleteSettings();
 *
 *    // Get WordPress Status.
 *    require('wp-elastic.api').getStatus();
 *
 * @module wp-elastic.api
 * @author potanin@UD
 */
define( 'wp-elastic.api', function wpElasticAPI( require, exports, module ) {
  // console.debug( 'wp-elastic.api', module.config().ajaxurl );

  /**
   * Flush All Settings.
   *
   * @example
   *
   *    require( 'wp-elastic.api' ).flushSettings()
   *
   * @param data {Object} Optional parameters.
   * @param callback {Function} Optional callback.
   */
  function deleteSettings( data, callback ) {
    console.debug( 'deleteSettings' );

    function ajaxCallback( data, status ) {
      console.debug( 'deleteSettings:response', data );

      if( 'function' === typeof callback ) {
        callback( null, data );
      }

    }

    jQuery.ajax({
      method: 'DELETE',
      dataType: 'json',
      url: [ module.config().ajaxurl, '?action=/elastic/settings' ].join( '' ),
      data: { data: data }
    }).done( ajaxCallback );

  }

  /**
   * Update Settings.
   *
   * @example
   *    require( 'wp-elastic.api' ).updateSettings({ service: { url: 'localhost:9200', index: 'my-index', key: { public: 'none' } } });
   *
   * @param data
   * @param callback
   */
  function updateSettings( data, callback ) {
    console.debug( 'updateSettings' );

    function ajaxCallback( data, status ) {
      console.debug( 'updateSettings:response', data );

      if( 'function' === typeof callback ) {
        callback( null, data );
      }

    }

    jQuery.ajax({
      method: 'POST',
      dataType: 'json',
      url: [ module.config().ajaxurl, '?action=/elastic/settings' ].join( '' ),
      data: { data: data }
    }).done( ajaxCallback );

  }

  /**
   * Get Settings.
   *
   * @param data
   * @param callback
   */
  function getSettings( data, callback ) {
    console.debug( 'getSettings' );

    function ajaxCallback( data, status ) {
      console.debug( 'getSettings:response', data );
      if( 'function' === typeof callback ) {
        callback( null, data );
      }
    }

    jQuery.ajax({
      method: 'GET',
      dataType: 'json',
      url: [ module.config().ajaxurl, '?action=/elastic/settings' ].join( '' ),
      data: { data: data }
    }).done( ajaxCallback );

  }

  /**
   * Get Overall Status
   *
   * @example
   *    require( 'wp-elastic.api' ).getStatus()
   *
   * @param data
   * @param callback
   */
  function getStatus( data, callback ) {
    console.debug( 'getStatus' );

    function ajaxCallback( data, status ) {
      console.debug( 'getStatus:response', data );
      if( 'function' === typeof callback ) {
        callback( null, data );
      }
    }

    jQuery.ajax({
      method: 'GET',
      dataType: 'json',
      url: [ module.config().ajaxurl, '?action=/elastic/status' ].join( '' ),
      data: { data: data }
    }).done( ajaxCallback );

  }

  return {
    version: '1.0.1',
    deleteSettings: deleteSettings,
    updateSettings: updateSettings,
    getSettings: getSettings,
    getStatus: getStatus
  }

});

