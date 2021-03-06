<?php

/**
 * The string list filter.
 * @author Bogdan Stancescu <bogdan@moongate.ro>
 * @copyright Copyright (c) September 2011, Bogdan Stancescu
 * @license http://www.gnu.org/licenses/gpl.html GNU Public License v3 or later
 * @version $Id$
 */
class LPC_HTML_list_filter_string extends LPC_HTML_list_filter
{
	var $input_size=20;

	protected $helpKey="lpcListFilterStringHelp";

	function prepare()
	{
		parent::prepare();
		$form=$this->searchForm;

		$default=addslashes($this->getCurrentValue());

		$formContent=$form->content['filterTable']->content['filterTR']->content['filterTD'];
		$formContent->a("<input type='text' name='".$this->GET_key."' value=\"".$default."\" size='".$this->input_size."'>");
	}

	function getSQL()
	{
		if (!isset($_REQUEST[$this->GET_key]) || !strlen($_REQUEST[$this->GET_key]))
			return NULL;

		$userVal = $this->preprocess_value($_REQUEST[$this->GET_key]);

		$filterVal=$this->listObject->queryObject->db->qstr('%'.$userVal.'%');
		if (isset($this->SQL_key))
			$key=$this->SQL_key;
		else
			$key=$this->list_key;
		return $key." LIKE ".$filterVal;
	}

	// Override this if you need to preprocess the values entered by the user
	// before injecting them into the SQL query.
	function preprocess_value($val)
	{
		return $val;
	}
}
