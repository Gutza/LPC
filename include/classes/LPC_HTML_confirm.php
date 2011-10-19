<?php

class LPC_HTML_confirm extends LPC_HTML_node
{
	function __construct($message)
	{
		parent::__construct("div");
		$this->setAttr('class','confirmation_message');
		$this->a($message);
	}
}
