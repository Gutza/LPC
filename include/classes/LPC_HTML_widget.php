<?php

abstract class LPC_HTML_widget extends LPC_HTML_node implements iLPC_HTML_widget
{

	protected $unconditional_preparation=false;
	private $pre_prepared=false;

	public function render()
	{
		if (!$this->content || $this->unconditional_preparation)
			$this->prepare();
		return parent::render();
	}

	public function pre_prepare()
	{
		if ($this->pre_prepared)
			return;
		$this->prepare();
		$this->pre_prepared=true;
	}

}
