<?php

/**
 * The list filter parent.
 * @author Bogdan Stancescu <bogdan@moongate.ro>
 * @copyright Copyright (c) September 2011, Bogdan Stancescu
 * @license http://www.gnu.org/licenses/gpl.html GNU Public License v3 or later
 * @version $Id$
 */
abstract class LPC_HTML_list_filter extends LPC_HTML_widget
{
	public $nodeName='div';

	/**
	* The $_REQUEST key associated with this filter. Optional.
	*/
	public $GET_key;

	/**
	* The list key associated with this filter. It gets overwritten unconditionally.
	*/
	public $list_key;

	/**
	* The list object this filter is attached to. It gets overwritten unconditionally.
	*/
	public $listObject;

	/**
	* The SQL key associated with this filter. Optional; if not specified, $this->list_key is used.
	*/
	public $SQL_key;

	/**
	* An LPC_HTML_form object that represents this filter's search form.
	* Typically populated by LPC_HTML_list_filter's own {@link prepare()}.
	*/
	public $searchForm;

	/**
	* The translation key for this filter's help info. Optional.
	* @type string
	*/
	protected $helpKey;

	/**
	* The name of the JavaScript variable that contains the help text
	* for all filters. The JS var contains an object.
	*/
	const JS_translation_var="LPC_list_filter_help_text";

	/*
	Notes:
	* We DO NOT have $this->parentNode and $this->ownerDocument set in getSQL()
	* We DO have $this->parentNode and $this->ownerDocument set in prepare()
	*/
	abstract public function getSQL();

	public function prepare()
	{
		$this->ownerDocument->content['head']->content['LPC list filter CSS']=new LPC_HTML_link('stylesheet','text/css',LPC_css."/LPC_list_filter.css");

		$default=addslashes($this->getCurrentValue());

		$form=new LPC_HTML_form(false,'get');
		$this->searchForm=$form;
		$this->a($form);
		foreach($_GET as $key=>$value) {
			if ($key==$this->GET_key)
				continue;
			$form->a("<input type='hidden' name='$key' value=\"".addslashes($value)."\">");
		}

		$table=new LPC_HTML_node('table');
		$table->setAttr('class','table_filter');
		$form->a($table, 'filterTable');

		$tr=new LPC_HTML_node('tr');
		$table->a($tr, 'filterTR');

		$td=new LPC_HTML_node('td');
		$td->setAttr('class','table_filter');
		$tr->a($td, 'filterTD');
		$td->a("<input type='hidden' name='".$this->listObject->getParam('p')."' value='1'>");

		$td=new LPC_HTML_node('td');
		$td->setAttr('class','table_filter');
		$tr->a($td, 'filterControls');
		$td->a("<input type='image' src='".LPC_ICON_MAGNIFIER."' alt=\""._LS('lpcFilterIcon')."\">");
		if (strlen($default))
			$td->a("<a href='".LPC_URI::getCurrent()->delVar($this->GET_key)->toString()."'><img src='".LPC_ICON_ERASER."' alt=\""._LS('lpcRemoveFilterIcon')."\"></a>");

		if (isset($this->helpKey)) {
			$td->a("<a href='#' onClick='alert(".self::JS_translation_var.".".$this->helpKey."); return false;'><img src='".LPC_ICON_INFO."'></a>");
			if (!isset($this->ownerDocument->content['head']->content['JS_help_'.$this->helpKey])) {
				$js=new LPC_HTML_script();
				$this->ownerDocument->content['head']->content['JS_help_'.$this->helpKey]=$js;
				$js->a("
if (typeof ".self::JS_translation_var." == 'undefined')
	var ".self::JS_translation_var." = {};
".self::JS_translation_var.".".$this->helpKey."=\"".str_replace("\n","\\n",str_replace("\r","",addslashes(_LS($this->helpKey))))."\";
				");
			}
		}
	}

	public function getCurrentValue()
	{
		if (!isset($this->GET_key))
			throw new RuntimeException("You need to define the GET_key if you want to use getCurrentValue()");

		if (isset($_REQUEST[$this->GET_key]))
			return $_REQUEST[$this->GET_key];
		else
			return;
	}
}
