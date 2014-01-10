<?php
// Default settings.

if (!function_exists('gettext_noop')) {
    function gettext_noop($s) { return $s; }
}

$settings = array();

///////////
// CORE //
/////////

$settings['DEBUG'] = false;
$settings['TEMPLATE_DEBUG'] = false;

// Whether the framework should propagate raw exceptions rather than catching
// them. This is useful under some testing situations and should never be used
// on a live site.
$settings['DEBUG_PROPAGATE_EXCEPTIONS'] = false;

// People who get code error notifications. In the format:
// array('email@example.com'        => 'Full Name',
//       'anotheremail@example.com' => 'Full Name');
$settings['ADMINS'] = array();

// List of IP addresses, as strings, that:
//   * See debug comments, when DEBUG is true
//   * Receive x-headers
$settings['INTERNAL_IPS'] = array();

// Local time zone for this installation. All choices can be found here:
// http://en.wikipedia.org/wiki/List_of_tz_zones_by_name (although not all
// systems may support all possibilities). When USE_TZ is True, this is
// interpreted as the default user time zone.
$settings['TIME_ZONE'] = 'Europe/Athens';

// Language code for this installation. All choices can be found here:
// http://www.i18nguy.com/unicode/language-identifiers.html
$settings['LANGUAGE_CODE'] = 'en-us';

// Languages we provide translations for, out of the box. The language name
// should be the utf-8 encoded local name for the language.
$settings['LANGUAGES'] = array(
    'el' => gettext_noop('Greek'),
    'en' => gettext_noop('English'),
    'es' => gettext_noop('Spanish'),
    'it' => gettext_noop('Italian'),
    'ro' => gettext_noop('Romanian'),
    'ru' => gettext_noop('Russian'),
    'sq' => gettext_noop('Albanian'),
);

// If you set this to False, Bjork will make some optimizations so as not
// to load the internationalization machinery.
$settings['USE_I18N'] = true;
$settings['LOCALE_PATHS'] = array();
$settings['LANGUAGE_COOKIE_NAME'] = 'bjork_language';

// Not-necessarily-technical managers of the site. They get broken link
// notifications and other various emails.
$settings['MANAGERS'] = $settings['ADMINS'];

// Default content type and charset to use for all HttpResponse objects, if a
// MIME type isn't manually specified. These are used to construct the
// Content-Type header.
$settings['DEFAULT_CONTENT_TYPE'] = 'text/html';
$settings['DEFAULT_CHARSET'] = 'utf-8';

// E-mail address that error messages come from.
$settings['SERVER_EMAIL'] = 'root@localhost';

// The email backend to use. For possible shortcuts see django.core.mail.
// The default is to use the SMTP backend.
// Third-party backends can be specified by providing a Python path
// to a module that defines an EmailBackend class.
$settings['EMAIL_BACKEND'] = 'bjork\core\mail\backends\php\EmailBackend';

// Host for sending email.
$settings['EMAIL_HOST'] = 'localhost';

// Port for sending email.
$settings['EMAIL_PORT'] = 25;

// Optional SMTP authentication information for EMAIL_HOST.
$settings['EMAIL_HOST_USER'] = '';
$settings['EMAIL_HOST_PASSWORD'] = '';
$settings['EMAIL_USE_TLS'] = false;

// List of strings representing installed apps.
$settings['INSTALLED_APPS'] = array();

// List of locations of the template source files, in search order.
$settings['TEMPLATE_DIRS'] = array();

// List of callables that know how to import templates from various sources.
// See the comments in bjork/core/template/loader.php for interface
// documentation.
$settings['TEMPLATE_LOADERS'] = array(
    'bjork\template\loaders\filesystem\Loader',
    'bjork\template\loaders\app_directories\Loader',
);

// List of processors used by RequestContext to populate the context.
// Each one should be a callable that takes the request object as its
// only parameter and returns a dictionary to add to the context.
$settings['TEMPLATE_CONTEXT_PROCESSORS'] = array(
    'bjork\core\context_processors\debug',
    'bjork\core\context_processors\i18n',
    'bjork\core\context_processors\media',
    'bjork\core\context_processors\tz',
    // 'bjork\core\context_processors\request',
    'bjork\contrib\messages\context_processors\messages',
);

// Output to use in template system for invalid (e.g. misspelled) variables.
$settings['TEMPLATE_STRING_IF_INVALID'] = '';

// Default email address to use for various automated correspondence from
// the site managers.
$settings['DEFAULT_FROM_EMAIL'] = 'webmaster@localhost';

// Subject-line prefix for email messages send with bjork\core\mail::mail_admins
// or ...mail_managers.  Make sure to include the trailing space.
$settings['EMAIL_SUBJECT_PREFIX'] = '[bjork] ';

// Whether to append trailing slashes to URLs.
$settings['APPEND_SLASH'] = true;

