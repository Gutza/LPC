<?php

define("LPC_OB_BASELINE",ob_get_level());

require_once "LPC_lib.php";

if (LPC_debug) {
	error_reporting(E_ERROR | E_PARSE);
	ini_set('display_errors',1);
	ini_set('display_startup_errors',1);
}

