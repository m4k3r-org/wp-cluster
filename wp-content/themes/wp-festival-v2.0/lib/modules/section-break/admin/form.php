<div class="section-break-form">

  <fieldset class="cfct-form-section">
    <legend><?php _e( 'Break Line Options' ); ?></legend>
    <label>
      <input <?php echo (!empty($data[$this->get_field_name( 'type' )])&&$data[$this->get_field_name( 'type' )]=='solid'?'checked="checked"':''); ?> name="<?php echo $this->get_field_name( 'type' ); ?>" type="radio" value="solid" />
      <?php _e( 'Solid' ); ?>
    </label>
    <label>
      <input <?php echo (!empty($data[$this->get_field_name( 'type' )])&&$data[$this->get_field_name( 'type' )]=='dotted'?'checked="checked"':''); ?> name="<?php echo $this->get_field_name( 'type' ); ?>" type="radio" value="dotted" />
      <?php _e( 'Dotted' ); ?>
    </label>
    <label>
      <input <?php echo (!empty($data[$this->get_field_name( 'type' )])&&$data[$this->get_field_name( 'type' )]=='dashed'?'checked="checked"':''); ?> name="<?php echo $this->get_field_name( 'type' ); ?>" type="radio" value="dashed" />
      <?php _e( 'Dashed' ); ?>
    </label>
    <label>
      <input <?php echo (!empty($data[$this->get_field_name( 'type' )])&&$data[$this->get_field_name( 'type' )]=='large-dashes'?'checked="checked"':''); ?> name="<?php echo $this->get_field_name( 'type' ); ?>" type="radio" value="large-dashes" />
      <?php _e( 'Large Dashes' ); ?>
    </label>
  </fieldset>

</div>