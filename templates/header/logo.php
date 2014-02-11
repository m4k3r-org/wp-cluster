<?php if( current_theme_supports( 'header-logo' ) && wp_disco( 'logo.url' ) ) : ?>
  <div class="logo_area_wrapper cfct-block inner_container">
    <a href="<?php echo home_url(); ?>" class="header_logo_image" title="<?php bloginfo( 'name' ); ?>">
      <img class="header_logo_image" src="<?php echo wp_disco( 'logo.url' ); ?>" alt="<?php bloginfo( 'name' ); ?>" />
    </a>
  </div>
<?php endif; ?>
