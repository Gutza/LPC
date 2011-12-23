<?php

class LPC_Session_key {
	static private $key;

	// Singleton
	private function __construct()
	{
	}

	public function get()
	{
		if (isset(self::$key))
			return self::$key;
		return self::fromSession();
	}

	private function fromSession()
	{
		if (isset($_SESSION['scaffolding_key'])) {
			self::$key=$_SESSION['scaffolding_key'];
			return self::$key;
		}
		return self::generate();
	}

	private function generate()
	{
		// The session key is not persistent, thus generated frequently and not very valuable;
		// as such, we don't want to exhaust the entropy pool for generating it.
		self::$key=$_SESSION['scaffolding_key']=sha1(microtime().print_r(posix_uname,1).print_r($_SESSION,1));
		return self::$key;
	}
}
