<?php

function LPC_set_session_name($suffix="", $override=false)
{
	static $set=false;
	if ($set && !$override)
		return;
	$set=true;

	$sess_hash=sha1(__FILE__);
	session_name('LPC_'.substr($sess_hash, 0, 5+hexdec(substr($sess_hash, -1))).$suffix);
}
