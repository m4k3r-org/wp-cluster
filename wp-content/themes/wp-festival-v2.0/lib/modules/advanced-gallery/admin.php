<?php 

/**
 * Do some prepare
 */
if ( !empty( $data['images'] ) && is_array( $data['images'] ) ) {
  $_new_images = array();
  foreach( $data['images'] as $_image ) {
    $_new_images[] = $_image;
  }
}

?>

<fieldset id="advanced-gallery-basic-info" class="cfct-form-section">
  
  <legend><?php _e( 'Settings', wp_festival2('domain') ) ?></legend>
  
  <label for="title"><?php _e( 'Title', wp_festival2('domain') ); ?></label>
  
  <span class="cfct-input-full">
    <input type="text" name="title" id="title" value="<?php echo esc_attr( isset( $data[ 'title' ] ) ? $data[ 'title' ] : '' ); ?>" />
  </span>
    
</fieldset>

<fieldset id="advanced-gallery-images" class="cfct-form-section">
  
  <legend><?php _e( 'Images', wp_festival2('domain') ) ?></legend>
  
  <script type="text/javascript">
    (function($) {
      var frame;
      
      var imagesViewModel = function( images ) {
        var self = this;
        self.images = ko.observableArray( images );
        self.removeImage = function() {
            self.images.remove(this);
        };
      };
      
      $( function() {
          $('#gallery-media-images-add').click( function( event ) {
              var $el = $(this);
              event.preventDefault();

              if ( frame ) {
                  frame.open();
                  return;
              }

              frame = wp.media.frames.advancedGallery = wp.media({
                  title: $el.data('choose'),
                  library: {
                      type: 'image'
                  },
                  button: {
                      text: $el.data('update'),
                      close: false
              },
              multiple: true
            });

            frame.on('select', function() {
              var selection = frame.state().get('selection');
              
              selection.map( function( attachment ) {
                attachment = attachment.toJSON();
                images_vm.images.push( {id:attachment.id, url:attachment.url} );
              });
              
              frame.close();
            });

            frame.open();
          });
          
          ko.applyBindings(images_vm = new imagesViewModel(<?php echo json_encode( $_new_images ); ?>), document.getElementById("gallery-media-images-list"));
        });
      }(jQuery));
  </script>
  
  <a id="gallery-media-images-add" href="#"
    data-choose="<?php esc_attr_e( 'Select' ); ?>"
    data-update="<?php esc_attr_e( 'Update' ); ?>"><?php _e( 'Select Images' ); ?>
  </a>
  
  <ul id="gallery-media-images-list" data-bind="foreach: images">
    <li>
      <input type="hidden" data-bind="attr:{name:'images['+id+'][id]'},value:id" />
      <input type="hidden" data-bind="attr:{name:'images['+id+'][url]'},value:url" />
      <a href="#" data-bind="click: $parent.removeImage">X</a>
      <div class="clearfix"></div>
      <img data-bind="attr:{src:url}" />
    </li>
  </ul>
  
</fieldset>