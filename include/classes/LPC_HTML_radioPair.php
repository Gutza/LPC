<?php

class LPC_HTML_radioPair extends LPC_HTML_node
{
	var $compact=true;

	function __construct($name,$label,$value,$checked=false)
	{
		$id='LPC'.self::getUID();
		$checkedH='';
		if ($checked)
			$checkedH=' checked';
		$this->a(
			"<input type='radio' name='$name' value=\"".htmlspecialchars($value)."\" id='".$id."'".$checkedH.">".
			"&nbsp;".
			"<label for='".$id."'>".$label."</label>"
		);
	}
}
