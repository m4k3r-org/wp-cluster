<?php
/**
 * 
 */
?>
<div class="wrap">
  <h2><?php _e( 'JavaScript Editor', get_wp_amd( 'text_domain' ) ); ?></h2>
  <form action="themes.php?page=amd-scripts" method="post" id="global-javascript-form">
    <?php wp_nonce_field( 'update_global_js_js', 'update_global_js_js_field' ); ?>
    <div class="metabox-holder has-right-sidebar">

      <div class="inner-sidebar">

        <div class="postbox">
          <h3><span><?php _e( 'Publish', get_wp_amd( 'text_domain' ) ); ?></span></h3>
          <div class="inside">
            <input class="button-primary" type="submit" name="publish" value="<?php _e( 'Save Javascript', get_wp_amd( 'text_domain' ) ); ?>"/>
          </div>
        </div>
        <div class="postbox">
          <h3><span><?php _e( 'Dependency', get_wp_amd( 'text_domain' ) ); ?></span></h3>
          <div class="inside">
            <?php foreach( get_wp_amd()->get_all_dependencies( 'javascript' ) as $dep => $dep_array ): ?>
              <label>
                <input type="checkbox" name="dependency[]" value="<?php echo $dep; ?>" <?php checked( in_array( $dep, $dependency ), true ); ?> />
                <a href="<?php echo $dep_array[ 'infourl' ]; ?>"> <?php echo $dep_array[ 'name' ]; ?> </a>
              </label>
              <br/>
            <?php endforeach; ?>
          </div>
        </div>
        <!-- ... more boxes ... -->
        <?php do_meta_boxes( 's-global-javascript', 'normal', $js ); ?>

      </div> <!-- .inner-sidebar -->

      <div id="post-body">
        <div id="post-body-content">
          <div id="global-editor-shell">
          <textarea style="width:100%; height: 360px; resize: none;" id="global-javascript" class="wp-editor-area" name="global-javascript"><?php echo $js[ 'post_content' ]; ?></textarea>
          </div>
        </div> <!-- #post-body-content -->
      </div> <!-- #post-body -->

    </div> <!-- .metabox-holder -->
  </form>
</div> <!-- .wrap -->