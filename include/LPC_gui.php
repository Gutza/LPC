<?php

// This file is not used any more -- these functions should be moved in class LPC_Page

function error_box($msg)
{
	$result="";
	$result.="<div style='border:1px solid red; background-color:#ffe0e0; padding-left:20px; margin-bottom:10px'>\n";
	$result.=$msg;
	$result.="</div>\n";
	return $result;
}

function error($msg)
{
	return "<div class='error'>$msg</div>";
}

function confirm($msg)
{
	return "<div class='confirm'>$msg</div>";
}

