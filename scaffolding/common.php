<?php

$u=LPC_User::getCurrent();

if (isset($_GET['langID'])) {
	$lang=new LPC_Language();
	$lang->fromKey($_GET,'langID');
	if ($lang->id) {
		LPC_Language::setCurrent($lang);
		header("Location: ".LPC_Url::remove_get_var($_SERVER['REQUEST_URI'],'langID'));
		exit;
	}
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
$langs->setAttr('onChange',"window.location=location.pathname+location.search+(location.search?'&':'?')+'langID='+this.options[this.selectedIndex].value;");
$langs->selected=LPC_Language::getCurrent()->id;
LPC_Page::getCurrent()->a($langSelect);

function exposeDirClasses($dir)
{
	$result=array(
		'name'=>array(),
		'formal'=>array(),
	);
	$d = dir($dir);
	while (false !== ($entry = $d->read())) {
		$fname=$dir."/".$entry;
		$class=substr($entry,0,-4);

		// First skip filesystem entries and known auxiliary classes
		if (!validClassFile($fname) || !validClassName($class))
			continue;

		// Second, skip abstract classes
		$cr=new ReflectionClass($class);
		if ($cr->isAbstract())
			continue;

		// Next, skip classes you don't have the right to
		if (!validateClassRights($class))
			continue;

		// Finally, skip internationalization children
		$instance=new $class();
		if (isset($instance->user_fields['i18n_parent']))
			continue;

		// Record everything else for display
		$result['name'][]=$class;
		$result['formal'][]=getFormalName($class);
	}
	$d->close();
	return $result;
}

function validClassFile($fname)
{
	return
		!is_dir($fname) &&
		substr($fname,-4)=='.php'
	;
}

function validClassName($class)
{
	static $LPC_classes=array(
		'LPC_Language',
	);
	if (in_array($class,$LPC_classes))
		return true;

	return
		substr($class,0,4)!='LPC_' &&
		class_exists($class) &&
		is_subclass_of($class,'LPC_object') &&
		get_parent_class($class)!='LPC_Object'
	;
}

function validateClassRights($class)
{
	$obj=new $class();
	$yes=$obj->hasScaffoldingRight('R');
	return $yes;
}

function getFormalName($class)
{
	if (!isset($class::$formalName))
		return $class;
	return $class::$formalName;
}

