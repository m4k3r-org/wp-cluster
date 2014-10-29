<?php
/**
 * @package Admin
 */
 
/**
 * Display the updated/error messages
 * Only needed as our settings page is not under options, otherwise it will automatically be included
 * @see settings_errors()
 */
require_once( ABSPATH . 'wp-admin/options-head.php' );
?>
<div class="wrap wpseo-admin-page wpseo-custom-meta">
  <h2 id="wpseo-title"><?php echo esc_html( get_admin_page_title() ); ?></h2>
  <div class="wpseo_content_wrapper">
    <div class="wpseo_content_cell" id="wpseo_content_top">
      <div class="metabox-holder">
        <div class="meta-box-sortables">
          <form name="wpseo-custom-meta" action="" method="POST">
            <ul>
              <?php for( $i=1; $i <= 10; $i++ ) : ?>
                <?php $option = isset( $options[ $i ] ) ? $options[ $i ] : array( 'name' => '', 'type' => '', 'content' => '' ) ?>
                <li>
                  <ul>
                    <li class="name">
                      <label><?php _e( 'Name', get_wp_seo_addon( 'text_domain' ) ); ?> <input type="text" value="<?php echo $option[ 'name' ] ?>" name="wpseo_custom_meta[<?php echo $i ?>][name]" /></label>
                      <select name="wpseo_custom_meta[<?php echo $i ?>][type]" >
                        <option value="name" <?php echo $option[ 'type' ] == 'name' ? 'selected="selected"' : ''; ?>>name</option>
                        <option value="property" <?php echo $option[ 'type' ] == 'property' ? 'selected="selected"' : ''; ?>>property</option>
                      </select>
                    </li>
                    <li class="content">
                      <label><?php _e( 'Content', get_wp_seo_addon( 'text_domain' ) ); ?> <input type="text" value="<?php echo $option[ 'content' ] ?>" name="wpseo_custom_meta[<?php echo $i ?>][content]" /></label>
                    </li>
                  </ul>
                  <p class="desc"></p>
                </li>
              <?php endfor; ?>
              <li><?php submit_button(); ?></li>
            </ul>
          </form>
        </div><!-- end of div meta-box-sortables -->
      </div><!-- end of div metabox-holder -->
    </div><!-- end of div wpseo_content_top -->
  </div><!-- end of div wpseo_content_wrapper -->
</div><!-- end of wrap -->