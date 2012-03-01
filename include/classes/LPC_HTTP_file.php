<?php
/**
* The generic LPC class for serving HTTP content.
* @author Bogdan Stancescu <bogdan@moongate.ro>
* @copyright Copyright (c) February 2012, Bogdan Stancescu
* @license http://www.gnu.org/licenses/gpl.html GNU Public License v3 or later
*/
class LPC_HTTP_file
{
	/**
	* The MIME type associated with this file.
	*/
	public $mimeType="binary/octet-stream";

	/**
	* The name of this file, when it is downloaded.
	*/
	public $fileName="";

	/**
	* The date at which the file was last modified.
	*/
	public $date=0;

	/**
	* Whether to serve this as an attachment
	* (by default it's served inline).
	*/
	public $asAttachment=false;

	/**
	* The actual content of this file.
	*/
	public $content="";

	/**
	* If the {@link $date} variable is not set and this is set,
	* the file will be cached for at most these many seconds
	* (Cache-Control: max-age=$cacheMaxAge)
	*/
	public $cacheMaxAge=0;

	/**
	* Loads all relevant data from a LPC_Object descendant.
	*/
	public function fromObject($object,$fileName)
	{
		if (!is_object($object) || !$object instanceof LPC_Object)
			throw new RuntimeException("Expecting a LPC_Object instance!");

		if (!$object->isValidFile($fileName))
			throw new RuntimeException("File identifier is invalid (".$fileName.")!");

		if (!$object->isPopulatedFile($fileName))
			return false;

		$this->content=$object->getAttr($object->dataStructure['files'][$fileName]['content']);
		if (isset($object->dataStructure['files'][$fileName]['mime']))
			$this->mimeType=$object->getAttr($object->dataStructure['files'][$fileName]['mime']);
		if (isset($object->dataStructure['files'][$fileName]['name']))
			$this->fileName=$object->getAttr($object->dataStructure['files'][$fileName]['name']);
		if (isset($object->dataStructure['files'][$fileName]['date']))
			$this->date=$object->getAttr($object->dataStructure['files'][$fileName]['date']);

		return true;
	}

	/**
	* Serves the file to the client.
	*/
	public function show()
	{
		$this->headerContentType();
		$this->headerContentDisposition();
		$this->headerContentLength();
		$this->cacheControl();
		$this->otherHeaders();
		$this->servePayload();
	}

	/**
	* Sends the Content-type header.
	*/
	protected function headerContentType()
	{
		header("Content-type: ".$this->mimeType);
	}

	/**
	* Sends the Content-disposition header
	*/
	protected function headerContentDisposition()
	{
		if (strlen($this->fileName))
			$fileName="; filename=\"".addslashes($this->fileName)."\"";
		else
			$fileName="";
		if ($this->asAttachment)
			$disposition="attachment";
		else
			$disposition="inline";
		header("Content-disposition: ".$disposition.$fileName);
	}

	/**
	* Sends the Content-length header
	*/
	protected function headerContentLength()
	{
		header("Content-length: ".strlen($this->content));
	}

	/**
	* Override this if you need to send other headers.
	*/
	protected function otherHeaders()
	{
	}

	/**
	* Cache control calls to LPC_Browser_cache
	*/
	protected function cacheControl()
	{
		if ($this->date)
			return LPC_Browser_cache::dateCache($this->date);

		if ($this->cacheMaxAge)
			return LPC_Browser_cache::ageCache($this->cacheMaxAge);
	}

	/**
	* Serves the actual content. This MUST always exit.
	*/
	protected function servePayload()
	{
		echo $this->content;
		exit;
	}
}
