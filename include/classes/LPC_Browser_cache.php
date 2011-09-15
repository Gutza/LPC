<?php

class LPC_Browser_cache
{

	// Singleton
	private function __construct() {}

	static function testCache($date)
	{
		// Set last-modified header
		header("Last-Modified: ".gmdate("D, d M Y H:i:s", $date)." GMT");
		// Make sure caching is turned on
		header('Cache-Control: must-revalidate');
		// UNSET expiration date and pragma
		header('Expires: ');
		header('Pragma: ');

		// Check if the HTTP_IF_MODIFIED_SINCE header is set
		if (!isset($_SERVER['HTTP_IF_MODIFIED_SINCE']))
			return false;

		// Check if page has changed
		if (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE'])!=$date)
			return false;

		header("HTTP/1.1 304 Not Modified");
		return true;
	}

}
