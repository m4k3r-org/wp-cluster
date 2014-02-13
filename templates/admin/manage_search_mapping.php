<?php
/**
 * Server Configuration template
 * @author korotkov@ud
 */
?>
<div class="wrap">

  <h2><?php _e('Current Index Mapping', DOMAIN_CURRENT_SITE) ?></h2>

  <p class="description"><?php _e('Editing later', DOMAIN_CURRENT_SITE) ?></p>

  <?php wp_disco()->search->action_messages(); ?>

  <?php  echo '<pre>';
  print_r( $mapping );
  echo '</pre>'; ?>

</div>