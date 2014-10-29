<?php
/**
 * Page Title
 */

$title = false;

switch( wp_festival()->get_query_template() ) {

  case 'category':
    $title = sprintf( __( 'Category Archives: %s', wp_festival('domain') ), '<span>' . single_cat_title( '', false ) . '</span>' );
    break;

  case 'tag':
    $title = sprintf( __( 'Tag Archives: %s', wp_festival('domain') ), '<span>' . single_tag_title( '', false ) . '</span>' );
    break;

  case 'search':
    $title = sprintf( __( 'Search Results for: %s', wp_festival('domain') ), '<span>' . get_search_query() . '</span>' );
    break;

  case 'author':
    $title = sprintf( __( 'Author Archives: %s', wp_festival('domain') ), '<span class="vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( "ID" ) ) ) . '" title="' . esc_attr( get_the_author() ) . '" rel="me">' . get_the_author() . '</a></span>' );
    break;

}

?>

<?php if( $title ) : ?>
  <section class="main-title">
    <h2><?php echo $title; ?></h2>
    <span class="hr"></span>
  </section>
<?php endif; ?>
