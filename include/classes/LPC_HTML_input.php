<?php

class LPC_HTML_input extends LPC_HTML_uid
{
	var $nodeName='input';

	function __construct($type=NULL,$name=NULL,$value=NULL)
	{
		if (!empty($type))
			$this->setAttr('type',$type);
		if (!empty($name))
			$this->setAttr('name',$name);
		if (is_array($value)) {
			if (isset($value[$name]))
				$this->setAttr('value',$value[$name]);
		} elseif (!empty($value))
			$this->setAttr('value',$value);
	}
}
