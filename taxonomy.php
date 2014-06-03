<?php
/**
 * Template for custom taxonomies, categories will use archive.php
 *
 * @author Usability Dynamics, Inc. <info@usabilitydynamics.com>
 * @package Flawless
 */
?>

<?php get_template_part( 'templates/taxonomy/taxonomy', get_post_type() ); ?>