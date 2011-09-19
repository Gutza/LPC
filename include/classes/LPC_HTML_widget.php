<?php

abstract class LPC_HTML_widget extends LPC_HTML_node
{

	protected $unconditional_preparation=false;
	private $pre_prepared=false;

	abstract public function prepare();

	public function render()
	{
		if (!$this->content || $this->unconditional_preparation)
			$this->prepare();
		return parent::render();
	}

	function pre_prepare()
	{
		if ($this->pre_prepared)
			return;
		$this->prepare();
		$this->pre_prepared=true;
	}

}
