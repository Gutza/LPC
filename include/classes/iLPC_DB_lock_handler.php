<?php

interface iLPC_DB_lock_handler
{
	public function lock($key, $timeout);
	public function unlock($key);
	public function isLocked($key);
}
