<?php

require "common.php";

$p=LPC_Page::getCurrent();
$p->title=_LS('scaffoldingTitle');
$p->st();

if (isset($_POST['LPC_scaffolding_class_name']))
	$class=$_POST['LPC_scaffolding_class_name'];
elseif (isset($_GET['c']))
	$class=$_GET['c'];
else {
	$p->a(new LPC_HTML_error(_LH('scaffoldingErrorNeedClass')));
	return;
}

$id=0;
if (isset($_GET['id']))
	$id=$_GET['id'];
if (!$id) {
	$p->a(new LPC_HTML_error(_LH('scaffoldingErrorNeedId')));
	return;
}
if (
	!validClassName($class) ||
	!($obj=new $class($id)) ||
	!$obj->hasScaffoldingRight('F')
) {
	$p->a(new LPC_HTML_error(_LH('genericErrorRights')));
	return;
}
if (!isset($_GET['file'])) {
	$p->a(new LPC_HTML_error(_LH('scaffoldingErrorNeedFile')));
	return;
}
$fileKey=$_GET['file'];
if (empty($obj->dataStructure['files'][$fileKey])) {
	$p->a(new LPC_HTML_error(_LH('scaffoldingErrorNeedValidFile',$fileKey)));
	return;
}
if ($id && !$obj->probe()) {
	$p->a(new LPC_HTML_error(_LH('genericErrorMissingObject',$class,$id)));
	return;
}

$meta=$obj->dataStructure['files'][$fileKey];

$mime="application/octet-stream";
if (isset($meta['mime']))
	$mime=$obj->getAttr($meta['mime']);
header("Content-type: ".$mime);

$name="";
if (isset($meta['name']))
	$name="; filename=\"".addslashes($obj->getAttr($meta['name']))."\"";
header("Content-disposition: attachment".$name);

header("Content-length: ".strlen($obj->getAttr($meta['content'])));

echo $obj->getAttr($meta['content']);
exit;
