<?php

require "common.php";

$p=LPC_Page::getCurrent();
$p->title=_LS('scaffoldingTitle');
$p->st();

$p->a("<div>"._LH('scaffoldingMsgAvailableClasses')."</div>");
$list=new LPC_HTML_node('ul');
$p->a($list);

$classes=exposeDirClasses(LPC_classes);
foreach($LPC_extra_class_dirs as $dir)
	$classes=array_merge($classes,exposeDirClasses($dir));

array_multisort(
	$classes['formal'],SORT_ASC, SORT_STRING,
	$classes['name'],SORT_ASC, SORT_STRING
);
foreach($classes['name'] as $idx=>$className) {
	$classFormal=$classes['formal'][$idx];
	$xtra="";
	if (isset($className::$formalDesc))
		$xtra=" &mdash; <i>".$className::$formalDesc."</i>";
	$list->a("<li><a href='objectList.php?c=".rawurlencode($className)."'>$classFormal</a>$xtra</li>");
}
