<{?php

$project_name   = "<?=$project_name?>";

// Edit this path to point to your project's settings file.
// Can be relative to the include path.
$settings_file  = "{$project_name}/settings.php";

// -- Bjork ------------------------------------------------------------------

// Load Bjork. Bjork must be in the include path.
require_once 'bjork/bjork.php';

// Set the settings file to use.
bjork::set_settings_file($settings_file);

// Add the project folder to the include path.
bjork::add_include_path(realpath(dirname(dirname(__FILE__))));

// Uncomment any of the following lines in order to preload those files.
//require_once 'bjork/conf/settings.php';
//require_once 'bjork/conf/urls.php';
//require_once 'bjork/core/context_processors.php';
//require_once 'bjork/core/exceptions.php';
//require_once 'bjork/core/handlers/base.php';
//require_once 'bjork/core/handlers/generic.php';
//require_once 'bjork/core/loading.php';
//require_once 'bjork/core/urlresolvers.php';
//require_once 'bjork/http/__init.php';
//require_once 'bjork/middleware/common.php';
//require_once 'bjork/middleware/csrf.php';
//require_once 'bjork/shortcuts/__init.php';
//require_once 'bjork/template/__init.php';
//require_once 'bjork/template/context.php';
//require_once 'bjork/template/loader.php';
//require_once 'bjork/utils/datastructures.php';
//require_once 'bjork/utils/encoding.php';
//require_once 'bjork/utils/functional.php';
//require_once 'bjork/utils/translation/__init.php';
//require_once 'os.php';
//require_once 'strutils.php';
//require_once 'urllib.php';

// If you are using sessions or messages, you may want to uncomment the next lines.
//require_once 'bjork/contrib/sessions/backends/base.php';
//require_once 'bjork/contrib/sessions/backends/signed_cookies.php';
//require_once 'bjork/contrib/sessions/middleware.php';
//require_once 'bjork/contrib/messages/__init.php';
//require_once 'bjork/contrib/messages/context_processors.php';
//require_once 'bjork/contrib/messages/middleware.php';
//require_once 'bjork/contrib/messages/storage/__init.php';
//require_once 'bjork/contrib/messages/storage/base.php';
//require_once 'bjork/contrib/messages/storage/session.php';

// If you are using the i18n features of Bjork, you may want to uncomment the next two lines.
//require_once 'bjork/middleware/locale.php';
//require_once 'bjork/utils/translation/trans_real.php';
//require_once 'gettext.php';

// ---------------------------------------------------------------------------

// Add project-specific bootstrap code here.
