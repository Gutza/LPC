<?php

// Only for Bootstrap
class LPC_HTML_BS_glyph extends LPC_HTML_node
{
	function __construct($glyph)
	{
		parent::__construct("span");
		$this->setClass("glyphicon glyphicon-".$glyph);
	}
}
