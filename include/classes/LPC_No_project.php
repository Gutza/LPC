<?php

class LPC_No_project
{
	var $id=0;
	var $noName="No project";

	function getAttr($var)
	{
		return $this->noName;
	}

	function getAttrH($var)
	{
		return htmlspecialchars($this->getAttr('var'));
	}
}
