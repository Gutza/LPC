<?php

class LPC_Cache_none extends LPC_Cache_base
{
	// Get, set and delete in the global cache
	public function getG($name)
	{
		return false;
	}

	public function setG($name,$value,$flags=NULL,$expiration=NULL)
	{
		return true;
	}

	public function deleteG($name,$expiration=NULL)
	{
		return true;
	}

	// Get, set and delete in this user's global cache
	public function getU($name,$userID=0)
	{
		return false;
	}

	public function setU($name,$value,$userID=0,$flags=NULL,$expiration=NULL)
	{
		return true;
	}

	public function deleteU($name,$userID=0,$expiration=NULL)
	{
		return true;
	}

	// Get, set and delete in this project's global cache
	public function getP($name,$projectID=0)
	{
		return false;
	}

	public function setP($name,$value,$projectID=0,$flags=NULL,$expiration=NULL)
	{
		return true;
	}

	public function deleteP($name,$projectID=0,$expiration=NULL)
	{
		return true;
	}

	// Get, set and delete in this user's cache for this project
	public function getUP($name,$userID=0,$projectID=0)
	{
		return false;
	}

	public function setUP($name,$value,$userID=0,$projectID=0,$flags=NULL,$expiration=NULL)
	{
		return true;
	}

	public function deleteUP($name,$userID=0,$projectID=0,$expiration=NULL)
	{
		return true;
	}

}
