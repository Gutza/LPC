<?php

require "common.php";

if (empty($_REQUEST['c'])) {
	$p->a(new LPC_HTML_error(_LH('scaffoldingErrorNeedClass')));
	return;
}

$class=$_REQUEST['c'];
if (!validClassName($class) || !validateClassRights($class)) {
	$p->a(new LPC_HTML_error(_LH('genericErrorRights')));
	return;
}

$p=LPC_Page::getCurrent();
$p->st(_LS('scaffoldingColumnVisibilityTitle', $class));

$p->a("<p>".
	"[<a href='objectList.php?c=".$class."'>"._LS('scaffoldingBackToList')."</a>]".
	"</p>");

$p->a("<p>"._LH('scaffoldingColumnVisibilityExplain')."</p>");

$obj=new $class();
$allAttrs=$obj->sGetAllAttributes();
$visAttrs=$obj->sGetVisibleAttributes();
$defAttrs=$obj->sGetDefaultVisibleAttributes();

if (!empty($_POST['diff'])) {
	$diff=$_POST['diff'];
	if (empty($_POST['visi'][$diff]))
		$new=false;
	else
		$new=true;
	$sv=new LPC_Scaffold_fld_visi();
	if ($new) {
		if (in_array($diff, $defAttrs))
			$sv->removeForcedVisi($obj, $diff);
		else
			$sv->addForcedVisi($obj, $diff, LPC_Scaffold_fld_visi::MOD_SHOW);
	} else {
		if (in_array($diff, $defAttrs))
			$sv->addForcedVisi($obj, $diff, LPC_Scaffold_fld_visi::MOD_HIDE);
		else
			$sv->removeForcedVisi($obj, $diff);
	}
	header("Location: ".$_SERVER['PHP_SELF']."?c=".get_class($obj));
	exit;
}

$f=new LPC_HTML_form();
$p->a($f);
$f->a("<input type='hidden' name='c' value=\"".htmlspecialchars($_REQUEST['c'])."\">");
$f->a("<input type='hidden' name='diff' id='diff' value=''>");

foreach($allAttrs as $idx=>$attr) {
	$div=new LPC_HTML_node();
	$f->a($div);
	$checked="";
	if (in_array($attr, $visAttrs))
		$checked="checked";
	$style="";
	if (in_array($attr, $defAttrs))
		$style="font-weight: bold";
	$div->a(
		"<input type='checkbox' name=\"visi[".htmlspecialchars($attr)."]\" value='1' id='att_$idx' ".$checked." onClick=\"document.getElementById('diff').value='".$attr."'; this.form.submit()\"> ".
		"<label for='att_$idx' style='$style'>".htmlspecialchars($attr)."</label>"
	);
}

