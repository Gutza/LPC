<?php

class LPC_HTML_listBS extends LPC_HTML_list
{
	var $tableClass = "table table-hover";

	function __construct()
	{
		parent::__construct();
		$this->addClass("container");
	}

	function getIcon($order)
	{
		$icon = new LPC_HTML_node("span");
		$icon->setClass("glyphicon")->setAttr("style", "margin-left: 3px");
		if ($order == "up")
			$icon->addClass("glyphicon-chevron-up");
		else
			$icon->addClass("glyphicon-chevron-down");

		return $icon;
	}
}
