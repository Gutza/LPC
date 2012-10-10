<?php

/**
 * ZooKeeper lock manager.
 * @author Bogdan Stancescu <bogdan@moongate.ro>
 * @copyright Copyright (c) August 2012, Bogdan Stancescu
 * @license http://www.gnu.org/licenses/gpl.html GNU Public License v3 or later
 */
// Class Zookeeper is http://pecl.php.net/zookeeper
class LPC_ZK_lock
{
	/**
	* ZooKeeper handler
	*
	* @var object
	*/
	static $zk_h;

	/**
	* Current ZooKeeper base path
	*
	* @var string
	*/
	private $base_path;

	public $default_acl=array(
		array(
			"perms"=>Zookeeper::PERM_ALL,
			"scheme"=>"world",
			"id"=>"anyone",
		),
	);

	/**
	* How many microseconds to wait between checks on the lock.
	* The default is 0.1 seconds, which should be reasonable
	* for most applications. Be advised that significantly shorter
	* delays might lead to unnecessary loads on the ZK cluster.
	*
	* @var int
	*/
	public $sleep_cycle=100000;

	/**
	* A local cache of known paths. Used by {link ensurePath()}.
	*
	* We exploit the assumption that we're always using the same ZK cluster.
	*
	* @var array
	*/
	static protected $known_paths=array();

	/**
	* Constructor. Takes a root path.
	*
	* Configure the ZooKeeper connection in LPC_config.php
	*
	* @param string $path the default path under which all subsequent
	*	operations will take place *by default*. Be advised that
	*	all operations can specify explicit paths if they start
	*	their respective path parameter with a slash ("/").
	* @param boolean $renew if set and evaluates true, renew
	*	the ZK connection. You typically do NOT want this
	*	(it will kill all your previous ephemeral nodes).
	*	This only makes sense if you fork.
	*/
	public function __construct($path, $renew=false)
	{
		if (!defined("LPC_ZOOKEEPER_HOST"))
			throw new RuntimeException("Please define constant ".
				"LPC_ZOOKEEPER_HOST if you want to use class ".
				get_class($this));
		if (!isset($path) || is_null($path) || !is_string($path) || !strlen($path))
			throw new RuntimeException("The path needs to be ".
				"a non-empty string.");
		while (substr($path, -1, 1)=='/') // Clip the final slash(es)
			$path=substr($path, 0, -1);
		if (!strlen($path)) // Add a slash if we actually work in the root
			$path='/';
		$this->base_path=$path;

		if ($renew && isset(self::$zk_h))
			self::$zk_h=NULL; // It NEEDS to be manually "unset" (not simply replaced with the new one)

		if (!isset(self::$zk_h)) {
			self::$zk_h=new Zookeeper(LPC_ZOOKEEPER_HOST);
			if (is_null(self::$zk_h->get("/")))
				throw new RuntimeException("Failed connecting to the ".
					"specified ZooKeeper server! (".LPC_ZOOKEEPER_HOST.")");
		}
	}

	/**
	* Lock a ZK key
	*
	* @param string $key the key to use. Typically a key under the default path
	*	defined in {@link __construct}, unless $key starts with a
	*	slash, in which case $key is the full path
	* @param int $timeout how many seconds to wait. If zero, just return.
	* @return boolean true on success, false on failure
	*/
	public function lock($key, $timeout=0)
	{
		$full_key=$this->computeFullKey($key);
		$this->ensurePath($full_key);
		$lock_key=self::$zk_h->create($full_key, 1, $this->default_acl, Zookeeper::EPHEMERAL | Zookeeper::SEQUENCE);
		if (!$lock_key)
			throw new RuntimeException("Failed creating lock node ".$full_key);
		if (!$this->waitForLock($lock_key, $full_key, $timeout)) {
			// Clean up
			self::$zk_h->delete($lock_key);
			return false;
		}
		return $lock_key;
	}

	public function unlock($lock_key)
	{
		return self::$zk_h->delete($lock_key);
	}

	private function waitForLock($my_key, $base_key, $timeout)
	{
		$deadline=microtime(true)+$timeout;
		$parent=self::getParentName($base_key);
		while(true) {
			if ($my_key==$this->getFirstLock($base_key))
				return true;
			if ($deadline<=microtime(true))
				return false;
			usleep($this->sleep_cycle);
		}
	}

	public function isLocked($key)
	{
		return !is_null($this->getFirstLock($this->computeFullKey($key)));
	}

	/**
	* Wait for all locks named $base_key.
	* $timeout in seconds; 0 means wait indefinitely
	*/
	public function waitForAllLocks($key, $timeout=0)
	{
		if ($timeout)
			$deadline=microtime(true)+$timeout;
		else
			$deadline=0;

		while(true) {
			if (!$this->isLocked($key))
				return true;
			if ($deadline && $deadline<=microtime(true))
				return false;
			usleep($this->sleep_cycle);
		}
	}

	/**
	* Get the first lock (or, in fact, any lock) prefixed as $base_key
	*/
	protected function getFirstLock($base_key, $any=false)
	{
		$parent=self::getParentName($base_key);
		$children=self::$zk_h->getChildren($parent);
		$first_lock=NULL;
		foreach($children as $child) {
			$child=$parent."/".$child;
			if (substr($child, 0, strlen($base_key))!=$base_key)
				continue;
			if ($any)
				return $child;
			if (is_null($first_lock) || $child<$first_lock)
				$first_lock=$child;
		}
		return $first_lock;
	}

	protected function computeFullKey($key)
	{
		if (substr($key, 0, 1)=='/')
			return $key;
		return $this->base_path.'/'.$key;
	}

	protected function ensurePath($key)
	{
		$parent=self::getParentName($key);
		if (in_array($key, self::$known_paths))
			return true;
		if (self::$zk_h->exists($parent))
			return true;
		if (!$this->ensurePath($parent))
			return false; // We should never execute this
		if (self::$zk_h->create($parent, 1, $this->default_acl))
			return true;
		throw new RuntimeException("Failed creating path [".$parent."]");
	}

	private function getParentName($key)
	{
		return dirname($key);
	}
}
