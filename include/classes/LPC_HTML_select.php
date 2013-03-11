<?php

class LPC_HTML_select extends LPC_HTML_widget
{

	var $nodeName='select';

	// The value of the selected item
	var $selected="";

	// What to select if $selected isn't set.
	var $default="";

	var $unconditional_preparation=true;

	/**
	* Constructor.
	*
	* @param string $name the name of the HTML <select> node
	* @param mixed $key the key in $array which contains the current value of this <select>;
	*	provide boolean true if you want to use $name
	* @param array $array the array which contains the value of this <select>;
	*	if not specified, $_REQUEST is used.
	*/
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

	function addOption($label,$value=NULL,$selected=false)
	{
		$o=$this->generateOption($label, $value);
		$this->a($o);

		if ($selected)
			$this->selected=$value;
	}

	function generateOption($label, $value=NULL)
	{
		$o=new LPC_HTML_node('option');
		if ($value===NULL)
			$value=$label;
		$o->setAttr('value',$value);
		$o->content=$label;
		return $o;
	}

	function removeOption($value)
	{
		foreach($this->content as $key=>$option) {
			if ($option->getAttr('value')==$value) {
				unset($this->content[$key]);
				return true;
			}
		}
		return false;
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

			$option->setAttr('selected','selected');
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
