<?php
require_once( get_template_directory(). '/vendor/kertz/twitteroauth/twitteroauth/twitteroauth.php');

$twitter = new TwitterOAuth(WP_SOCIAL_STREAM_TWITTER_CONSUMER_KEY, WP_SOCIAL_STREAM_TWITTER_CONSUMER_SECRET, null, null);
