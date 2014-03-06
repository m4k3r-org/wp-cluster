<?php
/**
 * 
 */
?>
<div class="wrap">
  <h2><?php _e( 'StyleSheet Editor', get_wp_amd( 'text_domain' ) ); ?></h2>
  <form action="themes.php?page=amd-styles" method="post" id="global-stylesheet-form">
    <?php wp_nonce_field( 'update_global_css_css', 'update_global_css_css_field' ); ?>
    <div class="metabox-holder has-right-sidebar">

      <div class="inner-sidebar">

        <div class="postbox">
          <h3><span><?php _e( 'Publish', get_wp_amd( 'text_domain' ) ); ?></span></h3>
          <div class="inside">
            <input class="button-primary" type="submit" name="publish" value="<?php _e( 'Save Stylesheet' ); ?>"/>
          </div>
        </div>
        <div class="postbox">
          <h3><span><?php _e( 'Dependency', get_wp_amd( 'text_domain' ) ); ?></span></h3>
          <div class="inside">
            <?php foreach( $this->get_all_dependencies( 'stylesheet' ) as $dep => $dep_array ): ?>
              <label><input type="checkbox" name="dependency[]" value="<?php echo $dep; ?>" <?php checked( in_array( $dep, $dependency ), true ); ?> /><a href="<?php echo $dep_array[ 'infourl' ]; ?>"> <?php echo $dep_array[ 'name' ]; ?> </a></label>
              <br/>
            <?php endforeach; ?>
          </div>
        </div>
        <!-- ... more boxes ... -->
        <?php do_meta_boxes( 's-global-stylesheet', 'normal', $css ); ?>

      </div> <!-- .inner-sidebar -->

      <div id="post-body">
        <div id="post-body-content">
          <div id="global-editor-shell">
          <textarea style="width:100%; height: 360px; resize: none;" id="global-stylesheet" class="wp-editor-area" name="global-stylesheet"><?php echo $css[ 'post_content' ]; ?></textarea>
          </div>
        </div> <!-- #post-body-content -->
      </div> <!-- #post-body -->

    </div> <!-- .metabox-holder -->
  </form>
</div> <!-- .wrap -->