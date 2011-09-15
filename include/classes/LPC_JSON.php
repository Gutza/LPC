<?php

/**
* A wrapper for PHP's own json_encode and json_decode (http://php.net/manual/en/book.json.php)
* if they are available, or fallback on PEAR's Services_JSON (http://pear.php.net/pepr/pepr-proposal-show.php?id=198)
*/
abstract class LPC_JSON
{
	public function encode($var)
	{
		if (function_exists('json_encode'))
			return json_encode($var);

		require_once "PEAR.php";
		require_once "Services/JSON.php";
		$json = new Services_JSON();
		return $json->encode($var);
	}

	public function decode($var)
	{
		if (function_exists('json_decode'))
			return json_decode($var,true);

		require_once "PEAR.php";
		require_once "Services/JSON.php";
		$json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
		return $json->decode($var);
	}
}
