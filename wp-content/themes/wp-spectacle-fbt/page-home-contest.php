<?php
ob_start();
dynamic_sidebar( 'contest_widget_area' );
$widget = ob_get_clean();

$data = json_decode( $widget );

if( isset( $data->type ) && ($data->type == 'widget_contest_countdown') && !empty( $data->data->dates ) ) :

  ?>
<div class="contest">
  <div class="container">
    <div class="row">
      <div class="col-xs-12">
        <a href="#" class="icon-downarrow visible-xs visible-sm hidden-md hidden-lg"></a>
        <h2><?php echo $data->data->title; ?></h2>
      </div>
    </div>

    <div class="row mobile-invisible">
      <div class="col-xs-12">
        <p><?php echo $data->data->description; ?></p>
      </div>
    </div>

    <div class="row mobile-invisible">
      <div class="col-xs-12">
        <div class="countdown clearfix" data-todate="2014-08-18">
          <div class="box days">
            <div>
              <strong>07</strong>
              Days
            </div>
          </div>
          <div class="box hours">
            <div>
              <strong>00</strong>
              Hrs
            </div>
          </div>

          <div class="box box-big vip-pass">
            <div>
              Win Free
              <strong>Tickets</strong>

              <span class="icon-ticket"></span>
            </div>
          </div>

          <div class="box minutes">
            <div>
              <strong>00</strong>
              Min
            </div>
          </div>
          <div class="box seconds">
            <div>
              <strong>00</strong>
              Sec
            </div>
          </div>
        </div>
      </div>
    </div>

    <?php get_template_part('page-home', 'winners'); ?>

  </div>
</div>
<script type="text/javascript">
  var countdown_data = <?php echo json_encode($data->data); ?>
</script>
<?php
else:
  echo $widget;
endif; ?>
<!-- .contest -->