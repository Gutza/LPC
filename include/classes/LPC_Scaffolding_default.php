<?php

class LPC_Scaffolding_default extends LPC_Base
{

	private static $currentInstance;

	function registerDataStructure()
	{
		$fields=array(
			"className"=>array(),
			"attName"=>array(),
			"language"=>array(
				'type'=>'integer',
				'link_class'=>'LPC_Language',
			),
			"defaultValue"=>array(
				'type'=>'longtext',
			),
		);

		$depend=array();

		return array(
			'table_name'=>'LPC_scaffolding_default',
			'id_field'=>'id',
			'fields'=>$fields,
			'depend'=>$depend,
		);
	}

	function getDefault($className,$attName,$languageID=0)
	{
		if (!$languageID)
			$languageID=LPC_Language::getCurrent()->id;

		if (!isset(self::$currentInstance))
			self::$currentInstance=new LPC_Scaffolding_default();
		$sd=&self::$currentInstance;
		$ds=$sd->search(
			array('className','attName','language'),
			array($className,$attName,$languageID)
		);
		if (!$ds)
			return "";
		return $ds[0]->getAttr('defaultValue');
	}
}
