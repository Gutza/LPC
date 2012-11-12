<?php

class LPC_Scaffold_fld_visi extends LPC_Base
{
	const MOD_SHOW="force_show";
	const MOD_HIDE="force_hide";

	function registerDataStructure()
	{
		return array(
			'table_name'=>'LPC_scaffold_fld_visi',
			'id_field'=>'id', // bogus
			'fields'=>array(), // useless
		);
	}

	function getDiff($class, $diffType)
	{
		$rs=$this->query("
			SELECT field_name
			FROM ".$this->getTableName()."
			WHERE
				user=? AND
				class_name=? AND
				action=?
			",array(
				LPC_User::getCurrent()->id,
				$class,
				$diffType,
			)
		);
		$fields=array();
		while(!$rs->EOF) {
			$fields[]=$rs->fields['field_name'];
			$rs->MoveNext();
		}
		return $fields;
	}

	function removeForcedVisi($obj, $attr)
	{
		$this->query("
			DELETE FROM ".$this->getTableName()."
			WHERE
				user=? AND
				class_name=? AND
				field_name=?
		", array(
			LPC_User::getCurrent()->id,
			get_class($obj),
			$attr,
		));
	}

	function addForcedVisi($obj, $attr, $mode)
	{
		$this->query("
			INSERT INTO ".$this->getTableName()."
			(user, class_name, field_name, action)
			VALUES (?, ?, ?, ?)
		", array(
			LPC_User::getCurrent()->id,
			get_class($obj),
			$attr,
			$mode,
		));
	}
}
