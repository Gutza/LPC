<?php

/**
 * The boolean list filter.
 * @author Bogdan Stancescu <bogdan@moongate.ro>
 * @copyright Copyright (c) April 2012, Bogdan Stancescu
 * @license http://www.gnu.org/licenses/gpl.html GNU Public License v3 or later
 * @version $Id$
 */
class LPC_HTML_list_filter_boolean extends LPC_HTML_list_filter
{
	protected $helpKey="lpcListFilterBooleanHelp";

	function prepare()
	{
		parent::prepare();
		$form=$this->searchForm;

		$default=addslashes($this->getCurrentValue());

		$formContent=$form->content['filterTable']->content['filterTR']->content['filterTD'];
		$formContent->a($this->getCheckbox(true));
		$formContent->a($this->getCheckbox(false));
	}

	function getCheckbox($bool)
	{
		if ($bool) {
			$labelKey="scaffoldingBooleanYes";
			$idTag="yes";
		} else {
			$labelKey="scaffoldingBooleanNo";
			$idTag="no";
		}
		$id=$this->GET_key."_".$idTag;
		$name=$this->GET_key."[".((int) $bool)."]";
		$checked=$_GET[$this->GET_key][$bool];
		if ($checked)
			$checked=" checked";
		else
			$checked="";
		return "<div style='min-width: 50px'><input type='checkbox' name='".$name."' $checked value='1' id='".$id."'><label for='".$id."'>"._LH($labelKey)."</label></div>";
	}

	function getSQL()
	{
		if (empty($_GET[$this->GET_key]))
			return NULL;
		if (isset($this->SQL_key))
			$key=$this->SQL_key;
		else
			$key=$this->list_key;
		$yes=isset($_GET[$this->GET_key][1]);
		$no= isset($_GET[$this->GET_key][0]);
		if ($yes==$no)
			return NULL;
		return $key."=".((int) (bool) $yes);
	}
}
