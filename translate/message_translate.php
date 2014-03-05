<?php

require "common.php";

if (empty($_REQUEST['m'])) {
	header("Location: index.php");
	exit;
}
if (isset($_REQUEST['l'])) {
	$l=LPC_Language::newLanguage();
	$l->fromKey($_REQUEST,'l');
	if ($l->id)
		$_SESSION['LPC_target_lang']=$l->id;
}

if (empty($_SESSION['LPC_target_lang'])) {
	header("Location: lang_select.php?m=".rawurlencode($msgKey));
	exit;
}

$p->title="Message translator";
$p->st();

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
if (!$ref->probe()) {
	$p->a("<div class='container'>&larr; <a href='message_select.php'>Back to the message list</a></div>");
	$p->a(new LPC_HTML_error("This reference message was not found in the database: [".htmlspecialchars($msgKey)."]"));
	$p->show();
	return;
	$p->show();
}
if (isset($_POST['delete'])) {
	$ref->delete();
	header("Location: message_select.php");
	exit;
}
$target_lang=new LPC_Language($_SESSION['LPC_target_lang']);
$reference_lang=new LPC_Language($_SESSION['LPC_reference_lang']);

if (isset($_POST['submit'])) {
	LPC_HTML_form::enforceSK();
	$msg->setAttr('translation',$_POST['translation']);
	$msg->save();
	$ref->setAttrs(array(
		'comment'=>$_POST['comment'],
		'system'=>isset($_POST['system']),
	));
	$ref->save();
}

// The links on the top of  the page
$links=array(
	"[<a href='lang_select.php?m=".rawurlencode($msgKey)."'>Translate this message to another language</a>]",
	"[<a href='message_select.php'>Message list</a>]"
);

$sql="
	SELECT ref.message_key
	FROM LPC_i18n_reference ref
	LEFT JOIN LPC_i18n_message msg ON msg.message_key=ref.message_key AND msg.language=".$_SESSION['LPC_target_lang']."
	WHERE msg.id IS NULL
	LIMIT 1
";
$rs=$msg->query($sql);
if (!$rs->EOF && $rs->fields[0]!=$msgKey)
	$links[]="[<a href='?m=".rawurlencode($rs->fields[0])."'>Find an untranslated message</a>]";
$p->a("<p class='container'>".implode(" &bull; ",$links)."</p>");
// Done links

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
$t->addClass('two-column-60');
$form->a($t);

$t->a(new LPC_HTML_form_row(array(
	'label'=>'Message key',
	'input'=>$msg->getAttrH('message_key'),
)));
$t->a(new LPC_HTML_form_row(array(
	'label'=>'Message in reference language ('.$reference_lang->getAttrH('name').')',
	'input'=>"<div style='height: 300px; overflow: auto'><tt>".nl2br($ref_trans->getAttrH('translation'))."</tt></div>",
)));
$trans = new LPC_HTML_node('textarea');
$trans->setAttrs(array(
	'name' => 'translation',
	'style' => 'width: 100%; height: 300px',
));
$trans->a($msg->getAttrH("translation"));
$input = new LPC_HTML_html_editor($trans);
$t->a(new LPC_HTML_form_row(array(
	'label'=>'Translation to target language ('.$target_lang->getAttrH('name').')',
	'input'=>$input,
	'explain'=>"If any parameters are specified in the code, they are formatted here as {0}, {1} etc in the most basic form. If you need more complex stuff, see <a href='http://www.php.net/manual/en/messageformatter.formatmessage.php#refsect1-messageformatter.formatmessage-examples'>the documentation</a>.",
)));
$t->a(new LPC_HTML_form_row(array(
	'label'=>'Comment',
	'input'=>"<textarea name='comment' rows='5' style='width:100%; font-size: 85%'>".$ref->getAttrH('comment')."</textarea>",
)));
$sysChecked="";
if ($ref->getAttr('system'))
	$sysChecked=' checked';
$t->a(new LPC_HTML_form_row(array(
	'label'=>'Message options',
	'input'=>
		"<input type='checkbox' name='system' value='1'$sysChecked id='system'> ".
		"<label for='system'>System message (not specific to this project)</label>"
)));
$t->a(new LPC_HTML_form_row(array(
	'label'=>'&nbsp;',
	'input'=>"<input type='submit' name='submit' value='Translate'> ".
		"<input type='submit' name='delete' value='Delete' onClick=\"return confirm('Are you sure you want to PERMANENTLY delete this message and all its translations?')\">",
)));
$form->addSK();
$p->show();
