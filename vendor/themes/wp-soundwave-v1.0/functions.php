<?php
//enable thumbnail

add_theme_support('post-thumbnails');
set_post_thumbnail_size('thumbnail',154, 154, true);
add_image_size( 'category-thumb', 466, 9999 );

$GLOBALS['content_width'] = 495;

//enable multiple sidebar

if ( function_exists('register_sidebar') )
register_sidebar(array('name'=>'Youtube',
'before_widget' => '',
'after_widget' => '',
'before_title' => '<h4>',
'after_title' => '</h4>',
));
register_sidebar(array('name'=>'Soundcloud',
'before_widget' => '',
'after_widget' => '',
'before_title' => '<h4>',
'after_title' => '</h4>',
));

?>
<?php
function mytheme_comment($comment, $args, $depth) {
$GLOBALS['comment'] = $comment; ?>
<div class="comment-main">
<li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
<div id="comment-<?php comment_ID(); ?>">

<div class="comment-meta commentmetadata"><a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ) ?>"><?php printf(__('%1$s at %2$s'), get_comment_date(), get_comment_time()) ?></a><?php edit_comment_link(__('(Edit)'),' ','') ?></div>

<div class="comment-author vcard">
<?php echo get_avatar( $comment->comment_author_email, 48 ); ?>

<p><?php printf(__('<cite class="fn">%s</cite> <span class="says">says:</span>'), get_comment_author_link()) ?></p>
</div>
<?php if ($comment->comment_approved == '0') : ?>
<em><?php _e('Your comment is awaiting moderation.') ?></em>
<br />
<?php endif; ?>

<div class="comment_text">
<?php comment_text() ?>
</div>
<div class="reply">
<?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
</div>
</div>
</div>
<?php
}
?>
<?php
add_filter( 'excerpt_length', 'custom_excerpt_length');
function custom_excerpt_length($length){
    return 50;
}
 
add_filter( 'excerpt_more', 'custom_excerpt_more' );
function custom_excerpt_more($more){
    return '...';
}







/*======================================================================================*/
/* Kriesi Custom Pagination - Slightly Modified - http://www.kriesi.at/archives/how-to-build-a-wordpress-post-pagination-without-plugin */
/*======================================================================================*/
function savoy_pagination($pages = '', $range = 3)
{  
     $showitems = ($range * 2)+1;  

     global $paged;
     if(empty($paged)) $paged = 1;

     if($pages == '')
     {
         global $wp_query;
         $pages = $wp_query->max_num_pages;		 
         if(!$pages)
         {
             $pages = 1;
         }
		 
     }   

     if(1 != $pages)
     {
         echo '<ul class="pagination group">';
         if($paged > 2 && $paged > $range+1 && $showitems < $pages) echo '<li><a href="'.get_pagenum_link(1).'" title="First">&laquo;</a></li>';
         if($paged > 1 && $showitems < $pages) echo '<li><a href="'.get_pagenum_link($paged - 1).'" title="Previous">&lsaquo;</a></li>';

         for ($i=1; $i <= $pages; $i++)
         {
             if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
             {
				 if($paged == $i){
					$class = 'current'; 
				 } else {
					$class = ''; 
				 }
                 echo '<li><a href="'.get_pagenum_link($i).'" class="'.$class.'">'.$i.'</a></li>';
             }
         }

         if ($paged < $pages && $showitems < $pages) echo '<li><a href="'.get_pagenum_link($paged + 1).'" title="Next">&rsaquo;</a></li>';  
         if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) echo '<li><a href="'.get_pagenum_link($pages).'" title="Last">&raquo;</a></li>';
         echo '</ul>';
		 
     }
	
}

add_filter( 'post_thumbnail_html', 'remove_thumbnail_dimensions', 10, 3 );
function remove_thumbnail_dimensions( $html, $post_id, $post_image_id ) {
    $html = preg_replace( '/(width|height)=\"\d*\"\s/', "", $html );
    return $html;
}


/**
 * Rename uploaded files as the hash of their original.
 *
 * @author sopp@ID
 */
function ttc_make_filename_hash( $filename ) {

  $info = pathinfo( $filename );
  $ext = empty( $info[ 'extension' ] ) ? '' : '.' . $info[ 'extension' ];
  $rnd = rand( 0, 99 );
  $name = basename( $filename, $ext );
  return md5( $name ) . $rnd . $ext;
}

add_filter( 'sanitize_file_name', 'ttc_make_filename_hash', 10 );
?>