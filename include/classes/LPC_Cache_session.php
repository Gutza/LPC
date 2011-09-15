<?php

class LPC_Cache_session implements iLPC_Cache
{

	public function __construct()
	{
		if (!isset($_SESSION['LPC']))
			$_SESSION['LPC']=array();
		if (!isset($_SESSION['LPC']['g_cache'])) {
			$_SESSION['LPC']['g_cache']=array();
			$_SESSION['LPC']['u_cache']=array();
			$_SESSION['LPC']['p_cache']=array();
			$_SESSION['LPC']['up_cache']=array();
		}
	}

	// GLOBAL -------------------------------------------------------------
	public function getG($name)
	{
		if(isset($_SESSION['LPC']['g_cache'][$name]))
			return $_SESSION['LPC']['g_cache'][$name];
		return false;
	}

	public function setG($name,$value,$flags=NULL,$expiration=NULL)
	{
		$_SESSION['LPC']['g_cache'][$name]=$value;
		return true;
	}

	public function deleteG($name,$expiration=NULL)
	{
		if(isset($_SESSION['LPC']['g_cache'][$name]))
			unset($_SESSION['LPC']['g_cache'][$name]);
		return true;
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
		$uid=self::uid($userID);
		if (
			isset($_SESSION['LPC']['u_cache'][$uid]) &&
			isset($_SESSION['LPC']['u_cache'][$uid][$name])
		)
			return $_SESSION['LPC']['u_cache'][$uid][$name];
		return false;
	}

	public function setU($name,$value,$userID=0,$flags=NULL,$expiration=NULL)
	{
		$uid=self::uid($userID);
		if (!isset($_SESSION['LPC']['u_cache'][$uid]))
			$_SESSION['LPC']['u_cache'][$uid]=array();
		$_SESSION['LPC']['u_cache'][$uid][$name]=$value;
		return true;
	}

	public function deleteU($name,$userID=0,$expiration=NULL)
	{
		$uid=self::uid($userID);
		if (
			isset($_SESSION['LPC']['u_cache'][$uid]) &&
			isset($_SESSION['LPC']['u_cache'][$uid][$name])
		)
			unset($_SESSION['LPC']['u_cache'][$uid][$name]);
		return true;
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
		$pid=self::pid($projectID);
		if (
			isset($_SESSION['LPC']['p_cache'][$pid]) &&
			isset($_SESSION['LPC']['p_cache'][$pid][$name])
		)
			return $_SESSION['LPC']['p_cache'][$pid][$name];
		return false;
	}

	public function setP($name,$value,$projectID=0,$flags=NULL,$expiration=NULL)
	{
		$pid=self::pid($projectID);
		if (!isset($_SESSION['LPC']['p_cache'][$pid]))
			$_SESSION['LPC']['p_cache'][$pid]=array();
		$_SESSION['LPC']['p_cache'][$pid][$name]=$value;
		return true;
	}

	public function deleteP($name,$projectID=0,$expiration=NULL)
	{
		$pid=self::pid($projectID);
		if (
			isset($_SESSION['LPC']['p_cache'][$pid]) &&
			isset($_SESSION['LPC']['p_cache'][$pid][$name])
		)
			unset($_SESSION['LPC']['p_cache'][$pid][$name]);
		return true;
	}

	// USER + PROJECT -----------------------------------------------------
	public function getUP($name,$userID=0,$projectID=0)
	{
		$uid=self::uid($userID);
		$pid=self::pid($projectID);
		if (
			isset($_SESSION['LPC']['up_cache'][$uid]) &&
			isset($_SESSION['LPC']['up_cache'][$uid][$pid]) &&
			isset($_SESSION['LPC']['up_cache'][$uid][$pid][$name])
		)
			return $_SESSION['LPC']['up_cache'][$uid][$pid][$name];
		return false;
	}

	public function setUP($name,$value,$userID=0,$projectID=0,$flags=NULL,$expiration=NULL)
	{
		$uid=self::uid($userID);
		$pid=self::pid($projectID);
		if (!isset($_SESSION['LPC']['up_cache'][$uid]))
			$_SESSION['LPC']['up_cache'][$uid]=array();
		if (!isset($_SESSION['LPC']['up_cache'][$uid][$pid]))
			$_SESSION['LPC']['up_cache'][$uid][$pid]=array();
		$_SESSION['LPC']['up_cache'][$uid][$pid][$name]=$value;
		return true;
	}

	public function deleteUP($name,$userID=0,$projectID=0,$expiration=NULL)
	{
		$uid=self::uid($userID);
		$pid=self::pid($projectID);
		if (
			isset($_SESSION['LPC']['up_cache'][$uid]) &&
			isset($_SESSION['LPC']['up_cache'][$uid][$pid]) &&
			isset($_SESSION['LPC']['up_cache'][$uid][$pid][$name])
		)
			unset($_SESSION['LPC']['up_cache'][$uid][$pid][$name]);
		return true;
	}

}
