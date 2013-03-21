<?php

/**
* URL utils
* *** THIS CLASS IS DEPRECATED, PLEASE USE LPC_URI INSTEAD! ***
*/
class LPC_Url
{
	public static function process_query($url=NULL)
	{
		if ($url===NULL)
			$url=self::get_current_URI();
		$up=parse_url($url); // URL parts
		$ub=''; // URL base
		if (isset($up['host'])) {
			if (isset($up['scheme'])) {
				$ub.=$up['scheme'];
			} else {
				$ub.="http";
			}
			$ub.="://";
			if (isset($up['user'])) {
				$ub.=$up['user'];
				if (isset($up['pass'])) {
					$ub.=":".$up['pass'];
				}
				$ub.='@';
			}
			$ub.=$up['host'];
		};
		$ub.=$up['path'];

		return array(
			'url_parts'=>$up,
			'url_base'=>$ub
		);
	}

	// Adapted from http://dev.kanngard.net/Permalinks/ID_20050507183447.html
	// Modifications:
	// - Simplified the code for HTTP and HTTPS only
	// - Added static caching
	// - Properly managing the port for HTTPS
	public static function get_current_URI()
	{
		static $current;
		if (isset($current))
			return $current;

		$protocol=empty($_SERVER["HTTPS"])?"http":(($_SERVER["HTTPS"]=="on")?"https":"http");
		$port="";
		if (
			($protocol=='http' && $_SERVER["SERVER_PORT"]!=80) ||
			($protocol=='https' && $_SERVER["SERVER_PORT"]!=443)
		)
			$port=':'.$_SERVER["SERVER_PORT"];

		$current=$protocol."://".$_SERVER['SERVER_NAME'].$port.$_SERVER['REQUEST_URI'];
		return $current;
	}

	public static function add_GET_var($url,$newVar,$newValue)
	{
		$nfo=self::process_query($url);

		// Base is fine, now let's see about the variables
		@parse_str($nfo['url_parts']['query'],$query);
		$query[$newVar]=$newValue;

		return $nfo['url_base']."?".http_build_query($query);
	}

	public static function remove_GET_var($url,$oldVar)
	{
		$nfo=self::process_query($url);

		@parse_str($nfo['url_parts']['query'],$query);
		if (isset($query[$oldVar]))
			unset($query[$oldVar]);
		$URLquery=http_build_query($query);
		if ($URLquery)
			$URLquery="?".$URLquery;
		return $nfo['url_base'].$URLquery;
	}

	public static function get_script($url=NULL)
	{
		$nfo=self::process_query($url);
		return $nfo['url_parts']['path'];
	}

	public static function get_full_script($url=NULL)
	{
		$nfo=self::process_query($url);
		return $nfo['url_base'];
	}
}
