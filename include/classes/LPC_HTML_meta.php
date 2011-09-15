<?php

class LPC_HTML_meta extends LPC_HTML_node
{

	public $nodeName='META';
	//public $shortTag=true; // Inherited true, this is just a reminder

	function __construct($attributes,$content)
	{
		$this->attributes=$attributes;
		$this->attributes['content']=$content;
	}

}
