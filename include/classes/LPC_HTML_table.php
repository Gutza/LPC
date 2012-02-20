<?php

class LPC_HTML_table extends LPC_HTML_node
{

	var $nodeName='table';

	function __construct($debug=false)
	{
		// Most of the time, this is what we need
		$this->setClass('default');
		if (!$debug)
			$this->compact=true;
	}

	function buildFromArray($rows)
	{
		$this->content=array();
		foreach($rows as $row) {
			$tr=new LPC_HTML_node('tr');
			$this->a($tr);
			foreach($row as $cell)
				$tr->a($this->buildTD($cell));
		}
	}

	function buildTD($cell)
	{
		$meta=$cell['meta'];
		$td=new LPC_HTML_td($meta['nN']);

		if (isset($meta['value']))
			$td->a($meta['value']);
		else
			$td->a($cell['html']);

		if (isset($meta['colspan']))
			$td->setAttr('colspan',(string) $meta['colspan']);
		if (isset($meta['rowspan']))
			$td->setAttr('rowspan',(string) $meta['rowspan']);

		if (isset($meta['type']))
			$td->excel['type']=$meta['type'];

		return $td;
	}

	function appendFormRow($rowData)
	{
		return $this->afr($rowData);
	}

	function afr($rowData)
	{
		$this->addClass('two-column');
		$this->a(new LPC_HTML_form_row($rowData));
	}
}
