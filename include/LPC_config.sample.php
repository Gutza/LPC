<?php

/**
 * LPC configuration sample file.
 *
 * You need to copy this file as LPC_config.php in this directory and
 * edit it to fit your setup.
 *
 * WARNING: Most settings HAVE do be defined explicitly
 *          (i.e. there are typically no defaults in place)
 * @author Bogdan Stancescu <bogdan@moongate.ro>
 */

// Include your own project's library. This is optional.
// require dirname(dirname(dirname(__FILE__)))."/include/MY_lib.php";

if (!function_exists('LPC_skip')) {
	function LPC_skip()
	{
		return false;
	}
}

// Change this to your project's short name
define('LPC_project_name','LPC');

// Change this to your project's long name
define('LPC_project_full_name','LPC, the PHP library');

// Change this to the URI pointing to your server's document root (no trailing slash)
define('LPC_base_url','http://www.example.com');

// Change this to the web path to your project (no trailing slash; empty if it's in the root)
define('LPC_project_url','/myProject');

// Change to true if you want to enable the GUI module.
define('LPC_GUI',false);
// Set the default page class
//define('LPC_page_class','LPC_Page');

// Default system language ID; taken from LPC_Language
define('LPC_default_language',1);

// System language type (LPC_Language field)
define('LPC_language_type','POSIX');

// The system path that contains this entire project (usually the directory above LPC)
define('LPC_base_path',dirname(dirname(dirname(__FILE__))));

// Change to true if you want LPC to automatically grab the output and populate the page
if (!defined('LPC_GUI_OB'))
	define('LPC_GUI_OB',false);

// Identify your user class, if you want to use native authentication
define('LPC_user_class','');

// Define this if you want LPC to ensure that all users are authenticated
// Seeing as it's an environment variable, you can also define it using
// external means, e.g. Apache's mod_env.
//putenv("LPC_auth=1");

// Identify your projects class, if you want to use them
define('LPC_project_class','');

// Enable if you want to display errors; also includes LPC/include/LPC_debug.php
define('LPC_debug',false);

// If you enabled debugging, you can override the default exception handler.
// String for function name, anything else to leave the PHP handler in place.
//define('LPC_exception_handler', NULL);

// Add other directories containing your own classes here, e.g.
// $LPC_extra_class_dirs=array(dirname(dirname(dirname(__FILE__)))."/include/classes");
$LPC_extra_class_dirs=array();

// LPC_CACHE_TYPE must be one of:
// none		no caching is performed (poor performance)
//		* requires nothing
// session	caching is performed in the session (duplicates caches)
//		* requires nothing
// database	caching is performed in the database (reasonable
//		performance)
//		* requires LPC database setup (LPC_Base.php)
// memcache	caching uses memcached (excellent performance);
//		* requires memcached (yum install memcached)
//		* requires memcache PHP extension (pecl install memcache)
//		* requires the next two settings (LPC_CACHE_MC_HOST and
//		  LPC_CACHE_MC_HOST)
// apc		caching uses APC (superb performance);
//		* requires pecl apc (depending on your distro, package
//		  php-pecl-apc may be available, or you might have to
//		  install it using pecl install apc)
//		* it's not available for CLI (technically it is available
//		  if you enable it with apc.enable_cli = 1, but it's typically
//		  useless for CLI because every new thread uses a new cache).
//		* as a corollary, if you use APC you will need to perform
//		  all permissions-related actions from within the interface
//		  (i.e. not from CLI), because otherwise your cache will
//		  be unaffected by the changes.
define('LPC_CACHE_TYPE','session');

// The host to use for memcache cache; not needed otherwise
//define('LPC_CACHE_MC_HOST','127.0.0.1');

// The port to use for memcache cache; not needed otherwise
//define('LPC_CACHE_MC_PORT',11211);

// Change this if you need to. You typically don't need to.
mb_internal_encoding("UTF-8");

// This value identifies this particular LPC_config.php file on this
// server/cluster. This key is used to identify this particular LPC deployment
// for system/cluster-wide settings (memcache, Zookeeper). Try to keep it as
// short as possible (a single character, if you can help it).
define('LPC_INSTALLATION_KEY','d'); // "d" for "default"

// Define as many databases as you need. You can define these at any time,
// but make sure you don't try to instantiate any object that uses a database
// key that hasn't been defined yet.
/*
LPC_DB::registerKey("sample_db_key", array(
	'type'=>"mysqli",
	'host'=>"localhost",
	'user'=>"sample_user",
	'password'=>"sample_password",
	'database'=>"sample_database",
//	'collation'=>'utf8'
));
*/

// Zookeeper configuration. Define it if you plan to use ZK locks.
//define("LPC_ZOOKEEPER_HOST", "localhost:2181");

// --------------------------------------------------------------------------
// You typically shouldn't need to change anything below this line
// --------------------------------------------------------------------------
// If needed, change this to the web path to the LPC base directory (no trailing slash)
define('LPC_url',LPC_project_url.'/LPC');

// If needed, change this to reflect the full URL of the LPC base directory (no trailing slash)
define('LPC_full_url',LPC_base_url.LPC_url);

// If needed, change this to reflect the full URL of your project (no trailing slash)
define('LPC_project_full_url',LPC_base_url.LPC_project_url);

define('LPC_images',LPC_url.'/images');
define('LPC_js',LPC_url.'/js');
define('LPC_css',LPC_url.'/css');

