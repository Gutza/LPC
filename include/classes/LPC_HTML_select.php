<?php

class LPC_HTML_select extends LPC_HTML_widget
{

	var $nodeName='select';

	// The value of the selected item
	var $selected="";

	// What to select if $selected isn't set.
	var $default="";

	var $unconditional_preparation=true;

	function __construct($name=NULL,$key=NULL,$array=NULL)
	{
		if ($name===NULL)
			return;

		$this->setAttr('name',$name);

		if ($key===NULL)
			return;
		if ($key===true)
			$key=$name;

		if ($array===NULL)
			$array=&$_REQUEST;
		$this->fromKey($array,$key);
	}

	function addOption($label,$value="",$selected=false)
	{
		$o=new LPC_HTML_node('option');
		$o->setAttr('value',$value);
		$o->content=$label;
		$this->a($o);

		if ($selected)
			$this->selected=$value;
	}

	function prepare()
	{
		if (!strlen($this->selected.$this->default))
			return;

		if ($this->selected)
			$selected=$this->selected;
		else
			$selected=$this->default;

		foreach($this->content as $option) {
			if ($option->getAttr('value')!=$selected)
				continue;

			$option->setAttr('selected','1');
			break;
		}
	}

	function fromKey($array,$key)
	{
		if (!isset($array[$key]))
			return $this->selected;
		return $this->selected=$array[$key];
	}
}
