<?php

require "common.php";

$p=LPC_Page::getCurrent();
$p->title=_LS('scaffoldingTitle');
$p->st();

$p->a(
	"<p>".
		"[<a href='index.php'>"._LS('scaffoldingSwitchClass')."</a>] &bull; ".
		"[<a href='changeVisi.php?c=".rawurlencode($_GET['c'])."'>"._LS('scaffoldingColumnVisibilityLink')."</a>]".
	"</p>"
);

if (empty($_GET['c'])) {
	$p->a(new LPC_HTML_error(_LH('scaffoldingErrorNeedClass')));
	return;
}

$class=$_GET['c'];
if (!validClassName($class) || !validateClassRights($class)) {
	$p->a(new LPC_HTML_error(_LH('genericErrorRights')));
	return;
}

$editDiv=false;
$obj=new $class();
if ($obj->hasScaffoldingRight('W')) {
	$editDiv=new LPC_HTML_node('p');
	$editDiv->a("[<a href='objectEdit.php?c=$class'>"._LS('scaffoldingCreateObject',$class)."</a>]");
	$p->a($editDiv);
}

$refdata=array('rd','rc','rid');
$rd=NULL;
foreach($refdata as $refatom) {
	if (isset($_POST['LPC_scaffolding_'.$refatom]))
		$$refatom=$_POST['LPC_scaffolding_'.$refatom];
	elseif (isset($_GET[$refatom]))
		$$refatom=$_GET[$refatom];
}
$query=$rObj=NULL;
if (strlen($rd)) {
	$rObj=new $rc($rid);
	if (!$rObj->probe()) {
		$p-a(new LPC_HTML_error(_LH('scaffoldErrorMissingRemote')));
		return;
	}
	$editDiv->a(" &bull; [<a href='objectEdit.php?c=".$_GET['rc']."&amp;id=".$_GET['rid']."'>"._LS('scaffoldingBackToParent',htmlspecialchars($_GET['rc']),htmlspecialchars($_GET['rid']))."</a>]");
	$query=$rObj->_makeGetLinksQuery($rd);
}
$p->a($obj->getScaffoldingList($query, $rObj, $rd));

if ($editDiv)
	$p->a($editDiv);

