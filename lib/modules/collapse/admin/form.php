<script type="text/javascript">

jQuery(document).ready(function(){
  jQuery( "#accordion" ).accordion({
    header: "> div > h3",
    collapsible: true,
    active: false,
    autoHeight: false,
    autoActivate: true
  });
  jQuery( "#accordion" ).sortable({
        axis: "y",
        handle: "h3",
        items: "div",
        receive: function(event, ui) {
            jQuery(ui.item).removeClass();
            jQuery(ui.item).removeAttr("style");
            jQuery( ".questions" ).accordion("add", "<div>" + ui.item.hmtl() + "</div>");
        }
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
  </fieldset>

</div>