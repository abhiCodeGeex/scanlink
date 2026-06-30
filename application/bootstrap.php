<?php defined('SYSPATH') or die('No direct script access.');

// ini_set('display_errors', 1);
// error_reporting(E_ALL);


// -- Environment setup --------------------------------------------------------

// Load the core Kohana class
require SYSPATH . 'classes/kohana/core' . EXT;

if (is_file(APPPATH . 'classes/kohana' . EXT)) {
	// Application extends the core
	require APPPATH . 'classes/kohana' . EXT;
} else {
	// Load empty core extension
	require SYSPATH . 'classes/kohana' . EXT;
}

/**
 * Set the default time zone.
 *
 * @see  http://kohanaframework.org/guide/using.configuration
 * @see  http://php.net/timezones
 */
date_default_timezone_set('Australia/Victoria');

/**
 * Set the default locale.
 *
 * @see  http://kohanaframework.org/guide/using.configuration
 * @see  http://php.net/setlocale
 */
setlocale(LC_ALL, 'en_US.utf-8');

/**
 * Enable the Kohana auto-loader.
 *
 * @see  http://kohanaframework.org/guide/using.autoloading
 * @see  http://php.net/spl_autoload_register
 */
spl_autoload_register(array('Kohana', 'auto_load'));

/**
 * Enable the Kohana auto-loader for unserialization.
 *
 * @see  http://php.net/spl_autoload_call
 * @see  http://php.net/manual/var.configuration.php#unserialize-callback-func
 */
ini_set('unserialize_callback_func', 'spl_autoload_call');

ini_set('upload_max_filesize', '4M');

// -- Configuration and initialization -----------------------------------------

/**
 * Set the default language
 */
I18n::lang('en-us');

/**
 * Set Kohana::$environment if a 'KOHANA_ENV' environment variable has been supplied.
 *
 * Note: If you supply an invalid environment name, a PHP warning will be thrown
 * saying "Couldn't find constant Kohana::<INVALID_ENV_NAME>"
 */
if (isset($_SERVER['KOHANA_ENV'])) {
	Kohana::$environment = constant('Kohana::' . strtoupper($_SERVER['KOHANA_ENV']));
}

/**
 * Initialize Kohana, setting the default options.
 *
 * The following options are available:
 *
 * - string   base_url    path, and optionally domain, of your application   NULL
 * - string   index_file  name of your index file, usually "index.php"       index.php
 * - string   charset     internal character set used for input and output   utf-8
 * - string   cache_dir   set the internal cache directory                   APPPATH/cache
 * - boolean  errors      enable or disable error handling                   TRUE
 * - boolean  profile     enable or disable internal profiling               TRUE
 * - boolean  caching     enable or disable internal caching                 FALSE
 */
Kohana::init(array(
	'base_url'   => 'https://scanlink.com.au/',
	'index.php' => FALSE,
	'errors'    => false
));

/**
 * Attach the file write to logging. Multiple writers are supported.
 */
Kohana::$log->attach(new Log_File(APPPATH . 'logs'));

/**
 * Attach a file reader to config. Multiple readers are supported.
 */
Kohana::$config->attach(new Config_File);

/**
 * Enable modules. Modules are referenced by a relative or absolute path.
 */
Kohana::modules(array(
	'auth'       => MODPATH . 'auth',       // Basic authentication
	'cache'      => MODPATH . 'cache',      // Caching with multiple backends
	'codebench'  => MODPATH . 'codebench',  // Benchmarking tool
	'database'   => MODPATH . 'database',   // Database access
	'image'      => MODPATH . 'image',      // Image manipulation
	'orm'        => MODPATH . 'orm',        // Object Relationship Mapping
	'unittest'   => MODPATH . 'unittest',   // Unit testing
	'userguide'  => MODPATH . 'userguide',  // User guide and API documentation
	'captch'  		=> MODPATH . 'captcha',
	'minion'  	=> MODPATH . 'minion',  // for cron
));

/**
 * Set the routes. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI.
 */


