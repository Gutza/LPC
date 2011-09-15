<?php

class LPC_Cache
{
	const TYPE_APC="apc";
	const TYPE_DATABASE="database";
	const TYPE_MEMCACHE="memcache";
	const TYPE_NONE="none";
	const TYPE_SESSION="session";

	const GENERIC_TYPE_FAKE=0;
	const GENERIC_TYPE_SESSION=1;
	const GENERIC_TYPE_GLOBAL=3;

	private static $type=self::GENERIC_TYPE_FAKE;

	static $current;

	private function __construct() {}

	public function getGenericType()
	{
		self::getCurrent();
		return self::$type;
	}

	public function getCurrent()
	{
		if (isset(self::$current))
			return self::$current;
		self::$current=self::buildCache();
		return self::$current;
	}

	private function buildCache()
	{
		if (!defined('LPC_CACHE_TYPE'))
			throw new RuntimeException("You need to define constant LPC_CACHE_TYPE in order to use the LPC cache functionality.");
		switch(LPC_CACHE_TYPE) {
			case self::TYPE_MEMCACHE:
				return self::build_cache_memcache();
			case self::TYPE_SESSION:
				return self::build_cache_session();
			case self::TYPE_DATABASE:
				return self::build_cache_database();
			case self::TYPE_NONE:
				return self::build_cache_none();
			case self::TYPE_APC:
				return self::build_cache_apc();
		}
		throw new InvalidArgumentException("Unknown cache type: ".LPC_CACHE_TYPE);
	}

	private function build_cache_memcache()
	{
		if (!defined('LPC_CACHE_MC_HOST'))
			throw new InvalidArgumentException("Memcache caches need a host (constant LPC_CACHE_MC_HOST is undefined).");
		if (!defined('LPC_CACHE_MC_PORT'))
			throw new InvalidArgumentException("Memcache caches need a port (constant LPC_CACHE_MC_PORT is undefined).");
		self::$type=self::GENERIC_TYPE_GLOBAL;
		return new LPC_Cache_memcache(LPC_CACHE_MC_HOST,LPC_CACHE_MC_PORT);
	}

	private function build_cache_session()
	{
		self::$type=self::GENERIC_TYPE_SESSION;
		return new LPC_Cache_session();
	}

	private function build_cache_database()
	{
		self::$type=self::GENERIC_TYPE_GLOBAL;
		return new LPC_Cache_database();
	}

	private function build_cache_none()
	{
		self::$type=self::GENERIC_TYPE_FAKE;
		return new LPC_Cache_none();
	}

	private function build_cache_apc()
	{
		self::$type=self::GENERIC_TYPE_GLOBAL;
		return new LPC_Cache_apc();
	}
}
