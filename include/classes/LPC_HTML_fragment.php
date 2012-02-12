<?php

class LPC_HTML_fragment extends LPC_HTML_base
{
	public $allowIndent=false;

	function render()
	{
		if (!$this->allowIndent)
			$this->indentCount--;
		parent::render();
		return $this->renderItem($this->content);
	}

}
