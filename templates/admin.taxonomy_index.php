<div class="wrap ud-admin-wrap">

  <h2><?php _e( 'ElasticSearch Taxonomy Indexing', HDDP ); ?></h2>

  <script type="text/javascript">
    jQuery(document).ready(function(){

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

    });
  </script>

  <table>
    <tr>
      <td>
        Reindex Terms
        <p class="description">
          Reindex all taxonomy terms
        </p>
      </td>
      <td>
        <input id="reindex-terms" type="button" value="Reindex" class="button button-primary" />
      </td>
    </tr>
  </table>

</div>