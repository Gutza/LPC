<?php

$p=LPC_Page::getCurrent();
$u=LPC_User::getCurrent();
if (!$u->hasPerm('LPC_Can_translate')) {
	$p->a(new LPC_HTML_error("You don't have the necessary permissions to perform translations on this system."));
	$p->show();
	exit;
}

$msgKey="";
if (isset($_REQUEST['m']))
	$msgKey=$_REQUEST['m'];
