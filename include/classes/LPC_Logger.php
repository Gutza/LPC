<?php

class LPC_Logger extends LPC_Base
{
	function registerDataStructure()
	{
	}

	function doLog($type, $obj)
	{
		$userID=0;
		if (LPC_User::configuredForUsers()) {
			$u=LPC_User::getCurrent(true);
			if ($u)
				$userID=$u->id;
		}

		if ($obj->logTrace)
			$trace=self::getTrace();
		else
			$trace=NULL;

		if (is_string($obj->logReason))
			$reason=$obj->logReason;
		else
			$reason=NULL;

		$attrs=serialize($obj->attr);
		$this->query("
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

	function getTrace()
	{
		$trace=debug_backtrace();
		$sani=array(); // sanitized trace
		foreach($trace as $idx=>$entry) {
			if ($idx<2)
				continue; // Skip LPC_Logger internal calls
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
		return serialize($sani);
	}
}
