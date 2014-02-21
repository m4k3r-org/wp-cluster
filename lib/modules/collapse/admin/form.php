<script type="text/javascript">
//** Init form UI */
jQuery(document).ready(function(){
  //** Add accordion */
  jQuery( "#accordion" ).accordion({
    header: "> div > h3",
    collapsible: true,
    active: false,
    autoHeight: false,
    autoActivate: true
  });
  //** Make them sortable */
  jQuery( "#accordion" ).sortable({
    axis: "y",
    handle: "h3",
    items: "div"
  });
  //** Add button handler */
  jQuery( "#collapse-add-bar" ).on('click', function() {
    var newDiv = '<div><h3><input type="text" name="<?php echo $this->get_field_name( 'bars[]' ) ?>" /></h3><div><textarea name="<?php echo $this->get_field_name( 'contents[]' ) ?>"></textarea></div></div>';
    jQuery('#accordion').append(newDiv);
    jQuery('#accordion').accordion("refresh");
  });
  //** Delete button handler */
  jQuery( "#accordion .collapse-delete" ).on('click', function(){
    jQuery( this ).parents(".item").remove();
  });
});
</script>

<div class="collapse-form">

  <fieldset class="cfct-form-section">
    <legend><?php _e( 'Bars' ); ?></legend>

    <div id="accordion">
      <?php if ( !empty( $data[$this->get_field_name( 'bars' )] ) && is_array( $data[$this->get_field_name( 'bars' )] ) ): ?>
      <?php foreach( $data[$this->get_field_name( 'bars' )] as $key => $bar ): ?>
        <div class="item">
          <h3>
            <input type="text" value="<?php echo $bar; ?>" name="<?php echo $this->get_field_name( 'bars[]' ) ?>" />
            <a class="collapse-delete" href="javascript:void(0);"><?php _e('Delete'); ?></a>
          </h3>
          <div>
            <textarea name="<?php echo $this->get_field_name( 'contents[]' ) ?>"><?php echo $data[$this->get_field_name( 'contents' )][$key]; ?></textarea>
          </div>
        </div>
      <?php endforeach; ?>
      <?php else: ?>
        <div class="item">
          <h3>
            <input type="text" name="<?php echo $this->get_field_name( 'bars[]' ) ?>" />
            <a class="collapse-delete" href="javascript:void(0);"><?php _e('Delete'); ?></a>
          </h3>
          <div>
            <textarea name="<?php echo $this->get_field_name( 'contents[]' ) ?>"></textarea>
          </div>
        </div>
      <?php endif; ?>
    </div>

    <button type="button" id="collapse-add-bar"><?php _e( 'Add row' ) ?></button>
  </fieldset>

</div>