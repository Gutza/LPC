<?php

function LPC_autoloader($class_name)
{
	$fname=LPC_classes.'/'.$class_name.'.php';
	if (file_exists($fname)) {
		include $fname;
		return true;
	}

	global $LPC_extra_class_dirs;

	foreach($LPC_extra_class_dirs as $dir) {
		$fname=$dir.'/'.$class_name.'.php';
		if (file_exists($fname)) {
			include $fname;
			return true;
		}
	}
	return false;
}

spl_autoload_register('LPC_autoloader',false);

