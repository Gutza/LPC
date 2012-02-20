<?php

class LPC_HTML_form_row extends LPC_HTML_widget
{

	var $rowData=array();
	var $nodeName='tr';

	function __construct($rowData=NULL)
	{
		if ($rowData)
			$this->rowData=$rowData;
	}

	function prepare()
	{
		$rd=&$this->rowData;
		$th=new LPC_HTML_node('th');
		$this->a($th);
		$th->a($rd['label']);

		if (isset($rd['explain']))
			$th->a("<div class='explain'>".$rd['explain']."</div>");

		$td=new LPC_HTML_node('td');
		$this->a($td);
		$td->a($rd['input']);
	}

}
