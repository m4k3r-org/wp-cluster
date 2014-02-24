<!--<div class="cb-collapse-accordion ">
  <?php if ( !empty( $data[$this->get_field_name( 'bars' )] ) && is_array( $data[$this->get_field_name( 'bars' )] ) ): ?>
  <?php foreach( $data[$this->get_field_name( 'bars' )] as $key => $bar ): ?>
    <div class="item">
      <h3><?php echo $bar; ?></h3>
      <div><?php echo $data[$this->get_field_name( 'contents' )][$key]; ?></div>
    </div>
  <?php endforeach; ?>
  <?php endif; ?>
</div>-->

<div class="panel-group collapse-module" id="<?php echo $data['module_id']; ?>">
  <?php if ( !empty( $data[$this->get_field_name( 'bars' )] ) && is_array( $data[$this->get_field_name( 'bars' )] ) ): ?>
  <?php foreach( $data[$this->get_field_name( 'bars' )] as $key => $bar ): ?>
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#<?php echo $data['module_id']; ?>" href="#<?php echo $data['module_id'].'_'.$key; ?>">
          <?php echo $bar; ?>
        </a>
      </h4>
    </div>
    <div id="<?php echo $data['module_id'].'_'.$key; ?>" class="panel-collapse collapse">
      <div class="panel-body">
        <?php echo $data[$this->get_field_name( 'contents' )][$key]; ?>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
  <?php endif; ?>
</div>
