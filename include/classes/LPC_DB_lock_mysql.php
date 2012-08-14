<?php

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
