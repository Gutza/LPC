<?php

class LPC_HTML_error extends LPC_HTML_node
{
	function __construct($message)
	{
		parent::__construct("div");
		$this->setAttr('class','error_message');
		$this->a($message);
	}
}
