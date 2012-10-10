<?php

/**
* Bogdan Stancescu <bogdan@moongate.ro>, May 2012
*/
class LPC_Error extends LPC_Base
{
	public static $email="";
	public static $save=false;

	const TYPE_WARNING='warning';

	function registerDataStructure()
	{
		$fields=array(
			"message"=>array(
				"type"=>"longtext",
			),
			"type"=>array(),
			"date_registered"=>array(
				"type"=>"datetime",
				"flags"=>array(
					'sqlDate' => true,
				),
			),
		);

		$depend=array();

		return array(
			'table_name'=>'LPC_error',
			'id_field'=>'id',
			'fields'=>$fields,
			'depend'=>$depend,
		);
	}

	function newWarning($message)
	{
		if (self::$save)
			$errObj=self::saveError($message, self::TYPE_WARNING);
		if (self::$email)
			mail($email, LPC_project_name." -- warning", $message);
	}

	function saveError($message, $type)
	{
		$error=new LPC_Error();
		$error->setAttrs(array(
			'message'=>$message,
			'type'=>$type,
			'date_registered'=>time(),
		));
		$error->save();
		return $errObj;
	}
}
