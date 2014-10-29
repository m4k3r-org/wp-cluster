<div id="post-loop">

	<h2 class="title loop-title post-loop-title">Latest Updates</h2>
	
	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
	  <div id="post-id-<?php the_ID(); ?>" <?php post_class( 'post' ); ?>>
	        
	          <?php
	            if ( has_post_thumbnail() ) {
	              $image_id  = get_post_thumbnail_id();
	              $image_url = wp_get_attachment_image_src( $image_id, 'post-image' );
	              $image_url = $image_url[ 0 ];
	              echo '<a href="' . get_permalink() . '"><img src="' . $image_url . '" alt="" class="thumbnail featured-image" /></a>';
	            }
	          ?>
	    <h3 class="title post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
	
	    <?php the_excerpt(); ?>
	
	    <div class="fix"> </div>
	
	  </div>
	<?php endwhile; else : ?>
	  <p><?php _e( 'Sorry, nothing matched your criteria.', FreaksBeatsTreats::$text_domain ) ?></p>
	<?php endif; ?>

</div>