Route::set(
	'mobile',
	'<user>/<profile_id>(/<action>)',
	array(
		'user' => '[a-zA-Z0-9-_]+',
		'profile_id' => '[0-9]+'
	)
)
	->defaults(array(
		'controller' => 'mobile',
		'action' => 'index',
	));



Route::set('default', '(<controller>(/<action>(/<id>(/<clientid>(/<mode>)))))')
	->defaults(array(
		'controller' => 'welcome',
		'action'     => 'index',
	));

define("GALATECH_API","https://www.scanlink.com.au/api/web/api.php/");
//define("GALATECH_API", "http://api.galatech.com.au/web/api.php/");
define("UPLOAD_MAX_FILESIZE", "10 MB");
define("GOOGLE_MAP_API_KEY", "AIzaSyA4XMnJxj6xVLpIC-EeQDoavw_DwK3VuaQ");
define('GOOGLE_GEO_LOCATION_API_KEY', 'AIzaSyAmcGFQwpSCBVYXkXf_kp6VpnlPQmNaw60');

define('LOCATION_DESCRIPTION', serialize(array('Outdoor Seating', 'Indoor Seating', 'Construction site', 'Showroom/Retail', 'Office', 'Vehicle', 'Cinema/Theatre', 'Gym/Swimming Pool', 'Other')));

define('CLIENT_SECRET', 'LuHhLVj9ghpd71GtFJQu2hR5');
// define('GEOLOCATIONUSERNAME', 'webplanextest');
define('GEOLOCATIONUSERNAME', 'scanlink');

//Visitation Api urls
/* define('VISITATION_API', 'https://dhhs-api-sit.gateway.nonprod.api.vic.gov.au/dhhs/visitations/v1/webhooks');
define('CONTRACT_TRACING_REQUEST_URL', 'https://dhhs-api-sit.gateway.nonprod.api.vic.gov.au/dhhs/visitations/v1/contact-tracing-requests');
define('AUTH_ACCESS_TOKEN_URL', 'https://dhhs-api-sit.oauth.nonprod.api.vic.gov.au/api/oauth/token');

define('OAUTH_CLIENT_ID', '9b86c26d-ee37-4194-a6ce-ed6857f86411');
define('OAUTH_CLIENT_SECRET', 'b101e8f9-9ba2-4a98-81d2-c42e72307bd2'); */
//Visitation Api urls

//Testing mode
// define('OAUTH_CLIENT_ID', '59cbcca1-ec3c-4055-a2c4-cb8ff49305c8');
// define('OAUTH_CLIENT_SECRET', '7fd2a93c-9d1c-4fc5-8bb0-ece706d9979b');

//production mode
define('OAUTH_CLIENT_ID', '9b86c26d-ee37-4194-a6ce-ed6857f86411');
define('OAUTH_CLIENT_SECRET', 'b101e8f9-9ba2-4a98-81d2-c42e72307bd2');

define('API_BASE_URL', 'https://dhhs-api-sit.gateway.nonprod.api.vic.gov.au');
// define('API_BASE_URL', 'https://api.vic.gov.au:443');
// define('AUTH_ACCESS_TOKEN_URL', 'https://oauth.api.vic.gov.au/api/oauth/token');


define('AUTH_ACCESS_TOKEN_URL', 'https://dhhs-api-sit.oauth.nonprod.api.vic.gov.au/api/oauth/token');
define('CONTRACT_TRACING_REQUEST_URL', API_BASE_URL . '/dhhs/visitations/v1/contact-tracing-requests');
define('VISITOR_POST_REQUEST_URL', API_BASE_URL . '/dhhs/visitations/v1/visitors');
define('CREATE_WEBHOOK_POST_REQUEST', API_BASE_URL . '/dhhs/visitations/v1/webhooks');
define('WEBHOOK_GET_REQUEST', API_BASE_URL . '/dhhs/visitations/v1/webhooks/');

define('GATEWAY_REQUEST_ID', '86032cbe-a804-4c3b-86ce-ec3041e3effc');

//Visitaion api mode testing or production
define('VISITAION_API_MODE', 'testing');
