<?php
/**
 * Theme Footer
 *
 */

if( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
  return;
}

?>

<?php if( is_front_page() ) { ?>
<footer class="frame">
  <div class="bottom container sponsors cfct-row c6-123456">
    <div class="inner_bottom clearfix cfct-block c6-123456">
      <div class="alpha"></div>
      <div class="cfct-module">
        <ul id="sponsors_scroller" >
          <li><a href="http://suncitymusicfestival.com"><img src="<?php echo home_url( '/assets/images/sponsor.scmf.png' ); ?>" alt="suncitymusicfestival.com" /></a></li>
          <li><a href="http://meltdowndallas.com"><img src="<?php echo home_url( '/assets/images/sponsor.meltdown.png' ); ?>" alt="" /></a></li>
          <li><a href="http://smftampa.com"><img src="<?php echo home_url( '/assets/images/sponsor.smf.png' ); ?>" alt="" /></a></li>
          <li><a href="http://alivemusicfestival.com"><img src="<?php echo home_url( '/assets/images/sponsor.alive_music_festival.png' ); ?>" alt="" /></a></li>
          <li><a href="http://umesouthpadre.com"><img src="<?php echo home_url( '/assets/images/sponsor.ultimate_music_experience.png' ); ?>" alt="" /></a></li>
          <li><a href="http://soundwaveaz.com"><img src="<?php echo home_url( '/assets/images/sponsor.sound_wave_music_festival.png' ); ?>" alt="" /></a></li>
          <li><a href="http://somethingwickedfestival.com"><img src="<?php echo home_url( '/assets/images/sponsor.something_wicked.png' ); ?>" alt="" /></a></li>
          <li><a href="http://dayafter.com"><img src="<?php echo home_url( '/assets/images/sponsor.day_after.png' ); ?>" alt="" /></a></li>
        </ul>
      </div>
    </div>
  </div>
</footer>
<?php } ?>

<footer class="frame">

  <div class="inner_footer container row-fluid" data-container-type="footer">

    <div class="logo span4 first">
      <div class="cfct-module"><?php wp_disco()->aside( 'footer-copyright' ); ?></div>
    </div>

    <div class="social span4">
      <div class="cfct-module">
        <h5>We Are Social</h5>
        <div class="row">
          <div class="span6 first"><a href="https://twitter.com/ddpworldwide" target="_blank" class="t"></a></div>
          <div class="span6"><a href="https://www.facebook.com/ddpworldwide" target="_blank" class="f"></a></div>
          <div class="span6"><a href="http://www.youtube.com/ddpworldwide" target="_blank" class="y"></a></div>
          <div class="span6 last"><a href="http://instagram.com/discodonniepresents" target="_blank" class="g"></a></div>
        </div>
      </div>
    </div>

  </div>

  <hr/>

  <div class="inner_bottom container row-fluid">

    <div class="span8 first">
        <div class="cfct-module">
          <?php wp_nav_menu( array( 'theme_location' => 'bottom_of_page_menu', 'menu_class' => 'footer-nav flawless-menu', 'fallback_cb' => 'flawless_list_pages' ) ); ?>
          <div class="footer_copyright">Copyright 1993-<?php echo date( 'Y' ); ?> SFX-Disco Operating LLC</div>
          <div class="footer_copyright">v<?php echo wp_disco()->version; ?></div>
        </div>
      </div>

    <div class="span4 last">
      <div class="cfct-module">
        <img alt="Eventribe" src="<?php echo home_url( '/assets/images/eb-ticketing-white.png' ); ?>" />
        <div class="footer_events_count"><p class="count"><?php echo wp_disco()->get_events_count(); ?></p><p>events hosted &amp; counting</p></div>
      </div>
    </div>

  </div>

</footer>

<?php wp_footer(); ?>
</body>
</html>
