<{?php
// Default settings for <?=$project_name?>.

$settings = array();


// Build paths inside the project like this: BASE_DIR . '/path/to/file'
if (!defined('BASE_DIR')) define('BASE_DIR', dirname(dirname(__FILE__)));


// Quick-start development settings - unsuitable for production

// SECURITY WARNING: keep the secret key used in production secret!
// Hardcoded values can leak through source control. Consider loading
// the secret key from an environment variable or a file instead.
$settings['SECRET_KEY'] = '<?=$secret_key?>';


// SECURITY WARNING: don't run with debug turned on in production!
$settings['DEBUG'] = true;

$settings['TEMPLATE_DEBUG'] = true;


// Application definition

$settings['INSTALLED_APPS'] = array(
    'bjork\contrib\messages',
    'bjork\contrib\sessions',
);

$settings['MIDDLEWARE_CLASSES'] = array(
    'bjork\contrib\sessions\middleware\SessionMiddleware',
    'bjork\middleware\common\CommonMiddleware',
    'bjork\middleware\csrf\CsrfViewMiddleware',
    'bjork\contrib\messages\middleware\MessageMiddleware',
);

$settings['ROOT_URLCONF'] = '<?=$project_name?>/urls.php';

// Internationalization

$settings['LANGUAGE_CODE'] = 'en-us';

$settings['TIME_ZONE'] = 'UTC';

$settings['USE_I18N'] = true;


// Static files (CSS, JavaScript, Images)

$settings['STATIC_URL'] = '/static/';


// Return settings to Bjork
return $settings;
