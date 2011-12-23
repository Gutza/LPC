<?php

class LPC_I18n_message extends LPC_Base
{
	function registerDataStructure()
	{
		$fields=array(
			"language"=>array(
				'type'=>'integer',
				'link_class'=>"LPC_language",
			),
			"message_key"=>array(
				'link_class'=>"LPC_i18n_reference",
			),
			"translation"=>array(),
		);

		$depend=array();

		return array(
			'table_name'=>'LPC_i18n_message',
			'id_field'=>'id',
			'fields'=>$fields,
			'depend'=>$depend,
		);
	}

	function onSave($new)
	{
		if ($new)
			return;
		$this->clearCache();
	}

	function clearCache()
	{
		LPC_I18n_messageFormatter::clearTranslationCache(
			$this->getAttr('message_key'),
			$this->getAttr('language')
		);
	}

	function beforeDelete($id)
	{
		$this->clearCache();
	}
}
