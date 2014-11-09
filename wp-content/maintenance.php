<?php
/**
 *
 * Loaded if a .maintenance file exists in ABSPATH, added during plugin updates
 *
 */
wp_load_translations_early();

header( "HTTP/1.1 503 Service Unavailable", true, 503 );
header( 'Content-Type: text/html; charset=utf-8' );
header( 'Retry-After: 600' );

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml"<?php if ( is_rtl() ) echo ' dir="rtl"'; ?>>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php _e( 'Site Maintenance' ); ?></title>
	<style>h1 {text-align:center}</style>
</head>
<body>
<h1><?php _e( 'Briefly unavailable for scheduled maintenance. Check back in a minute.' ); ?></h1>
</body>