<?php if( is_front_page() ) { ?>
  <div class="bottom container sponsors cfct-row c6-123456">
      <div class="inner_bottom clearfix cfct-block c6-123456">
        <div class="alpha"></div>
        <div class="cfct-module">
          <ul id="sponsors_scroller">
            <li style="width: 165px;"><a href="http://www.suncitymusicfestival.com"><img src="<?php bloginfo( 'stylesheet_directory' ); ?>/img/sponsor_logos/scmf.png"/></a></li>
            <li style="width: 158px;"><a href="http://www.meltdowndallas.com"><img src="<?php bloginfo( 'stylesheet_directory' ); ?>/img/sponsor_logos/meltdown.png"/></a></li>
            <li><a href="http://smftampa.com"><img src="<?php bloginfo( 'stylesheet_directory' ); ?>/img/sponsor_logos/smf.png"/></a></li>
            <li><a href="http://alivemusicfestival.com"><img src="<?php bloginfo( 'stylesheet_directory' ); ?>/img/sponsor_logos/alive_music_festival.png"/></a></li>
            <!-- <li><a href="javascript:void(0);"><img src="<?php bloginfo( 'stylesheet_directory' ); ?>/img/sponsor_logos/audible.png" /></a></li> -->
            <!-- <li style="width: 163px;"><a href="http://www.dasenergifestival.com"><img src="<?php bloginfo( 'stylesheet_directory' ); ?>/img/sponsor_logos/das_energi_festival.png" /></a></li> -->
            <!-- <li style="width: 126px;"><a href="http://electricdaisycarnival.com"><img src="<?php bloginfo( 'stylesheet_directory' ); ?>/img/sponsor_logos/edc_dallas.png" /></a></li> -->
            <li style="width: 190px;"><a href="http://umesouthpadre.com/"><img src="<?php bloginfo( 'stylesheet_directory' ); ?>/img/sponsor_logos/ultimate_music_experience.png"/></a></li>
            <!-- <li style="width: 126px;"><a href="http://electricdaisycarnival.com"><img src="<?php bloginfo( 'stylesheet_directory' ); ?>/img/sponsor_logos/edc_orlando.png" /></a></li> -->
            <li style="width: 180px;"><a href="http://soundwaveaz.com"><img src="<?php bloginfo( 'stylesheet_directory' ); ?>/img/sponsor_logos/sound_wave_music_festival.png"/></a></li>
            <!-- <li style="width: 126px;"><a href="http://electricdaisycarnival.com"><img src="<?php bloginfo( 'stylesheet_directory' ); ?>/img/sponsor_logos/edc_puerto_rico.png" /></a></li> -->
            <!-- <li style="width: 153px;"><a href="http://electricforestfestival.com"><img src="<?php bloginfo( 'stylesheet_directory' ); ?>/img/sponsor_logos/electric_forest.png" /></a></li> -->
            <!-- <li><a href="javascript:void(0);"><img src="<?php bloginfo( 'stylesheet_directory' ); ?>/img/sponsor_logos/get_freaky.png" /></a></li> -->
            <!-- <li style="width: 180px;"><a href="http://magneticmusicfestival.com"><img src="<?php bloginfo( 'stylesheet_directory' ); ?>/img/sponsor_logos/magnetic_music_festival.png" /></a></li> -->
            <!-- <li><a href="http://www.moonlightmasquerade.com"><img src="<?php bloginfo( 'stylesheet_directory' ); ?>/img/sponsor_logos/moonlight_masquerade.png" /></a></li> -->
            <!-- <li style="width: 165px;"><a href="http://nocturnalwonderland.com"><img src="<?php bloginfo( 'stylesheet_directory' ); ?>/img/sponsor_logos/nocturnal_wonderland_texas.png" /></a></li> -->
            <li><a href="http://somethingwickedfestival.com"><img src="<?php bloginfo( 'stylesheet_directory' ); ?>/img/sponsor_logos/something_wicked.png"/></a></li>
            <li><a href="javascript:void(0);"><img src="<?php bloginfo( 'stylesheet_directory' ); ?>/img/sponsor_logos/zoolu.png"/></a></li>
            <li><a href="http://dayafter.com"><img src="<?php bloginfo( 'stylesheet_directory' ); ?>/img/sponsor_logos/day_after.png"/></a></li>

            <?php /** Leaving as example
            <!-- Extras -->
            <li><a href="#"><img src="http://placekitten.com/141/48" / ></a></li>
            <li><a href="#"><img src="http://placekitten.com/142/48" / ></a></li>
            <li><a href="#"><img src="http://placekitten.com/143/48" / ></a></li>
            <li><a href="#"><img src="http://placekitten.com/144/48" / ></a></li>
            <li><a href="#"><img src="http://placekitten.com/145/48" / ></a></li> */
            ?>
          </ul>
          <script type="text/javascript">
            (function( $ ) {
              $( function() { /* on DOM ready */
                $( "#sponsors_scroller" ).simplyScroll();
              } );
            })( jQuery );
          </script>
        </div>
      </div>
    </div>
<?php } ?>

</div>

  <div class="footer bottom-of-page clearfix">

    <div class="inner_footer container row-fluid" container_type="footer">

      <div class="logo span4 first">
        <div class="cfct-module">
          <p><?php flawless_footer_copyright(); ?></p>
        </div>
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
            <div class="footer_copyright">v2.1.0</div>
          </div>
        </div>

        <div class="span4 last">
          <div class="cfct-module">
          <img width="100px" alt="Eventribe" style="float:right;margin-left:20px;" src="<?php echo get_stylesheet_directory_uri(); ?>/img/eb-ticketing-white.png"/>
          <div class="footer_events_count">
            <p class="count"><?php echo hddp::get_events_count(); ?></p>
            <p>events hosted &amp; counting</p>
          </div>
          </div>
        </div>

    </div>

  </div>

</div><?php //** .wrapper */ ?>
<?php wp_footer(); ?>
</body>
</html>
