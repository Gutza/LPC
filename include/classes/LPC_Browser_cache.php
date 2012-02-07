<?php
/**
* A generic browser cache class for LPC
* @author Bogdan Stancescu <bogdan@moongate.ro>
* @copyright Copyright (c) January 2011, Bogdan Stancescu
* @license http://www.gnu.org/licenses/gpl.html GNU Public License v3 or later
*/
class LPC_Browser_cache
{
	// Singleton
	private function __construct() {}

	/**
	* Manage cache based on the date this content was last changed.
	*
	* @return boolean true if the cache is still valid (which means you
	* can exit), false otherwise (which means you should probably serve
	* the content).
	*/
	static function dateCache($date)
	{
		// Set last-modified header
		header("Last-Modified: ".gmdate("D, d M Y H:i:s", $date)." GMT");
		// Make sure caching is turned on
		header('Cache-Control: must-revalidate');
		// UNSET expiration date and pragma
		header('Expires:');
		header('Pragma:');

		// Check if the HTTP_IF_MODIFIED_SINCE header is set
		if (!isset($_SERVER['HTTP_IF_MODIFIED_SINCE']))
			return false;

		// Check if page has changed
		if (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE'])!=$date)
			return false;

		header("HTTP/1.1 304 Not Modified");
		return true;
	}

	/**
	* Manage cache based on maximum age.
	*
	* @return boolean always false (serve the content).
	*/
	function ageCache($maxAge)
	{
		header("Cache-Control: max-age=".$maxAge);

		// UNSET expiration date and pragma
		header('Expires:');
		header('Pragma:');

		return false;
	}
}
