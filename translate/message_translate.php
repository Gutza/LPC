<?php

require "common.php";

if (empty($_REQUEST['m'])) {
	header("index.php");
	exit;
}

$p->title="Message translator";
$p->st();

$p->a("<p>".
	"[<a href='lang_select.php?m=".rawurlencode($_REQUEST['m'])."'>Translate this message to another language</a>] &bull; ".
	"[<a href='message_select.php'>Message list</a>]".
"</p>");

$msg=new LPC_I18n_message();
$msg=$msg->search(
	array('language','message_key'),
	array($_SESSION['LPC_target_lang'],$msgKey)
);
if ($msg)
	$msg=$msg[0];
else {
	$msg=new LPC_I18n_message();
	$msg->setAttr('language',$_SESSION['LPC_target_lang']);
	$msg->setAttr('message_key',$msgKey);
}

$ref=new LPC_I18n_reference($msgKey);
$target_lang=new LPC_Language($_SESSION['LPC_target_lang']);
$reference_lang=new LPC_Language($_SESSION['LPC_reference_lang']);

if (isset($_POST['submit'])) {
	LPC_HTML_form::enforceSK();
	$msg->setAttr('translation',$_POST['translation']);
	$msg->save();
	$ref->setAttr('comment',$_POST['comment']);
	$ref->save();
}

$ref_trans=new LPC_I18n_message();
$ref_trans=$ref_trans->search(
	array('language','message_key'),
	array($_SESSION['LPC_reference_lang'],$msgKey)
);
if ($ref_trans)
	$ref_trans=$ref_trans[0];
else
	$ref_trans=new LPC_I18n_message();

$form=new LPC_HTML_form();
$form->a("<input type='hidden' name='m' value=\"".$msg->getAttrF('message_key')."\">");
$p->a($form);
$t=new LPC_HTML_table();
$form->a($t);

$t->a(new LPC_HTML_form_row(array(
	'label'=>'Message key',
	'input'=>$msg->getAttrH('message_key'),
)));
$t->a(new LPC_HTML_form_row(array(
	'label'=>'Message in reference language ('.$reference_lang->getAttrH('name').')',
	'input'=>$ref_trans->getAttrH('translation'),
)));
$t->a(new LPC_HTML_form_row(array(
	'label'=>'Translation to target language ('.$target_lang->getAttrH('name').')',
	'input'=>"<textarea name='translation' rows='5' style='width:100%'>".$msg->getAttrH("translation")."</textarea>"
)));
$t->a(new LPC_HTML_form_row(array(
	'label'=>'Comment',
	'input'=>"<textarea name='comment' rows='5' style='width:100%; font-size: 85%'>".$ref->getAttrH('comment')."</textarea>",
)));
$t->a(new LPC_HTML_form_row(array(
	'label'=>'&nbsp;',
	'input'=>"<input type='submit' name='submit' value='Translate'>"
)));
$form->addSK();
