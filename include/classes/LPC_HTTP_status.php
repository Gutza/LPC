<?php
// Feb 2012
class LPC_HTTP_status
{
	// Do feel free to add codes to this list from your code.
	// The current list has been compiled from http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
	// but you can find more in http://en.wikipedia.org/wiki/List_of_HTTP_status_codes
	public static $allCodes=array(
		100=>'Continue',
		101=>'Switching Protocols',
		200=>'OK',
		201=>'Created',
		202=>'Accepted',
		203=>'Non-Authoritative Information',
		204=>'No Content',
		205=>'Reset Content',
		206=>'Partial Content',
		300=>'Multiple Choices',
		301=>'Moved Permanently',
		302=>'Found',
		303=>'See Other',
		304=>'Not Modified',
		305=>'Use Proxy',
		307=>'Temporary Redirect',
		400=>'Bad Request',
		401=>'Unauthorized',
		402=>'Payment Required',
		403=>'Forbidden',
		404=>'Not Found',
		405=>'Method Not Allowed',
		406=>'Not Acceptable',
		407=>'Proxy Authentication Required',
		408=>'Request Timeout',
		409=>'Conflict',
		410=>'Gone',
		411=>'Length Required',
		412=>'Precondition Failed',
		414=>'Request-URI Too Long',
		415=>'Unsupported Media Type',
		416=>'Requested Range Not Satisfiable',
		417=>'Expectation Failed',
		500=>'Internal Server Error',
		501=>'Not Implemented',
		502=>'Bad Gateway',
		503=>'Service Unavailable',
		504=>'Gateway Timeout',
		505=>'HTTP Version Not Supported',
	);

	private static $callbacks=array();

	private function __construct()
	{
		// Singleton
	}

	public function registerHandler($statusCode, $callback)
	{
		self::$callbacks[$statusCode]=$callback;
	}

	public function trigger($statusCode, $exit=true)
	{
		if (isset(self::$callbacks[$statusCode])) {
			call_user_func_array(self::$callbacks[$statusCode], array($statusCode));
			if ($exit)
				exit;
		}
		switch($statusCode) {
			case 403:
				self::forbidden();
				break;
			case 404:
				self::not_found();
				break;
			default:
				self::other($statusCode);
		}
		if ($exit)
			exit;
	}

	private function not_found()
	{
		header("HTTP/1.0 404 Not found");
		echo <<<EOERR
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>404 Not found</title>
</head><body>
<h1>Not found</h1>
<p>The page you're looking for doesn't exist on this server.</p>
</body></html>
EOERR
	        ;
	}

	private function forbidden()
	{
		header("HTTP/1.0 403 Forbidden");
		echo <<<EOERR
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>403 Forbidden</title>
</head><body>
<h1>Forbidden</h1>
<p>You don't have permission to access this resource on this server.</p>
</body></html>
EOERR
		;
	}

	private function other($statusCode)
	{
		if (empty(self::$allCodes[$statusCode]))
			self::other(500);
		$codeName=self::$allCodes[$statusCode];
		header("HTTP/1.0 ".$statusCode." ".$codeName);
		echo <<<EOERR
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>$statusCode $codeName</title>
</head><body>
<h1>$statusCode $codeName</h1>
</body></html>
EOERR
		;
		exit;
	}
}
