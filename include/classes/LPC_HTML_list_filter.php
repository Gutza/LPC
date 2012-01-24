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

	/*
	Notes:
	* We DO NOT have $this->parentNode and $this->ownerDocument set in getSQL()
	* We DO have $this->parentNode and $this->ownerDocument set in prepare()
	*/
	abstract public function getSQL();


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
