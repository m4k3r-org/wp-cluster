<?php
/*
Plugin Name: Amazon S3 Uploads
Plugin URI: http://wordpress.org/extend/plugins/amazon-s3-uploads/
Author: Artem Titkov
Author URI: https://profiles.google.com/117859515361389646005
Description: Moves your uploads to Amazon S3 via cron jobs.
Version: 1.9.4
*/

require_once __DIR__.'/plugin/asssu/__init.php';
$asssu = new \asssu\Asssu();

register_activation_hook(__FILE__, 'activation_hook');
function activation_hook() {
    $asssu = new \asssu\Asssu();
    $asssu->activation_hook();
}

register_deactivation_hook(__FILE__, 'deactivation_hook');
function deactivation_hook() {
    $asssu = new \asssu\Asssu();
    $asssu->deactivation_hook();
}
