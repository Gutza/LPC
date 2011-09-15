<?php

abstract class LPC_Cache_global extends LPC_Cache_base implements iLPC_Cache
{

	// USER ---------------------------------------------------------------
	private function nameU($name,$userID)
	{
		if ($userID)
			return $name.'.u'.$userID;

		$u=LPC_User::getCurrent();
		return $name.'.u'.$u->id;
	}

	public function getU($name,$userID=0)
	{
		return $this->getG(self::nameU($name,$userID));
	}

	public function setU($name,$value,$userID=0,$flags=NULL,$expiration=NULL)
	{
		return $this->setG(self::nameU($name,$userID),$value,$flags,$expiration);
	}

	public function deleteU($name,$userID=0,$expiration=NULL)
	{
		return $this->deleteG(self::nameU($name,$userID),$expiration);
	}

	// PROJECT ------------------------------------------------------------
	private function nameP($name,$projectID)
	{
		if ($projectID)
			return $name.'.p'.$projectID;

		$p=LPC_Project::getCurrent();
		return $name.'.p'.$p->id;
	}

	public function getP($name,$projectID=0)
	{
		return $this->getG(self::nameP($name,$projectID));
	}

	public function setP($name,$value,$projectID=0,$flags=NULL,$expiration=NULL)
	{
		return $this->setG(self::nameP($name,$projectID),$value,$flags,$expiration);
	}

	public function deleteP($name,$projectID=0,$expiration=NULL)
	{
		return $this->deleteG(self::nameP($name,$projectID),$expiration);
	}

	// USER + PROJECT -----------------------------------------------------
	private function nameUP($name,$userID,$projectID)
	{
		if (!$userID) {
			$u=LPC_User::getCurrent();
			$userID=$u->id;
		}
		if (!$projectID) {
			$p=LPC_Project::getCurrent();
			$projectID=$p->id;
		}
		return $name.'.u'.$userID.'.p'.$projectID;
	}

	public function getUP($name,$userID=0,$projectID=0)
	{
		return $this->getG(self::nameUP($name,$userID,$projectID));
	}

	public function setUP($name,$value,$userID=0,$projectID=0,$flags=NULL,$expiration=NULL)
	{
		return $this->setG(self::nameUP($name,$userID,$projectID),$value,$flags,$expiration);
	}

	public function deleteUP($name,$userID=0,$projectID=0,$expiration=NULL)
	{
		return $this->deleteG(self::nameUP($name,$userID,$projectID),$expiration);
	}

}
