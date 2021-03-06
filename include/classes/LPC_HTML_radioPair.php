<?php

class LPC_HTML_radioPair extends LPC_HTML_node
{
	var $compact=true;
	private $radioID;

	/**
	* Constructor.
	*
	* @param string name the name of the radio input
	* @param string label the content of the label tag
	* @param string value the value of the radio input
	* @param mixed $checked boolean to indicate explicitly, array to auto-match (think $_POST)
	*/
	function __construct($name,$label,$value,$checked=false)
	{
		$this->radioID='LPC'.self::getUID();
		$checkedH='';
		if (is_bool($checked)) {
			if ($checked)
				$checkedH=' checked';
		} elseif (is_array($checked)) {
			if (isset($checked[$name]) && $checked[$name]==$value)
				$checkedH=' checked';
		}
		$this->a(
			"<input type='radio' name='$name' value=\"".htmlspecialchars($value)."\" id='".$this->radioID."'".$checkedH.">".
			"&nbsp;".
			"<label for='".$this->radioID."'>".$label."</label>"
		);
	}

	function getRadioID()
	{
		return $this->radioID;
	}
}
