<?php
/**
 * Splash page for SMF Tampa
 */
?>

<!DOCTYPE html>
<html class="no-js">
<head>
  <title>Sunset Music Festival 2015</title>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <meta http-equiv="Pragma" content="public" />
  <meta http-equiv="Cache-Type" content="text/html; charset=UTF-8" />
  <meta http-equiv="Cache-Control" content="public, max-age=1800" />
  <meta http-equiv="Expires" content="Fri, 4 Dec 2014 11:00:00 EST" />

  <link rel="shortcut icon" href="/wp-content/themes/wp-festival-smf/favicon.ico">

  <link rel="stylesheet" href="/wp-content/themes/wp-festival-smf/static/styles/app.css">
  <script type="text/javascript" data-main="/wp-content/themes/wp-festival-smf/static/scripts/src/app" src="http://cdn.udx.io/udx.requires.js"></script>

  <?php wp_head(); ?>
</head>

<body>
  <header class="main-header">

    <a href="#" class="share share-popup">
      <span class="icon-share"></span>
      <span class="text">Share</span>
    </a>

    <div class="organizer-logos clearfix">
      <a href="http://discodonniepresents.com" target="_blank" class="disco-donnie">
        <img src="/wp-content/themes/wp-festival-smf/static/images/organizer-logo-disco-donnie.png" alt="Disco Donnie Presents">
      </a>
      <a href="http://globalgrooveevents.com" target="_blank" class="sunset-events">
        <img src="/wp-content/themes/wp-festival-smf/static/images/organizer-logo-sunset-events.png" alt="Sunset Events">
      </a>

			<a href="https://www.facebook.com/committeeent" target="_blank" class="committee">
				<img src="/wp-content/themes/wp-festival-smf/static/images/organizer-logo-committee.png" alt="Committee">
			</a>

    </div>
  </header>

  <div class="content container">

    <div class="row">
      <div class="col-xs-12 col-sm-4">
        <span class="ic icon-place"></span>
				<hr class="short">
        <h3>Raymond James <br class="force-nl">Stadium</h3>
        <h2>Tampa, Florida</h2>

        <a href="https://goo.gl/maps/qRSZg" target="_blank" class="button">See on Map</a>
      </div>

      <div class="col-xs-12 col-sm-4">
        <span class="ic icon-date"></span>
				<hr class="short">
				<h3>Memorial Day <br class="force-nl">Weekend</h3>
				<h2>May 23 &amp; 24, 2015</h2>

        <a href="https://discodonniepresents.findor.com/results/list?checkIn=2015-03-22&checkOut=2015-03-25&room1=2&eventID=3" target="_blank" class="button">Book a Room</a>
      </div>

      <div class="col-xs-12 col-sm-4">
        <span class="ic icon-tickets"></span>
				<hr class="short">
				<h3>Early bird tickets <br class="force-nl">on sale</h3>
				<h2>Fri, Dec 5, <br class="force-nl">2014</h2>

        <div class="countdown">
          <div class="timer" data-todate="Fri, 05 Dec 2014 10:00:00 EST">
            <div class="days">0</div>
            <div class="hours">0</div>
            <div class="minutes">0</div>
            <div class="seconds">0</div>
          </div>
          <div class="timer-meta clearfix">
            <div class="day">D</div>
            <div class="hour">H</div>
            <div class="minute">M</div>
            <div class="second">S</div>
          </div>
        </div>
      </div>
    </div>
  </div>

	<footer>
		<div class="container">
			<div class="row">
				<div class="col-xs-12">
					<a href="https://www.facebook.com/SMFTAMPA" target="_blank" class="icon-facebook hover-pop"></a>
					<a href="https://twitter.com/SMFTampa" target="_blank" class="icon-twitter hover-pop"></a>
					<a href="https://www.youtube.com/channel/UC17AVS-axW6YecuumgxtC2g" target="_blank" class="icon-youtube hover-pop"></a>
					<a href="http://instagram.com/SMFTampa/" target="_blank" class="icon-instagram hover-pop"></a>
				</div>
			</div>
		</div>
	</footer>

  <div class="share-overlay overlay">
    <a href="#" class="icon-close"></a>

    <div class="overlay-content">

      <div class="share-wrapper clearfix">
				<a href="https://twitter.com/intent/tweet?original_referer=/&text=%23SMFTampa%202015%20tickets%20go%20on-sale%20Friday%2C%20December%205th%20at%2010%3A00%20AM%20ET!%20http%3A%2F%2Fsmftampa.com%2F" target="_blank" class="twitter">
					<span class="icon-twitter"></span>
				</a>

				<a href="https://www.facebook.com/sharer/sharer.php?u=http://smftampa.com" target="_blank" class="facebook">
					<span class="icon-facebook"></span>
				</a>

				<a href="https://plus.google.com/share?url=http://smftampa.com" target="_blank" class="google-plus">
					<span class="icon-google-plus"></span>
				</a>

				<a href="http://pinterest.com/pin/create/button/?url=http://smftampa.com&media=//wp-content/themes/wp-festival-smf/static/images/background-2000.jpg&description=SMF Tampa" target="_blank" class="pinterest">
					<span class="icon-pinterest"></span>
				</a>

			</div>
    </div>

    <div class="bg"></div>
  </div>

	<?php wp_footer(); ?>

</body>
</html>
