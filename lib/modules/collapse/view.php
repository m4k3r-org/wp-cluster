<script type="text/javascript">
  jQuery( document ).ready(function(){
    jQuery( '.<?php echo $data['module_id']; ?>' ).accordion({
      header: "> div > h3",
      collapsible: true,
      active: false,
      autoHeight: false,
      autoActivate: true
    });
  });
</script>
<div class="cb-collapse-accordion <?php echo $data['module_id']; ?>">
  <?php if ( !empty( $data[$this->get_field_name( 'bars' )] ) && is_array( $data[$this->get_field_name( 'bars' )] ) ): ?>
  <?php foreach( $data[$this->get_field_name( 'bars' )] as $key => $bar ): ?>
    <div class="item">
      <h3><?php echo $bar; ?></h3>
      <div><?php echo $data[$this->get_field_name( 'contents' )][$key]; ?></div>
    </div>
  <?php endforeach; ?>
  <?php endif; ?>
</div>