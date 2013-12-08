<div class="wrap">
<h2 class="title">Veneer Admin</h2>

  <?php

  // is_multisite();

  if( $_GET[ 'test' ] == 'single' ) {
    $content = $wpdb->get_var( "SELECT post_content from {$wpdb->posts} WHERE ID = 37414;" );

    //$content = str_replace( array( "ÃƒÂ«", "ÃƒÂ¶" ), array( "ë", "ö" ), $content );
    echo $content;

  }

  ?>

</div>
