<?php
ob_start();
dynamic_sidebar('winner_widget_area');
$winners = ob_get_clean();
$winners = json_decode( $winners, true );
?>


<?php
if ( (array_key_exists('type', $winners)) && ($winners[ 'type' ] == 'widget_winner') ):
  if ( (array_key_exists('data', $winners)) && (!empty($winners[ 'data' ])) ):
    ?>
    <div class="winners">

      <div class="row">
        <div class="col-xs-12">
          <h5><span>Our Recent Winners</span></h5>
        </div>
      </div>

      <div class="row clearfix">
        <div class="col-xs-12 clearfix">
          <div class="winners-slider-container">
            <div class="winners-slider">
              <?php
              $winners = $winners[ 'data' ];
              for( $i = 0, $mi = count( $winners ); $i < $mi; $i++ ):
                ?>

                <a href="<?php echo $winners[ $i ][ 'post_url' ]; ?>" class="item item-<?php echo $winners[ $i ][ 'source' ]; ?>">
                  <div class="inner">
                    <img src="<?php echo $winners[ $i ][ 'author_profile_picture' ]; ?>" alt="Profile" class="avatar">

                    <div class="meta">
                      <h4><?php echo $winners[ $i ][ 'author_name' ]; ?></h4>
                      <time><?php echo $winners[ $i ][ 'created_time' ]; ?></time>
                    </div>
                    <div class="clearfix"></div>

                    <p><?php echo $winners[ $i ][ 'title' ]; ?></p>
                  </div>

                  <?php if ( $winners[ $i ][ 'image_url' ] !== null ): ?>
                    <img src="<?php echo $winners[ $i ][ 'image_url' ]; ?>" alt="Image" class="main-pic">
                  <?php endif; ?>

                </a>

              <?php endfor; ?>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-xs-12">
          <div class="indicator-container">
            <div class="indicator-parent">
              <div class="indicator">
                <span class="icon-spectacle-indicator"></span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  <?php endif; endif; ?>