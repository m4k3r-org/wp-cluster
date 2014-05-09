<?php
/**
 * [hdp_gallery] shortcode template
 *
 * @author peshkov@UD
 */
?>
<div class="hdp_gallery">

  <header class="block-title-wrapper action-wrap hr-bottom pad20">
    <div class="row-fluid">
      <?php if( !empty( $data[ 'title' ] ) ) : ?>
        <h2 class="span9"><?php echo $data[ 'title' ]; ?></h2>
      <?php endif; ?>
      <a class="<?php echo !empty( $data[ 'title' ] ) ? 'span3' : 'span12'; ?> tabs_anchor" data-anchor="<?php echo $data[ 'show_as' ]; ?>" href="#"><i class="icon <?php echo $data[ 'anchor' ][ 'icon' ]; ?> icon-dd"></i><span class="text"><?php echo $data[ 'anchor' ][ 'text' ]; ?></span></a>
    </div>
  </header>

  <?php if( !empty( $data[ 'content' ] ) ) : ?>
    <div class="block-description pad20">
      <?php echo $data[ 'content' ]; ?>
    </div>
  <?php endif; ?>

  <div class="block-gallery-content">
  
    <div class="hdp_gallery_tab tab-gallery <?php echo $data[ 'show_as' ] == 'gallery' ? 'active' : ''; ?>">
      <div class="hdp_gallery_slider">
        <ul class="slides">
          <?php foreach( $data[ 'images' ] as $count => $image ) : $_image = hdp_get_image_link_with_custom_size( $image->ID, '890', '460', true ); ?>
            <li class="hdp_gallery_slide_count_<?php echo $count; ?>">
            <div class="hdp_gallery_slide hdp_gallery_slide_count_<?php echo $count; ?>" data-gallery-slide-count="<?php echo $count; ?>">
              <?php if( in_array( $data[ 'fancybox' ], array( 'on', 'true' ) ) ) : ?>
                <?php if( $_fimage = wp_get_attachment_image_src( $image->ID, 'full' ) ) : ?>
                  <a class="img-wrap fancybox" href="<?php echo $_fimage[ 0 ]; ?>">
                  <img src="<?php echo $_image[ 'url' ]; ?>" alt=""/>
                </a>
                <?php else : ?>
                  <div class="img-wrap">
                    <img src="<?php echo $_image[ 'url' ]; ?>" alt=""/>
                  </div>
                <?php endif; ?>
              <?php elseif( $image->custom_url ) : ?>
                <a class="img-wrap" href="<?php echo $image->custom_url; ?>">
                  <img src="<?php echo $_image[ 'url' ]; ?>" alt=""/>
                </a>
              <?php
              else : ?>
                <div class="img-wrap">
                  <img src="<?php echo $_image[ 'url' ]; ?>" alt=""/>
                </div>
              <?php endif; ?>
              <?php if( !empty( $image->post_title ) ) : ?>
                <?php if( $image->custom_url ) : ?>
                  <h3><a class="img-wrap" href="<?php echo $image->custom_url; ?>"><?php echo $image->post_title; ?></a></h3>
                <?php else: ?>
                  <h3><?php echo $image->post_title; ?></h3>
                <?php endif; ?>
              <?php endif; ?>
              <?php if( !empty( $image->post_excerpt ) ) : ?>
                <p><?php echo $image->post_excerpt; ?></p>
              <?php endif; ?>
            </div>
          </li>
          <?php endforeach; ?>
        </ul>
        <div class="controls_container"></div>
      </div>
    </div>
    
    <div class="hdp_gallery_tab tab-list <?php echo $data[ 'show_as' ] == 'list' ? 'active' : ''; ?>">
      <ul>
      <?php foreach( $data[ 'images' ] as $image ) : $_image = hdp_get_image_link_with_custom_size( $image->ID, '890', '460', true ); ?>
        <li class="hr-bottom pad20">
          <?php if( in_array( $data[ 'fancybox' ], array( 'on', 'true' ) ) ) : ?>
            <?php if( $_fimage = wp_get_attachment_image_src( $image->ID, 'full' ) ) : ?>
              <a class="img-wrap fancybox" href="<?php echo $_fimage[ 0 ]; ?>">
              <img src="<?php echo $_image[ 'url' ]; ?>" alt=""/>
            </a>
            <?php else : ?>
              <div class="img-wrap">
                <img src="<?php echo $_image[ 'url' ]; ?>" alt=""/>
              </div>
            <?php endif; ?>
          <?php elseif( $image->custom_url ) : ?>
            <a class="img-wrap" href="<?php echo $image->custom_url; ?>">
              <img src="<?php echo $_image[ 'url' ]; ?>" alt=""/>
            </a>
          <?php
          else : ?>
            <div class="img-wrap">
              <img src="<?php echo $_image[ 'url' ]; ?>" alt=""/>
            </div>
          <?php endif; ?>
          <?php if( !empty( $image->post_title ) ) : ?>
            <?php if( $image->custom_url ) : ?>
              <h3><a class="img-wrap" href="<?php echo $image->custom_url; ?>"><?php echo $image->post_title; ?></a></h3>
            <?php else: ?>
              <h3><?php echo $image->post_title; ?></h3>
            <?php endif; ?>
          <?php endif; ?>
          <?php if( !empty( $image->post_excerpt ) ) : ?>
            <p><?php echo $image->post_excerpt; ?></p>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
      </ul>
      <footer class="action-wrap">
        <div class="row-fluid">
          <a class="span12 tabs_anchor" data-anchor="<?php echo $data[ 'show_as' ]; ?>" href="#"><i class="icon <?php echo $data[ 'anchor' ][ 'icon' ]; ?> icon-dd"></i><span class="text"><?php echo $data[ 'anchor' ][ 'text' ]; ?></span></a>
        </div>
      </footer>
    </div>
  
  </div>
  
</div>