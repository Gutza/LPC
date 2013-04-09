<?php

class LPC_HTML_link extends LPC_HTML_node
{
	public $nodeName='link';
	//public $shortTag=true; inherited true

	public function __construct($rel,$type,$href)
	{
		$this->attributes=array(
			'rel'=>$rel,
			'type'=>$type,
			'href'=>$href
		);
	}
}
