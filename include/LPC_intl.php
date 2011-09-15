<?php

function __L($message)
{
	if (func_num_args()==1)
		return $message;
	$args=func_get_args();
	array_shift($args);
	return vsprintf($message,$args);
}
