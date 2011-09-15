<?php

class LPC_group_categories extends LPC_Base
{
	function registerDataStructure()
	{
		$fields=array(
			"name"=>array(
				'flags'=>array (
					'NULL' => true,
				),
			),
			"short_desc"=>array(
				'flags'=>array (
					'NULL' => true,
				),
			),
			"description"=>array(
				'flags'=>array (
					'NULL' => true,
				),
			),
			"parent"=>array(
				'type'=>'integer',
			),
		);

		$depend=array();

		return array(
			'table_name'=>'LPC_group_categories',
			'id_field'=>'id',
			'fields'=>$fields,
			'depend'=>$depend
		);
	}
}
