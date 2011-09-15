<?php

define("LPC_start_time",microtime(true));

if (isset($_SERVER['REQUEST_METHOD']))
	session_start();

if (!isset($_SESSION['LPC']))
	$_SESSION['LPC']=array();

define('LPC_include',dirname(__FILE__));
define('LPC_classes',LPC_include.'/classes');
define('LPC_base',dirname(LPC_include));

require LPC_include."/adodb5/adodb.inc.php";
require LPC_include."/LPC_include_manager.php";
require LPC_include."/LPC_intl.php";

require dirname(__FILE__)."/LPC_config.php";

if (LPC_GUI)
	require LPC_include."/LPC_icons.php";

if (LPC_GUI_OB)
	ob_start();

if (LPC_debug)
	require LPC_include."/LPC_debug.php";

if (getenv("LPC_auth") && isset($_SERVER['REMOTE_ADDR'])) // not for CLI
	LPC_User::getCurrent();
