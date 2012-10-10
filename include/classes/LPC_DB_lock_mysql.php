<?php

/**
 * Database lock manager -- MySQL implementation.
 * @author Bogdan Stancescu <bogdan@moongate.ro>
 * @copyright Copyright (c) August 2012, Bogdan Stancescu
 * @license http://www.gnu.org/licenses/gpl.html GNU Public License v3 or later
 */
class LPC_DB_lock_mysql implements iLPC_DB_lock_handler
{
	var $dbObj;

	function __construct($obj)
	{
		$this->dbObj=$obj;
	}

	function uniformize($sql, $params)
	{
		$rs=$this->dbObj->query($sql, $params);
		if (is_null($rs->fields[0]))
			return NULL;
		return (bool) $rs->fields[0];
	}

	function lock($key, $timeout)
	{
		return $this->uniformize("SELECT GET_LOCK(?, ?)", array($key, $timeout));
	}

	function unlock($key)
	{
		return $this->uniformize("SELECT RELEASE_LOCK(?)", array($key));
	}

	function isLocked($key)
	{
		return $this->uniformize("IS_FREE_LOCK(?)", array($key));
	}
}
