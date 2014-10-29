<!-- begin #slider -->

<div id="slider_container" class="container">

	<div class="flexslider loading ">
		<ul class="slides">
			
			<?php
				$captions = array();
				$tmp = $wp_query;
				$slider = get_term_by('id', of_get_option('sc_slidertag'), 'sliders' ) ;
				$slider = $slider->slug;
				$wp_query = new WP_Query('post_type=featured&orderby=menu_order&order=ASC&sliders='. $slider);
				
				if($wp_query->have_posts()) : while($wp_query->have_posts()) : $wp_query->the_post();
				$fitemlink = get_post_meta($post->ID,'snbf_fitemlink',true);
				$fitemcaption = get_post_meta($post->ID,'snbf_fitemcaption',true);

			?>
        	

			<?php
				$thumbId = get_image_id_by_link ( get_post_meta($post->ID, 'snbf_slideimage_src', true) );
				$thumb = wp_get_attachment_image_src($thumbId, 'slide', false);

			?>
			<li><a href="<?php echo $fitemlink ?>">
			<img src="<?php echo $thumb[0] ?>" data-id="<?php echo $thumbId; ?>" alt="<?php echo $fitemcaption ?>" /></a>
			<?php if ($fitemcaption!='') : ?>
			<div class="flex-caption">
				<h3><?php echo $fitemcaption ?></h3>
			</div>
			<?php endif ?>
			</li>


		    <?php
		    endwhile; wp_reset_query();
		    endif;
		    $wp_query = $tmp;
	    	?>
		</ul>
	</div>
</div>

<!-- end #slider -->


<!-- flex slider & slider settings -->
<script type="text/javascript">
jQuery.noConflict();
	jQuery(document).ready(function(){
		if ( jQuery( '#full-width-slider .flexslider' ).length && jQuery() ) {
		jQuery('#full-width-slider .flexslider').flexslider({
			animation:'<?php if(of_get_option('sc_slidereffect')==''): echo 'slide';
				  else: echo of_get_option('sc_slidereffect');
				  endif;?>',
			controlNav:true,
			animationLoop: true,  
			controlsContainer:"",
			pauseOnHover: true,  
			nextText:"&rsaquo;",
			prevText:"&lsaquo;",
			keyboardNav: true,  
			slideshowSpeed: <?php if(of_get_option('sc_sliderpausetime')==''): echo '3000';
				  else: echo of_get_option('sc_sliderpausetime');
				  endif;?>,
			animationSpeed: <?php if(of_get_option('sc_slideranimationspeed')==''): echo '500';
				  else: echo of_get_option('sc_slideranimationspeed');
				  endif;?>,
			start: function(slider) {
				slider.removeClass('loading');
			}

		});
		}
	});
</script>

<!-- end flex slider & slider settings -->