// Whether to prepend the "www." subdomain to URLs that don't have it.
$settings['PREPEND_WWW'] = false;

// Override the server-derived value of SCRIPT_NAME
$settings['FORCE_SCRIPT_NAME'] = null;

// List of compiled regular expression objects representing User-Agent strings
// that are not allowed to visit any page, systemwide. Use this for bad
// robots/crawlers. Here are a few examples:
//     
//     $settings['DISALLOWED_USER_AGENTS'] = array(
//         '/^NaverBot.*/',
//         '/^EmailSiphon.*/',
//         '/^SiteSucker.*/',
//         '/^sohu-search/'
//     );
$settings['DISALLOWED_USER_AGENTS'] = array();

$settings['ABSOLUTE_URL_OVERRIDES'] = array();

// A secret key for this particular Bjork installation. Used in secret-key
// hashing algorithms. Set this in your settings, or Bjork will complain
// loudly.
$settings['SECRET_KEY'] = '';

// Absolute filesystem path to the directory that will hold user-uploaded files.
// Example: "/home/media/media.radial.gr/media/"
$settings['MEDIA_ROOT'] = '';

// URL that handles the media served from MEDIA_ROOT.
// Example: "http://media.radial.gr/media/"
$settings['MEDIA_URL'] = '';

// Absolute path to the directory that holds static files.
// Example: "/home/media/media.radial.gr/static/"
$settings['STATIC_ROOT'] = '';

// URL that handles the static files served from STATIC_ROOT.
// Example: "http://media.radial.gr/static/"
$settings['STATIC_URL'] = null;

/////////////////
// MIDDLEWARE //
///////////////

// List of middleware classes to use.  Order is important; in the request phase,
// this middleware classes will be applied in the order given, and in the
// response phase the middleware will be applied in reverse order.
$settings['MIDDLEWARE_CLASSES'] = array(
    'bjork\middleware\common\CommonMiddleware',
    'bjork\contrib\sessions\middleware\SessionMiddleware',
    'bjork\middleware\csrf\CsrfViewMiddleware',
    'bjork\contrib\messages\middleware\MessageMiddleware',
);

///////////////
// SESSIONS //
/////////////

$settings['SESSION_COOKIE_NAME'] = 'sessionid';             // Cookie name. This can be whatever you want.
$settings['SESSION_COOKIE_AGE'] = 60 * 60 * 24 * 7 * 2;     // Age of cookie, in seconds (default: 2 weeks).
$settings['SESSION_COOKIE_DOMAIN'] = null;                  // A string like ".lawrence.com", or None for standard domain cookie.
$settings['SESSION_COOKIE_SECURE'] = false;                 // Whether the session cookie should be secure (https:// only).
$settings['SESSION_COOKIE_PATH'] = '/';                     // The path of the session cookie.
$settings['SESSION_COOKIE_HTTPONLY'] = true;                // Whether to use the non-RFC standard httpOnly flag (IE, FF3+, others)
$settings['SESSION_SAVE_EVERY_REQUEST'] = false;            // Whether to save the session data on every request.
$settings['SESSION_EXPIRE_AT_BROWSER_CLOSE'] = false;       // Whether a user's session cookie expires when the Web browser is closed.
$settings['SESSION_ENGINE'] = 'bjork\contrib\sessions\backends\signed_cookies'; // The module to store session data
$settings['SESSION_FILE_PATH'] = null;                      // Directory to store session files if using the file session module. If None, the backend will use a sensible default.

////////////
// CACHE //
//////////

$settings['CACHES'] = array(
    'default' => array(
        'backend' => 'bjork\core\cache\backends\dummy\DummyBackend',
    ),
);

//////////////
// SIGNING //
////////////

$settings['SIGNING_BACKEND'] = 'bjork\core\signing\TimestampSigner';

///////////
// CSRF //
/////////

// Namespaced callable to be used as view when a request is
// rejected by the CSRF middleware.
$settings['CSRF_FAILURE_VIEW'] = 'bjork\views\csrf\csrf_failure';

// Settings for CSRF cookie.
$settings['CSRF_COOKIE_NAME'] = 'csrftoken';
$settings['CSRF_COOKIE_DOMAIN'] = null;
$settings['CSRF_COOKIE_PATH'] = '/';
$settings['CSRF_COOKIE_SECURE'] = false;

// List of views to exempt from csrf processing. This is to workaround
// the fact that PHP does not have function decorators.
$settings['CSRF_EXEMPT_VIEWS'] = array();

///////////////
// MESSAGES //
/////////////

// Class to use as messages backend
$settings['MESSAGE_STORAGE'] = 'bjork\contrib\messages\storage\session\SessionStorage';

//////////////
// TESTING //
////////////

// The name of the class to use to run the test suite
$settings['TEST_RUNNER'] = 'bjork\test\runner\DiscoverRunner';


// Return settings to Bjork
return $settings;
