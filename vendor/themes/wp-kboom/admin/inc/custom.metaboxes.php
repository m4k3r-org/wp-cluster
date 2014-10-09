<?php

if(is_admin()) {
    wp_enqueue_script('custom-meta-boxes', get_template_directory_uri() . '/admin/js/custom.metaboxes.js', array('jquery'),'', true);
    wp_enqueue_style('post-formats', OPTIONS_FRAMEWORK_DIRECTORY .'css/post-formats.css');
}

add_action( 'add_meta_boxes', 'cd_meta_box_add' );
function cd_meta_box_add()
{
	add_meta_box( 'post-details', 'cd_meta_box_cb', 'post', 'normal', 'high' );
}

function cd_meta_box_cb( $post )
{
	$values = get_post_custom( $post->ID );
	$aside_post = isset( $values['aside_post'] ) ? esc_attr( $values['aside_post'][0] ) : '';
	$gallery_post_images = isset( $values['gallery_post_images'] ) ? esc_attr( $values['gallery_post_images'][0] ) : '';
	$image_post_preview = isset( $values['image_post_preview'] ) ? esc_attr( $values['image_post_preview'][0] ) : '';
	$quote_post = isset( $values['quote_post'] ) ? esc_attr( $values['quote_post'][0] ) : '';
	$quote_author = isset( $values['quote_author'] ) ? esc_attr( $values['quote_author'][0] ) : '';
	$link_post_url = isset( $values['link_post_url'] ) ? esc_attr( $values['link_post_url'][0] ) : '';
	$link_post_description = isset( $values['link_post_description'] ) ? esc_attr( $values['link_post_description'][0] ) : '';
	$video_post_m4v = isset( $values['video_post_m4v'] ) ? esc_attr( $values['video_post_m4v'][0] ) : '';
	$video_post_ogv = isset( $values['video_post_ogv'] ) ? esc_attr( $values['video_post_ogv'][0] ) : '';
	$video_post_poster = isset( $values['video_post_poster'] ) ? esc_attr( $values['video_post_poster'][0] ) : '';
	$video_post_embed = isset( $values['video_post_embed'] ) ? esc_attr( $values['video_post_embed'][0] ) : '';
	$audio_post_mp3 = isset( $values['audio_post_mp3'] ) ? esc_attr( $values['audio_post_mp3'][0] ) : '';
	$audio_post_ogg = isset( $values['audio_post_ogg'] ) ? esc_attr( $values['audio_post_ogg'][0] ) : '';
	$audio_post_poster = isset( $values['audio_post_poster'] ) ? esc_attr( $values['audio_post_poster'][0] ) : '';
	wp_nonce_field( 'my_meta_box_nonce', 'meta_box_nonce' );
	?>

	<!-- aside metaboxes -->
	<p id="aside-post-code" style="display:none;">
		<label for="aside_post">The Aside</label>
		<textarea name="aside_post" id="aside_post" style="width:60%;" rows="5"><?php echo $aside_post; ?></textarea><br/>
		<span>Input your aside.</span>
	</p>
	<!-- aside metaboxes -->

	<!-- gallery metaboxes -->
	<p id="gallery-post-code" style="display:none;">
		<label for="gallery_post_images">Gallery Post Images</label>
		<input type="button" value="Upload Images" id="gallery_post_images" name="gallery_post_images" class="button"><br/>
		<span>Click to upload images to this gallery post.</span>
	</p>
	<!-- gallery metaboxes -->

	<!-- image metaboxes -->
	<p id="image-post-code" style="display:none;">
		<label for="image_post">Image Post</label>
		<input type="text" name="image_post_preview" id="image_post_preview" value="<?php echo $image_post_preview; ?>" style="width:50%;"/>
		<input type="button" value="Upload Images" id="image_post_upload" name="image_post_upload" class="button"><br/>
		<img src="<?php echo $image_post_preview; ?>" id="image_post_preview_img" style="width: 50%; margin-left: 35%; margin-top: 20px;"/><br/>
		<span>Click to upload image to this post.</span>
	</p>
	<!-- image metaboxes -->

	<!-- quote metaboxes -->
	<p id="quote-post-code" style="display:none;">
		<label for="quote_post">The Quote</label>
		<textarea name="quote_post" id="quote_post" style="width:60%;" rows="5"><?php echo $quote_post; ?></textarea><br/>
		<span>Input your quote.</span>

		<br/><br/>

		<label for="quote_author">Quote Author</label>
		<input type="text" name="quote_author" id="quote_author" value="<?php echo $quote_author; ?>" style="width:60%;"/><br/>
		<span>Enter the quote author name.</span>

	</p>
	<!-- quote metaboxes -->

	<!-- link metaboxes -->
	<p id="link-post-code" style="display:none;">
        <label for="link_post_url">Link URL</label>
		<input type="text" name="link_post_url" id="link_post_url" value="<?php echo $link_post_url; ?>" style="width:60%;"/><br/>
		<span>Enter the URL to be used for this Link post. for example: http://www.site5.com</span>

    	<br/><br/>

		<label for="link_post_description">Link Description</label>
		<textarea name="link_post_description" id="link_post_description" style="width:60%;" rows="5"><?php echo $link_post_description; ?></textarea><br/>
		<span>Enter the description to be used for this link. for example: Site5 WordPress Hosting</span>
	</p>
	<!-- link metaboxes -->

	<!-- video metaboxes -->
	<p id="video-post-code" style="display:none;">
		<label for="video_post_m4v">M4V File URL</label>
		<input type="text" name="video_post_m4v" id="video_post_m4v" value="<?php echo $video_post_m4v; ?>" style="width:60%;"/><br/>
		<span>Enter the URL to the .m4v video file url.</span>

        <br/><br/>

		<label for="video_post_ogv">OGV File URL</label>
		<input type="text" name="video_post_ogv" id="video_post_ogv" value="<?php echo $video_post_ogv; ?>" style="width:60%;"/><br/>
		<span>Enter the URL to the .ogv, video file url.</span>

		<br/><br/>

		<label for="video_post_poster">Video Poster Image</label>
		<input type="text" name="video_post_poster" id="video_post_poster" value="<?php echo $video_post_poster; ?>" style="width:50%;"/>
		<input type="button" value="Upload Images" id="video_post_poster_upload" name="video_post_poster_upload" class="button"><br/>
		<img src="<?php echo $video_post_poster; ?>" id="video_post_poster_img" style="width: 50%; margin-left: 35%; margin-top: 20px;"/><br/>
		<span>The preview image. The preview image should be min 500px wide.</span>

        <br/><br/>

		<label for="video_post_embed">Embedded Video Code</label>
		<textarea name="video_post_embed" id="video_post_embed" style="width:60%;" rows="5"><?php echo $video_post_embed; ?></textarea><br/>
		<span>If you are using something other than self hosted video such as Youtube or Vimeo, paste the embed code here. </span>
	</p>
	<!-- video metaboxes -->

	<!-- audio metaboxes -->
	<p id="audio-post-code" style="display:none;">
		<label for="audio_post_mp3">MP3 File URL</label>
		<input type="text" name="audio_post_mp3" id="audio_post_mp3" value="<?php echo $audio_post_mp3; ?>" style="width:60%;"/><br/>
		<span>Enter the URL to the .mp3 audio file url.</span>

        <br/><br/>

		<label for="audio_post_ogg">OGG File URL</label>
		<input type="text" name="audio_post_ogg" id="audio_post_ogg" value="<?php echo $audio_post_ogg; ?>" style="width:60%;"/><br/>
		<span>Enter the URL to the .oga, .ogg audio file url.</span>

		<br/><br/>

		<label for="audio_post_poster">Audio Poster Image</label>
		<input type="text" name="audio_post_poster" id="audio_post_poster" value="<?php echo $audio_post_poster; ?>" style="width:50%;"/>
		<input type="button" value="Upload Images" id="audio_post_poster_upload" name="audio_post_poster_upload" class="button"><br/>
		<img src="<?php echo $audio_post_poster; ?>" id="audio_post_poster_img" style="width: 50%; margin-left: 35%; margin-top: 20px; "/><br/>
		<span>The preview image for this audio track. Image width should be min 500px.</span>
	</p>
	<!-- audio metaboxes -->


	<?php
}


