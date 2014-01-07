<?php

/**
* A fluent URI manipulation class
* @author Bogdan Stancescu <bogdan@moongate.ro>
*/
class LPC_URI implements iLPC_HTML
{
	protected $uriString=NULL;
	protected $uriParts=NULL;
	private static $currentInstance=NULL;

	/**
	* Constructor. If no URI is provided, it uses the current URL.
	*
	* @param string $uri the URI to process.
	*/
	public function __construct($uri=NULL)
	{
		$this->loadUri($uri);
	}

	/**
	* Returns a copy of the current URI
	*
	* @return object a LPC_URI instance representing the current URI
	*/
	public static function getCurrent()
	{
		if (empty(self::$currentInstance))
			self::$currentInstance = new LPC_URI();

		return self::$currentInstance->dupe();
	}

	/**
	* Call this if you want to process a new URI.
	*
	* @param string $uri the URI to process, or NULL to use the current URL.
	*/
	public function loadUri($uri = NULL)
	{
		$this->processURI($uri);
		return $this;
	}

	/**
	* Set a variable in the query part of the URI.
	*
	* If the variable already exists, it is replaced with the
	* new value; if it doesn't exist, it's added.
	*
	* @param string $name the name of the variable
	* @param string $nalue the value of the variable
	* @param string $uri the URI to add/replace the variable in
	* @return object this URI object, after applying the changes
	*/
	public function setVar($name, $value)
	{
		if (empty($this->uriParts['query']))
			$this->uriParts['query']=array();
		$this->uriParts['query'][$name] = $value;

		return $this;
	}

	/**
	* Executes {@link setVar()} for each of the pairs in the
	* associative array passed as $vars
	*/
	public function setVars($vars)
	{
		foreach($vars as $name => $value)
			$this->setVar($name, $value);

		return $this;
	}

	/**
	* Remove a variable from the query part of the URI.
	*
	* If the variable exists, it is removed. Otherwise,
	* this object is returned unchanged.
	*
	* @param string $name the variable name to remove
	* @return object this URI object, after applying the changes
	*/
	public function delVar($name)
	{
		if (!isset($this->uriParts['query'][$name]))
			return $this;

		unset($this->uriParts['query'][$name]);
		return $this;
	}

	/**
	* Executes {@link delVar} for each of the names in the
	* indexed array passed as $names
	*/
	public function delVars($names)
	{
		foreach($names as $name)
			$this->delVar($name);

		return $this;
	}

	/**
	* Retrieve the value of query parameter $name.
	* If not present, it returns NULL
	*/
	public function getVar($name)
	{
		if (!isset($this->uriParts['query'][$name]))
			return NULL;
		return $this->uriParts['query'][$name];
	}

	public function toString($escapedAmps=false)
	{
		if ($escapedAmps)
			$amp="&amp;";
		else
			$amp=ini_get("arg_separator.output");

		$base = $this->getUrlBase();
		if (empty($this->uriParts['query']))
			return $base;

		return $base."?".http_build_query($this->uriParts['query'], '', $amp);
	}

	public function render()
	{
		return $this->toString();
	}

	/**
	* Returns the path part of the URI
	*
	* @return string the path part of the current URI
	*/
	public function getPath()
	{
		return $this->uriParts['path'];
	}

	/**
	* Returns the scheme + host + port + path parts of the URI
	*
	* @return string the scheme + host + port + path parts of the URI
	*/
	public function getFullPath()
	{
		return $this->getUrlBase();
	}

	/**
	* Returns the URL base path (scheme + host + port + path)
	*
	* @return string the URL base path
	*/
	protected function getUrlBase()
	{
		$up = "";
		if (isset($this->uriParts["user_pass"]))
			$up = $this->uriParts["user_pass"];

		return
			$this->uriParts["scheme"].
			$up.
			$this->uriParts["host"].
			$this->uriParts["path"]
		;
	}

	/**
	* Returns a clone of this object. Unfortunately we can't use "clone"
	* because it's a reserved keyword in PHP, but that's all this does.
	*
	* @return object a clone of this object
	*/
	public function dupe()
	{
		return clone $this;
	}

