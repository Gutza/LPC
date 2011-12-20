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
	function prepare()
	{
		if (isset($_REQUEST[$this->GET_key]))
			$default=addslashes($_REQUEST[$this->GET_key]);
		else
			$default="";

		$form=new LPC_HTML_form(false,'get');
		$this->a($form);

		$this->ownerDocument->content['head']->content['LPC list filter CSS']=new LPC_HTML_link('stylesheet','text/css',LPC_css."/LPC_list_filter.css");

		foreach($_GET as $key=>$value) {
			if ($key==$this->GET_key)
				continue;
			$form->a("<input type='hidden' name='$key' value=\"".addslashes($value)."\">");
		}
		$form->a("<table class='table_filter'><tr><td class='table_filter' style='width:100%'>");
		$form->a("<input type='hidden' name='".$this->listObject->getParam('p')."' value='1'>");
		$form->a("<input type='text' name='".$this->GET_key."' value=\"".$default."\" style='width:100%'>");
		$form->a("</td><td class='table_filter'>");
		$form->a("<input type='image' src='".LPC_ICON_MAGNIFIER."' alt='Filter'>");
		$form->a("</td></tr></table>");
	}

	function getSQL()
	{
		if (!isset($_REQUEST[$this->GET_key]) || !strlen($_REQUEST[$this->GET_key]))
			return NULL;
		$filterVal=$this->listObject->queryObject->db->qstr('%'.$_REQUEST[$this->GET_key].'%');
		if (isset($this->SQL_key))
			$key=$this->SQL_key;
		else
			$key=$this->list_key;
		return $key." LIKE ".$filterVal;
	}
}
