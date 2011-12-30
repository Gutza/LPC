<?php

$u=LPC_User::getCurrent();

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
		if (!validClassFile($fname) || !validClassName($class) || !validateClassRights($class))
			continue;
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

