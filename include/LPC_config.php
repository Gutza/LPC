<?php

// Change this to the web path to your project
define('LPC_url','/DT');

// Change this to reflect the full URL of your project; note the path above
define('LPC_full_url','http://vtm.moongate.ro'.LPC_url);

// Change to true if you want to enable the GUI module.
define('LPC_GUI',false);
// Set the default page class
//define('LPC_page_class','LPC_Page');

// Change to false if you want to manually populate the page
if (!defined('LPC_GUI_OB'))
	define('LPC_GUI_OB',true);

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

// Add other directories containing your own classes here, e.g.
// $LPC_extra_class_dirs=array(dirname(__FILE__)."/classes");
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
define('LPC_CACHE_TYPE','session');

// The host to use for memcache cache; not needed otherwise
//define('LPC_CACHE_MC_HOST','127.0.0.1');

// The port to use for memcache cache; not needed otherwise
//define('LPC_CACHE_MC_PORT',11211);

// This value identifies this particular LPC_config.php file on this
// server. This key is used to identify this particular LPC deployment
// for system-wide settings (e.g. for memcache). Try to keep it as
// short as possible (a single character, if you can help it).
// In an ideal world, this would've been set by default to the path
// of this LPC_config.php file, but that would've been too long a string.
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

LPC_DB::registerKey("vtm_dt", array(
	'type'=>"mysqli",
	'host'=>"localhost",
	'user'=>"vtm_dt",
	'password'=>"v3t3m3d3t3",
	'database'=>"vtm_dt",
	'collation'=>'utf8'
));

// --------------------------------------------------------------------------
// You typically shouldn't need to change anything below this line
// --------------------------------------------------------------------------
define('LPC_images',LPC_url.'/images');
define('LPC_js',LPC_url.'/js');
define('LPC_css',LPC_url.'/css');

