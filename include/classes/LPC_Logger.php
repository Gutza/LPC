<?php

class LPC_Logger extends LPC_Base
{
	function registerDataStructure()
	{
	}

	function doLog($type, $obj)
	{
		$attrs=serialize($obj->attr);
		$this->query("
			INSERT INTO LPC_log
				(entry_date, entry_type, log_class, log_id, entry_attrs)
				VALUES (?, ?, ?, ?, ?)
		",array(
			date('Y-m-d H:i:s'),
			$type,
			get_class($obj),
			$obj->id,
			$attrs,
		));
	}
}
