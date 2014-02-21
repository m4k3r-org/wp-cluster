<?php
/**
 * Page Title
 */
 
$title = false;
 
switch( wp_festival()->get_query_template() ) {

  case 'category':
    $title = 'Blah Blah';
    break;
    
  case 'tag':
    break;
    
  case 'search':
    break;
    
  case 'author':
    break;

}

?>

<?php if( $title ) : ?>
  <section class="main-title">
    <h2><?php echo $title; ?></h2>
  </section>
<?php endif; ?>
