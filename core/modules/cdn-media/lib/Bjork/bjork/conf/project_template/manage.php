#!/usr/bin/env php
<{?php
if (php_sapi_name() == 'cli') {
    $project_name = '<?=$project_name?>';
    require_once dirname(__FILE__) . "/{$project_name}/bootstrap.php";
    bjork::execute_from_command_line();
}
