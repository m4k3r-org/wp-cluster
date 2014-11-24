<?php
/**
 * Header - My Account Dropdown
 *
 * This can be overridden in child themes using get_template_part()
 *
 * @package Flawless
 * @since Flawless 3.0
 *
 */

  //** Do not display this tab for logged in users */
  if(is_user_logged_in()) { return; }

  //** Check if this section is disabled in settings */
  if($flawless['hide_header_login'] == 'true') { return; }
  
  //** Determine if login form should be rendered in navbar */
  if( $flawless[ 'navbar' ][ 'show_login' ] == 'true') return;

  $function = create_function('$c', '
    $c["login"]["id"] = "dropdown_header_login";
    $c["login"]["title"] = __("Login", "wpp");
    $c["login"]["class"] = "option_tab dropdown_tab_login";
    $c["login"]["href"] = "#";
    return $c;
  ');

  add_filter('flawless_header_links', $function, 50, 1);

?>

<div id="dropdown_header_login" class="dropdown_header_login header_dropdown_div header_login_div">
  <ul class="flawless_dropdown_elements container clearfix">
    <li class="header_login_section header_dropdown_section">
    <?php echo flawless_my_account_module::render_module( array(
      'redirect_to' => is_singular() ? get_permalink($post->ID) : get_bloginfo('url')
    )); ?>
    </li>
  </ul>
</div>
