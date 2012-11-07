<?php

class LPC_Logger extends LPC_Base
{
	static private $logObj;

	function __construct($id=0)
	{
		if (defined("LPC_LOGGER_DB_KEY"))
			$this->dbKey=LPC_LOGGER_DB_KEY;

		parent::__construct($id);
	}

	function registerDataStructure()
	{
		return array(
			'table_name'=>'LPC_Log',
			'id_field'=>'id',
			'fields'=>array(),
		);
	}

	static function doLog($type, $obj)
	{
		if (empty(self::$logObj))
			self::$logObj=new LPC_Logger();

		if (get_class(self::$logObj->db)=="MongoDB")
			return self::mongoLog($type, $obj);

		return self::adoLog($type, $obj);
	}

	static function getUserID()
	{
		$userID=0;
		if (LPC_User::configuredForUsers()) {
			$u=LPC_User::getCurrent(true);
			if ($u)
				$userID=$u->id;
		}

		return $userID;
	}

	static function adoLog($type, $obj)
	{
		$userID=self::getUserID();
		if ($trace=self::getTrace($obj))
			$trace=serialize($trace);

		$reason=NULL;
		if (is_string($obj->logReason))
			$reason=$obj->logReason;

		$attrs=serialize($obj->attr);


		self::$logObj->query("
			INSERT INTO LPC_log
				(entry_date, entry_type, log_class, log_id, log_user, entry_attrs, trace, reason)
				VALUES (?, ?, ?, ?, ?, ?, ?, ?)
		",array(
			date('Y-m-d H:i:s'),
			$type,
			get_class($obj),
			$obj->id,
			$userID,
			$attrs,
			$trace,
			$reason,
		));
	}

	static function mongoLog($type, $obj)
	{
		$doc=array(
			"entry_date"=>date('Y-m-d H:i:s'),
			"entry_type"=>$type,
			"log_class"=>get_class($obj),
			"log_id"=>$obj->id,
			// User -- later
			"entry_attrs"=>$obj->attr,
		);
		if ($userID=self::getUserID())
			$doc["log_user"]=$userID;
		if ($trace=self::getTrace($obj))
			$doc["trace"]=$trace;
		if (is_string($obj->logReason))
			$doc["reason"]=$obj->logReason;

		$table=self::$logObj->getTableName();

		self::$logObj->db->$table->insert($doc);
	}

	// You only need this when you fork
	function renewStatic()
	{
		self::$logObj=NULL;
	}

	static function getTrace($obj)
	{
		if (!$obj->logTrace)
			return NULL;
		
		$trace=debug_backtrace();
		$sani=array(); // sanitized trace
		foreach($trace as $idx=>$entry) {
			if ($idx<2)
				continue; // Skip LPC_Logger internal calls
			if (isset($entry['file']))
				// Where there's a file, there's a line,
				// is what my old man used to say. Good times.
				$sani1=array(
					'file'=>$entry['file'],
					'line'=>$entry['line'],
				);
			if (isset($entry['function']))
				$sani1['function']=$entry['function'];
			if (isset($entry['class']))
				$sani1['class']=$entry['class'];
			$sani[]=$sani1;
		}
		return $sani;
	}
}
