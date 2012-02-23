<?php

require "common.php";
$p->title="Message checker";
$p->st();

$p->a(
	"<p>".
		"[<a href='message_select.php'>Back to the message list</a>]".
	"</p>"
);

if (isset($_GET['delete']))
	$p->a(deleteOne($_GET['delete']));

$obsolete=get_obsolete();
if (isset($_GET['deleteAll']) && $_GET['deleteAll']) {
	$p->a(deleteMany($obsolete));
	$obsolete=get_obsolete();
}
if (!$obsolete) {
	$p->a("<p>There are no obsolete message keys.</p>");
	return;
}

$p->a("<p>[<a href='?deleteAll=1' onClick='return confirm(\"Are you sure you want to delete ALL obsolete messages?\")'>Delete all</a>]</p>");

$msg=new LPC_HTML_node('div');
$p->a($msg);

$msg->a("The following message keys seem to be obsolete (or at least they haven't been found <b><i>as such</i></b> under <tt>".LPC_base_path."</tt> in any .php files):");

$list=new LPC_HTML_node('ul');
$msg->a($list);

foreach($obsolete as $obso) {
	$entry=new LPC_HTML_node('li');
	$list->a($entry);
	$entry->a("<a href='?delete=".rawurlencode($obso)."'><img src='".LPC_ICON_ERASER."' alt='Delete'></a>");
	$entry->a(htmlspecialchars($obso));
}

function find_message($message_key)
{
	exec(
		"grep -wrnq --include=\*.php --exclude-dir=".escapeshellarg(LPC_path."/docs")." ".
			escapeshellarg($message_key)." ".
			escapeshellarg(LPC_base_path),
		$devnull,
		$exitcode
	);
	return $exitcode==0;
}

function deleteMany($obsolete)
{
	$fname=getFName();
	foreach($obsolete as $obso)
		if (!deleteMessage($obso,$fname))
			return deleteConfirm($fname,true);
	return deleteConfirm($fname);
}

function deleteOne($obso)
{
	$fname=getFName();
	if (!deleteMessage($obso,$fname))
		return new LPC_HTML_error("Failed deleting message key ".htmlspecialchars($obso));
	return deleteConfirm($fname);
}

function getFName()
{
	return tempnam(NULL,"LPC_".LPC_INSTALLATION_KEY."_msgBak.");
}

function deleteMessage($message_key,$bak_fname)
{
	$fp=fopen($bak_fname,'a');
	if (!$fp)
		throw new RuntimeException("Failed opening backup file ".$bak_fname);
	$ref=new LPC_I18n_reference($message_key);
	fputs($fp,serialize(LPC_Impex::export($ref)));
	fclose($fp);
	$result=$ref->delete();
	return $result;
}

function deleteConfirm($fname,$ok=true)
{
	$good=new LPC_HTML_confirm("The deleted messages were saved in ".htmlspecialchars($fname));
	if ($ok)
		return $good;

	$mixed=new LPC_HTML_node('div');
	$mixed->a($good);
	$bad=new LPC_HTML_error("Some messages couldn't be deleted.");
	$mixed->a($bad);
	return $mixed;
}

function get_obsolete()
{
	$ref=new LPC_I18n_reference();
	$refs=$ref->search(NULL,NULL,0);
	$obsolete=array();
	foreach($refs as $ref) {
		if (find_message($ref->id))
			continue;
		$obsolete[]=$ref->id;
	}
	return $obsolete;
}
