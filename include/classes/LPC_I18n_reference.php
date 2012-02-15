<?php

class LPC_I18n_reference extends LPC_Base
{
	function registerDataStructure()
	{
		$fields=array(
			"comment"=>array(),
			"system"=>array(
				"type"=>"boolean",
			),
		);

		$depend=array(
			"translations"=>array(
				"class"=>"LPC_I18n_message",
				"attr"=>"message_key",
			),
		);

		return array(
			'table_name'=>'LPC_i18n_reference',
			'id_field'=>'message_key',
			'fields'=>$fields,
			'depend'=>$depend,
		);
	}
}
