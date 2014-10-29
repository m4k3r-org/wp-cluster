<div data-requires="wp-elastic.admin.js" class="wrap view-model">
  <h2 data-bind="text: title"></h2>

  <div data-requires="wp-elastic.mapping.js" class="section wp-elastic-mapping view-model">
    <h3>Mapping</h3>
  </div>

  <div data-requires="wp-elastic.settings.js" class="section wp-elastic-settings view-model">
    <h3>Settings</h3>
  </div>

</div>

<script>require.set( 'baseUrl', '/modules/wp-elastic/static/scripts/' );</script>