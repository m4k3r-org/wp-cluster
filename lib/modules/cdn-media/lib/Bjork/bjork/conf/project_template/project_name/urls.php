<{?php

use bjork\conf\urls;

// Delete this line once you have setup your URL conf.
throw new \Exception('Add views and edit your URL conf to get started.');

return urls::patterns('',
    // Examples:
    array('^$', '<?=$project_name?>\views\home', 'name'=>'home'),
    array('^<?=$project_name?>/', urls::import('<?=$project_name?>/foo/urls.php'))
);
