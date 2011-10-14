<?php

class LPC_Cache_apc extends LPC_Cache_global
{

	function __construct()
	{
	}

	public function getG($name)
	{
		return apc_fetch($this->un($name));
	}

	public function setG($name,$value,$flags=NULL,$expiration=NULL)
	{
		if ($expiration===null)
			$expiration=0;
		elseif ($expiration>2592000)
			$expiration=$expiration-time();
		
		return apc_store($this->un($name),$value,$expiration);
	}

	// $expiration is ignored
	public function deleteG($name,$expiration=NULL)
	{
		apc_delete($this->un($name));
	}

}
