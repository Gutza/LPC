<?php

/**
* This class generates a unique token for the specified field in the specified class, populates it and saves the object.
* Race conditions are avoided. Tokens are guaranteed to only contain alphanumeric characters, plus "-" and "_" for base64.
*
* Note: This SAVES the object, so make sure you account for that in your code's logic.
* Bogdan, 2011-02
*/

class LPC_Token_generator
{
	var $method="sha1"; // the only one supported right now -- 40 characters long
	var $encoding="base64"; // plain or base64; applied before trimming
	var $length=40; // Specifies the maximum length
	var $options=0;

	const OPT_NO_ZERO=1;
	const OPT_NO_O=2;
	const OPT_NO_ONE=4;
	const OPT_NO_L=8;
	const OPT_ALL_UPPERCASE=16;
	const OPT_ALL_LOWERCASE=32;

	private $object;
	private $field;

	function __construct($object,$field)
	{
		$this->object=$object;
		$this->field=$field;
	}

	function generate()
	{
		$fp=fopen(__FILE__,'r');
		flock($fp,LOCK_EX);

		$success=false;
		while(!$success) {
			$token=$this->instantToken();
			$success=!$this->object->search($this->field,$token);
		}
		$this->object->setAttr($this->field,$token);
		$success=$this->object->save();

		flock($fp,LOCK_UN);
		fclose($fp);

		return $success;
	}

	function instantToken()
	{
		$token=$this->encode(sha1(rand().microtime()));
		if ($this->length<strlen($token))
			$token=substr($token,0,$this->length);
		$token=$this->processOptions($token);
		return $token;
	}

	function processOptions($token)
	{
		if ($this->options & self::OPT_NO_ZERO)
			$token=str_replace('0','Z',$token);
		if ($this->options & self::OPT_NO_O)
			$token=str_replace('O','E',str_replace('o','m',$token));
		if ($this->options & self::OPT_NO_ONE)
			$token=str_replace('1','X',$token);
		if ($this->options & self::OPT_NO_L)
			$token=str_replace('L','U',str_replace('o','t',$token));
		if ($this->options & self::OPT_ALL_UPPERCASE)
			$token=strtoupper($token);
		elseif ($this->options & self::OPT_ALL_LOWERCASE)
			$token=strtolower($token);
		return $token;
	}

	function encode($token)
	{
		switch($this->encoding) {
			case 'plain':
				return $token;
			case 'base64':
				return str_replace("+","-",str_replace("/","_",substr(base64_encode($this->to8bit($token)),0,-1)));
			default:
				throw new RuntimeException("Unknown encoding (".$this->encoding.")");
		}
	}

	function to8bit($plain_data)
	{
		$data='';
		while(strlen($plain_data)) {
			$data.=chr(hexdec(substr($plain_data,0,2)));
			$plain_data=substr($plain_data,2);
		}
		return $data;
	}
}
