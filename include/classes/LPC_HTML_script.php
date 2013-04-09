<?php

class LPC_HTML_script extends LPC_HTML_node
{
	public $nodeName="script";
	public $shortTag=false;

	public function __construct($src=NULL,$type="text/javascript")
	{
		if (isset($src))
			$this->setAttr('src',$src);
		$this->setAttr('type',$type);
	}
}
