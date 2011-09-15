<?php

function LPC_autoloader($class_name)
{
	$fname=LPC_classes.'/'.$class_name.'.php';
	if (!file_exists($fname))
		return false;

	include $fname;
	return true;
}

spl_autoload_register('LPC_autoloader',false);

