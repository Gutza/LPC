<?php

require "common.php";

$p=LPC_Page::getCurrent();
$p->st('scaffoldingTitle');

if (empty($_GET['c'])) {
	$p->a(new LPC_HTML_error(__LH('scaffoldingErrorNeedClass')));
	return;
}
$class=$class=$_GET['c'];

if (empty($_GET['id'])) {
	$p->a(new LPC_HTML_error(_LH('scaffoldingErrorNeedID')));
	return;
}
$id=$_GET['id'];

if (
	!validClassName($class) ||
	!($obj=new $class($id)) ||
	!$obj->hasScaffoldingRight('D') // there should be a separate right for adding/dropping links
) {
	$p->a(new LPC_HTML_error(_LH('genericErrorRights')));
	return;
}

if (empty($_GET['rd'])) {
	$p->a(new LPC_HTML_error(_LH('scaffoldingErrorNeedDependency')));
	return;
}
$rd=$_GET['rd'];

if (empty($_GET['rc'])) {
	$p->a(new LPC_HTML_error(__LH('scaffoldingErrorNeedClass')));
	return;
}
$rclass=$_GET['rc'];

if (empty($_GET['rid'])) {
	$p->a(new LPC_HTML_error(_LH('scaffoldingErrorNeedID')));
	return;
}
$rid=$_GET['rid'];

if (
	!validClassName($rclass) ||
	!($robj=new $rclass($rid)) ||
	!$robj->hasScaffoldingRight('D') // there should be a separate right for adding/dropping links
) {
	$p->a(new LPC_HTML_error(_LH('genericErrorRights')));
	return;
}

$obj->dropLink($rd, $robj);
header("Location: ".$_GET['rt']);
exit;