	/**
	* Process an URI, break it down into parts and
	* populate {@link $uriString} and {@link $uriParts}
	*
	* @param string $uri the URI to process, or NULL for the current URL
	*/
	protected function processURI($uri=NULL)
	{
		if ($uri===NULL)
			$uri=self::getCurrentURI();

		$this->uriString=$uri;

		$up=parse_url($this->uriString); // URL parts
		$tup=array( // temporary URI parts
			"scheme" => "",
			"user_path" => "",
			"host" => "",
			"port" => "",
			"path" => "",
			"query" => array(),
		);
		if (isset($up['host'])) {
			if (isset($up['scheme']))
				$tup["scheme"] = $up['scheme'];
			else
				$tup["scheme"] = "http";
			$tup["scheme"] .= "://"; // we want this here (see getUrlBase())

			if (isset($up['user'])) {
				$tup["user_path"] = $up['user'];
				if (isset($up['pass'])) {
					$$tup["user_path"] .= ":".$up['pass'];
				}
				$tup["user_path"] .= '@';
			}
			$tup["host"] = $up['host'];

			if (isset($up['port']))
				$tup["port"] = ":".$up['port'];
		}
		@parse_str($up['query'], $query);
		$tup['query'] = $query;

		$tup["path"] = $up['path'];

		$this->uriParts = $tup;
	}

	// Adapted from http://dev.kanngard.net/Permalinks/ID_20050507183447.html
	// Modifications:
	// - Simplified the code for HTTP and HTTPS only
	// - Added static caching
	// - Properly managing the port for HTTPS
	public static function getCurrentURI()
	{
		static $current;
		if (isset($current))
			return $current;

		$protocol = "http";
		if (isset($_SERVER['HTTPS']) && $_SERVER["HTTPS"]=="on")
			$protocol = "https";

		$port="";
		if (
			($protocol=='http' && $_SERVER["SERVER_PORT"]!=80) ||
			($protocol=='https' && $_SERVER["SERVER_PORT"]!=443)
		)
			$port=':'.$_SERVER["SERVER_PORT"];

		$current=$protocol."://".$_SERVER['SERVER_NAME'].$port.$_SERVER['REQUEST_URI'];
		return $current;
	}

	/**
	* Removes everything except A-Za-z0-9 from a string, and replaces all different characters with minus signs.
	* Minus signs are not duplicated, and they are never left at the beginning or the end of the string.
	*/
	public static function onlySafe($str)
	{
		$newString = $newerString = ereg_replace("[^A-Za-z0-9]", "-", $str);

		do {
			$newString = $newerString;
			$newerString = str_replace("--", "-", $newString);
		} while ($newerString != $newString);

		$result = $newerString;
		while(substr($result, 0, 1) == '-')
			$result = substr($result, 1);
		while(substr($result, -1) == '-')
			$result = substr($result, 0, -1);

		return $result;
	}

	/**
	* Switch SSL on (https) or off (http) for this URI.
	* Throws exceptions if you try to use it on an URI with no scheme set,
	* or on URIs that use any schemes other than http ot https
	*/
	public function switchSSL($enable)
	{
		$isSSL = $this->isSSL();
		if ($enable == $isSSL)
			return $this;

		if ($enable)
			$this->uriParts['scheme'] = "https://";
		else
			$this->uriParts['scheme'] = "http://";

		return $this;
	}

	public function isSSL()
	{
		static $isSSL = NULL;
		if (NULL !== $isSSL)
			return $isSSL;

		if (empty($this->uriParts['scheme']))
			throw new RuntimeException("Can't determine SSL for URIs without a scheme (".$this->toString().")");

		if (!in_array($this->uriParts['scheme'], array("http://", "https://")))
			throw new RuntimeException("Unknown scheme for SSL (".$this->uriParts['scheme'].")");

		return $isSSL = ("https://" == $this->uriParts['scheme']);
	}
}

