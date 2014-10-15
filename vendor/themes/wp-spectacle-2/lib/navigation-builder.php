<?php

/**
 * Class Spectacle_Navigation_Builder
 *
 * Used to generate the navigation menu
 */
class Spectacle_Navigation_Builder
{
  private $_menu_id = null;
  private $_menu_items = null;

  /**
   * Render the menu items based on the theme-location
   *
   * @param null $theme_location
   * @param bool $rendered
   *
   * @return mixed
   */
  public function get( $theme_location = null, $rendered = true )
  {
    if ( $theme_location === null ) { return false; }

    $locations = get_nav_menu_locations();

    if
    (
      ( is_array( $locations ) )
      && ( array_key_exists( $theme_location, $locations ) )
    )
    {
      // Set the menu ID by theme-location
      $this->_menu_id = $locations[ $theme_location ];

      // Get the menu items
      $this->_menu_items = $this->_get_menu_items();

      if ( $rendered )
      {
        return $this->_render();
      }
      else
      {
        return $this->_menu_items;
      }

    }

    return false;
  }

  /**
   * Get the menu items based on the menu ID / theme-location
   *
   * @return mixed
   */
  private function _get_menu_items()
  {
    if ( $this->_menu_id === null ) { return false; }

    // Get the actual menu
    $menu = wp_get_nav_menu_object( $this->_menu_id );

    // Return the menu items
    return wp_get_nav_menu_items($menu->term_id);
  }

  /**
   * Render the menu
   */
  private function _render()
  {
    if ( $this->_menu_items === null ) { return false; }

    $html = '<ul>';

    foreach ( $this->_menu_items as $key => $menu_item )
    {
      if ( (int) $menu_item->menu_item_parent === 0 )
      {
        $children = $this->_get_menu_item_children( $menu_item );

        $class = '';
        $a_class = '';
        if ( $children !== false )
        {
          $class = 'dropdown';
          $a_class = 'dropdown-toggle';
        }

        $html .= '<li class="' .$class .'"><a href="' .$menu_item->url .'" class="' .$a_class .'" data-pid="' .$menu_item->ID .'">' .$menu_item->title .'</a>';

        if ( $children !== false )
        {
          $html .= '<ul class="dropdown-menu" data-pid="' .$menu_item->ID .'">';

          for ( $i = 0, $mi = count( $children ); $i < $mi; $i++ )
          {
            $html .= '<li><a href="' .$children[ $i ]->url .'">' .$children[ $i ]->title .'</a></li>';
          }

          $html .= '</ul>';
        }

        $html .= '</li>';
      }
    }

    $html .= '</ul>';

    return $html;
  }

  /**
   * Get the children of a given menu item
   *
   * @param null $menu_item
   *
   * @return array|bool
   */
  private function _get_menu_item_children ( $menu_item = null )
  {
    if ( $menu_item === null ) { return false; }

    $children = [];

    foreach ( $this->_menu_items as $key => $child )
    {
      if ( (int) $child->menu_item_parent === $menu_item->ID )
      {
        array_push( $children, $child );
      }
    }

    if ( empty( $children ) ) { return false; }

    return $children;
  }
}