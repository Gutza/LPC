<?php

class LPC_Cache_memcache extends LPC_Cache_global
{

	private static $mcO; // memcache object

	public function __construct($host,$port)
	{
		if (isset(self::$mcO))
			return null;
		self::$mcO=new Memcache();
		if (!self::$mcO->connect($host,$port))
			throw new RuntimeException("Failed connecting to the memcache server.");
	}

	// GLOBAL -------------------------------------------------------------
	public function getG($name)
	{
		return self::$mcO->get($this->un($name));
	}

	public function setG($name,$value,$flags=NULL,$expiration=NULL)
	{
		$flag=NULL;
		if (is_array($flags) && in_array(self::FLAG_COMPRESSED,$flags))
			$flag=MEMCACHE_COMPRESSED;

		// Fail silently -- if the memcache server is down, we complain in the constructor
		return self::$mcO->set($this->un($name),$value,$flag,$expiration);
	}

	public function deleteG($name,$expiration=NULL)
	{
		return self::$mcO->delete($this->un($name),$expiration);
	}

}
