<?php
/**
 * Footer Navigation Menu.
 *
 * @author Usability Dynamics
 * @module wp-escalade  
 * @since wp-escalade 0.1.0
 */

?>
<!-- Navigation starts -->
<section class="footer-nav">
<?php
wp_nav_menu( array(
  'theme_location'  => 'footer',
  'container'       => false,
  'fallback_cb'     => false,
  'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
  'depth'           => 1
));
?>
</section>
<!-- Navigation ends -->