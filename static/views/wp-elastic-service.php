<?php
 /**
  *
  */
 ?>
<section data-requires="/modules/wp-elastic/static/scripts/wp-elastic.settings.js" class="wrap view-model">
  <h2><?php _e( 'wpElastic Settings', wp_elastic( 'domain' ) ); ?></h2>
  <h2 data-bind="text: title"></h2>
  <pre data-bind="text: settings"></pre>
</section>

<script id="wp-elastic-settings-script">

  // Declare dependencies.
  if( 'function' === typeof require ) {
    require.config({
      deps: [ 'udx.utility', '/modules/wp-elastic/static/scripts/wp-elastic.api.js', 'knockout' ],
      config: {
        'wp-elastic.api': {
          ajaxurl: ajaxurl
        }
      },
      callback: viewCallback
    });
  }

  /**
   * Initialize View.
   *
   * @param utility
   * @param ko
   */
  function viewCallback( utility, ko ) {
    // console.debug( 'viewCallback' );
  }

</script>

<style id="wp-elastic-settings-style">
  section.wrap h2 { color: blue; }
</style>


