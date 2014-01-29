<?php
/**
 * The Artist Block
 *
 * @author Usability Dynamics
 * @module festival
 * @since festival 0.1.0
 */

$festival = wp_festival();

?>

<div class="row heading">
  <div class="col-md-12 col-sm-12 text-center">
    <h3>The Artist</h3>
    <span class="hr"></span>
    <p>Donec ultrices ultricies tellus, vel molestie massa hendrerit eu. Curabitur egestas semper nunc, at suscipit arcu tristique eget.</p>
  </div>
</div>

<div class="row block artist-previews">

  <div class="col-md-3 col-sm-3 artist-preview">
    <div class="date">
      <span class="week-day">Monday,</span> <span class="month">Nov</span> <span class="day">18</span>
      <span class="hr"></span>
      <div class="clearfix"></div>
    </div>
    <div class="image">
      <img class="img-responsive" src="<?php echo $festival->get_image_link_by_post_id( -1, array( 'width' => '738', 'height' => '880' ) ); ?>" />
      <div class="caption">Baaver</div>
    </div>
  </div>

  <div class="col-md-3 col-sm-3 artist-preview">
    <div class="date"></div>
    <div class="image">
      <img class="img-responsive" src="<?php echo $festival->get_image_link_by_post_id( -1, array( 'width' => '738', 'height' => '880' ) ); ?>" />
      <div class="caption">Brodinski</div>
    </div>
  </div>

  <div class="col-md-3 col-sm-3 artist-preview">
    <div class="date">
      <span class="week-day">Monday,</span> <span class="month">Nov</span> <span class="day">19</span>
      <span class="hr"></span>
      <div class="clearfix"></div>
    </div>
    <div class="image">
      <img class="img-responsive" src="<?php echo $festival->get_image_link_by_post_id( -1, array( 'width' => '738', 'height' => '880' ) ); ?>" />
      <div class="caption">Baaver</div>
    </div>
  </div>

  <div class="col-md-3 col-sm-3 artist-preview">
    <div class="date"></div>
    <div class="image">
      <img class="img-responsive" src="<?php echo $festival->get_image_link_by_post_id( -1, array( 'width' => '738', 'height' => '880' ) ); ?>" />
      <div class="caption">Green Zommbie</div>
    </div>
  </div>

</div>