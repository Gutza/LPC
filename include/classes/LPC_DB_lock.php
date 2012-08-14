<?php

class LPC_DB_lock
{
	var $dbHandler;

	const DEBUG_TRUE="success";
	const DEBUG_FALSE="failure";
	const DEBUG_NULL="error";
	const DEBUG_UNKNOWN="unknown result";

	/**
	* Constructor. Takes a LPC_Object descendant for the database connection.
	*/
	public function __construct($obj)
	{
		switch($obj->db->databaseType) {
			case "mysqli":
			case "mysql":
				$dbHandlerClass="LPC_DB_lock_mysql";
				break;
			default:
				throw new RuntimeException("Unknown database type: ".$this->db_obj->db->databaseType);
		}
		$this->dbHandler=new $dbHandlerClass($obj);
	}

	/**
	* Locks a key. Blocking. Assume that new locks release old locks.
	*
	* @param string $key the lock key
	* @param int $timeout timeout in seconds
	* @return mixed true on success, false on failure, NULL on error
	*/
	public function lock($key, $timeout=PHP_INT_MAX)
	{
		return $this->dbHandler->lock($key, $timeout);
	}

	/**
	* Releases a lock.
	*
	* @param string $key the lock key
	* @return mixed true on success, false on failure, NULL on error
	*/
	public function unlock($key)
	{
		return $this->dbHandler->unlock($key);
	}

	/**
	* Checks whether a key is locked.
	*
	* @param string $key the lock key
	* @return mixed true on success, false on failure, NULL on error
	*/
	public function isLocked($key)
	{
		return $this->dbHandler->isLocked($key);
	}

	/**
	* Converts the result of all methods in this class to a human-readable string.
	* Useful for debugging.
	*
	* @param mixed $result the return value of any of the methods in this class
	* @return string a human readable string representing the result
	*/
	static public function debug_result($result)
	{
		if ($result===true)
			return self::DEBUG_TRUE;
		elseif ($result===false)
			return self::DEBUG_FALSE;
		elseif ($result===NULL)
			return self::DEBUG_NULL;
		else
			return self::DEBUG_UNKNOWN;
	}
}
