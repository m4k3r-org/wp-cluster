<div class="wrap">
<h2><?php _e( 'Jobs', $_locale ); ?></h2>

  <?php /* $wp_list_table->views(); */ ?>

  <form id="jobs-filter" action="<?php echo admin_url( 'tools.php?page=veneer-jobs' ); ?>" method="get">
    <?php /* $wp_list_table->search_box( $post_type_object->labels->search_items, 'post' ); */ ?>
    <?php /* $wp_list_table->display(); */ ?>
  </form>

  <div id="ajax-response"></div>
  <br class="clear"/>

</div>