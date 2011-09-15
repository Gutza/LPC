<?php

interface iLPC_Cache
{
	const FLAG_COMPRESSED=1;

	// Get, set and delete in the global cache
	public function getG($name);
	public function setG($name,$value,$flags=NULL,$expiration=NULL);
	public function deleteG($name,$expiration=NULL);

	// Get, set and delete in this user's global cache
	public function getU($name,$userID=0);
	public function setU($name,$value,$userID=0,$flags=NULL,$expiration=NULL);
	public function deleteU($name,$userID=0,$expiration=NULL);

	// Get, set and delete in this project's global cache
	public function getP($name,$projectID=0);
	public function setP($name,$value,$projectID=0,$flags=NULL,$expiration=NULL);
	public function deleteP($name,$projectID=0,$expiration=NULL);

	// Get, set and delete in this user's cache for this project
	public function getUP($name,$userID=0,$projectID=0);
	public function setUP($name,$value,$userID=0,$projectID=0,$flags=NULL,$expiration=NULL);
	public function deleteUP($name,$userID=0,$projectID=0,$expiration=NULL);

}
