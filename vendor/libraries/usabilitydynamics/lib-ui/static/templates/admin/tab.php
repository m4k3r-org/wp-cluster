<?php
/**
 * Settings page tab template
 */
?>
<div class="settings-tab <?php echo isset( $menu[ 'id' ] ) ? $menu[ 'id' ] : ''; ?>">
  <?php if( !empty( $menu[ 'desc' ] ) ) : ?>
    <div class="desc"><?php echo $menu[ 'desc' ]; ?></div>
  <?php endif; ?>
  <div class="accordion-container">
    <ul class="outer-border">
      <?php foreach( $this->get( 'sections', 'schema', array() ) as $section ) : ?>
        <?php if( !$menu || $menu[ 'id' ] == $section[ 'menu' ] ) : ?>
          <li class="accordion-section open" ><?php $this->get_template_part( 'section', array( 'section' => $section ) ); ?></li>
        <?php endif; ?>
      <?php endforeach; ?>
    </ul>
  </div>
</div>