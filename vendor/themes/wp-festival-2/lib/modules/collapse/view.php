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
  <?php if ( !empty( $data[$this->get_field_name( 'title' )] ) ): ?>
  <h2 class="container-fluid"><?php echo $data[$this->get_field_name( 'title' )]; ?></h2>
  <?php endif; ?>
  <?php if ( !empty( $data[$this->get_field_name( 'bars' )] ) && is_array( $data[$this->get_field_name( 'bars' )] ) ): ?>
  <?php foreach( $data[$this->get_field_name( 'bars' )] as $key => $bar ): ?>
  <div class="panel panel-default">
    <div data-toggle="collapse" data-parent="#<?php echo $data['module_id']; ?>" class="panel-heading collapsed" href="#<?php echo $data['module_id'].'_'.$key; ?>">
      <h4 class="panel-title">
        <a href="javascript:void(0);" class="down">
          <?php echo $bar; ?>
        </a>
        <span class="collapse-sign icon icon-down"></span>
        <span class="collapse-sign icon icon-up"></span>
        <div class="clearfix"></div>
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
