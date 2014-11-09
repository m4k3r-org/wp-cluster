<?php

require_once get_template_directory() .'/lib/core.php';
require_once 'lib/chmf.php';


$chmf = new \WP_Spectacle\CHMF\Core();
$chmf->load_scripts();
