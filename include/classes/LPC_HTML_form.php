<?php

class LPC_HTML_form extends LPC_HTML_node
{
	var $nodeName='form';

	function __construct($action=false,$method='post',$files=false)
	{
		if ($action===false)
			$action=$_SERVER['PHP_SELF'];
		$this->setAttr('action',$action);
		$this->setAttr('method',$method);
		if ($files)
			$this->setAttr('enctype','multipart/form-data');
		else
			$this->setAttr('enctype','application/x-www-form-urlencoded');
	}
}
