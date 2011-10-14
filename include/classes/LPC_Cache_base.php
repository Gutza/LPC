<?php

abstract class LPC_Cache_base implements iLPC_Cache
{

	/*
	* Unique name -- provides a proper unique name for this installation
	*/
	final function un($name)
	{
		return LPC_INSTALLATION_KEY.'.'.$name;
	}

	/*
	* Flexible getUP(). Calls getUP(), getU(), getP() or getG(),
	* depending on which parameters are non-zero.
	*/
	final function getUPf($name,$userID=0,$projectID=0)
	{
		if ($userID && $projectID)
			return $this->getUP($name,$userID,$projectID);
		elseif ($userID)
			return $this->getU($name,$userID);
		elseif ($projectID)
			return $this->getP($name,$projectID);
		else
			return $this->getG($name);
	}

	/*
	* Flexible setUP(). Calls setUP(), setU(), setP() or setG(),
	* depending on which parameters are non-zero.
	*/
	final function setUPf($name,$value,$userID=0,$projectID=0,$flags=NULL,$expiration=NULL)
	{
		if ($userID && $projectID)
			return $this->setUP($name,$value,$userID,$projectID,$flags,$expiration);
		elseif ($userID)
			return $this->setU($name,$value,$userID,$flags,$expiration);
		elseif ($projectID)
			return $this->setP($name,$value,$projectID,$flags,$expiration);
		else
			return $this->setG($name,$value,$flags,$expiration);
	}

}
