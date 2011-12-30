<?php

require "common.php";

$p=LPC_Page::getCurrent();
//$p->renderMode='raw';
$p->noHeader=true;

$p->head->a(new LPC_HTML_script(LPC_js."/jquery.js"));
$p->head->a(new LPC_HTML_script(LPC_js."/LPC_scaffolding.js"));

if (empty($_GET['c'])) {
	$p->a(new LPC_HTML_error(_LH('scaffoldingErrorNeedClass')));
	return;
}

$class=$_GET['c'];
if (!validClassName($class) || !validateClassRights($class)) {
	$p->a(new LPC_HTML_error(_LH('genericErrorRights')));
	return;
}

$obj=new $class();
$cancelDiv=new LPC_HTML_node('p');
$cancelDiv->a("[<a href='#' onClick='return LPC_scaffolding_cancelPick()'>"._LS('scaffoldingCancelPick')."</a>]");
$p->a($cancelDiv);

$l=$obj->getBaseList();
$l->onProcessHeaderRow='rhr';
$l->onProcessBodyRow='rbr';

$p->a($l);

if ($cancelDiv)
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
	global $obj;
	$td=new LPC_HTML_node('td');
	$row->a($td);
	$td->a("[<a href='#' onClick='return LPC_scaffolding_pick(\"".addslashes($rowData[$obj->dataStructure['id_field']])."\")'>"._LS('scaffoldingPickThis')."</a>]");
	return true;
}
