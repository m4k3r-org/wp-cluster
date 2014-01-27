<?php
/**
 * The template for displaying Comments.
 *
 * @author Usability Dynamics
 * @module festival  
 * @since festival 0.1.0
 */
?>
<?php if ( comments_open() && shortcode_exists('fbcomments') ) echo do_shortcode('[fbcomments scheme=dark]'); ?>