<?php

require "common.php";

$p=LPC_Page::getCurrent();
$p->title=_LS('scaffoldingTitle');
$p->st();

$p->head->a(new LPC_HTML_script(LPC_js."/jquery.js"));
$p->head->a(new LPC_HTML_script(LPC_js."/LPC_scaffolding.js"));

if (isset($_POST['LPC_scaffolding_class_name']))
	$class=$_POST['LPC_scaffolding_class_name'];
elseif (isset($_GET['c']))
	$class=$_GET['c'];
else {
	$p->a(new LPC_HTML_error(_LH('scaffoldingErrorNeedClass')));
	return;
}

if (isset($_GET['rt']))
	$return="[<a href='".$_GET['rt']."'>"._LS('scaffoldingBackToList')."</a>]";
else
	$return="[<a href='objectList.php?c=".rawurlencode($class)."'>"._LS('scaffoldingSwitchObject')."</a>]";

if (isset($_GET['rc']) && isset($_GET['rid']))
	$return.=" &bull; [<a href='?c=".$_GET['rc']."&amp;id=".$_GET['rid']."'>"._LS('scaffoldingBackToParent',htmlspecialchars($_GET['rc']),htmlspecialchars($_GET['rid']))."</a>]";

$id=0;
if (isset($_POST['LPC_scaffolding_id']))
	$id=$_POST['LPC_scaffolding_id'];
elseif (isset($_GET['id']))
	$id=$_GET['id'];

$newSame="";
if ($id)
	$newSame="[<a href='?c=$class'>"._LS('scaffoldingCreateObject',$class)."</a>] &bull; ";
$p->a(
"<p>".
	$return." &bull; ".
	$newSame.
	"[<a href='index.php'>"._LS('scaffoldingSwitchClass')."</a>]".
"</p>"
);

$refdata=array('rd','rc','rid');
foreach($refdata as $refatom) {
	if (isset($_POST['LPC_scaffolding_'.$refatom]))
		$$refatom=$_POST['LPC_scaffolding_'.$refatom];
	elseif (isset($_GET[$refatom]))
		$$refatom=$_GET[$refatom];
}
if ($id) {
	$submitLabelKey='scaffoldingButtonEdit';
} else {
	$submitLabelKey='scaffoldingButtonCreate';
}

if (
	!validClassName($class) ||
	!($obj=new $class($id)) ||
	!$obj->hasScaffoldingRight('W')
) {
	$p->a(new LPC_HTML_error(_LH('genericErrorRights')));
	return;
}
if ($id && !$obj->probe()) {
	$p->a(new LPC_HTML_error(_LH('genericErrorMissingObject',$class,$id)));
	return;
}

if (
	isset($_POST['LPC_scaffolding_submit_button']) ||
	isset($_POST['LPC_scaffolding_submit_plus'])
) {
	try {
		LPC_HTML_form::enforceSK();
		$obj->processScaffoldingEdit();

		if (withAttach()) {
			$link=new $rc($rid);
			$link->createLink($rd,$obj);
		}

		$url=$_SERVER['REQUEST_URI'];
		if (isset($_POST['LPC_scaffolding_submit_button']) && !isset($_GET['id']))
			$url=LPC_Url::add_get_var($url,'id',$obj->id);
		elseif (isset($_POST['LPC_scaffolding_submit_plus']) && isset($_GET['id']))
			$url=LPC_Url::remove_get_var($url,'id');
		header("Location: ".$url);
		exit;
	} catch (Exception $e) {
		$p->a(new LPC_HTML_error(_LH('scaffoldingSaveError',$e->getMessage())));
	}
}

$skipAttr="";
if (withAttach()) {
	$link=new $rc();
	$skipAttr=$link->dataStructure['depend'][$rd]['attr'];
}

$form=new LPC_HTML_form($_SERVER['REQUEST_URI'],'post',true);
$p->a($form);
$form->addSK();
$t=new LPC_HTML_table();
$t->compact=false;
$form->a($t);

$attrs=$obj->getScaffoldingAttributes();
foreach($attrs as $attr)
	if ($attr!=$skipAttr)
		$t->a($obj->getScaffoldingEditRow($attr));

if ($class::$i18n_class) {
	$t->a("<tr><th colspan='2'>"._LH('scaffoldingLocalizedSection')."</th></tr>");
	$obj->initI18n();
	$i18n_fields=$obj->i18n_object->getScaffoldingAttributes();
	foreach($i18n_fields as $attr)
		$t->a($obj->i18n_object->getScaffoldingEditRow($attr));
}

$submit="<input type='submit' name='LPC_scaffolding_submit_button' value=\"".addslashes(_LS($submitLabelKey))."\">";
if (withAttach())
	$submit.=" <input type='submit' name='LPC_scaffolding_submit_plus' value=\"".addslashes(_LS('scaffoldingButtonEditPlus'))."\">";
$t->a(new LPC_HTML_form_row(array(
	'label'=>'&nbsp;',
	'input'=>$submit,
)));

function withAttach()
{
	global $rc, $rid, $rd;
	return
		!empty($rc) &&
		!empty($rid) &&
		!empty($rd);
}
