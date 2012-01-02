<?php

require "common.php";

if (isset($_GET['langID'])) {
	$lang=new LPC_Language();
	$lang->fromKey($_GET,'langID');
	if ($lang->id) {
		LPC_Language::setCurrent($lang);
		header("Location: ".LPC_Url::remove_get_var($_SERVER['REQUEST_URI'],'langID'));
		exit;
	}
}

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

$langSelect=new LPC_HTML_node('div');
$langSelect->setAttr('style','float: right');
$langSelect->a(_LH('scaffoldingSelectLang'));

$langs=new LPC_HTML_select();
$langSelect->a($langs);
$langObjs=new LPC_Language();
$langObjs=$langObjs->search(NULL,NULL,'name');
foreach($langObjs as $langObj)
	$langs->addOption($langObj->getAttr('name'),$langObj->id);
$langs->setAttr('onChange',"window.location=location.pathname+location.search+'&langID='+$(this).find('option:selected').val();");
$langs->selected=LPC_Language::getCurrent()->id;
$p->a(
"<p>".
	$langSelect->render().
	"[<a href='objectList.php?c=".rawurlencode($class)."'>"._LS('scaffoldingSwitchObject')."</a>] &bull; ".
	"[<a href='index.php'>"._LS('scaffoldingSwitchClass')."</a>]".
"</p>"
);

$id=0;
if (isset($_POST['LPC_scaffolding_id']))
	$id=$_POST['LPC_scaffolding_id'];
elseif (isset($_GET['id']))
	$id=$_GET['id'];

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

if (isset($_POST['LPC_scaffolding_submit_button'])) {
	try {
		LPC_HTML_form::enforceSK();
		$obj->processScaffoldingEdit();

		if (!empty($rc) && !empty($rid) && !empty($rd)) {
			$link=new $rc($rid);
			$link->createLink($rd,$obj);
		}
		header("Location: objectList.php?c=".get_class($obj));
		exit;
	} catch (Exception $e) {
		$p->a(new LPC_HTML_error(_LH('scaffoldingSaveError',$e->getMessage())));
	}
}

$form=new LPC_HTML_form(false,'post',true);
$p->a($form);
$form->a("<input type='hidden' name='LPC_scaffolding_class_name' value='$class'>");
$form->a("<input type='hidden' name='LPC_scaffolding_id' value='$id'>");
foreach($refdata as $refatom) {
	if (!empty($$refatom)) // Reference dependency
		$form->a("<input type='hidden' name='LPC_scaffolding_".$refatom."' value=\"".addslashes($$refatom)."\">");
}
$form->addSK();
$t=new LPC_HTML_table();
$t->compact=false;
$form->a($t);

foreach($obj->dataStructure['fields'] as $attr=>$desc)
	$t->a($obj->getScaffoldingEditRow($attr));

if ($class::$i18n_class) {
	$t->a("<tr><th colspan='2'>"._LH('scaffoldingLocalizedSection')."</th></tr>");
	$obj->initI18n();
	$i18n_fields=$obj->i18n_object->getScaffoldingFields();
	foreach($i18n_fields as $attr)
		$t->a($obj->i18n_object->getScaffoldingEditRow($attr));
}

$t->a(new LPC_HTML_form_row(array(
	'label'=>'&nbsp;',
	'input'=>"<input type='submit' name='LPC_scaffolding_submit_button' value=\"".addslashes(_LS($submitLabelKey))."\">",
)));
