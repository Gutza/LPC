<?php

class LPC_HTML_form extends LPC_HTML_node
{
	var $nodeName = 'form';
	var $action = "";
	var $method = "";
	var $enctype = "";

	public function __construct($action=false, $method='post', $files=false)
	{
		parent::__construct();

		if ($action===false) 
			$action=LPC_URI::getCurrentURI();

		$this->setAttr('action', $action);
		$this->setAttr('method', $method);
		if ($files)
			$this->setAttr('enctype','multipart/form-data');
		else
			$this->setAttr('enctype','application/x-www-form-urlencoded');
	}

	/**
	* Adds the session key to this form. You can then check it with LPC_HTML_form::checkSK().
	*/
	public function addSK($name='__LPC_SK')
	{
		$this->p("<input type='hidden' name=\"".addslashes($name)."\" value='".session_id()."'>");
	}

	public static function checkSK($name='__LPC_SK')
	{
		return !empty($_REQUEST[$name]) && $_REQUEST[$name]==session_id();
	}

	public static function enforceSK($name='__LPC_SK')
	{
		if (self::checkSK($name))
			return;
		echo _LH('genericErrorSessionKey');
		exit;
	}

	public function switchSSL($enable)
	{
		$uri = new LPC_URI($this->getAttr('action'));
		$this->setAttr('action', $uri->switchSSL($enable));
	}
}
