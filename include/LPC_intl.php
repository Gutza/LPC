<?php

function __L($message)
{
	if (func_num_args()==1)
		return $message;
	$args=func_get_args();
	array_shift($args);
	return vsprintf($message,$args);
}

/**
* Translate a string that will end up as a stand-alone HTML string.
* This is actually a call to _LS(), but it may be used to display
* a link to the message itself for translators.
*
* @param string $message the ID of the message to translate
* @param string* $paramN parameters for this message
* @return string the translated message
*/
function _LH($message)
{
	return call_user_func_array("_LS",func_get_args());
}

/**
* Translate a generic string.
*
* @param string $message the ID of the message to translate
* @param string* $paramN parameters for this message
* @return string the translated message
*/
function _LS($message)
{
	if (!strlen($message))
		return "";

	$args=func_get_args();
	array_shift($args);

	$msgfmt=LPC_I18n_messageFormatter::get($message);
	if (!$msgfmt)
		return "";
	return $msgfmt->format($args);
}
