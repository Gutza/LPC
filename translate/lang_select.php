<?php

require "common.php";

if (isset($_POST['submit'])) {
	$_SESSION['LPC_target_lang']=$_POST['target_lang'];
	$_SESSION['LPC_reference_lang']=$_POST['reference_lang'];
	header("Location: index.php?m=".rawurlencode($msgKey));
	exit;
}

$p->title="Set language";
$p->st();

$p->a("<p>Please select a language to translate to, and a reference language. You only need to do this once per session (or if you want to translate to a different language).</p>");

$form=new LPC_HTML_form();
$p->a($form);

$form->a("<input type='hidden' name='m' value=\"".addslashes($msgKey)."\">");

$t=new LPC_HTML_table();
$form->a($t);

$ref_selector=new LPC_HTML_select('target_lang',$_SESSION,'LPC_target_lang');
$lng=new LPC_Language();
$langs=$lng->search('translated',1,'name');
foreach($langs as $lang)
	$ref_selector->addOption($lang->getAttrF('name'),$lang->id);

$t->a(new LPC_HTML_form_row(array(
	'label'=>'Reference language',
	'input'=>$ref_selector
)));

$target_selector=new LPC_HTML_select('reference_lang',$_SESSION,'LPC_reference_lang');
$langs=$lng->search(NULL,NULL,'name');
foreach($langs as $lang)
	$target_selector->addOption($lang->getAttrF('name'),$lang->id);

$t->a(new LPC_HTML_form_row(array(
	'label'=>'Target language',
	'input'=>$target_selector
)));

$t->a(new LPC_HTML_form_row(array(
	'label'=>'&nbsp;',
	'input'=>"<input type='submit' name='submit' value='Select languages'>"
)));
