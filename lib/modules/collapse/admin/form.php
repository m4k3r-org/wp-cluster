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
        items: "div",
//        receive: function(event, ui) {
//            jQuery(ui.item).removeClass();
//            jQuery(ui.item).removeAttr("style");
//            jQuery( ".questions" ).accordion("add", "<div>" + ui.item.hmtl() + "</div>");
//        }
    });

    jQuery( "#collapse-add-bar" ).on('click', function() {
        var newDiv = '<div><h3><input type="text" name="<?php echo $this->get_field_name( 'bars[]' ) ?>" /></h3><div><textarea name="<?php echo $this->get_field_name( 'contents[]' ) ?>"></textarea></div></div>';
        jQuery('#accordion').append(newDiv);
        jQuery('#accordion').accordion("refresh");
    });
});

</script>

<div class="collapse-form">

  <fieldset class="cfct-form-section">
    <legend><?php _e( 'Bars' ); ?></legend>

    <div id="accordion">
      <div class="item">
        <h3><input type="text" name="<?php echo $this->get_field_name( 'bars[]' ) ?>" /></h3>
        <div>
          <textarea name="<?php echo $this->get_field_name( 'contents[]' ) ?>"></textarea>
        </div>
      </div>
    </div>

    <button type="button" id="collapse-add-bar"><?php _e( 'Add row' ) ?></button>
  </fieldset>

</div>