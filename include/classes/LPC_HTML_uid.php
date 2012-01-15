<?php

class LPC_HTML_uid extends LPC_HTML_widget
{
	function prepare()
	{
		if (empty($this->id))
			$this->setUID();
	}
}
