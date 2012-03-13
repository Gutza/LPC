<?php

require "common.php";

$p=LPC_Page::getCurrent();
$p->title=_LS('scaffoldingTitle');
$p->st();

if (isset($_GET['c']))
	$class=$_GET['c'];
else {
	$p->a(new LPC_HTML_error(_LH('scaffoldingErrorNeedClass')));
	return;
}

$id=0;
if (isset($_GET['id']))
	$id=$_GET['id'];
if (!$id) {
	$p->a(new LPC_HTML_error(_LH('scaffoldingErrorNeedID')));
	return;
}

if (
	!validClassName($class) ||
	!($obj=new $class($id)) ||
	!$obj->hasScaffoldingRight('D')
) {
	$p->a(new LPC_HTML_error(_LH('genericErrorRights')));
	return;
}

if (
	isset($_GET['k']) &&
	$_GET['k']==session_id()
)
	$obj->processScaffoldingDelete();
