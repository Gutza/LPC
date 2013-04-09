<?php

class LPC_HTML_anchor extends LPC_HTML_node
{
	var $nodeName='a';

	function __construct($href, $content, $attrs=array())
	{
		parent::__construct();
		$this->content=$content;
		$this->setAttr('href', $href);
		$this->setAttrs($attrs);
	}
}
