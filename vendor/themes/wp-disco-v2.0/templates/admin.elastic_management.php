<script type="text/javascript">
  jQuery(document).ready(function(){

    //** Reindex Terms */
    jQuery('#reindex-terms').on('click', function(e){

      var self = jQuery( e.target );

      self.attr('disabled', 1).val('Please wait...');

      jQuery.ajax( ajaxurl, {
        type: 'GET',
        dataType: 'json',
        data: {
          action: 'reindex_taxonomies'
        },
        success: function( response ) {
          self.val( response.message ).removeAttr('disabled');
        },
        error: function() {
          self.val( 'Something went wrong...' );
        }
      });

    });

    //** Remapping */
    jQuery('#remap-all').on('click', function(e){

      var self = jQuery( e.target );

      self.attr('disabled', 1).val('Please wait...');
      jQuery( '.remap-results' ).empty();

      jQuery.ajax( ajaxurl, {
        type: 'GET',
        dataType: 'json',
        data: {
          action: 'remap_all'
        },
        success: function( response ) {
          self.val( 'Done' ).removeAttr('disabled');
          for( var i in response.results ) {
            if ( response.results[i].acknowledged ) {
              jQuery( '.remap-results' ).append('<li>'+i+' - Acknowledged</li>');
            } else {
              jQuery( '.remap-results' ).append('<li>'+i+' - '+response.results[i]+'</li>');
            }
          }
        },
        error: function() {
          self.val( 'Something went wrong...' );
        }
      });

    });

  });
</script>

<div class="wrap ud-admin-wrap">

  <h2><?php _e( 'Taxonomy Terms Indexing', HDDP ); ?></h2>

  <table>
    <tr>
      <td>
        <?php _e( 'Reindex Terms', HDDP ); ?>
        <p class="description">
          <?php _e( 'Reindex all taxonomy terms', HDDP ); ?>
        </p>
      </td>
      <td>
        <input id="reindex-terms" type="button" value="Reindex" class="button button-primary" />
      </td>
    </tr>
  </table>

  <h2><?php _e( 'Mapping', HDDP ); ?></h2>

  <p class="description"><?php _e( 'Edit <code>/json/elasticsearch-mapping.json</code> to manage mapping.' ); ?></p>

  <input id="remap-all" type="button" value="Remap All Types" class="button button-primary" />

  <ul class="remap-results"></ul>

  <textarea style="width:100%;font-family:monospace;" rows="20">
    <?php echo file_get_contents( get_stylesheet_directory().'/json/elasticsearch-mapping.json' ); ?>
  </textarea>

</div>