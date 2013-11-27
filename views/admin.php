<div class="wrap">
<h2 class="title">Veneer Admin</h2>

<?php

if( $_GET[ 'test' ] ) {

  $encoding_job = new \UsabilityDynamics\Job( array(
    "type" => 'encoding',
    "asdf" => 'asdfsa',
  ));

  die( '<pre>' . print_r( $encoding_job, true ) . '</pre>' );

}
?>

</div>
