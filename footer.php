</div> <!-- #doc -->

<?php if ( has_nav_menu( 'main-navigation' ) ): ?>

<div class="navigation-overlay overlay">
  <a href="#" class="icon-spectacle-close"></a>
  <div class="overlay-content">
    <nav class="clearfix">
      <?php
        $menu = new Spectacle_Navigation_Builder();
        echo $menu->get( 'main-navigation' );
      ?>
    </nav>
  </div>
</div>

<?php endif; ?>

</body>
</html>