<?php

class LPC_Cache_database extends LPC_Cache_base
{
	public $lastDate; // specific to LPC_Cache_database, this is the date of the last value retrieved from the database

	public function __construct()
	{
	}

	public function registerDataStructure()
	{
	}

	// GLOBAL -------------------------------------------------------------
	public function getG($name)
	{
		return self::getDB($name,0,0);
	}

	public function setG($name,$value,$flags=NULL,$expiration=NULL)
	{
		return self::setDB($name,$value,0,0,$flags,$expiration);
	}

	public function deleteG($name,$expiration=NULL)
	{
		return self::deleteDB($name,0,0,$expiration);
	}

	// USER ---------------------------------------------------------------
	private function uid($userID)
	{
		if ($userID)
			return $userID;
		return LPC_User::getCurrent()->id;
	}

	public function getU($name,$userID=0)
	{
		$userID=self::uid($userID);
		return self::getDB($name,$userID,0);
	}

	public function setU($name,$value,$userID=0,$flags=NULL,$expiration=NULL)
	{
		$userID=self::uid($userID);
		return self::setDB($name,$value,$userID,0,$flags,$expiration);
	}

	public function deleteU($name,$userID=0,$expiration=NULL)
	{
		$userID=self::uid($userID);
		return self::deleteDB($name,$userID,0,$expiration);
	}

	// PROJECT ------------------------------------------------------------
	private function pid($projectID)
	{
		if ($projectID)
			return $projectID;
		return LPC_Project::getCurrent()->id;
	}

	public function getP($name,$projectID=0)
	{
		$projectID=self::pid($projectID);
		return self::getDB($name,0,$projectID);
	}

	public function setP($name,$value,$projectID=0,$flags=NULL,$expiration=NULL)
	{
		$projectID=self::pid($projectID);
		return self::setDB($name,$value,0,$projectID,$flags,$expiration);
	}

	public function deleteP($name,$projectID=0,$expiration=NULL)
	{
		$projectID=self::pid($projectID);
		return self::deleteDB($name,0,$projectID,$expiration);
	}

	// USER + PROJECT -----------------------------------------------------
	public function getUP($name,$userID=0,$projectID=0)
	{
		$userID=self::uid($userID);
		$projectID=self::pid($projectID);
		return self::getDB($name,$userID,$projectID);
	}

	public function setUP($name,$value,$userID=0,$projectID=0,$flags=NULL,$expiration=NULL)
	{
		$userID=self::uid($userID);
		$projectID=self::pid($projectID);
		return self::setDB($name,$value,$userID,$projectID,$flags,$expiration);
	}

	public function deleteUP($name,$userID=0,$projectID=0,$expiration=NULL)
	{
		$userID=self::uid($userID);
		$projectID=self::pid($projectID);
		return self::deleteDB($name,$userID,$projectID,$expiration);
	}

	// DATABASE LAYER -----------------------------------------------------
	private function getDB($name,$userID,$projectID)
	{
		$rs=$this->query("
			SELECT
				UNIX_TIMESTAMP(date_set) AS date_set,
				value
			FROM LPC_cache
			WHERE
				user=? AND
				project=? AND
				name=?
		",array($userID,$projectID,$name));

		if ($rs->EOF)
			return false;

		$this->lastDate=$rs->fields['date_set'];
		return unserialize($rs->fields['value']);
	}

	private function setDB($name,$value,$userID,$projectID,$flags,$expiration)
	{
		self::deleteDB($name,$userID,$projectID,$expiration);
		return $this->query("
			INSERT INTO LPC_cache
			(user, project, name, date_set, value)
			VALUES (?, ?, ?, NOW(), ?)
		",array($userID,$projectID,$name,serialize($value)));
	}

	private function deleteDB($name,$userID,$projectID,$expiration)
	{
		return $this->query("
			DELETE FROM LPC_cache
			WHERE
				user=? AND
				project=? AND
				name=?
		",array($userID,$projectID,$name));
	}

}
