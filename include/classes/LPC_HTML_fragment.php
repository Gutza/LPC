<?php

class LPC_HTML_fragment extends LPC_HTML_base
{

	function render()
	{
		$this->indentCount--;
		parent::render();
		return $this->renderItem($this->content);
	}

}
