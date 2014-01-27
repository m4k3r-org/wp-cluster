<?php
/**
 * Name: HDDP Backend Functions
 * Description: Functions meant to be used on backend.
 * Author: Insidedesign
 * Author URI: http://www.insidedesign.info/
 *
 */
 
 
 /**
 * Rename uploaded files as the hash of their original.
 *
 * @author sopp@ID
 */
function make_filename_hash($filename) {
	$info = pathinfo($filename);
	$ext  = empty($info['extension']) ? '' : '.' . $info['extension'];
	$name = basename($filename, $ext);
	return md5($name) . $ext;
}
add_filter('sanitize_file_name', 'make_filename_hash', 10);
