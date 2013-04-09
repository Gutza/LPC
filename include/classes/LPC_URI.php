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
		if (empty($this->uriParts['url_parts']['query']))
			$this->uriParts['url_parts']['query']=array();
		$this->uriParts['url_parts']['query'][$name] = $value;

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
		if (!isset($this->uriParts['url_parts']['query'][$name]))
			return $this;

		unset($this->uriParts['url_parts']['query'][$name]);
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
		if (!isset($this->uriParts['url_parts']['query'][$name]))
			return NULL;
		return $this->uriParts['url_parts']['query'][$name];
	}

	public function toString($escapedAmps=false)
	{
		if ($escapedAmps)
			$amp="&amp;";
		else
			$amp=ini_get("arg_separator.output");

		return $this->uriParts['url_base']."?".http_build_query($this->uriParts['url_parts']['query'], '', $amp);
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
		return $this->uriParts['url_parts']['path'];
	}

	/**
	* Returns the scheme + host + port + path parts of the URI
	*
	* @return string the scheme + host + port + path parts of the URI
	*/
	public function getFullPath()
	{
		return $this->uriParts['url_base'];
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
		@parse_str($up['query'], $query);
		$up['query'] = $query;

		$ub.=$up['path'];

		$this->uriParts = array(
			'url_parts'=>$up,
			'url_base'=>$ub
		);
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
}