add_action( 'save_post', 'cd_meta_box_save' );
function cd_meta_box_save( $post_id )
{
	// Bail out if we're doing an auto save
	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

	// If our nonce isn't there, or we can't verify it, bail out
	if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'my_meta_box_nonce' ) ) return;

	// If our current user can't edit this post, bail out
	if( !current_user_can( 'edit_post' ) ) return;

	// Now, actually save the data
	$allowed = array(
		'a' => array(
                    'href' => array(), 'title' => array()),
                'iframe' => array(
                    'src' => array(),'name' => array(),'width' => array(),'height' => array(),'frameborder' => array(),'longdesc' => array(),'align' => array(),'marginwidth' => array(),'marginheight' => array(),'scrolling' => array())
	);

	// Make sure your data is set
	if( isset( $_POST['aside_post'] ) )
	update_post_meta( $post_id, 'aside_post', wp_kses( $_POST['aside_post'], $allowed ) );

	if( isset( $_POST['gallery_post_images'] ) )
	update_post_meta( $post_id, 'gallery_post_images', wp_kses( $_POST['gallery_post_images'], $allowed ) );

	if( isset( $_POST['image_post_preview'] ) )
	update_post_meta( $post_id, 'image_post_preview', wp_kses( $_POST['image_post_preview'], $allowed ) );

	if( isset( $_POST['quote_post'] ) )
	update_post_meta( $post_id, 'quote_post', wp_kses( $_POST['quote_post'], $allowed ) );

	if( isset( $_POST['quote_author'] ) )
	update_post_meta( $post_id, 'quote_author', wp_kses( $_POST['quote_author'], $allowed ) );

	if( isset( $_POST['link_post_url'] ) )
	update_post_meta( $post_id, 'link_post_url', wp_kses( $_POST['link_post_url'], $allowed ) );

	if( isset( $_POST['link_post_description'] ) )
	update_post_meta( $post_id, 'link_post_description', wp_kses( $_POST['link_post_description'], $allowed ) );

	if( isset( $_POST['video_post_m4v'] ) )
	update_post_meta( $post_id, 'video_post_m4v', wp_kses( $_POST['video_post_m4v'], $allowed ) );

	if( isset( $_POST['video_post_ogv'] ) )
	update_post_meta( $post_id, 'video_post_ogv', wp_kses( $_POST['video_post_ogv'], $allowed ) );

	if( isset( $_POST['video_post_poster'] ) )
	update_post_meta( $post_id, 'video_post_poster', wp_kses( $_POST['video_post_poster'], $allowed ) );

	if( isset( $_POST['video_post_embed'] ) )
	update_post_meta( $post_id, 'video_post_embed', wp_kses( $_POST['video_post_embed'], $allowed ) );

	if( isset( $_POST['audio_post_mp3'] ) )
	update_post_meta( $post_id, 'audio_post_mp3', wp_kses( $_POST['audio_post_mp3'], $allowed ) );

	if( isset( $_POST['audio_post_ogg'] ) )
	update_post_meta( $post_id, 'audio_post_ogg', wp_kses( $_POST['audio_post_ogg'], $allowed ) );

	if( isset( $_POST['audio_post_poster'] ) )
	update_post_meta( $post_id, 'audio_post_poster', wp_kses( $_POST['audio_post_poster'], $allowed ) );
}
?>