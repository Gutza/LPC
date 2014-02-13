<?php

class LPC_HTML_message extends LPC_HTML_widget
{
	protected $messageText = "";
	protected $messageClass = array(
		LPC_HTML_Document::ENV_HTML => "",
		LPC_HTML_Document::ENV_BOOTSTRAP => "",
	);
	public $nodeName = "div";

	function __construct($message)
	{
		parent::__construct();
		$this->messageText = $message;
	}

	function prepare()
	{
		$env = $this->ownerDocument->environment;

		switch($env) {
		case LPC_HTML_Document::ENV_BOOTSTRAP:
			$this->setClass("container");
			$msgDiv = new LPC_HTML_node();
			$this->a($msgDiv);
			break;
		case LPC_HTML_Document::ENV_HTML:
			$msgDiv = $this;
			break;
		default:
			throw new RuntimeException("Unknown environment!");
		}

		$msgDiv
			->setClass($this->messageClass[$env])
			->a($this->messageText);
	}
}
