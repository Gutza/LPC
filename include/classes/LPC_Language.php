<?php

class LPC_Language extends LPC_Base
{
	private static $currentInstance=NULL;
	private static $defaultInstance=NULL;
	private $locale;
	private static $langStack=array();

	function registerDataStructure()
	{
		$fields=array(
			"name"=>array(),
			"translated"=>array(
				'type'=>'integer',
			),
			"locale_POSIX"=>array(),
		);

		$depend=array();

		return array(
			'table_name'=>'LPC_language',
			'id_field'=>'id',
			'fields'=>$fields,
			'depend'=>$depend
		);
	}

	public static function pushCurrent($object=NULL)
	{
		array_push(self::$langStack, self::getCurrent());
		self::setCurrent($object, true);
	}

	public static function popCurrent()
	{
		self::setCurrent(array_pop(self::$langStack), true);
	}

	public static function setCurrent($object=NULL, $temporary=false)
	{
		if (!isset($object) || !$object->id) {
			unset(self::$currentInstance, $_SESSION['LPC']['current_language_id']);
			return true;
		}
		self::$currentInstance=$object;
		if (!$temporary)
			$_SESSION['LPC']['current_language_id']=$object->id;
		return true;
	}

	public static function getCurrent()
	{
		if (isset(self::$currentInstance))
			return self::$currentInstance;

		$langObj=self::newLanguage();
		if (get_class($langObj)!=get_class()) {
			self::setCurrent($langObj->getCurrent());
			return $langObj->getCurrent();
		}

		if (isset($_SESSION['LPC']['current_language_id'])) {
			$lang=self::newLanguage($_SESSION['LPC']['current_language_id']);
			if ($lang->probe())
				self::setCurrent($lang);
		}

		$default=self::getDefault();
		self::setCurrent($default);
		return self::$currentInstance;
	}

	public static function getDefault()
	{
		if (!defined('LPC_default_language'))
			throw new RuntimeException("You have to define constant LPC_default_language if you want to rely on LPC defaults.");

		if (isset(self::$defaultInstance))
			return self::$defaultInstance;
		$lang=self::newLanguage(LPC_default_language);
		if (!$lang->probe())
			throw new RuntimeException("No entry found in the languages table for the default language code (constant LPC_default_language=\"".LPC_default_language."\")");
		self::$defaultInstance=$lang;
		return self::$defaultInstance;
	}

	public static function newLanguage($id=0)
	{
		$class=self::getLanguageClass();
		return new $class($id);
	}

	protected function getLanguageClass()
	{
		if (defined("LPC_language_class"))
			return LPC_language_class;

		return "LPC_Language";
	}

	public function getLocale()
	{
		if (isset($this->locale))
			return $this->locale;

		if (!defined("LPC_language_type"))
			throw new RuntimeException("You must define constant LPC_language_type if you want to use translations (default='POSIX')");
		if (!$this->id)
			throw new RuntimeException("LPC_Language::getLocale() called on an object without an id!");

		$this->locale=$this->getAttr('locale_'.LPC_language_type);
		return $this->locale;
	}

	public function strftime($format, $timestamp=NULL)
	{
		if ($timestamp===NULL)
			$timestamp=time();

		$clocale=setlocale(LC_TIME,0);
		setlocale(LC_TIME,$this->getLocale());
		$result=strftime($format, $timestamp);
		setlocale(LC_TIME,$clocale);
		return $result;
	}
}
