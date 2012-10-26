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
	* ZooKeeper handler (a Zookeeper instance)
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

	/**
	* The default ACL used for ZK keys. You generally don't want to
	* mess with this, if you have a private ZK cluster.
	*
	* @var array
	*/
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
	* How many seconds to wait before timing out on connections.
	*
	* @var float
	*/
	public $connection_timeout=0.1;

	/**
	* A local cache of known paths. Used by {link ensurePath()}.
	*
	* We exploit the assumption that we're always using the same ZK cluster.
	*
	* @var array
	*/
	static protected $known_paths=array();

	/**
	* The name of the actual lock key.
	*
	* @var string
	*/
	var $lock_name="lock-";

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
			throw new RuntimeException(
				"Please define constant LPC_ZOOKEEPER_HOST if ".
				"you want to use class ".get_class($this)
			);

		if (!isset($path) || is_null($path) || !is_string($path) || !strlen($path))
			throw new RuntimeException(
				"The path needs to be a non-empty string."
			);

		while (substr($path, -1, 1)=='/')
			// Clip the final slash(es)
			$path=substr($path, 0, -1);

		if (!strlen($path))
			// Add a slash if we actually work in the root
			$path='/';

		$this->base_path=$path;

		if ($renew && isset(self::$zk_h))
			// It NEEDS to be manually "unset" (not simply replaced with the new one)
			self::$zk_h=NULL;

		if (empty(self::$zk_h)) {
			self::$zk_h=new Zookeeper();
			self::$zk_h->connect(LPC_ZOOKEEPER_HOST);
			$this->waitForConnection();
		}
	}

	function waitForConnection()
	{
		$deadline=microtime(true)+$this->connection_timeout;
		while(self::$zk_h->getState()!=Zookeeper::CONNECTED_STATE) {
			if ($deadline <= microtime(true))
				throw new RuntimeException("Zookeeper connection timed out!");
			usleep($this->sleep_cycle);
		}
	}

	/**
	* Lock a ZK key.
	*
	* Creates a sequenced key and waits for {@link waitForLock()} to confirm
	* ours is the first in that sequence.
	*
	* On success, it returns the sequence lock key (sequence number included),
	* which can then be used with {@link unlock()} to unlock.
	*
	* @param string $key the key to use. Typically a key under the default path
	*	defined in {@link __construct}, unless $key starts with a
	*	slash, in which case $key is the full path
	* @param int $timeout how many seconds to wait. If zero, just return.
	* @return mixed (boolean) false on failure, or (string) sequence lock key
	*/
	public function lock($key, $timeout=0)
	{
		$full_key=$this->computeFullKey($this->getLockName($key));
		$this->ensurePath($full_key);
		$lock_key=self::$zk_h->create(
			$full_key, // path
			1, // value
			$this->default_acl, // ACL
			Zookeeper::EPHEMERAL | Zookeeper::SEQUENCE // flags
		);
		if (!$lock_key)
			throw new RuntimeException("Failed creating lock node ".$full_key);

		if (!$this->waitForLock($lock_key, $full_key, $timeout)) {
			// Clean up
			self::$zk_h->delete($lock_key);
			return false;
		}

		return $lock_key;
	}

	/**
	* Get a generic name and return the name of the ZK key appropriate for locking.
	*
	* ZK sequences are per parent node, so this ensures we're always working inside
	* the requested node. It simply appends a slash and {@link $lock_name} to the
	* specified key name.
	*
	* @param string $key the desired name for the lock
	* @return string the name of the ZK node for this lock
	*/
	protected function getLockName($key)
	{
		return $key."/".$this->lock_name;
	}

	/**
	* Unlocks a ZK key.
	*
	* It needs to be provided with a valid sequence lock key, as returned by
	* {@link lock()}.
	*
	* @param string $lock_key the sequence lock key to unlock
	* @return bool true on success, false on failure
	*/
	public function unlock($lock_key)
	{
		if (!is_string($lock_key))
			throw new DomainException(
				"This method expects a string!"
			);
		return self::$zk_h->delete($lock_key);
	}

	/**
	* Waits for a sequenced key to be the first in the sequence,
	* thus ensuring that specific key's process has the lock.
	*
	* @param string $my_key a sequence lock key, as created in
	*		{@link lock}
	* @param string $base_key the lock key's associated full lock key
	*		(with full, absolute path)
	* @param float $timeout how long to wait before giving up.
	* @return bool true if ours is the first key in the sequence, false
	*		otherwise.
	*/
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

	/**
	* Check if there's ANY lock on a specific key.
	*
	* Be advised this returns true if ANY lock is in place
	* for this key, regardless of its index in the sequence.
	* That is, if you obtain a lock and then call this, it
	* will tell you it IS locked.
	*
	* @param string $key the key to check for
	* @return bool true if there is any lock, false otherwise
	*/
	public function isLocked($key)
	{
		return !is_null($this->getFirstLock($this->computeFullKey($this->getLockName($key))));
	}

	/**
	* Wait for ALL locks named $base_key to die.
	* Be advised, this uses {@link isLocked()}, so it does NOT
	* wait for all PREVIOUS locks to die -- this waits for ALL
	* locks to die. That is, if you obtain a lock on this key
	* prior to calling this method, this will also wait for
	* YOUR own lock to be removed before returning true.
	*
	* $timeout in seconds; 0 means wait indefinitely
	*
	* @param string $key the key to wait for
	* @param float $timeout the amount of time to wait, in seconds
	* @return bool true if all previous locks for this key
	*		are dead, false if we timed out while waiting.
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
	* Get the first lock prefixed as $base_key.
	*
	* Depending on parameter $any, it returns the first lock it finds
	* (irrespective of that key's position in the sequence), or
	* the first lock in the sequence. The latter is more expensive,
	* since it needs to sift through all matching keys, looking for
	* the smallest one, so make sure to specify $any=true if you
	* only want to check if there's any lock whatsoever.
	*
	* @param string $base_key the full key (with path), but
	*		without any sequence info
	* @param bool $any whether to return any key in the sequence, or
	*		specifically the first one
	* @return mixed (string) the first sequence lock (sequence number
	*		included), or (NULL) if there is none.
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

	/**
	* Converts a relative key to a full key.
	*
	* This uses {@link $base_path} to prepend the path to the
	* specified key. If the key is already a full key (i.e.
	* it starts with a "/") then the key is returned unchanged.
	*
	* @param string $key the key to process
	* @return string the associated full key (with absolute path)
	*/
	protected function computeFullKey($key)
	{
		if (substr($key, 0, 1)=='/')
			return $key;
		return $this->base_path.'/'.$key;
	}

	/**
	* Ensures the path of the specified key exists, and creates
	* all required parents if necessary. It does NOT create the key itself.
	*
	* @param $key the full key (with path)
	* @return bool true on success. On failure it throws an exception.
	*/
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

	/**
	* Returns the parent's name for a specified key.
	*
	* @param string $key the full key (with path)
	* @return string the key parent's path
	*/
	private function getParentName($key)
	{
		return dirname($key);
	}
}
