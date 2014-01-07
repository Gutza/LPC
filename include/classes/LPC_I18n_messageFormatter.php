<?php

/**
* LPC message formatter
*
* Retrieves translations from the database and caches them.
* Bogdan Stancescu <bogdan@moongate.ro>, December 2011
*/

class LPC_I18n_messageFormatter extends MessageFormatter
{

	static $known_references=array();
	static $object_cache=array();
	static $cache_enabled;

	public static function get($messageKey)
	{
		$langID=LPC_Language::getCurrent()->id;
		if (!isset(self::$object_cache[$langID]))
			self::$object_cache[$langID]=array();

		if (!isset(self::$object_cache[$langID][$messageKey]))
			self::$object_cache[$langID][$messageKey]=new LPC_I18n_messageFormatter($messageKey);

		return self::$object_cache[$langID][$messageKey];
	}

	function __construct($messageKey)
	{
		$lang=LPC_Language::getCurrent();
		return parent::__construct(
			$lang->getLocale(),
			self::getTranslation($messageKey)
		);
	}

	static function getTranslation($messageKey)
	{
		if ($translation=self::translationFromCache($messageKey))
			return $translation;

		$lang=LPC_Language::getCurrent();
		$entry=new LPC_I18n_message();
		$entries=$entry->search(
			array('message_key','language'),
			array($messageKey,$lang->id)
		);

		if (!$entries)
			$entries=$entry->search(
				array('message_key','language'),
				array($messageKey,LPC_language::getDefault()->id)
			);

		if ($entries) {
			$translation=$entries[0]->getAttr('translation');
			self::translationToCache($messageKey,$translation);
			return $translation;
		}

		self::checkReference($messageKey);

		return "[[".$messageKey."]]";
	}

	static function checkCache()
	{
		if (isset(self::$cache_enabled))
			return self::$cache_enabled;
		self::$cache_enabled=(LPC_Cache::getSpeed()==LPC_Cache::SPEED_FAST);
		return self::$cache_enabled;
	}

	static function translationFromCache($messageKey)
	{
		if (!self::checkCache())
			return false;

		$cache=LPC_Cache::getCurrent();
		return $cache->getG(self::getCacheKey($messageKey));
	}

	static function translationToCache($messageKey,$translation)
	{
		if (!self::checkCache())
			return;

		$cache=LPC_Cache::getCurrent();
		return $cache->setG(self::getCacheKey($messageKey),$translation);
	}

	static function clearTranslationCache($messageKey,$langID)
	{
		if (!self::checkCache())
			return;

		$cache=LPC_Cache::getCurrent();
		return $cache->deleteG(self::getCacheKey($messageKey,$langID));
	}

	static function getCacheKey($messageKey,$langID=0)
	{
		if (!$langID)
			$langID=LPC_Language::getCurrent()->id;
		return "LPC.i18n.".$langID."-".$messageKey;
	}

	static function checkReference($messageKey)
	{
		if (in_array($messageKey,self::$known_references))
			return;
		self::$known_references[]=$messageKey;

		$ref=new LPC_I18n_reference($messageKey);
		if ($ref->probe())
			return;

		$ref->insertWithId($messageKey);
	}
}
