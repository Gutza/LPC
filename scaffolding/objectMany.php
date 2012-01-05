<?php

require "common.php";

$p=LPC_Page::getCurrent();
$p->title=_LS('scaffoldingTitle');
$p->st();

/*
$p->head->a(new LPC_HTML_script(LPC_js."/jquery.js"));
$p->head->a(new LPC_HTML_script(LPC_js."/LPC_scaffolding.js"));
*/

if (empty($_GET['c'])) {
	$p->a(new LPC_HTML_error(_LH('scaffoldingErrorNeedClass')));
	return;
}

$class=$_GET['c'];
if (!validClassName($class) || !validateClassRights($class)) {
	$p->a(new LPC_HTML_error(_LH('genericErrorRights')));
	return;
}

$rObj=new $_GET['rc']($_GET['rid']);
if (isset($_GET['caid'])) {
	$rObj->createLink($_GET['rd'],$_GET['caid']);
	$url=LPC_Url::remove_get_var($_SERVER['REQUEST_URI'],'caid');
	header("Location: ".$url);
	exit;
}
if (isset($_GET['crid'])) {
	$rObj->dropLink($_GET['rd'],$_GET['crid']);
	$url=LPC_Url::remove_get_var($_SERVER['REQUEST_URI'],'crid');
	header("Location: ".$url);
	exit;
}

$obj=new $class();
$cancelDiv=new LPC_HTML_node('p');
$cancelDiv->a("[<a href='".$_GET['rt']."'>"._LS('scaffoldingCancelPick')."</a>]");
$p->a($cancelDiv);

$l=$obj->getBaseList();
$l->onProcessHeaderRow='rhr';
$l->onProcessBodyRow='rbr';

$p->a($l);

$p->a($cancelDiv);

function rhr($row)
{
	$th=new LPC_HTML_node('th');
	$row->a($th);
	$th->a(_LH('scaffoldingActionHeader'));
	return true;
}

function rbr($row,&$rowData)
{
	global $obj, $rObj;
	$td=new LPC_HTML_node('td');
	$row->a($td);
	$cid=$rowData[$obj->dataStructure['id_field']];
	if ($rObj->createLink($_GET['rd'],$cid,1)) {
		$url=LPC_Url::add_get_var($_SERVER['REQUEST_URI'],'crid',$cid);
		$td->a("[<a href='".$url."'>"._LS('scaffoldingRemoveThis')."</a>]");
	} else {
		$url=LPC_Url::add_get_var($_SERVER['REQUEST_URI'],'caid',$cid);
		$td->a("[<a href='".$url."'>"._LS('scaffoldingAddThis')."</a>]");
	}
	return true;
}
