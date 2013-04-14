<?php

class LPC_HTML_warning extends LPC_HTML_node
{
	function __construct($message)
	{
		parent::__construct("div");
		$this->setClass('warning_message');
		$this->a($message);
	}
}
