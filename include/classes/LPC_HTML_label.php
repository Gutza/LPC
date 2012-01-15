<?php

class LPC_HTML_label extends LPC_HTML_widget
{
	var $nodeName='label';
	var $for=NULL;

	function __construct($for=NULL,$label=NULL)
	{
		if (!empty($for))
			$this->for=$for;
		if (!empty($label))
			$this->a($label);
	}

	function prepare()
	{
		if (!$this->for)
			return;
		if (is_string($this->for)) {
			$this->setAttr('for',$this->for);
			return;
		}
		if (is_object($this->for)) {
			if ($this->for instanceof LPC_HTML_widget)
				$this->for->pre_prepare();
			if (!empty($this->for->id))
				$this->setAttr('for',$this->for->id);
		}
	}

}
