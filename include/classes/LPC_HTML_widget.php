<?php

abstract class LPC_HTML_widget extends LPC_HTML_node
{

	protected $unconditional_preparation=false;

	abstract public function prepare();

	public function render()
	{
		if (!$this->content || $this->unconditional_preparation)
			$this->prepare();
		return parent::render();
	}

}
