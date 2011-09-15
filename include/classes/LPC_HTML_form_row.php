<?php

class LPC_HTML_form_row extends LPC_HTML_widget
{

	var $rowData=array();

	function __construct($rowData=NULL)
	{
		parent::__construct('tr');
		if ($rowData)
			$this->rowData=$rowData;
	}

	function prepare()
	{
		$rd=&$this->rowData;
		$th=new LPC_HTML_node('th');
		$th->setAttr('style','width:50%');
		$this->a($th);
		$th->a($rd['label']);

		$td=new LPC_HTML_node('td');
		$this->a($td);
		$td->a($rd['input']);
	}

}
