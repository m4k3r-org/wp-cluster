<?php
if( !class_exists( 'ImageModule' ) ) {
  require_once( dirname( dirname( __FILE__ ) ) . '/image/image.php' );
}
if( !class_exists( 'FestivalHeroModule' ) && class_exists( 'ImageModule' ) ) {
  class FestivalHeroModule extends ImageModule {
    protected $_deprecated_id = 'cfct-module-hero'; // deprecated property, not needed for new module development

    protected $default_alignment = 'center-center';
    protected $default_box_height = 150;

    /**
     * Set up the module
     */
    public function __construct() {
      $opts = array(
        'description' => __( 'Superimage!', 'carrington-build' ),
        'icon'        => 'hero/icon.png'
      );

      cfct_build_module::__construct( 'cfct-module-hero', __( 'Hero', 'carrington-build' ), $opts );
    }

    public function admin_form( $data ) {
      $html = '';
      $html .= parent::admin_form( $data );

      // content
      $html .= '
				<fieldset class="cfct-ftl-border ' . $this->id_base . '-content-section">
					<legend>Content</legend>
					<div class="cfct-inline-els">
						<label for="' . $this->get_field_id( 'title' ) . '">' . __( 'Title' ) . '</label>
						<input type="text" name="' . $this->get_field_name( 'title' ) . '" id="' . $this->get_field_id( 'title' ) . '" value="' . ( !empty( $data[ $this->get_field_name( 'title' ) ] ) ? esc_html( $data[ $this->get_field_name( 'title' ) ] ) : '' ) . '" />
					</div>
					<div class="clear"></div>
					<div class="cfct-inline-els">
						<label for="' . $this->get_field_id( 'content' ) . '">' . __( 'Content' ) . '</label>
						<textarea name="' . $this->get_field_name( 'content' ) . '" id="' . $this->get_field_id( 'content' ) . '">'
            . ( !empty( $data[ $this->get_field_name( 'content' ) ] ) ? htmlspecialchars( $data[ $this->get_field_name( 'content' ) ] ) : '' ) .
            '</textarea>
          </div>
        </fieldset>
      ';
    
      // content
      $html .= '
        <style>
          .share label { width: auto !important; }
          .share span { line-height: 30px; padding-left: 6px; }
          .share input[type="text"] { width: 30% !important; margin-left: 10px; }
        </style>
				<fieldset class="cfct-ftl-border ' . $this->id_base . '-content-section share">
					<legend>Share</legend>
					<div class="cfct-inline-els">
						<label for="' . $this->get_field_id( 'fb_like' ) . '"> 
              <input type="checkbox" name="' . $this->get_field_name( 'fb_like' ) . '" id="' . $this->get_field_id( 'fb_like' ) . '" value="true" ' . ( !empty( $data[ $this->get_field_name( 'fb_like' ) ] ) && $data[ $this->get_field_name( 'fb_like' ) ] == 'true' ? 'checked="checked"' : '' ) . '" />
              <span>' . __( 'Enable Facebook Like Button' ) . '</span>
            </label>
					</div>
          <div class="clear"></div>
          <div class="cfct-inline-els">
						<label for="' . $this->get_field_id( 'fb_app_id' ) . '">' . __( 'Facebook Application ID' ) . '</label>
						<input type="text" name="' . $this->get_field_name( 'fb_app_id' ) . '" id="' . $this->get_field_id( 'fb_app_id' ) . '" value="' . ( !empty( $data[ $this->get_field_name( 'fb_app_id' ) ] ) ? esc_html( $data[ $this->get_field_name( 'fb_app_id' ) ] ) : '' ) . '" />
            <span>(' . __( 'Required' ) . ')</span>
					</div>
          <div class="cfct-inline-els">
						<label for="' . $this->get_field_id( 'fb_url' ) . '">' . __( 'Facebook Custom Like URL' ) . '</label>
						<input type="text" name="' . $this->get_field_name( 'fb_url' ) . '" id="' . $this->get_field_id( 'fb_url' ) . '" value="' . ( !empty( $data[ $this->get_field_name( 'fb_url' ) ] ) ? esc_html( $data[ $this->get_field_name( 'fb_url' ) ] ) : '' ) . '" />
            <span>(' . __( 'Optional' ) . ')</span>
					</div>
					<div class="clear"></div>
          <div class="cfct-inline-els">
						<label for="' . $this->get_field_id( 'tw_share' ) . '"> 
              <input type="checkbox" name="' . $this->get_field_name( 'tw_share' ) . '" id="' . $this->get_field_id( 'tw_share' ) . '" value="true" ' . ( !empty( $data[ $this->get_field_name( 'tw_share' ) ] ) && $data[ $this->get_field_name( 'tw_share' ) ] == 'true' ? 'checked="checked"' : '' ) . '" />
              <span>' . __( 'Enable Twitter Share Button' ) . '</span>
            </label>
					</div>
          <div class="clear"></div>
          <div class="cfct-inline-els">
						<label for="' . $this->get_field_id( 'tw_account' ) . '">' . __( 'Twitter Account' ) . '</label>
						<input type="text" name="' . $this->get_field_name( 'tw_account' ) . '" id="' . $this->get_field_id( 'tw_account' ) . '" value="' . ( !empty( $data[ $this->get_field_name( 'tw_account' ) ] ) ? esc_html( $data[ $this->get_field_name( 'tw_account' ) ] ) : '' ) . '" />
            <span>(' . __( 'Optional' ) . ')</span>
					</div>
					<div class="clear"></div>
          <div class="cfct-inline-els">
						<label for="' . $this->get_field_id( 'tw_hashtag' ) . '">' . __( 'Twitter Hashtag' ) . '</label>
						<input type="text" name="' . $this->get_field_name( 'tw_hashtag' ) . '" id="' . $this->get_field_id( 'tw_hashtag' ) . '" value="' . ( !empty( $data[ $this->get_field_name( 'tw_hashtag' ) ] ) ? esc_html( $data[ $this->get_field_name( 'tw_hashtag' ) ] ) : '' ) . '" />
            <span>(' . __( 'Optional' ) . ')</span>
					</div>
					<div class="clear"></div>
          <div class="cfct-inline-els">
						<label for="' . $this->get_field_id( 'gp_share' ) . '"> 
              <input type="checkbox" name="' . $this->get_field_name( 'gp_share' ) . '" id="' . $this->get_field_id( 'gp_share' ) . '" value="true" ' . ( !empty( $data[ $this->get_field_name( 'gp_share' ) ] ) && $data[ $this->get_field_name( 'gp_share' ) ] == 'true' ? 'checked="checked"' : '' ) . '" />
              <span>' . __( 'Enable Google Plus Share Button' ) . '</span>
            </label>
					</div>
					<div class="clear"></div>
        </fieldset>
      ';

      // formatting
      $selected_alignment = ( !empty( $data[ $this->get_field_name( 'hero_alignment' ) ] ) ? esc_attr( $data[ $this->get_field_name( 'hero_alignment' ) ] ) : $this->default_alignment );
      $html .= '
				<fieldset class="cfct-ftl-border">
					<legend>Formatting</legend>
					<div class="cfct-inline-els ' . $this->id_base . '-c6-12">
						<label>Image Alignment</label>
						<div class="' . $this->id_base . '-hero-align-select">
							<table>
								<tbody>
									<tr>
										<td><input type="radio" name="' . $this->get_field_name( 'hero_alignment' ) . '" value="top-left" ' . checked( $selected_alignment, 'top-left', false ) . '/>
										<td><input type="radio" name="' . $this->get_field_name( 'hero_alignment' ) . '" value="top-center" ' . checked( $selected_alignment, 'top-center', false ) . '/>
										<td><input type="radio" name="' . $this->get_field_name( 'hero_alignment' ) . '" value="top-right" ' . checked( $selected_alignment, 'top-right', false ) . '/>
									</tr>
									<tr>
										<td><input type="radio" name="' . $this->get_field_name( 'hero_alignment' ) . '" value="center-left" ' . checked( $selected_alignment, 'center-left', false ) . '/>
										<td><input type="radio" name="' . $this->get_field_name( 'hero_alignment' ) . '" value="center-center" ' . checked( $selected_alignment, 'center-center', false ) . '/>
										<td><input type="radio" name="' . $this->get_field_name( 'hero_alignment' ) . '" value="center-right" ' . checked( $selected_alignment, 'center-right', false ) . '/>
									</tr>
									<tr>
										<td><input type="radio" name="' . $this->get_field_name( 'hero_alignment' ) . '" value="bottom-left" ' . checked( $selected_alignment, 'bottom-left', false ) . '/>
										<td><input type="radio" name="' . $this->get_field_name( 'hero_alignment' ) . '" value="bottom-center" ' . checked( $selected_alignment, 'bottom-center', false ) . '/>
										<td><input type="radio" name="' . $this->get_field_name( 'hero_alignment' ) . '" value="bottom-right" ' . checked( $selected_alignment, 'bottom-right', false ) . '/>
									</tr>
								</tbody>
							</table>
							<div id="' . $this->id_base . '-selected-alignment">' . str_replace( '-', '/', $selected_alignment ) . '</div>
						</div>
					</div>
					<div class="' . $this->id_base . '-c6-34">
						<label for="' . $this->get_field_id( 'box-height' ) . '">' . __( 'Box Height', 'carrington-build' ) . '</label>
						<input type="text" name="' . $this->get_field_name( 'box-height' ) . '" id="' . $this->get_field_id( 'box-height' ) . '" value="' . ( !empty( $data[ $this->get_field_name( 'box-height' ) ] ) ? esc_attr( $data[ $this->get_field_name( 'box-height' ) ] ) : $this->default_box_height ) . '" /> <span>pixels</span>
					</div>
				</fieldset>
			';

      return $html;
    }

    /**
     * Display the module content in the Post-Content
     *
     * @param array $data - saved module data
     *
     * @return array string HTML
     */
    public function display( $data ) {
      global $wp_query;
      /** Backup wp_query */
      $wp_query->data[ 'hero' ] = array(
        'image_src' => ( isset( $data[ $this->get_field_name( 'image_id' ) ] ) ? wp_get_attachment_image_src( intval( $data[ $this->get_field_name( 'image_id' ) ] ), esc_attr( $data[ $this->get_field_name( 'image_id' ) . '-size' ] ), false ) : '' ),
        'image_alignment' => ( isset( $data[ $this->get_field_name( 'image_id' ) ] ) ? esc_attr( str_replace( '-', ' ', $data[ $this->get_field_name( 'hero_alignment' ) ] ) ) : '' ),
        'title' => ( !empty( $data[ $this->get_field_name( 'title' ) ] ) ? esc_html( $data[ $this->get_field_name( 'title' ) ] ) : '' ),
        'content' => ( !empty( $data[ $this->get_field_name( 'content' ) ] ) ? $this->wp_formatting( $data[ $this->get_field_name( 'content' ) ] ) : '' ),
        'box_height' => ( !empty( $data[ $this->get_field_name( 'box-height' ) ] ) ? intval( $data[ $this->get_field_name( 'box-height' ) ] ) : 0 ),
        'id_base' => $this->id_base,
        'url' => $this->get_link_url( $data ),
        'fb_like' => ( !empty( $data[ $this->get_field_name( 'fb_like' ) ] ) && $data[ $this->get_field_name( 'fb_like' ) ] == 'true' ? true : false ),
        'fb_app_id' => ( !empty( $data[ $this->get_field_name( 'fb_app_id' ) ] ) ? esc_html( $data[ $this->get_field_name( 'fb_app_id' ) ] ) : '' ),
        'fb_url' => ( !empty( $data[ $this->get_field_name( 'fb_url' ) ] ) ? esc_html( $data[ $this->get_field_name( 'fb_url' ) ] ) : '' ),
        'tw_share' => ( !empty( $data[ $this->get_field_name( 'tw_share' ) ] ) && $data[ $this->get_field_name( 'tw_share' ) ] == 'true' ? true : false ),
        'tw_account' => ( !empty( $data[ $this->get_field_name( 'tw_account' ) ] ) ? esc_html( $data[ $this->get_field_name( 'tw_account' ) ] ) : '' ),
        'tw_hashtag' => ( !empty( $data[ $this->get_field_name( 'tw_hashtag' ) ] ) ? esc_html( $data[ $this->get_field_name( 'tw_hashtag' ) ] ) : '' ),
        'gp_share' => ( !empty( $data[ $this->get_field_name( 'gp_share' ) ] ) && $data[ $this->get_field_name( 'gp_share' ) ] == 'true' ? true : false ),
      );
      /** Get our template */
      ob_start();
      get_template_part( 'templates/aside/hero' );
      /** Return our string */
      return ob_get_clean();
      
    }

    /**
     * Add custom javascript to the post/page admin
     *
     * @return string JavaScript
     */
    public function admin_js() {
      $js = parent::admin_js();
      $js .= '
				cfct_builder.addModuleLoadCallback("' . $this->id_base . '", function() {
					$("input[name=' . $this->get_field_name( 'hero_alignment' ) . ']").click(function() {
						$("#' . $this->id_base . '-selected-alignment").html($(this).val().replace("-", "/"));
					});
				});
			';

      return $js;
    }

    public function admin_css() {
      $css = parent::admin_css();
      $css .= '
				#' . $this->id_base . '-edit-form fieldset.' . $this->id_base . '-content-section .cfct-inline-els textarea {
					height: 150px;
				}	
				#' . $this->id_base . '-edit-form fieldset .cfct-inline-els.' . $this->id_base . '-c6-12 {
					width: 300px;
					height: 100px;
					float: left;
				}
				#' . $this->id_base . '-edit-form fieldset .' . $this->id_base . '-c6-34 {
					float: left;
					display: block;
					margin-top: 10px;
					margin-top: 5px;
				}
				#' . $this->id_base . '-edit-form fieldset .' . $this->id_base . '-c6-34 input[type=text] {
					width: 50px;
				}
				#' . $this->id_base . '-edit-form fieldset .' . $this->id_base . '-c6-34 label {
					width: 90px;
				}
				#' . $this->id_base . '-edit-form fieldset .' . $this->id_base . '-hero-align-select {
					float: left;
				}
				#' . $this->id_base . '-edit-form fieldset .' . $this->id_base . '-hero-align-select table td {
					padding: 5px;
				}
				#' . $this->id_base . '-edit-form fieldset .' . $this->id_base . '-selected-alignment {
					text-align: center;
				}
			';

      return $css;
    }

// Content Move Helpers

    protected $reference_fields = array( 'global_image', 'post_image', 'image_id' );

    public function get_referenced_ids( $data ) {
      $references = array();
      foreach( $this->reference_fields as $field ) {
        $id = $this->get_data( $field, $data );
        if( $id ) {
          $post                 = get_post( $id );
          $references[ $field ] = array(
            'type'      => 'post_type',
            'type_name' => $post->post_type,
            'value'     => $id
          );
        }
      }

      return $references;
    }

    public function merge_referenced_ids( $data, $reference_data ) {
      if( !empty( $reference_data ) && !empty( $data ) ) {
        foreach( $this->reference_fields as $field ) {
          if( isset( $data[ $this->gfn( $field ) ] ) && isset( $reference_data[ $field ] ) ) {
            $data[ $this->gfn( $field ) ] = $reference_data[ $field ][ 'value' ];
          }
        }
      }

      return $data;
    }
  }
}