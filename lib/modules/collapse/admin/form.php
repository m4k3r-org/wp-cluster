<script type="text/javascript">
//** Init form UI */
jQuery(document).ready(function(){
  //** Make them sortable */
  jQuery( "#accordion" ).sortable({
    axis: "y",
    handle: "a.collapse-sort",
    items: "div"
  });
  //** Add button handler */
  jQuery( "#collapse-add-bar" ).on('click', function() {
    var newDiv = '<div class="item"><a class="collapse-delete" href="javascript:void(0);"><?php _e('Delete'); ?></a><input type="text" name="<?php echo $this->get_field_name( 'bars[]' ) ?>" /><div class="clear"></div><textarea name="<?php echo $this->get_field_name( 'contents[]' ) ?>"></textarea></div>';
    jQuery('#accordion').append(newDiv);
    jQuery( "#accordion" ).sortable('refresh');
  });
  //** Delete button handler */
  jQuery( "#accordion .collapse-delete" ).live('click', function(){
    jQuery( this ).parents(".item").remove();
    jQuery( "#accordion" ).sortable('refresh');
  });
});
</script>

<style type="text/css">
  .collapse-sort {cursor:move;}
</style>

<div class="collapse-form">
  <fieldset class="cfct-form-section">
    <legend><?php _e( 'Title' ); ?></legend>

    <input type="text" value="<?php echo $data[$this->get_field_name( 'title' )]; ?>" name="<?php echo $this->get_field_name( 'title' ) ?>" />
  </fieldset>

  <fieldset class="cfct-form-section">
    <legend><?php _e( 'Bars' ); ?></legend>

    <div id="accordion">
      <?php if ( !empty( $data[$this->get_field_name( 'bars' )] ) && is_array( $data[$this->get_field_name( 'bars' )] ) ): ?>
      <?php foreach( $data[$this->get_field_name( 'bars' )] as $key => $bar ): ?>
        <div class="item">
          <a class="collapse-sort" href="javascript:void(0);"><?php _e('Sort'); ?></a>
          <a class="collapse-delete" href="javascript:void(0);"><?php _e('Delete'); ?></a>
          <input type="text" value="<?php echo $bar; ?>" name="<?php echo $this->get_field_name( 'bars[]' ) ?>" />
          <div class="clear"></div>
          <textarea name="<?php echo $this->get_field_name( 'contents[]' ) ?>"><?php echo $data[$this->get_field_name( 'contents' )][$key]; ?></textarea>
        </div>
      <?php endforeach; ?>
      <?php else: ?>
        <div class="item">
          <a class="collapse-delete" href="javascript:void(0);"><?php _e('Delete'); ?></a>
          <input type="text" name="<?php echo $this->get_field_name( 'bars[]' ) ?>" />
          <div class="clear"></div>
          <textarea name="<?php echo $this->get_field_name( 'contents[]' ) ?>"></textarea>
        </div>
      <?php endif; ?>
    </div>

    <button type="button" id="collapse-add-bar"><?php _e( 'Add row' ) ?></button>
  </fieldset>

</div>