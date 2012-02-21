<?php

class LPC_HTML_td extends LPC_HTML_widget
{
	var $nodeName='td';
	var $excel=array();
	var $metaKey='LPC_Excel_meta';
	var $metaClass='LPC_Excel_meta';

	// HTML or Excel
	var $renderMode='HTML';

	protected $unconditional_preparation=true;

	function __construct($header=false)
	{
		if ($header)
			$this->nodeName='th';
	}

	function prepare()
	{
		if (!count($this->excel) || $this->renderMode=='Excel') {
			if (isset($this->content[$this->metaKey]))
				unset($this->content[$this->metaKey]);
			return;
		}

		$meta=new LPC_HTML_node('span');
		$meta->setClass($this->metaClass);
		$meta->setAttr('style','display:none');
		$meta->compact=true;
		$meta->a(LPC_JSON::encode($this->excel));

		$this->content[$this->metaKey]=$meta;
	}

}
