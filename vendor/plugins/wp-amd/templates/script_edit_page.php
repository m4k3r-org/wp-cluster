<?php
/**
 * 
 */
?>
<?php if( !empty( $data[ 'msg' ] ) ) : ?>
  <div class="updated"><p><strong><?php echo $data[ 'msg' ]; ?></strong></p></div>
<?php endif; ?>
<div class="wrap">
  <h2><?php _e( 'JavaScript Editor', get_wp_amd( 'text_domain' ) ); ?></h2>
  <form action="themes.php?page=amd-page-script" method="post" id="global-javascript-form">
    <?php wp_nonce_field( 'update_amd_script', 'update_amd_script_nonce' ); ?>
    <div class="metabox-holder has-right-sidebar">

      <div class="inner-sidebar">

        <div class="postbox">
          <h3><span><?php _e( 'Publish', get_wp_amd( 'text_domain' ) ); ?></span></h3>
          <div class="inside">
            <input class="button-primary" type="submit" name="publish" value="<?php _e( 'Save Javascript', get_wp_amd( 'text_domain' ) ); ?>"/>
          </div>
        </div>
        
        <?php do_meta_boxes( 'amd_script', 'normal', $data ); ?>

      </div> <!-- .inner-sidebar -->

      <div id="post-body">
        <div id="post-body-content">
          <div id="global-editor-shell">
          <textarea style="width:100%; height: 360px; resize: none;" id="global-javascript" class="wp-editor-area" name="content"><?php echo $data[ 'post_content' ]; ?></textarea>
          </div>
        </div> <!-- #post-body-content -->
      </div> <!-- #post-body -->

    </div> <!-- .metabox-holder -->
  </form>
</div> <!-- .wrap -->