<?php

define("LPC_start_time",microtime(true));

if (isset($_SERVER['REQUEST_METHOD'])) {
	$sess_hash=sha1(__FILE__);
	session_name('LPC_'.substr($sess_hash, 0, 5+hexdec(substr($sess_hash, -1))));
	unset($sess_hash);

	session_start();
}

if (!isset($_SESSION['LPC']))
	$_SESSION['LPC']=array();

define('LPC_include',dirname(__FILE__));
define('LPC_classes',LPC_include.'/classes');
define('LPC_base',dirname(LPC_include));

require LPC_include."/adodb5/adodb.inc.php";
require LPC_include."/LPC_include_manager.php";
require LPC_include."/LPC_intl.php";
require LPC_include."/LPC_config.php";

if (LPC_GUI)
	require LPC_include."/LPC_icons.php";

if (LPC_GUI_OB)
	ob_start();

if (
	getenv("LPC_auth") &&
	isset($_SERVER['REMOTE_ADDR']) && // not for CLI
	!LPC_User::getCurrent(true) &&
	($usr=LPC_User::newUser()) && // lazy instantiation
	LPC_URI::getCurrent()->getPath() != $usr->recoverPasswordURL() &&
	LPC_URI::getCurrent()->getFullPath() != $usr->processTokenBaseURL()
)
	LPC_User::getCurrent();

function LPC_prefill(&$array, $values)
{
	$count=0;
	foreach($values as $key=>$value) {
		if (array_key_exists($key, $array))
			continue;
		$array[$key]=$value;
		$count++;
	}
	return $count;
}
